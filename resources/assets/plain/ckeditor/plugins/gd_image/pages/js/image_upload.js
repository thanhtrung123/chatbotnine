/*
 * 画像アップロード処理用javascript
 */

//-------------------------------------------------
//	決定ボタンを押された際の処理
//	【引数】
//		now_file_path	: 編集中のフォルダパス
//	【戻値】
//		false	: 処理終了
//-------------------------------------------------
function cxSubmit_Upload(now_file_path) {
    var err_msg = '';
    var new_file_name_db = new Array();
    var new_file_name_path = new Array();
    var new_file_path = new Array();
    var check_file_name = new Array();
    // 初期化
    for (i = 0, c = 0; document.image_upload.elements['rename_file_path_' + i]; i++) {
        $('#rename_file_path_' + i).val("");
    }

    //ファイル情報の取得
    for (i = 0; document.image_upload.elements['image_name_' + i]; i++) {
        //画像名称の取得
        if ($('#image_name_' + i)) {
            if (typeof $('#image_name_' + i).val() === "undefined") {
                new_file_name_db[i] = "";
            } else {
                new_file_name_db[i] = $('#image_name_' + i).val();
            }
        }
        //ファイルパスの取得
        if ($('#image_path_' + i)) {
            if (typeof $('#image_path_' + i).val() === "undefined") {
                //ファイルパスの取得
                new_file_path[i] = "";
                // ファイル名のみ取得
                new_file_name_path[i] = "";
            } else {
                //ファイルパスの取得
                new_file_path[i] = $('#image_path_' + i).val();
                // ファイル名のみ取得
                new_file_name_path[i] = $('#image_path_' + i).val().slice($('#image_path_' + i).val().lastIndexOf('\\') + 1);
            }
            check_file_name[i] = new_file_name_path[i];
            // ファイル名を小文字に変換
            new_file_name_path[i] = new_file_name_path[i].toLowerCase();
        }
    }

    //必須チェック
    for (i = 0, c = 0; i < new_file_name_db.length; i++) {
        //画像名称
        if (new_file_path[i].length > 0 && new_file_name_db[i].length <= 0)
            err_msg += "画像名称"
                    + (!$('#image_id') || $('#image_id').val() == "" ? (i + 1)
                            : "") + "を入力してください。\n";
        // ファイルパス
        if (new_file_name_db[i].length > 0 && new_file_path[i].length <= 0)
            err_msg += (!$('#image_id') || $('#image_id').val() == "" ? "ファイル"
                    + (i + 1) : "差し替えファイル")
                    + "を選択してください。\n";
        // 画像指定なし
        if (new_file_name_db[i].length <= 0 && new_file_path[i].length <= 0)
            c++;
        if (c == new_file_name_db.length)
            err_msg += "アップロードする画像の指定がありません。画像を指定してください。\n";
    }

    //ファイル名チェック
    for (i = 0; i < new_file_name_path.length; i++) {
        //文字列が存在する場合
        if (new_file_name_path[i].length > 0) {
            //拡張子チェック
            var File_Exte;
            File_Exte = ALLOWED_EXTENSIONS_IMAGE.split(",");
            for (j = 0, c = 0; j < File_Exte.length; j++) {
                var ExteRegEx = new RegExp("(\\." + File_Exte[j] + ")$");
                if (!new_file_name_path[i].match(ExteRegEx, "i"))
                    c++;
                if (c == File_Exte.length)
                    err_msg += "ファイル" + (i + 1) + " : "
                            + "指定されたファイルはアップロードすることが出来ません。\n";
            }
            //使用不可の記号チェック
            if (new_file_name_path[i].match(/[^\w\-_\.~]/))
                err_msg += "アップロードできないファイル名です。ファイル名に使用できるのは半角英数字と - _ . ~ です。\n";
            // 「.」が複数含まれているかチェック
            if (new_file_name_path[i].indexOf('.') != new_file_name_path[i]
                    .lastIndexOf('.'))
                err_msg += "ファイル名に「.」が二つ以上ついているファイルはアップロードすることが出来ません。\n";
            // 「.」がファイル名の先頭に含まれているかチェック
            if (new_file_name_path[i].indexOf('.') == 0)
                err_msg += "ファイル名の先頭に「.」がついているファイルはアップロードすることが出来ません。\n";
        }
    }

    //エラーが存在すれば、returnする
    if (err_msg) {
        alert(err_msg);
        return false;
    }

    // try {
    // 	$('image_upload').submit();
    // } catch (e) {
    // 	alert("リクエストの送信に失敗しました。\n入力情報を確認してください。");
    // }
    try {
        var file_name = [];
        for (i = 0; i < new_file_name_path.length; i++) {
            // if (i > 0 && new_file_name_path[i] != "")
            //     prm += '&';
            if (new_file_name_path[i] != "")
                file_name[i] = new_file_name_path[i];
        }
        $.ajax({
            'url': checkImage,
            'type': 'POST',
            'dataType': 'json',
            'data': { filename: file_name},
        }).done(function (response) {
            var res = response.datas
            if (Object.keys(res).length > 0) {
                var html = '以下の画像ファイルは上書きされます。\r\n';
                for (var key in res) {
                    html += 'ファイル' + key + ': ' + res[key] + '\r\n';
                }
                var c_firm = confirm(html);
                if (c_firm == true) {
                    $('form#image_upload')[0].submit();
                } else {
                    return false;
                }
            } else {
                $('form#image_upload')[0].submit();
            }
        }).fail(function(xhr, ajaxOps, error) {
            alert("同名のファイルが存在します。\nファイル名を変更してください。");
        });
    } catch (e) {
        console.log(e.message);
        alert("リクエストの送信に失敗しました。\n入力情報を確認してください。");
    }
    return false;
}

//-------------------------------------------------
// 通信成功処理
// 【引数】
// r : 番号,ファイルパス 同名のファイルが存在する場合
// : 番号,空文字 同名のファイルが存在しない場合
// : 番号,none 入力ファイルが存在しない場合
// 【戻値】
// なし
// -------------------------------------------------
function RenameCheck_Success(r) {
    var msg = "";
    var rename_msg = "";
    fileinfo = r.responseText.split(",");
    for (i = 0; i < fileinfo.length; i++) {
        //上書きを行なう必要があるか確認
        // if(fileinfo[i].length > 0) msg += "ファイル" + (i + 1) + "\n";
        if (fileinfo[i].length > 0) {
            msg += "ファイル" + (i + 1) + "\n";
            rename_msg += "ファイル" + (i + 1) + " => "
                    + fileinfo[i].slice(fileinfo[i].lastIndexOf('/') + 1)
                    + "\n";
        }
    }

    //ダイアログを表示し、上書きを行うか確認する
    /*
     * if(msg != "" &&
     * !confirm("登録を行なう下記のファイルに同名のファイルが存在します。\nこのまま登録を行なうと、ファイルを上書きします。よろしいですか？\n\n【上書きファイル】\n" +
     * msg)) return false; try{ $('image_upload').submit(); } catch(e){
     * alert("リクエストの送信に失敗しました。\n入力情報を確認してください。"); }
     */

    if (msg != ""
            && !confirm("登録を行なう下記のファイルに同名のファイルが存在します。\n上書きしますか？\n\n【上書きファイル】\n"
                    + msg + "\nキャンセルを押すと下記のファイル名に変更されて登録されます。\n\n【下記のファイル名】\n"
                    + rename_msg)) {
        for (i = 0; i < fileinfo.length; i++) {
            if (fileinfo[i].length > 0)
                $('#rename_file_path_' + i).val() = fileinfo[i];
        }
    }
    try {
        $('#image_upload').submit();
    } catch (e) {
        alert("リクエストの送信に失敗しました。\n入力情報を確認してください。");
    }
}

//-------------------------------------------------
// 通信失敗処理
// 【引数】
// なし
// 【戻値】
// false : 処理終了
// -------------------------------------------------
function cxFailure() {
    alert("ファイルのアップロードに失敗しました。");
    return false;
}
