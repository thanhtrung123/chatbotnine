/*
 * FCK画像設定用共通関数
 */

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

/**
 * 画像編集フォルダの削除処理
 *	
 *【引数】
 *	valueObj	オブジェクト
 *				['edit_id']	画像編集に使用したフォルダ名
 *	
 *【備考】
 *	PHP を呼び出し、画像編集に使用したフォルダを削除する
 *	
 */
function cxImgEditFolderDelete(valueObj) {

	// 引数のチェック
	if (!valueObj['edit_id']) {
		return false;
	}

	// パラメータ作成
	prm = '?edit_id=' + valueObj['edit_id'];
	$.ajax({
        'url': url_refresh + prm,
        'type': 'GET',
    }).done(function (response) {
    }).fail(function(xhr, ajaxOps, error) {
        alert("同名のファイルが存在します。\nファイル名を変更してください。");
	});
}