<?php

namespace App\Services\Admin;

use App\Services\Admin\Traits\WysiwygTrait;
use App\Services\RepositoryServiceInterface;
use App\Services\Admin\ImageInformationService;
use App\Repositories\ImageInformation\ImageInformationRepositoryInterface;
use File;
use DB;

/**
 * Class WysiwygFileUploadService
 * @package App\Services\Admin
 */
class WysiwygFileUploadService
{
    use LogTrait;
    use WysiwygTrait;
    
    /**
     * @var ImageInformationService
     */
    private $service;

    /**
     * @var ImageInformationRepositoryInterface
     */
    private $repository;
    
     /**
      * WysiwygFileUploadService constructor
      * @param ImageInformationService $service
      * @param ImageInformationRepositoryInterface $repository
     */
    public function __construct(ImageInformationService $service, ImageInformationRepositoryInterface $repository)
    {
        $this->service = $service;
        $this->repository = $repository;
    }
    
    /**
     * リポジトリ取得
     * @return ImageInformationRepositoryInterface
     */
    public function getRepository()
    {
        return $this->repository;
    }
    
    /**
     * Upload image
     * @param array $data contain request
     * @return array data
     */
    public function processUploadImage($data)
    {
        //フォルダパス
        $real_base_path = public_path(env('QA_IMAGES_PATH', ''));
        //ファイルパス
        $file_path = "";
        $real_file_path = "";
        //アップロードファイル数分ループ
        $i = 0;
        //ファイル名
        $file_name = "";
        //エラーメッセージ
        $err_msg = "";
        //一時使用ファイルパス
        $base_path_tmp = "";
        $filedtl_ary_insert = array();
        $filedtl_ary_update = array();
        $file_path_upload = array();
        // Sysn Image
        try {
            DB::beginTransaction();
            // Get list image from db
            $image_lists_db = $this->service->getDataImage();
            // Get list image from server
            $images_list_dir = $this->service->getListImageDir();
            // Check image upload
            $this->service->checkImageDBWithDir($image_lists_db, $images_list_dir);
            $this->service->checkImageDirWithDB($image_lists_db, $images_list_dir);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $err_msg = config('message.upload_file_fail');
        }
        foreach ($_FILES as $file_key => $up_file) {
            //ファイルID
            $i = substr($file_key, -1);
            //ファイルが無ければスキップ
            if ($up_file['name'] == "") {
                continue;
            }
            $base_path_tmp = env('QA_IMAGES_PATH', '');
            
            //ファイルパスの設定
            //リネームが存在した場合、リネームされたファイル名を使用する
            if (($data['rename_file_path_' . $i] ?? '') != "") {
                $file_name = strtolower(basename($data['rename_file_path_' . $i]));
            }
            else {
                $file_name = strtolower($up_file['name']);
            }
            $file_path = $file_name;
            $real_file_path = public_path($base_path_tmp . '/' . $file_path);
            $overwrite = FALSE;
            //同名ファイルの確認
            if (File::exists($real_file_path)) {
                $overwrite = TRUE;
            }
            $err_msg = $this->checkValidationFile($up_file, $file_name);
            //エラーがあれば、抜ける
            if ($err_msg != "") {
                break;
            }

            if (!File::isDirectory(public_path($base_path_tmp))) {
                File::makeDirectory(public_path($base_path_tmp), 0777, true, true);
            }
            //ファイルの移動
            if (@move_uploaded_file($up_file["tmp_name"], ($real_file_path))) {
                chmod($real_file_path, 0777);
                $file_path_upload[] = $real_file_path;
            } else {
                $err_msg = config('message.upload_file_fail');
            }
            //エラーがあれば、抜ける
            if ($err_msg != "") {
                break;
            }
            if ($overwrite == TRUE) {
                $img_info = $this->repository->findOneBy(['file_path' => $base_path_tmp . '/' . $file_path]);
                if ($img_info) {
                    $filedtl_ary_update[$img_info['id']] = array(
                        'update_at' => date('Y-m-d H:i:s'),
                        'file_name' => $data['image_name_' . $i],
                    );
                } else {
                    $filedtl_ary_insert[] = [
                        'file_name' => $data['image_name_' . $i],
                        'file_path' => $base_path_tmp . '/' . $file_path,
                        'post_at' => date('Y-m-d H:i:s'),
                        'update_at' => date('Y-m-d H:i:s')
                    ];
                }
            } else {
                //登録用配列の設定
                $filedtl_ary_insert[] = [
                    'file_name' => $data['image_name_' . $i],
                    'file_path' => $base_path_tmp . '/' . $file_path,
                    'post_at' => date('Y-m-d H:i:s'),
                    'update_at' => date('Y-m-d H:i:s')
                ];
            }
        }
        //エラーメッセージがあれば、通常表示
        if ($err_msg != "") {
            foreach ($file_path_upload as $file_path_real) {
                if (@is_file($file_path_real)) {
                    @unlink($file_path_real);
                }
            }
            $err_msg =  "ファイル" . ($i + 1) . " : " . $err_msg . '\n';
            return $err_msg;
        } else {
            try {
                DB::beginTransaction();
                if ($filedtl_ary_insert) {
                    $this->repository->createMulti($filedtl_ary_insert);
                }
                if ($filedtl_ary_update) {
                    foreach ($filedtl_ary_update as $id => $data) {
                        $this->repository->update($id, $data);
                    }
                }
                DB::commit();
                return $err_msg;
            } catch (\Exception $e) {
                DB::rollBack();
                foreach ($file_path_upload as $file_path_real) {
                    if (@is_file($file_path_real)) {
                        @unlink($file_path_real);
                    }
                }
                $err_msg = config('message.save_db_fail');
                return $err_msg;
            }
        }
    }
    
    /**
     * Get list info data image
     * @param $data contain request
     * @return array data
     */
    public function getDataImage($data)
    {
        $page = 0;
        $max_row = 0;
        $maxrow_list = config('wysiwyg.config.max_images_displayed');
        // 検索条件配列
        $search = array(
            "keyword" => "", 
            "image_update_datetime" => "",
            "image_thumbnail_flg" => config('wysiwyg.config.flag_off'),
            "max_row_list" => $maxrow_list,
            "page" => 1,
        );
        foreach ($data ?? [] as $key => $item) {
            $search[$key] = $item;
        }
        // Get data img information all
        $data_img_all = $this->repository->getAll();
        foreach ($data_img_all as $img) {
            if(!File::exists(public_path($img['file_path']))) {
                $del = $this->repository->deleteOneById($img['id']);
            }
        }
        $page = (isset($data['page']) && is_numeric($data['page']) ? floor($data['page']) : 1);
        $max_row = isset($data['maxrow']) && is_numeric($data['maxrow']) ? floor($data['maxrow']) : key($maxrow_list);
        // Get data image
        $data_image = $this->repository->getDataImageInfo($search, $max_row);
        return [
            'data_image' => $data_image,
            'maxRow' => $max_row,
            'search' => $search
        ];
    }
    
    /**
     * Update image information
     * @param integer $id tbl_image_info.id
     * @param array $params image info
     * @return string $error_message
     */
    public function updateImageInfo($id, $params)
    {
        $error_message = '';
        $params['file_name'] = $params['image_name'];
        $params['update_at'] = date('Y-m-d H:i:s');
        $img_info = $this->repository->findImageInfo($id, $params['image_name']);
        if (!$img_info) {
            $this->repository->update($id, $params);
            return $error_message;
        }
        $error_message = config('message.file_same_exist');
        return $error_message;
    }
    
    /**
     * Delete image info
     * @param integer $id tbl_image_info.id
     * @return bool TRUE|FALSE
     */
    public function wysiwygImageDelete($id)
    {
        //Get img info
        $img_info = $this->repository->getOneById($id);
        // check and remove file path
        if (@is_file(public_path($img_info['file_path'])) && @unlink(public_path($img_info['file_path']))) {
            // Delete img
            $del = $this->repository->deleteOneById($id);
            return $del;
        }
        return false;
    }
    
    /**
     * Resize image info
     * @param integer $data Request
     * @return array data
     */
    public function wysiwygImageControll($data)
    {
       // 画像情報格納配列
        $image = array(
            // 画像名称
            "name" => "",
            // 画像パス
            "path" => "",
            // X
            "width" => 0,
            // Y
            "height" => 0,
            // 表示する画像パス
            "picture" => ""
        );

        // DBへ書き込む値
        $db_value = array(
            "name" => "",
            "path" => ""
        );

        // GET値より取得
        if (isset($data['id'])) {
            $image_id = $data['id'];
        }

        // POST値より取得
        if (isset($data['image_id'])) {
            $image_id = $data['image_id'];
        }
        // 現在のページパス
        $page_path = "";
        // エラーメッセージ
        $err_msg = "";
        $sucess_msg = "";
        $exit_flg = config('wysiwyg.config.flag_off');
        // 各種フラグ
        $resize_flg = config('wysiwyg.config.flag_off');
        $trimming_flg = config('wysiwyg.config.flag_off');
        $cancel_flg = config('wysiwyg.config.flag_off');
        $decision_flg = config('wysiwyg.config.flag_off');
        $rewrite_flg = config('wysiwyg.config.flag_off');
        $end_flg = config('wysiwyg.config.flag_off');
        $rewrite_enable = config('wysiwyg.config.flag_on');
        $img_use_flg = config('wysiwyg.config.flag_off');
        // 作業ID(ユニークフォルダ名に使用)の取得
        if (!isset($data['image_edit_id'])) {
            // 現在時間で新規作成
            $edit_id = uniqid(TRUE);
            //強制フラグON
            $rewrite_flg = config('wysiwyg.config.flag_on');
        } else {
            // POST値がある場合は引き継ぐ
            $edit_id = $data['image_edit_id'];
        }

        // IDを取得できない場合はエラー
        if (!isset($image_id)) {
            return array('status' => false, 'message' => config('message.img_not_selected'));
        }
        // Get Image Info
        $img_info = $this->repository->getOneById($image_id);
        if ($img_info) {
            $image['path'] = $img_info['file_path'];
            $image['picture'] = $img_info['file_path'];
            $image['name'] = $img_info['file_name'];
            $image['id'] = $img_info['id'];
            // フルパスの作成
            $image['filename'] = public_path($img_info['file_path']);
            // -- 画像の存在チェック
            if (!@is_file($image['filename'])) {
                return array('status' => false, 'message' => config('message.img_not_exist'));
            }
            // 画像情報取得
            $page_path = gd_dirname($img_info['file_path']);
            // DB登録用のデータを作成
            $db_value['id'] = $img_info['id'];
            $db_value['name'] = $img_info['file_name'];
            $db_value['path'] = $img_info['file_path'];
            // -- 画像の存在チェック
            $img_data = getimagesize($image['filename']);
            // 画像サイズ
            $image['width'] = $img_data[0];
            $image['height'] = $img_data[1];
            $ret_width = $img_data[0];
            $ret_height = $img_data[1];
            if (isset($data['image_edit_mode'])) {
                if ($data['image_edit_mode'] == config('wysiwyg.config.resize_mode')) {
                    // リサイズフラグセット
                    $resize_flg = config('wysiwyg.config.flag_on');
                } else if ($data['image_edit_mode'] == config('wysiwyg.config.trimming_mode')) {
                    // トリミングフラグセット
                    $trimming_flg = config('wysiwyg.config.flag_on');
                } else if ($data['image_edit_mode'] == config('wysiwyg.config.cancel_mode')) {
                    // キャンセルフラグセット
                    $cancel_flg = config('wysiwyg.config.flag_on');
                } else if ($data['image_edit_mode'] == config('wysiwyg.config.decision_mode')) {
                    // 決定フラグセット
                    $decision_flg = config('wysiwyg.config.flag_on');
                }
            }
            // 上書きフラグ
            if (isset($data['image_rewrite_flg'])) {
                $rewrite_flg = $data['image_rewrite_flg'];
            }
            if (in_array(config('wysiwyg.config.flag_on'), array(
                $resize_flg,
                $trimming_flg,
                $cancel_flg,
                $decision_flg
            ))) {
                // Get path forder temp image
                $tmp_dir = public_path(env('QA_IMAGES_PATH') . '/temp');
                if (!@is_dir($tmp_dir)) {
                    if (!@mkdir($tmp_dir)) {
                        return array('status' => false, 'errors' => config('message.create_folder_error'));
                    }
                    chmod($tmp_dir, 0777);
                }
                // ユーザーIDフォルダ内にユニークIDフォルダを作成
                $tmp_dir .= "/" . $edit_id;
                if (!@is_dir($tmp_dir)) {
                    if (!@mkdir($tmp_dir)) {
                        return array('status' => false, 'errors' => config('message.create_folder_error'));
                    }
                    chmod($tmp_dir, 0777);
                }
                // コピー先画像パスの決定
                $image['copyfile'] = $this->copyFileNameCreate($image['filename'], $tmp_dir . "/");
            }
            while (1) {
                if ($resize_flg == config('wysiwyg.config.flag_on')) {
                    // POSTのチェック
                    if (!isset($data['image_rs_x']) || !isset($data['image_rs_y'])) {
                        // POSTを取得できない場合エラー
                        $err_msg = config('message.resize_img_fail');
                        $exit_flg = config('wysiwyg.config.flag_on');
                        break;
                    }
                    // POST取得
                    if (isset($data['image_edit_picture'])) {
                        $image['filename'] = public_path($data['image_edit_picture']);
                    }
                    $resize['width'] = $data['image_rs_x'];
                    $resize['height'] = $data['image_rs_y'];
                    // リサイズ
                    if ($this->wysiwygImageResize($image['filename'], $image['copyfile'], $resize['width'], $resize['height']) === FALSE) {
                        $err_msg = config('message.resize_img_fail');
                        $exit_flg = config('wysiwyg.config.flag_on');
                        break;
                    }
                    chmod($image['copyfile'], 0777);
            
                } else if ($trimming_flg == config('wysiwyg.config.flag_on')) {
                    // POSTのチェック
                    if (!isset($data['image_tm_x1']) || !isset($data['image_tm_y1']) || !isset($data['image_tm_x2']) || !isset($data['image_tm_y2'])) {
                        // POSTを取得できない場合エラー
                        $err_msg = config('message.crop_img_fail');
                        $exit_flg = config('wysiwyg.config.flag_on');
                        break;
                    }
            
                    // POST取得
                    if (isset($data['image_edit_picture'])) {
                        $image['filename'] = public_path($data['image_edit_picture']);
                    }
                    $trimming['x1'] = $data['image_tm_x1'];
                    $trimming['y1'] = $data['image_tm_y1'];
                    $trimming['x2'] = $data['image_tm_x2'];
                    $trimming['y2'] = $data['image_tm_y2'];
            
                    // トリミング
                    if ($this->wysiwygImageTrimming($image['filename'], $image['copyfile'], $trimming['x1'], $trimming['y1'], $trimming['x2'], $trimming['y2']) === FALSE) {
                        $err_msg = config('message.crop_img_fail');
                        $exit_flg = config('wysiwyg.config.flag_on');
                        break;
                    }
                    chmod($image['copyfile'], 0777);
            
                } else if ($cancel_flg == config('wysiwyg.config.flag_on')) {
                    // 一つ前の編集画像取得＆最新の編集削除
                    $image['copyfile'] = $this->imageEditCancel($image['filename'], $tmp_dir . "/");
                    // 一つ前の編集がなければ元画像をセット
                    if ($image['copyfile'] == "") {
                        $image['copyfile'] = $image['filename'];
                    }
            
                } else if ($decision_flg == config('wysiwyg.config.flag_on')) {
                    try {
                        DB::beginTransaction();
                         // 編集した画像の最新を取得
                        $image['copyfile'] = $this->getCopyFileName($image['filename'], $tmp_dir . "/");
                        // 編集した画像がない場合は登録されている画像
                        if (!@is_file($image['copyfile'])) {
                            $image['copyfile'] = $image['filename'];
                        }
                        if ($rewrite_flg == config('wysiwyg.config.flag_on')) {
                            // Update data
                            $update_img = array(
                                'update_at' => date('Y-m-d H:i:s')
                            );
                            $this->repository->update($db_value['id'], $update_img);
                        } else {
                            // Create forder image
                            if (!@is_dir(public_path($page_path))) {
                                if (!@mkdir(public_path($page_path))) {
                                    $err_msg = config('message.register_img_fail');
                                    $exit_flg = config('wysiwyg.config.flag_on');
                                    break;
                                }
                                chmod(public_path($page_path), 0777);
                            }
                            $image['filename'] = public_path($page_path) . "/" . $data['filelink_path'] . substr($db_value['path'], strrpos($db_value['path'], "."));
                            $db_value['path'] = str_replace(public_path(''), "", $image['filename']);
                            $db_value['name'] = $data['filelink_name'];
                            // DB情報の削除
                            // IDは自動付加
                            if (isset($db_value['id'])) unset($db_value['id']);
                            if (@is_file($image['filename'])) {
                                $err_msg = config('message.file_name_exist');
                                break;
                            }
                            // Get img Info
                            $img_info = $this->repository->findOneBy(['file_path' => $db_value['path']]);
                            if ($img_info) {
                                // Delete img infor
                                $this->repository->deleteOneById($img_info['id']);
                            }
                            // Insert data
                            $insert_img_data = array(
                                'file_name' => $db_value['name'],
                                'file_path' => $db_value['path'],
                                'post_at' => date('Y-m-d H:i:s'),
                                'update_at' => date('Y-m-d H:i:s'),
                            );
                            $this->repository->create($insert_img_data);
                        }
                        if ($this->wysiwygImageCopy($image['copyfile'], $image['filename']) === FALSE) {
                            // コピー失敗
                            $err_msg = config('message.register_img_fail');
                            break;
                        }
                        chmod($image['filename'], 0777);
                        // 画像情報取得用に
                        $image['copyfile'] = $image['filename'];
                        // 処理終了フラグオン
                        $end_flg = config('wysiwyg.config.flag_on');
                        // エラーが無ければ終了
                        if ($err_msg == "") $exit_flg = config('wysiwyg.config.flag_on');
                        $sucess_msg = config('message.file_upload_complete');
                        DB::commit();
                    } catch (\Exception $e) {
                        DB::rollBack();
                        $err_msg = config('message.register_img_fail');
                        $exit_flg = config('wysiwyg.config.flag_on');
                        break;
                    }
                }
                if (in_array(config('wysiwyg.config.flag_on'), array(
                    $resize_flg,
                    $trimming_flg,
                    $cancel_flg,
                    $decision_flg
                ))) {
                    // 画像情報取得(GD)
                    $img_data = getimagesize($image['copyfile']);
                    // 画像サイズ
                    $image['width'] = $img_data[0];
                    $image['height'] = $img_data[1];
                    // 表示画像
                    $image['picture'] = str_replace(public_path('/'), "", $image['copyfile']);
            
                    // 上書きの場合は更新
                    if (!in_array(config('wysiwyg.config.flag_off'), array(
                        $rewrite_flg,
                        $decision_flg
                    ))) {
                        $ret_width = $img_data[0];
                        $ret_height = $img_data[1];
                    }
                }
                break;
            }
            if (strlen($err_msg) > 0) {
                $end_flg = config('wysiwyg.config.flag_on');
            }
            $data = array(
                'image_id' => $image_id,
                'image' => $image,
                'db_value' => $db_value,
                'errMsg' => $err_msg,
                'sucess_msg' => $sucess_msg,
                'imgUseFlg' => $img_use_flg,
                'edit_id' => $edit_id,
                'retWidth' => $ret_width,
                'retHeight' => $ret_height,
                'endFlg' => $end_flg,
                'exitFlg' => $exit_flg,
                'rewriteEnable' => $rewrite_enable,
                'rewriteFlg' => $rewrite_flg
            );
            return array('status' => true, 'data' => $data);
        } else {
            return array('status' => false, 'message' => config('message.file_not_exist'));
        }
    }
    
    /**
     * Refresh image info
     * @param $edit_id Id temp resize
     * @return bool TRUE|FALSE
     */
    public function wysiwygImageRefresh($edit_id)
    {
        if ($edit_id == 'all') {
            // Remove file temp
            foreach (glob(public_path(env('QA_IMAGES_PATH', '') . '/temp/*')) as $path_dir) {
                if (!deleteDirectory($path_dir)) {
                    return FALSE;
                }
            }
        } else {
            $path_dir = public_path(env('QA_IMAGES_PATH', '') . '/temp/' . $edit_id);
            if (is_dir($path_dir)) {
                if (!deleteDirectory($path_dir)) {
                    return FALSE;
                }
            }
        }
        return TRUE;
    }

    /**
     * Rename image info
     * @param $now_file_path path image now
     * @param $new_file_name path image new
     * @return $file
     */
    public function wysiwygImageRename($ext, $new_file_name, $new_name)
    {
        $err_msg = NULL;
        public_path(env('QA_IMAGES_PATH', ''));
        $file_path = public_path(env('QA_IMAGES_PATH', '') . '/' . $new_file_name . $ext);
        if (@is_file($file_path)) {
            $err_msg = config('message.file_name_exist');
        }
        $img_info = $this->repository->findOneBy(['file_name' => $new_name]);
        if ($img_info) {
            $err_msg = config('message.file_same_exist');
        }
        return $err_msg;
    }
}