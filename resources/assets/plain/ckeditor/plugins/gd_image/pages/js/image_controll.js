/*
 * 画像編集
 */

// イメージ読み込み

if (!window.cxIframeLayerCallback) {
    window.cxIframeLayerCallback = window.frameElement.cxIframeLayerCallback;
}

/**
 * 画面読み込み後
 * 
 * 決定処理後の場合は window クローズ
 * 
 * returnValue retObj['id'] = 画像のID (DBに登録されている); retObj['path'] =
 * 画像のパス(DBに登録されている); retObj['edit_id'] = 編集ID(tempフォルダ内に作成されるユニークフォルダ);
 * retObj['user_id'] = ユーザーID(tempフォルダ内に作成されるフォルダ);
 * 
 */
window.onload = function(){

    // 親画面に返す値をセット
    if ( retObj ) {
        window.returnValue = retObj;
    }

    // 処理終了フラグのチェック
    if ( endFlg && endFlg == 1 ) {
        if (errMsg) {
            alert( errMsg );
        }
        if ( exitFlg && exitFlg == 1 ) {
            // 使用したフォルダの削除
            //cxImgEditFolderDelete( retObj );
            // 親画面に返す値をセット
            if(retObj){
                retObj['endFlg'] = true;
                window.returnValue = retObj;
            }
            if(sucessMsg) {
                alert(sucessMsg);
            }
            // ウィンドウ(ダイアログ)を閉じる
            window.frameElement.cxIframeLayerCallback(window.returnValue);
        }
    }
}


/**
 * リサイズ
 *
 *	1. 入力がなければ警告
 *	2. 数字でない文字列の入力なら警告
 *	3. 0以下の入力なら警告
 *	4. 画像サイズ以上の入力なら警告
 *
 */
function cxResize() {

    // -- モードの設定 -- //
    $('#image_edit_mode').val(RESIZE_MODE);
    // -- リサイズに必要な情報の取得 -- //
    var imageWidth = $('#image_rs_x').val();
    var imageHeight = $('#image_rs_y').val();

    // -- 入力チェック -- //

    // 必須チェック
    if ( imageWidth == "" ) {
        alert('横幅が入力されていません。');
        $('#image_rs_x').focus();
        return false;
    }
    if ( imageHeight == "" ) {
        alert('高さが入力されていません。');
        $('#image_rs_y').focus();
        return false;
    }
    // 数値チェック
    chkW = cxDateNumeric( imageWidth );
    if( ! chkW ) {
        alert('横幅に数字ではない文字列が入力されています。');
        $('#image_rs_x').focus();
        return false;
    }
    chkH = cxDateNumeric( imageHeight );
    if( ! chkH ) {
        alert('高さに数字ではない文字列が入力されています。');
        $('#image_rs_y').focus();
        return false;
    }
    // 最小値チェック
    if( Number( imageWidth ) <= 0 ) {
        alert('横幅に0以下の数字は入力できません。');
        $('#image_rs_x').focus();
        return false;
    }
    if( Number( imageHeight ) <= 0 ) {
        alert('高さに0以下の数字は入力できません。');
        $('#image_rs_y').focus();
        return false;
    }
    // 最大値チェック
    if( Number( imageWidth ) + Number( imageHeight ) == Number(IMAGE_MAX_X) + Number(IMAGE_MAX_Y) ) {
        alert('サイズ変更後のサイズが現在のサイズと同じです。');
        $('#image_rs_x').focus();
        return false;
    }
    if( Number( imageWidth ) > IMAGE_MAX_X ) {
        alert('横幅に' + IMAGE_MAX_X + 'より大きい数字は入力できません。');
        $('#image_rs_x').focus();
        return false;
    }
    if( Number( imageHeight ) > IMAGE_MAX_Y ) {
        alert('高さに' + IMAGE_MAX_Y + 'より大きい数字は入力できません。');
        $('#image_rs_y').focus();
        return false;
    }

    // -- サブミット -- //
    cxSubmit();
    return false;

}

/**
 * キャンセル
 *
 */
function cxCancel() {
    // モードの設定
    $('#image_edit_mode').val(CANCEL_MODE);

    // 編集を戻すことの確認
    if ( ! window.confirm( '画像の編集をひとつ前の状態に戻しますか？\n※前の状態に戻すと元に戻すことはできません。' ) ) {
        return false;
    }

    // -- サブミット -- //
    cxSubmit();
    return false;
}

/**
 * 決定
 *
 *	上書き(別名保存)の確認ダイアログの表示
 *
 */
function cxDecision() {
    // モードの設定
    $('#image_edit_mode').val(DECISION_MODE);

    // 上書きの確認
    if ( $('#image_rewrite_flg')[0].checked ) {
        if ( ! window.confirm( '編集を行った画像を上書き保存します。よろしいですか？' ) ) {
            return false;
        }
        //サブミット
        cxSubmit();
    }
    // 新規追加の確認
    else {
        if ( ! window.confirm( '編集を行った画像を別名で保存します。よろしいですか？' ) ) {
            return false;
        }
        //名称指定用レイヤーを表示
        if(olg_file_name) $('#filelink_name').val(olg_file_name);
        if(olg_file_path) $('#filelink_path').val(olg_file_path);
        cxLayer('#image_reffer',1,340,340);
    }
    return false;
}

/**
 * 名称指定用レイヤーの決定
 *
 * @return false;
 */
function cxRefferSubmit(image_id) {
    err_msg = "";
    // ファイル名の取得
    new_file_name = $('#filelink_path').val();
    new_name = $('#filelink_name').val();
    ext = $('#file_ext').val();
    check_file_name = new_file_name;
    // ファイル名を小文字に変換
    new_file_name = new_file_name.toLowerCase();

    // 必須チェック
    if($('#filelink_name').val().length <= 0) err_msg += "画像名称を入力してください。\n";
    if($('#filelink_path').val().length <= 0) err_msg += "ファイル名を入力してください。\n";

    //ファイル名チェック
    // 使用不可の記号チェック
    if(new_file_name.match(/[^\w\-_~]/)) err_msg += "指定できないファイル名です。ファイル名に使用できるのは半角英数字と - _ ~ です。\n";

    // エラーが存在すれば、returnする
    if(err_msg){
        alert(err_msg);
        return false;
    }
    //同名のファイルが存在するか確認
    // cxAjaxCommand('cxFCKRename_Check','now_file_path=' + now_file_path + '&new_file_name=' + new_file_name,RenameCheck_Success);
    $.ajax({
        'url': url_rename + '?ext=' + ext + '&new_file_name=' + new_file_name + '&new_name=' + new_name,
        'type': 'GET',
    }).done(function (response) {
        if (response != "") {
            alert(response);
        } else {
            cxSubmit();
        }
    }).fail(function(xhr, ajaxOps, error) {
        alert("同名のファイルが存在します。\nファイル名を変更してください。");
    });
    return false;
}

/**
 * 同名のファイルチェック通信成功処理
 *
 * @param r 同名のファイルが存在する場合はファイルパスを、同名のファイルが存在しない場合は空文字を返す
 * @return false 処理終了
 */
function RenameCheck_Success(r){
    //同名のファイルが存在する場合
    if(r.responseText.length > 0){
        alert("同名のファイルが存在します。\nファイル名を変更してください。");
        return false;
    }
    //同名のファイルが存在しない場合
    cxSubmit();
    return false;
}

/**
 * 通信失敗処理
 *
 * @return false 処理終了
 */
function cxFailure(){
    alert("ファイルの登録処理に失敗しました。");
    return false;
}

// 送信(全ボタン共通)
function cxSubmit() {
    // -- サブミット -- //
    document.image_controll.submit();
    return false;
}

/**
 * アスペクト比の値を入力ボックスに代入
 *
 *【引数】
 *	lwEmID	横幅の入力を行うことができるエレメント
 *	lhEmID	高さの入力を行うことができるエレメント
 *	rw		アスペクト比計算の元となる横幅
 *	rh		アスペクト比計算の元となる高さ
 *	mode	0 = 高さを求める(横幅に入力がある場合)
 *			1 = 横幅を求める(高さに入力がある場合)
 *
 *【戻値】
 *	なし
 *
 *【備考】
 *	入力ボックスの値を書き換えます
 *
 */
function cxAspect( lwEmID, lhEmID, rw, rh, mode ) {

    var lw = $('#' + lwEmID).val();
    var lh = $('#' + lhEmID).val();

    if ( mode == 0 ) {
        // 縦
        $('#' + lhEmID).val(cxBreadth( lw, rw, rh, "HEIGHT" ));
    } else {
        // 横
        $('#' + lwEmID).val(cxBreadth( lh, rw, rh, "WIDTH" ));
    }

}

/**
 * 上書きチェックボックス変更時処理
 *
 *【引数】
 *	chkE	チェックボックスのID名
 *
 *【戻値】
 *	なし
 *
 *【備考】
 *	編集画像が使用されている場合は確認ダイアログの表示
 *
 */
function cxRewriteChk( chkE ) {
    if ( $( '#' + chkE )[0].checked ) {
        if ( imgUseFlg && imgUseFlg == 1 ) {
            // チェックしてないことにする
            $( '#' + chkE )[0].checked = false;
            // 確認
            if ( ! window.confirm( '現在編集中の画像は別ページで使用されています。\n上書きしてもよろしいでしょうか。' ) ) {
                // いいえならチェック解除
                return false;
            }
            // はいならチェック
            $( '#' +  chkE ).checked = true;
        }
    }
}

/**
 * 渡された値と同一比率の幅を返す
 * 
 * 【引数】 breadth 横幅・高さ width 元となる画像等の横幅 height 元となる画像等の高さ mode 計算の仕方
 * 
 * 【戻値】 ret 算出された横幅・高さ
 * 
 * 【備考】 mode = "WIDTH" の場合は横幅を算出 mode = "HEIGHT"の場合は高さを算出 結果は値を四捨五入して返します
 * 
 */
function cxBreadth(breadth, width, height, mode) {

    // 結果変数 宣言
    var ret = "";

    // 入力なし
    if (breadth == "" || width == "" || height == "") {
        return ret;
    }

    if (cxDateNumeric(breadth) == true) {
        if (mode == "WIDTH") {
            // 横
            ret = Math.round((breadth * width) / height);
        } else if (mode == "HEIGHT") {
            // 縦
            ret = Math.round((breadth * height) / width);
        }
    }

    return ret;

}