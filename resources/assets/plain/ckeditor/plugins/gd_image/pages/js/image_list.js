/*
 * 画像リスト
 */

// イメージ読み込み
cxPreImages(path_link + '/ckeditor/images/fckimage/btn_imageedit_on.jpg',
        path_link + '/ckeditor/images/fckimage/btn_setup_on.jpg',
        path_link +  '/ckeditor/images/fckimage/title_imageset.jpg',
        path_link +  '/ckeditor/images/fckimage/btn_close.jpg',
        path_link +  '/ckeditor/images/fckimage/tab_pc_off.jpg',
        path_link +  '/ckeditor/images/fckimage/tab_server_on.jpg',
        path_link +  '/ckeditor/images/fckimage/bar_search.jpg',
        path_link +  '/ckeditor/images/fckimage/bar_filelist.jpg',
        path_link +  '/ckeditor/images/fckimage/btn_search.jpg');

// -------------------------------------------------
// ドキュメントロード時
// 【引数】
// e : イベント
// 【戻値】
// true
// -------------------------------------------------
window.document.onkeydown = function(e) {
    e = e || event || this.parentWindow.event;
    switch (e.keyCode) {
    //ESC
    case 27:
        window.parent.Cancel();
        return false;
        break;
    }
    return true;
}

//-------------------------------------------------
// 親画面にオブジェクトを返す
// 【引数】
// id : 画像のID
// 【戻値】
// なし
// -------------------------------------------------
function cxReturn(id) {
    // 代入
    $('#url').val($('#url_' + id).val());
    $('#width').val($('#width' + id).val());
    $('#height').val($('#height' + id).val());
    $('#alt').val($('#image_name_' + id).html().replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;"));
    // $('#url')[0].value = $F('#url_' + id);
    // $('#width')[0].value = $F('#width_' + id);
    // $('#height')[0].value = $F('height_' + id);
    // $('#alt')[0].value = $('image_name_' + id).innerHTML.unescapeHTML();

    $('#image_select').submit();
    return false;
}

//-------------------------------------------------
// プロパティを開く
// 【引数】
// id : 画像のID
// 【戻値】
// なし
// -------------------------------------------------
function cxShowProperty(id) {
    prop_path = url_proptype + "?id=" + id;
    cxIframeLayer(
            prop_path,
            460,
            250,
            COVER_SETTING.COLOR,
            '',
            function (pObj) {
                // 戻り値がundefined以外なら再検索
                if (pObj != undefined)
                    cxSearch();
            }
    );
}

//-------------------------------------------------
// 検索ボタン押下後サブミット
// 【引数】
// なし
// 【戻値】
// なし
// -------------------------------------------------
function cxSearch() {
    $('#image_list').submit();
    return false;
}

//-------------------------------------------------
// 画像編集ダイアログを表示する
// 【引数】
// id : 画像のID
// 【戻値】
// なし
// -------------------------------------------------
function cxEdit(id) {
    var newDlg;
    var Open_URL;
    var Open_Title;
    var Open_Width;
    var Open_Height;
    Open_URL = url_controll
            + "?id="
            + id + (mode == "library" ? "&prev_page=library" : "");
    Open_Title = "サイト内にリンク";
    Open_Width = Infinity;
    Open_Height = Infinity;
    Open_resize = true;
    var fobj = {'edit_id' : 'all'};
    cxImgEditFolderDelete(fobj);
    // ダイアログ表示
    cxIframeLayer(
        Open_URL,
        Open_Width,
        Open_Height,
        COVER_SETTING.COLOR,
        '',
        function (pObj) {
            cxImgEditFolderDelete(pObj);
            // 戻り値がundefined以外なら再検索
            if (pObj != undefined) {
                if (pObj['endFlg'])
                    cxSearch();
            }
        },
        undefined,
        true
    );
}

/**
 * 指定された画像を削除する
 * @param id 画像のID
 * @param name 画像の名称
 * @return false
 */
function cxDeleteImage(id, name) {
    //確認用ダイアログを表示
    if (confirm(name + "を削除します。よろしいですか？\r\n※先に戻すことはできません。")) {
        //削除実行PHPを呼び出す(Ajax)
        $.ajax({
            'url': url_delete + '?image_id=' + id,
            'type': 'GET',
        }).done(function (response) {
            if (response != "") {
                alert(response);
            } else {
                alert('画像を削除しました');
                cxSearch();
            }
        }).fail(function(xhr, ajaxOps, error) {
            alert(name + 'の削除に失敗しました');
        });
    }
    return false;
}


/**
 * ページ番号をPOSTする
 * @param page ページ番号
 * @return false
 */
function cxPageSet(page) {
    $('#page_post').find('input[name="page').val(page);
    $('#page_post').find('input[name="maxrow').val($('#dispNum').val());
    $('#page_post').submit();
    return false;
}

/**
 * 表示件数を変える
 * @param prev_num 表示件数
 * @return false
 */
function cxDispNum(prev_num) {
    $('#page_post').find('input[name="page').val(1);
    $('#page_post').find('input[name="maxrow').val(prev_num);
    $('#page_post').submit();
    return false;
}
