<?php

namespace App\Services\Admin\Traits;

use File;

/**
 * Trait for using Service
 * Trait WysiwygTrait
 * @package App\Services\Admin\Traits
 */
trait WysiwygTrait
{
    /**
     * 画像コピー先ファイル名を作成
     * @param $original_file_name コピー元ファイルパス
     * @param $up_folder 画像がアップロードされているフォルダ
     * @return $copy_file_name
     *
    */
    function copyFileNameCreate($original_file_name, $up_folder)
    {
        // 拡張子取得
        $path_info = pathinfo($original_file_name);
        $exte = "." . $path_info['extension'];
        // コピー先ファイル名(拡張子なし)
        $copy_file = basename($original_file_name, $exte);
        $copy_file = preg_replace("/(_[\d]*)$/", "", $copy_file);
        // -- 連番付加 -- //
        $cnt = 1;
        while (1) {
            // コピー先ファイル名
            $copy_file_name = $up_folder . $copy_file . "_" . sprintf("%d", $cnt) . $exte;
            // 存在チェック
            if (!@is_file($copy_file_name)) {
                // 存在しない場合はbreak
                break;
            }
            // カウントアップ
            $cnt++;
        }
        // コピー先ファイル名を返す
        return $copy_file_name;
    }

    /**
     * 画像コピー先ファイルのファイル名を取得
     * @param $original_file_name コピー元ファイルパス
     * @param $up_folder 画像がアップロードされているフォルダ
     * @param $back_number 最新からのバックナンバー
     * @return $copy_file_name
    */
    function getCopyFileName($original_file_name, $up_folder, $back_number = 0)
    {
        // 拡張子取得
        $path_info = pathinfo($original_file_name);
        $exte = "." . $path_info['extension'];
        // コピー先ファイル名(拡張子なし)
        $copy_file = basename($original_file_name, $exte);
        $copy_file = preg_replace("/(_[\d]*)$/", "", $copy_file);
        // 連番付加
        $cnt = 1;
        while (1) {
            // コピー先ファイル名
            $copy_file_name = $up_folder . $copy_file . "_" . sprintf("%d", $cnt) . $exte;
            // 存在チェック
            if (!@is_file($copy_file_name)) {
                // 存在しない場合は -1 が最新
                if ($cnt - 1 - $back_number > 0) {
                    // 指定した番号のファイルが存在
                    $copy_file_name = $up_folder . $copy_file . "_" . sprintf("%d", $cnt - 1 - $back_number) . $exte;
                }
                else {
                    // ない
                    $copy_file_name = "";
                }
                break;
            }
            // カウントアップ
            $cnt++;
        }
        // コピー先ファイル名を返す
        return $copy_file_name;
    }

    /**
     * 画像編集キャンセル処理
     * @param $original_file_name コピー元ファイルパス
     * @param $up_folder
     * @return call function
    */
    function imageEditCancel($original_file_name, $up_folder)
    {
        // 編集の最新を削除
        $fn = $this->getCopyFileName($original_file_name, $up_folder);
        if (@is_file($fn)) {
            @unlink($fn);
        }
        // 一つ前の編集画像パスを返す
        return $this->getCopyFileName($original_file_name, $up_folder);
    }

    /**
     * リサイズ画像を作成する
     * @param $original_path コピー元画像パス
     * @param $copy_path コピー先画像パス
     * @param $img_w リサイズX
     * @param $img_h リサイズY
     * @return bool
    */
    function wysiwygImageResize($original_path, $copy_path, $img_w, $img_h)
    {
        // コピー元画像が存在しなければエラー
        if (!@is_file($original_path)) return FALSE;
        // コピー元画像情報取得
        $image_info = getimagesize($original_path);
        // 配列作成
        $original = array(
                "path" => $original_path, 
                "x1" => 0, 
                "y1" => 0, 
                "x2" => $image_info[0], 
                "y2" => $image_info[1]
        );
        $copy = array(
                "path" => $copy_path, 
                "x1" => 0, 
                "y1" => 0, 
                "x2" => $img_w, 
                "y2" => $img_h
        );
        // アスペクト比
        $with_d = $img_w / $original['x2'];
        $height_d = $img_h / $original['y2'];
        $aspect = ($with_d < $height_d) ? $with_d : $height_d;
        $copy['x2'] = ceil($original['x2'] * $aspect);
        $copy['y2'] = ceil($original['y2'] * $aspect);
        // 画像加工
        if ($this->wysiwygImageEdit($original, $copy) === FALSE) {
            return FALSE;
        }
        return TRUE;
    }

    /**
     * 画像の編集を行う
     *【引数】
     * @param $original コピー元画像
     * @param $copy コピー先画像
     * @return bool TRUE|FALSE
    */
    function wysiwygImageEdit($original, $copy)
    {
        // check extension loaded
        if (!extension_loaded('gd')) {
            return FALSE;
        }
        // GDのバージョンが対応していない場合はエラー
        if (!function_exists("ImageCreateTrueColor")) return FALSE;
        
        // コピー元、コピー先画像が指定されていなければエラー
        if (!isset($original['path']) || !isset($copy['path'])) return FALSE;
        if (!isset($original['x1']) || !isset($copy['x1'])) return FALSE;
        if (!isset($original['y1']) || !isset($copy['y1'])) return FALSE;
        if (!isset($original['x2']) || !isset($copy['x2'])) return FALSE;
        if (!isset($original['y2']) || !isset($copy['y2'])) return FALSE;
        
        // コピー元画像の存在チェック
        if (!@is_file($original['path'])) return FALSE;
        // コピー元画像情報取得
        $image_info = getimagesize($original['path']);
        // 画像情報追加
        $original['width'] = $original['x2'] - $original['x1'];
        $original['height'] = $original['y2'] - $original['y1'];
        $copy['width'] = $copy['x2'] - $copy['x1'];
        $copy['height'] = $copy['y2'] - $copy['y1'];
        
        // 拡張子によっての処理
        switch ($image_info[2]) {
            // GIF
            case IMG_GIF :
                $img_t = ImageCreateTrueColor($copy['width'], $copy['height']);
                $img_i = ImageCreateFromGIF($original['path']);
                if (!$img_i) return FALSE;
                //透過GIFに対応
                if (($tc = $this->getGifTransparent($original['path'])) !== FALSE) {
                    @imagefill($img_t, 0, 0, @imagecolorallocate($img_t, $tc["red"], $tc["green"], $tc["blue"]));
                    $colorstotal = @imagecolorstotal($img_i);
                    @imagetruecolortopalette($img_t, false, $colorstotal);
                    @imagecolortransparent($img_t, @imagecolorclosest($img_t, $tc["red"], $tc["green"], $tc["blue"]));
                }
                break;
            case 3 :
            // PNG
            case IMG_PNG :
                $img_t = ImageCreateTrueColor($copy['width'], $copy['height']);
                $img_i = @ImageCreateFromPNG($original['path']);
                if (!$img_i) return FALSE;
                break;
            // JPG
            case IMG_JPG :
            case IMG_JPEG :
                $img_t = ImageCreateTrueColor($copy['width'], $copy['height']);
                $img_i = @ImageCreateFromJPEG($original['path']);
                if (!$img_i) return FALSE;
                break;
            // 他
            default :
                return FALSE;
        }
        ImageCopyResampled($img_t, $img_i, $copy['x1'], $copy['y1'], $original['x1'], $original['y1'], $copy['width'], $copy['height'], $original['width'], $original['height']);
        // 画像作成
        $new_info = pathinfo($copy['path']);
        switch ($new_info['extension']) {
            case 'gif' : // GIF
                ImageGIF($img_t, $copy['path']);
                break;
            case 'png' : // PNG
                ImagePNG($img_t, $copy['path']);
                break;
            default : // 他 JPG
                ImageJpeg($img_t, $copy['path']);
                break;
        }
        //破棄
        ImageDestroy($img_i);
        ImageDestroy($img_t);
        return TRUE;
    }

    /**
     * トリミング画像を作成する
     *【引数】
     * @param $original_path コピー元画像パス
     * @param $copy_path コピー先画像パス
     * @param $x1 トリミング左上座標X
     * @param $y1 トリミング左上座標Y
     * @param $x2 トリミング右下座標X
     * @param $y2 トリミング右下座標Y
     * @return bool TRUE|FALSE
    */
    function wysiwygImageTrimming($original_path, $copy_path, $x1, $y1, $x2, $y2)
    {
        // コピー元画像が存在しなければエラー
        if (!@is_file($original_path)) return FALSE;
        // 配列作成
        $original = array(
            "path" => $original_path, 
            "x1" => $x1, 
            "y1" => $y1, 
            "x2" => $x2, 
            "y2" => $y2
        );
        $copy = array(
                "path" => $copy_path, 
                "x1" => 0, 
                "y1" => 0, 
                "x2" => $x2 - $x1, 
                "y2" => $y2 - $y1
        );
        // 画像加工
        if ($this->wysiwygImageEdit($original, $copy) === FALSE) {
            return FALSE;
        }
        return TRUE;
    }

    /**
     * Copy image
     *【引数】
     * @param $original_path コピー元画像パス
     * @param $copy_path コピー元画像パス
     * @return bool TRUE|FALSE
    */
    function wysiwygImageCopy($original_path, $copy_path)
    {
        // 同一画像なら TRUE
        if ($original_path == $copy_path) {
            return TRUE;
        }
        // コピー元画像が存在しなければエラー
        if (!@is_file($original_path)) return FALSE;
        // コピー元画像情報取得
        $image_info = getimagesize($original_path);
        // 配列作成
        $original = array(
                // コピー元
                "path" => $original_path, 
                "x1" => 0, 
                "y1" => 0, 
                "x2" => $image_info[0], 
                "y2" => $image_info[1]
        );
        $copy = array(
                // コピー先
                "path" => $copy_path, 
                "x1" => 0, 
                "y1" => 0, 
                "x2" => $image_info[0], 
                "y2" => $image_info[1]
        );
        // 画像コピー
        if (@copy($original_path, $copy_path) === FALSE) {
            return FALSE;
        }
        return TRUE;
    }
    
    /**
     * Check validation file
     * @param $up_file
     * @param $file_name
     * @return string $err_msg
    */
    function checkValidationFile($up_file, $file_name)
    {
        $err_msg = '';
        //ファイルチェック
        if (!is_uploaded_file($up_file["tmp_name"])) {
            $err_msg = config('message.file_invalid');
            return $err_msg;
        }
        //ファイルエラーチェック
        if ($up_file["error"] != UPLOAD_ERR_OK) {
            $err_msg = config('message.upload_file_fail');
            return $err_msg;
        }
        $file_ext =  strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        if (!in_array($file_ext, config('wysiwyg.config.type_file_image'))) {
            $err_msg = str_replace(":file_name:", $file_name , config('message.extension'));
        }
        $image_info = @getimagesize($up_file["tmp_name"]);
        if ((!isset($image_info)) OR (isset($image_info) && $image_info == null)) {
            $err_msg = config('message.file_specify_cannot_upload');
            return $err_msg;
        }

        if ($image_info[0] > config('wysiwyg.config.max_image_width')) {
            $err_msg = str_replace(":file_name:", $file_name , config('message.max_image_width'));
        }
        if ($image_info[1] > config('wysiwyg.config.max_image_height')) {
            $err_msg = str_replace(":file_name:", $file_name , config('message.max_image_height'));
        }
        if ($err_msg != '') {
            return $err_msg;
        }
        //ファイル名チェック
        //使用不可の記号チェック
        if (preg_match('/[^\w\-_\.~]/i', $file_name)) {
            $err_msg = config('message.file_name_regex');
        }
        //「.」が複数含まれているかチェック
        if (strpos($file_name, '.') != strrpos($file_name, '.')) {
            $err_msg = config('message.file_name_wrong');
        }
        //「.」がファイル名の先頭に含まれているかチェック
        if (strpos($file_name, '.') == 0) {
            $err_msg = config('message.cannot_be_upload');
        }
        //ファイルサイズチェック
        //0byte以下のファイルチェック
        if ($up_file["size"] <= 0) {
            $err_msg = config('message.file_0kb');
        }
        //ファイルMAXサイズチェック
        if ($up_file["size"] > config('wysiwyg.config.max_image_capacity') * 1024) {
            $err_msg = config('message.file_too_large');
        }
        return $err_msg;
    }
    
    /**
     * GIF画像の透過情報を取得する
     * @param $img_path 画像のパス 
     * @return 透過GIFの場合は、透過情報の配列を返す。それ以外の場合は、FALSEを返す。
     */
    function getGifTransparent($img_path)
    {
        //画像の情報を取得
        $sz = @getimagesize($img_path);
        //GIF画像の作成
        $im = @imagecreatefromgif($img_path);
        //横幅のピクセル分ループ
        for($sx = 0; $sx < $sz[0]; $sx++) {
            //縦幅のピクセル分ループ
            for($sy = 0; $sy < $sz[1]; $sy++) {
                //指定ピクセルのRGBを取得
                $rgb = @imagecolorat($im, $sx, $sy);
                //取得したRGBのインデックスを取得
                $idx = @imagecolorsforindex($im, $rgb);
                //α値が0以外の場合（透過の場合）
                if ($idx['alpha'] !== 0) {
                    //イメージの削除
                    @imagedestroy($im);
                    return $idx;
                }
            }
        }
        //イメージの削除
        @imagedestroy($im);
        return false;
    }
}