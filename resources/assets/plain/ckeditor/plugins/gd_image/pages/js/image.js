/*
 * 画像設定用javascript
 */

//定数の設定
var CKEDITOR = window.top.CKEDITOR;
var oEditorWin = CKEDITOR.currentInstance.window.$;
// var oEditor = window.top.document.getElementsByTagName('iframe')[0]; // window.parent.InnerDialogLoaded();
// var FCK = oEditor.FCK;
// var FCKLang = oEditor.FCKLang;
// var FCKConfig = oEditor.FCKConfig;
// var FCKDebug = oEditor.FCKDebug;

// 変数の設定
var oImage_real;
var oImage;
var oImageOriginal;
var bHasImage;
var sPrevUrl;
var bLockRatio = true;

// ダイアログ下部のボタンを消す
// window.parent.SetButton(false);

// -------------------------------------------------
// キーを押したときの処理
// 【引数】
// なし
// 【戻値】
// なし
// -------------------------------------------------
window.document.onkeydown = function(e) {
	e = e || event || this.parentWindow.event;
	switch (e.keyCode) {
	//ENTER
	case 13:
		var oTarget = e.srcElement || e.target;
		if (oTarget.tagName == 'TEXTAREA')
			return;
		if (oTarget.tagName == 'INPUT'
				&& (oTarget.type == 'text' || oTarget.type == 'file')) {
			if (oTarget.form.id == 'image_upload')
				$('submit_upload').click();
			else if (oTarget.form.id == 'image_property')
				$('submit_property').click();
		}
		return false;
		break;
	// ESC
	// case 27:
		// window.parent.Cancel();
		// return false;
		// break;
	}
	return true;
}

//-------------------------------------------------
// ロード時に実行される
// 【引数】
// なし
// 【戻値】
// なし
// -------------------------------------------------
window.onload = function() {
	//フォーカスを自身のウィンドウにする
	window.self.focus();
	// 定数テキストの書き換え
	CKEDITOR.FCKLanguageManager.TranslatePage(document);
	// キーに名称を付ける
	GetE('btnLockSizes').title = CKEDITOR.FCKLanguageManager.FCKLang.DlgImgSizeLocked;
	GetE('btnResetSize').title = CKEDITOR.FCKLanguageManager.FCKLang.DlgBtnResetSize;
	// GETされたモードがuploadなら、load処理終了
	if (get_mode == "upload") {
		ShowE('divProperty', false);
		ShowE('divUpload', true);
		return;
	}
	//エラーメッセージがあれば、アラート表示して、load処理終了
	if (err_msg) {
		ShowE('divProperty', false);
		ShowE('divUpload', true);
		alert(err_msg);
		return;
	}
	//現在選択されている画像の取得
	oImage_real = GetSelectedElement();
	if (oImage_real && oImage_real.tagName != 'IMG'
			&& !(oImage_real.tagName == 'INPUT' && oImage_real.type == 'image'))
		oImage_real = null;
	// POSTの値を取得
	LoadPost();
	// 画面上のイメージを選択している場合
	if (oImage_real) {
		//画像が無ければ、画面上のイメージを取得
		if (!oImage)
			oImage = oImage_real;
		// 画面上イメージの選択の有無
		bHasImage = true;
	}
	//画面上のイメージを選択していない場合
	else
		bHasImage = false;
	// プロパティを表示
	if (oImage) {
		ShowE('divProperty', true);
		ShowE('divUpload', false);
	}
	//アップロードを表示
	else {
		ShowE('divProperty', false);
		ShowE('divUpload', true);
		return;
	}
	//リンクに設定されている値の取得
	LoadSelection();
	// プレビュー情報の取得
	LoadPreview();
	// alt入力フォームの表示切り替え
	setAlt();
}

//-------------------------------------------------
// POSTの値を取得し、イメージオブジェクトを作成する
// 【引数】
// なし
// 【戻値】
// なし
// -------------------------------------------------
function LoadPost() {
	//POST値が無ければ、returnする
	if (POST["url"] == "")
		return;
	// イメージオブジェクトの作成
	oImage = document.createElement('IMG');
	// POST値の取得
	oImage.src = POST["url"];
	oImage.width = POST["width"];
	oImage.height = POST["height"];
	oImage.alt = POST["alt"];
	sPrevUrl = POST["url"];
}

//-------------------------------------------------
// 現在設定されている値を取得する
// 【引数】
// なし
// 【戻値】
// なし
// -------------------------------------------------
function LoadSelection() {
	//イメージオブジェクトが存在しなければ、returnする
	if (!oImage)
		return;
	// 画像パスを取得
	var sUrl = GetAttribute(oImage, 'data-cke-saved-src', '');
	if (sUrl.length == 0)
		sUrl = GetAttribute(oImage, 'src', '');
	sPrevUrl = sUrl;

	// イメージにセットされている値の取得
	GetE('txtAlt').value = GetAttribute(oImage, 'alt', '');
	if (GetE('txtAlt').value == "")
		GetE('chkAlt').checked = true;
	// 表示位置を取得
	GetE('cmbAlign').value = GetAttribute(oImage, 'align', '');
	switch (GetE('cmbAlign').value) {
	case 'left':
		GetE('align02').checked = true;
		break;
	case 'right':
		GetE('align03').checked = true;
		break;
	case 'center':
	case 'middle':
		GetE('align04').checked = true;
		break;
	case 'top':
		GetE('align05').checked = true;
		break;
	case 'bottom':
		GetE('align06').checked = true;
		break;
	default:
		GetE('align01').checked = true;
		break;
	}
	//イメージの縦・横幅を取得
	var iWidth, iHeight;
	var regexSize = /^\s*(\d+)px\s*$/i;
	// イメージの横幅
	if (oImage.style.width) {
		var aMatch = oImage.style.width.match(regexSize);
		if (aMatch)
			iWidth = aMatch[1];
	}
	//イメージの縦幅
	if (oImage.style.height) {
		var aMatch = oImage.style.height.match(regexSize);
		if (aMatch)
			iHeight = aMatch[1];
	}
	//値の取得
	GetE('txtWidth').value = iWidth ? iWidth
			: GetAttribute(oImage, "width", '');
	GetE('txtHeight').value = iHeight ? iHeight : GetAttribute(oImage,
			"height", '');
}

//-------------------------------------------------
// プレビュー用画像の情報作成
// 【引数】
// なし
// 【戻値】
// なし
// -------------------------------------------------
var TimerID;
function LoadPreview() {
	//プレビュー画像にURLをセット
	GetE('imgPreview').src = sPrevUrl + "?rnd=" + getRand(5);
	// オリジナル画像のセット
	UpdateOriginal(POST["url"] == "" ? null : true);
	TimerID = setInterval('imgComp_Check()', 100);
	// プレビュー表示の値を取得
	iWidth = (GetE('txtWidth').value != "" ? GetE('txtWidth').value
			: oImage.offsetWidth);
	iHeight = (GetE('txtHeight').value != "" ? GetE('txtHeight').value
			: oImage.offsetHeight);
	// プレビュー画像用サイズの取得
	reduction_rate = Reduction_Rate_Fixation(iWidth, iHeight, 130, 130);
	// プレビュー画像にサイズをセット
	GetE('imgPreview').width = Math.round(iWidth
			* (reduction_rate != 0 ? reduction_rate : 1));
	GetE('imgPreview').height = Math.round(iHeight
			* (reduction_rate != 0 ? reduction_rate : 1));
}
function imgComp_Check() {
	if (oImageOriginal.complete == undefined || oImageOriginal.complete) {
		clearInterval(TimerID);
		// プレビュー表示の値を取得
		iWidth = oImageOriginal.width;
		iHeight = oImageOriginal.height;
		// プレビュー画像用サイズの取得
		reduction_rate = Reduction_Rate_Fixation(iWidth, iHeight, 130, 130);
		// プレビュー画像にサイズをセット
		GetE('imgPreview').width = Math.round(iWidth
				* (reduction_rate != 0 ? reduction_rate : 1));
		GetE('imgPreview').height = Math.round(iHeight
				* (reduction_rate != 0 ? reduction_rate : 1));
	}
}

//-------------------------------------------------
// オリジナル情報を持った画像オブジェクトの作成
// 【引数】
// resetSize : サイズをリセットする場合のフラグ
// 【戻値】
// なし
// -------------------------------------------------
function UpdateOriginal(resetSize) {
	//イメージオブジェクトの作成
	oImageOriginal = null;
	oImageOriginal = document.createElement('IMG');
	// 画像のパスを設定
	oImageOriginal.src = GetE('imgPreview').src;
	// サイズをリセットする
	if (resetSize) {
		oImageOriginal.onload = function() {
			this.onload = null;
			GetE('txtWidth').value = oImageOriginal.width;
			GetE('txtHeight').value = oImageOriginal.height;
		}
	}
}

//-------------------------------------------------
// 決定ボタンが押された際の処理
// 【引数】
// なし
// 【戻値】
// true : 成功時
// false : 失敗時
// -------------------------------------------------
function cxSubmit_Property() {
	//altのチェック
	if (GetE('chkAlt').checked == false) {
		//altの取得
		var alt = trim(GetE('txtAlt').value);
		// 必須チェック
		if (alt.length == 0) {
			alert('代替テキストを入力してください。');
			GetE('txtAlt').focus();
			return false;
		}
	}
	//イメージが無ければ、画面上に作成
	if (!bHasImage) {
		// oImage_real = FCK.CreateElement('IMG');
        oImage_real = CreateAndInsertImageToEditor();
        // 値のセット
        UpdateImage(oImage_real.$);
    }
	else {
		CKEDITOR.currentInstance.fire('saveSnapshot');
        // 値のセット
        UpdateImage(oImage_real);
    }
	// 作成したイメージを選択状態にする
	if (!bHasImage)
		// FCK.Selection.SelectNode(oImage_real);
        CKEDITOR.currentInstance.getSelection().selectElement(oImage_real);
	
    CKEDITOR.dialog.getCurrent().parts.close.$.click();
    // 正常終了
	return true;
}

//-------------------------------------------------
// FCKeditorの選択部分に入力した値をセットする
// 【引数】
// oimg : イメージオブジェクト
// 【戻値】
// なし
// -------------------------------------------------
function UpdateImage(oimg) {
	oimg.src = sPrevUrl + "?rnd=" + getRand(5);
	SetAttribute(oimg, "data-cke-saved-src", sPrevUrl);
	oimg.setAttribute( "alt", GetE('chkAlt').checked == false ? trim(GetE('txtAlt').value) : '');
	SetAttribute(oimg, "width", GetE('txtWidth').value);
	SetAttribute(oimg, "height", GetE('txtHeight').value);
	SetAttribute(oimg, "align", GetE('cmbAlign').value);
	// スタイルで指定された横幅を消す
	if (oimg.style.width)
		oimg.style.width = '';
	// スタイルで指定された縦幅を消す
	if (oimg.style.height)
		oimg.style.height = '';
}

//-------------------------------------------------
// 入力値のロック状態を切り替える
// 【引数】
// lockButton : ロックボタンのオブジェクト
// 【戻値】
// なし
// -------------------------------------------------
function SwitchLock(lockButton) {
	//ロックを変更する
	bLockRatio = !bLockRatio;
	lockButton.className = (bLockRatio ? 'BtnLocked' : 'BtnUnlocked');
	lockButton.title = (bLockRatio ? CKEDITOR.FCKLanguageManager.FCKLang.DlgImgSizeLocked
			: CKEDITOR.FCKLanguageManager.FCKLang.DlgImgSizeUnLocked);

	// ロックがtrueの場合
	if (bLockRatio) {
		//サイズを変更する
		if (GetE('txtWidth').value.length > 0)
			OnSizeChanged('WIDTH');
		else
			OnSizeChanged('HEIGHT');
	}
}

//-------------------------------------------------
// 入力された値と同一比率になるように入力されていない値を変更する
// 【引数】
// dimension : 入力されたフォームの情報
// 【戻値】
// なし
// -------------------------------------------------
function OnSizeChanged(dimension) {
	if (bLockRatio) {
		if (dimension == 'WIDTH')
			GetE('txtHeight').value = cxBreadth(GetE('txtWidth').value,
					oImageOriginal.width, oImageOriginal.height, 'HEIGHT');
		else if (dimension == 'HEIGHT')
			GetE('txtWidth').value = cxBreadth(GetE('txtHeight').value,
					oImageOriginal.width, oImageOriginal.height, 'WIDTH');
	}
}

//-------------------------------------------------
// 変更したサイズをオリジナルサイズに戻す
// 【引数】
// なし
// 【戻値】
// なし
// -------------------------------------------------
function ResetSizes() {
	if (!oImageOriginal)
		return;
	GetE('txtWidth').value = oImageOriginal.width;
	GetE('txtHeight').value = oImageOriginal.height;
}

//-------------------------------------------------
// altの入力フォームをチェックボックスにより切り替える
// 【引数】
// なし
// 【戻値】
// なし
// -------------------------------------------------
function setAlt() {
	//チェックが付いていた場合
	if (GetE('chkAlt').checked == true) {
		GetE('txtAlt').disabled = true;
		GetE('txtAlt').style.backgroundColor = "#C0C0C0";
	}
	//チェックが付いていなかった場合
	else {
		GetE('txtAlt').disabled = "";
		GetE('txtAlt').style.backgroundColor = "";
	}
}

//-------------------------------------------------
// 画像の回り込みをセットする
// 【引数】
// val : 表示位置
// 【戻値】
// なし
// -------------------------------------------------
function alignSet(val) {
	//値をセット
	GetE('cmbAlign').value = val;
	switch (val) {
	case 'left':
		GetE('align02').checked = true;
		break;
	case 'right':
		GetE('align03').checked = true;
		break;
	case 'center':
	case 'middle':
		GetE('align04').checked = true;
		break;
	case 'top':
		GetE('align05').checked = true;
		break;
	case 'bottom':
		GetE('align06').checked = true;
		break;
	default:
		GetE('align01').checked = true;
		break;
	}
}

//-------------------------------------------------
// 画像の変更倍率を取得する(固定版)
// 【引数】
// olg_w : 元の横幅
// olg_h : 元の縦幅
// new_w : 新しい横幅
// new_h : 新しい縦幅
// 【戻値】
// 変更する倍率
// -------------------------------------------------
function Reduction_Rate_Fixation(olg_w, olg_h, new_w, new_h) {
	//変更倍率
	var reduction_rate = 0;
	// 指定値とのサイズの差を取得
	var w = olg_w - new_w;
	var h = olg_h - new_h;

	// 縦・横両方が、指定値と違う場合
	if (w > 0 && h > 0) {
		if (w > h)
			reduction_rate = new_w / olg_w;
		else
			reduction_rate = new_h / olg_h;
	}
	//縦・横どちらかが、指定値と違う場合
	else if (w > 0 || h > 0) {
		//横が違う場合
		if (w > 0)
			reduction_rate = new_w / olg_w;
		// 縦が違う場合
		else if (h > 0)
			reduction_rate = new_h / olg_h;
		// それ以外の場合
		else
			reduction_rate = 1;
	}
	//両方同じ場合
	else
		reduction_rate = 1;
	// 値を返す
	return (isNaN(reduction_rate) ? 0 : reduction_rate);
}

//-------------------------------------------------
// ランダム数字列を取得する
// 【引数】
// num : 桁数
// 【戻値】
// ランダムな数字列
// -------------------------------------------------
function getRand(num) {
	var ret = 0;
	for ( var i = 0; i < num; i++) {
		ret = ret * 10 + (Math.floor(Math.random() * 10));
	}
	return ret;
}

// functions from FCK
function GetSelectedElement() {
    var ele = CKEDITOR.currentInstance.getSelection().getSelectedElement();
    if (ele == null) return null;
    var C = ele.$; 
    // var A = oEditorWin && oEditorWin.getSelection();
    // if (!A || A.rangeCount < 1) return null;
    // var B = A.getRangeAt(0);
    // if (B.startContainer != B.endContainer || B.startContainer.nodeType != 1 || B.startOffset != B.endOffset - 1) return null;
    // var C = B.startContainer.childNodes[B.startOffset];
    if (C.nodeType != 1) return null;
    return C;
};

function CreateAndInsertImageToEditor() {
    var img = CKEDITOR.dom.element.createFromHtml('<img>');
    CKEDITOR.currentInstance.insertElement(img);
    return img;
}