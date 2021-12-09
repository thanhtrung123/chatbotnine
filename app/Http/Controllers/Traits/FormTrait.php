<?php

namespace App\Http\Controllers\Traits;

use Maatwebsite\Excel\Importer;
use Validator;
use Storage;
use Illuminate\Http\Request;
use ZipArchive;
use File;

/**
 * フォーム使用コントローラ用トレイト
 * Trait FormTrait
 * @package App\Http\Controllers\Traits
 */
trait FormTrait
{

    /**
     * リソースに対するパーミッションを自動設定
     * @param string $resource リソース名
     */
    public function autoSetPermission($resource)
    {
        $privileges = config('acl.privileges');
        foreach (array_keys($privileges) as $action) {
            $this->middleware("permission:{$resource} {$action}", ['only' => [$action]]);
        }
    }

    /**
     * 登録リクエスト
     * @param Request $request
     * @return bool
     */
    public function isStore($request)
    {
        return ($request->get('store', false) !== false);
    }

    /**
     * 確認リクエスト
     * @param Request $request
     * @return bool
     */
    public function isConfirm($request)
    {
        return ($request->get('confirm', false) !== false);
    }

    /**
     * 確認画面からリダイレクトしたか
     * @param Request $request
     * @return bool
     */
    public function isRedirectConfirm($request)
    {
        return !empty($request->old('confirm', false));
    }

    /**
     * 確認画面用リダイレクトパラメータ取得
     * @param Request $request
     * @return array
     */
    public function getRedirectConfirm($request)
    {
        return [
            'isConfirm' => $this->isRedirectConfirm($request),
        ];
    }

    /**
     * 状態名取得
     * @param Request $request
     * @return string
     */
    public function getModeName($request)
    {
        $routeName = $request->route()->getName();
        if (preg_match('/import_store$/', $routeName)) {
            $modeName = __('インポート');
        } else if (preg_match('/store$/', $routeName)) {
            $modeName = _('登録');
        } else if (preg_match('/update$/', $routeName)) {
            $modeName = _('修正');
        } else if (preg_match('/destroy$/', $routeName)) {
            $modeName = _('削除');
        } else {
            $modeName = '';
        }
        return $modeName;
    }

    /**
     * Zipセッションキー取得
     * @return string
     */
    public function getZipSessionKey()
    {
        return self::ROUTE_NAME . '.zip_path';
    }
    
    /**
     * FileImportセッションキー取得
     * @return string
     */
    public function getFileImportSessionKey()
    {
        return self::ROUTE_NAME . '.file_import_path';
    }

    /**
     * FileImport保存&確認
     * @param Request $request
     * @param Importer $import
     * @param string $pathName 保存パス
     * @return array
     */
    public function confirmFile($request, $import, $pathName)
    {
        $id = md5(uniqid());
        $hasError = false;
        $path  = $_FILES['excel']['name'];
        $ext = pathinfo($path, PATHINFO_EXTENSION);
        $file_path = $request->file('excel')->storeAs($pathName, $id . '.' . $ext);
        $request->session()->put($this->getFileImportSessionKey(), $file_path);
        $data = $errors = [];
        $offset = 1;
        if (method_exists($import, 'headingRow')) {
            $offset += $import->headingRow();
        }
        try {
            $data = $import->toArray($file_path)[0];
        } catch (\Exception $e) {
            $errors['---'] = [[$ext.'の読み込みに失敗しました。']];
        }
        foreach ($data as $idx => $row) {
            $validator = Validator::make($row, $import->rules());
            $validator->setAttributeNames($import->customValidationAttributes());
            if (method_exists($import, 'validateRow')) {
                $error = $import->validateRow($validator, $row);
                if (!empty($error)) $errors[$idx + $offset] = $error;
            }
            if ($validator->fails()) {
                $errors[$idx + $offset] = $validator->errors()->toArray();
            }
        }
        //エラーがあったら消す
        if (!empty($errors)) {
            $hasError = true;
            Storage::delete($file_path);
        }
        return [
            'data' => $data,
            'errors' => ["{$pathName}_errors" => $errors],
            'hasError' => $hasError,
        ];
    }

    /**
     * Confirm file zip
     * @param Request $request
     * @param string $path_name 保存パス
     * @return array
     */
    public function confirmFileZip($request, $path_name)
    {
        $custom_messages = [
            'mimes' => __('bot.message.scenario_upload_zip_error')
        ];
        $validator = Validator::make($request->all(), [
            'zip' => 'file|mimes:zip|max:' . config('scenario.import_export.post_max_size'),
        ], $custom_messages);
        if ($validator->fails()) {
            return [
                'errors' => $validator->errors()->first(),
            ];
        }
        $errors = '';
        $zip_file = $request->file('zip');
        $zip = new ZipArchive;
        if ($zip->open($zip_file, ZipArchive::CREATE) !== TRUE) {
            $errors = __('bot.message.scenario_upload_error');
        } else {
            $zip_file_content = array();
            for ($i = 0; $i < $zip->count(); $i++) {
                $zip_file_content[] = $zip->statIndex($i)['name'] ?? NULL;
            }
            foreach (config('scenario.import_export.file_json') as $name_file) {
                $key_json = array_search($name_file, $zip_file_content);
                if ($key_json === false) {
                    $errors = __('bot.message.scenario_upload_zip_error');
                    break;
                }
            }
        }
        if ($errors != '') {
            return [
                'errors' => $errors,
            ];
        }
        $id = md5(uniqid());
        $ext = pathinfo($zip_file->getClientOriginalName(), PATHINFO_EXTENSION);
        $file_path = $request->file('zip')->storeAs($path_name, $id . '.' . $ext);
        $request->session()->put($this->getZipSessionKey(), $file_path);
        return [
            'errors' => $errors,
        ];
    }
    
    /**
     * Fileインポート
     * @param Request $request
     * @param Importer $import
     */
    public function importFile($request, $import)
    {
        ini_set('max_execution_time', config('excel.imports.timeout', 600));
        ini_set('memory_limit', config('excel.imports.memory', '512M'));
        $file_path = $request->session()->get($this->getFileImportSessionKey());
        $import->import($file_path);
        Storage::delete($file_path);
    }

    /**
     * 一時保存Fileクリア
     * @param Request $request
     */
    public function clearFile($request)
    {
        if ($this->isRedirectConfirm($request)) return;
        $file_path = $request->session()->get($this->getFileImportSessionKey());
        if (!empty($file_path)) {
            Storage::delete($file_path);
            $request->session()->forget($this->getFileImportSessionKey());
            //何らかの原因で残ってしまったものも削除(1日以上)
            $ext_import = config('excel.imports.extensions');
            foreach ($ext_import as $ext) {
                foreach (glob(Storage::path(dirname($file_path)) . '/*' . $ext) as $path) {
                    if ((time() - filemtime($path)) < 86400) continue;
                    unlink($path);
                }
            }
        }
    }
    
    /**
     * Clear file zip
     * @param Request $request
     * @param string $path_name
     */
    public function clearZipFile($request, $path_name)
    {
        if ($this->isRedirectConfirm($request)) return;
        if ($request->session()->has($this->getZipSessionKey())) {
            $zip_path = storage_path('app/' . $request->session()->get($this->getZipSessionKey()));
            if (is_file($zip_path)) {
                unlink($zip_path);
            }
            $request->session()->forget($this->getZipSessionKey());
        } else if (is_dir(storage_path('app/' . $path_name))) {
            File::deleteDirectory(storage_path('app/' . $path_name));
        }
    }

    /**
     * フラッシュメッセージ
     * @param array $ary
     * @return array
     */
    public function createFlushMessage($ary)
    {
        return [
            'flush_message' => $ary,
        ];
    }

    /**
     * エラー用フラッシュメッセージ
     * @param array $ary
     * @return array
     */
    public function createFlushError($ary)
    {
        return [
            'flush_error' => $ary,
        ];
    }

    /**
     * 直接エラー表示
     * @param array $ary
     * @return array
     */
    public function createDirectError($ary)
    {
        return [
            'direct_errors' => $ary,
        ];
    }
}