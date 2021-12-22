/*
 *
 */
// Create Element.remove() function if not exist
if (!('remove' in Element.prototype)) {
    Element.prototype.remove = function() {
        if (this.parentNode) {
            this.parentNode.removeChild(this);
        }
    };
}
//adminルートパス
var baseUrl = location.pathname.substring(0,location.pathname.indexOf("/ai/")+"/ai/".length);
if(baseUrl.indexOf("/") != 0) baseUrl = "/"+baseUrl;
var aiadmin_path = baseUrl + 'admin';
var prev_layer;
var prev_submenu_id;
var prev_mobile_submenu_id;
var URLRegEx = new RegExp("^s?https?:\/\/[-_.!~*'()a-zA-Z0-9;\/?:\@&=+\$,%#]+$");
var EMailRegEx = new RegExp("^([a-zA-Z0-9\.\\-\/_]{1,})@([a-zA-Z0-9\.\\-\/_]{1,})\.([a-zA-Z0-9\.\\-\/_]{1,})$");
var edit_closet_flg = true;
var COVER_SETTING = {
  NOCOVER : {value: 0}, 
  TRANSPARENT: {value: 1, opacity: 0}, 
  COLOR : {value: 2, opacity: 0.3}
};

//画像のプリロード
function cxPreImages() {
	var d=document;
	if(d.images){
		if(!d.MM_p) d.MM_p=new Array();
		var i;
		var j=d.MM_p.length;
		var a=cxPreImages.arguments;
		for(i=0; i<a.length; i++) {
			if (a[i].indexOf("#")!=0){ 
				d.MM_p[j]=new Image;
				d.MM_p[j++].src=a[i];
			}
		}
	}
}


//
function cxDateNumeric(p) {
	var temp1,temp2;
	var regObj = new RegExp("[^0-9]","i");
	//
	temp1 = p.match(regObj)
	if(temp1) {
		return false;
	} else {
		return true;
	}
}


function trim(value){
	return String(value).replace(/^\s+|\s+$/g, "");
}

function cxCloser(element_id) {
	$(element_id).style.display = 'none';
	clearInterval(SubmenuIntervalId[element_id]);
}


function cxComboHidden(exc){
	if(exc == undefined)exc = Array(0);
	oElms = document.getElementsByTagName("select");	
	for(i = 0; i < oElms.length; i++){
		flg = 0;
		if($(exc)){
			for(j = 0; j < exc.length; j++){
				if(oElms[i].id == exc[j])flg = 1;
			}
		}
		if(flg == 0){
			oElms[i].style.visibility = 'hidden';
		}
	}
}


/**
 * サーバ上にあるファイルの存在チェックを行う(Ajaxの同期処理)
 * 
 * @param file_path ファイルのパス
 * @return ファイルが存在する場合は、TRUE。そうでない場合は、FALSEを返す。
 */
function file_exists(file_path){
	//引数のチェック
	if(file_path == "") return false;
	try{
		var result = eval('(' + request.transport.responseText + ')');
		if(result == true) return true;
		else return false;
	}
	catch(err){
		return false;
	}
}

/**
 * 配列に値があるかチェックする
 * 
 * @param needle 探す値
 * @param haystack 配列
 * @param strict 型チェックの有無
 * @return 
 */
function in_array(needle,haystack,strict){
	//変数の宣言
	var ret_flg = false;
	var key = "";

	//引数が無くてもエラーにならないように。
	strict = !!strict;

	//チェック
	for(key in haystack){
		if((strict && haystack[key] === needle) || (!strict && haystack[key] == needle)){
			ret_flg = true;
			break;
		}
	}

	return ret_flg;
}

/**
 * ファイルサイズに3桁ごと「,」を付与する
 * 
 * @param val 3桁ごとに区切る数字（ファイルサイズ）
 * @return 3桁ごとに区切られた数字
 */
function numEdit(val){
	val += "";
	var cnt = 0;
	var ret = "";
	var len = val.length;
	for(var i = 0;i < len;i++){
		var s = val.substring(i,i + 1);
		ret += s;
		cnt++;
		if((len - cnt) / 3 > 0 && (len - cnt) % 3 == 0) ret += ',';
	}
	return ret;
}


/**
 * 正規表現のメタ文字をエスケープする
 * @param str 文字列
 * @return 正規表現のメタ文字をエスケープした文字列
 */
function reg_replace(pStr){
	//エスケープするメタ文字
	var ary = [['\\','\\\\'],['/','\\\/'],['.','\\\.'],['*','\\\*'],['+','\\\+'],['-','\\\-'],['?','\\\?'],['(','\\\('],[')','\\\)'],['[','\\\['],[']','\\\]'],['|','\\\|']];
	//エスケープ処理
	for(var i = 0;i < ary.length;i++){
		pStr = pStr.replace(ary[i][0],ary[i][1]);
	}
	return pStr;
}

function cxEscapeHtmlChars(str) {
	return str.replace(/["<>&]/g,function($0){
		switch($0) {
			case '<':
				return "&lt;";
			case '>':
				return "&gt;";
			case '"':
				return "&quot;";
			case "&":
				return "&amp;";
		}
	 });
}

/**
 * HTML用にエスケープした文字を元に戻す
 * @param str 文字列
 * @return HTML用エスケープを元に戻した文字列
 */
function cxUnEscapeHtmlChars(str) {
	return str.replace(/(&lt;|&gt;|&quot;|&amp;)/g,function($0){
		switch($0) {
			case '&lt;':
				return "<";
			case '&gt;':
				return ">";
			case '&quot;':
				return '"';
			case '&amp;':
				return "&";
		}
	 });
}

/********** 二度押し防止アクション START************/
// window の Load イベントを取得する。
window.onload = window_Load;
	
function window_Load() {
	var i;

	// 全リンクのクリックイベントを submittableObject_Click で取得する。
	for (i = 0; i < document.links.length; i ++) {
		var item = document.links[i]
		Object.Aspect.around(item, "onclick", checkLoading);
	}

	if(document.getElementsByTagName("form").length == 0){
		return true;
	}

	// 全ボタンのクリックイベントを submittableObject_Click で取得する。
	for (i = 0; i < document.forms[0].elements.length; i ++) {
		var item = document.forms[0].elements[i]
		if (item.type == "button" ||
			item.type == "submit" ||
			item.type == "reset") {
		Object.Aspect.around(item, "onclick", checkLoading);
		}
	}

	return true;
}

//2度押し抑止アスペクト
var checkLoading = function(invocation) {
	if (isDocumentLoading()) {
		alert("処理中です…");
		return false;
	}

	return invocation.proceed();
}

//画面描画が終わったかどうか
function isDocumentLoading() {
	return (document.readyState != null &&
			document.readyState != "complete");
}

//アスペクト用
Object.Aspect = {
	_around: function(target, methodName, aspect) {
		var method = target[methodName];
		target[methodName] = function() {
			var invocation = {
				"target" : this,
				"method" : method,
				"methodName" : methodName,
				"arguments" : arguments,
				"proceed" : function() {
					if (!method) {
						return true;
					}
					return method.apply(target, this.arguments);
				}
			};
			return aspect.apply(null, [invocation]);
		};
	},
	around: function(target, methodName, aspect) {
		this._around(target, methodName, aspect);
	}
}
/********** 二度押し防止アクション END************/

// モーダルダイヤログ表示用
function cxShowModalDialog(path,name,status){
	dd = new Date();
	if(path.indexOf("?") == -1){
		path += "?refprm="+dd.getTime();
	} else {
		path += "&refprm="+dd.getTime();
	}
	retObj = showModalDialog(path,name,status);
	return retObj;
}
/**
 * 小窓表示
 * @param path 小窓に表示するファイルのURL
 * @param name ウィンドウ名
 * @param status パラメータ
 * @return 成功 -> ウィンドウオブジェクト 失敗 -> null
 */
function cxShowWindow(path,name,status){
	dd = new Date();
	if(path.indexOf("?") == -1){
		path += "?refprm="+dd.getTime();
	} else {
		path += "&refprm="+dd.getTime();
	}
	retObj = window.open(path,name,status);
	return retObj;
}

// 編集画面の閉じる対応
function cxEditNonProp(){
	edit_closet_flg = false;
}

// 日付チェックの共通アラート表示
function disp_date_error(dc,focus_id){
	msg = new Array();
	msg = msg.concat(dc);
	if (msg.length > 0) {
		msg_str = msg.join('\n');
		alert(msg_str);
		if(focus_id)$(focus_id).focus();
		return false;
	}
	return true;
}

// 全ての文字列 s1 を s2 に置き換える
function replaceAll(expression, org, dest){
	return expression.split(org).join(dest);
} 

//IE8対応用ラジオボタンチェック
function isSelectCheck(id) {
	var select_ch_flg = false;
	var select = document.getElementsByName(id);
	for(var i=0; i<select.length; i++) {
		if (select[i].checked == true) {
			select_ch_flg = true;
			break;
		}
	}
	return select_ch_flg;
}
/**
 * IEのバージョン取得
 * @return TRUE->Varの数字 FALSE->IE以外
 */
function cxGetIeVersion(){
	
	//ブラウザのエンジン名
	var user_agent = window.navigator.userAgent.toLowerCase();
	//ブラウザのバージョン
	var app_ver = window.navigator.appVersion.toLowerCase();
	// IEの場合
	if (user_agent.indexOf("msie") > -1) {
		// IE6
		if (app_ver.indexOf("msie 6.0") > -1) return 6;
		// IE7
		if (app_ver.indexOf("msie 7.0") > -1) return 7;
		// IE8
		if (app_ver.indexOf("msie 8.0") > -1) return 8;
		// IE9
		if (app_ver.indexOf("msie 9.0") > -1) return 9;
		// IE10
		if (app_ver.indexOf("msie 10.0") > -1) return 10;
	}
	// IE11
	else if (user_agent.indexOf("trident") > -1) {
		if (app_ver.indexOf("rv:11.0") > -1) return 11;
	}
	// IE以外のブラウザ
	return false;
}
/**
 * 文字列をDOMに変換する
 * @param str 変換対象文字列
 * @return 文字列をDOMに変換したもの(DIVに入っている）
 */
function String2DOM(str){
	var n = document.createElement("div");
	n.innerHTML = str;
	return n;
}

/**
 * 連続する半角スペース変換
 * @palam		$rep_str 置換対象文字列
 * @return		置換後文字列
 *
 * 【備考】
 *	半角スペース以外の文字列が含まれない場合は置換しない。
 */
function replace_consecutive_space(rep_str){
	//半スペのみは置換しない
	if (rep_str.replace(/(^\s+)|(\s+$)/g, "") == '') {
		return rep_str;
	}
	//置換
	rep_str = rep_str.replace(/( )( +)/g, function(){return arguments[1] + arguments[2].replace(/ /g,'&nbsp;')});
	return rep_str;
}

function setCookie(cname, cvalue, exdays) {
	var newcookie = cname + "=" + cvalue + ";"
	if (exdays > 0) {
		var d = new Date();
		d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
		newcookie += "expires=" + d.toUTCString() + ";";
	}
	newcookie += "path=/";
	document.cookie = newcookie;
}

function getCookie(cname) {
	var name = cname + "=";
	var ca = document.cookie.split(';');
	for (var i = 0; i < ca.length; i++) {
		var c = ca[i];
		while (c.charAt(0) == ' ') {
			c = c.substring(1);
		}
		if (c.indexOf(name) == 0) {
			return c.substring(name.length, c.length);
		}
	}
	return "";
}

function deleteCookie(cname) {
	document.cookie = cname + '=;expires=Thu, 01 Jan 1970 00:00:01 GMT;path=/';
}

function getSelectedRadioValue(name) {
	var radios = document.getElementsByName(name);
	for (var i = 0; i < radios.length; i++) {
		if (radios[i].checked) {
			return radios[i].value;
		}
	}
	return null;
}

function setSelectedRadio(name, value) {
	var radios = document.getElementsByName(name);
	for (var i = 0; i < radios.length; i++) {
		if (radios[i].value === value) {
			radios[i].checked = true;
			return true;
		}
	}
	return null;
}

function cxCreateCover(coverSetting) {
	if (coverSetting == undefined || coverSetting == null || coverSetting == COVER_SETTING.NOCOVER) {
		return undefined;
	}
	var cover = document.createElement('div');
	cover.style.position = 'absolute';
	cover.style.top = 0;
	cover.style.left = 0;
	// make it cover whole page
	cover.style.width = window.top.document.documentElement.scrollWidth + 'px';
	cover.style.height = window.top.document.documentElement.scrollHeight + 'px';
	cover.style.backgroundColor = 'rgba(0,0,0,' + coverSetting.opacity + ')';
	cover.style.zIndex = 99999;
	return cover;
}

/**
 * レイヤー中央表示/非表示
 *
 * t: id of layer
 * sw: = 1 then show layer, = 0 then hide layer
 * width: width of layer
 * height: height of layer
 * coverSetting: COVER_SETTING enum
**/
function cxLayer(t,sw,width,height,coverSetting){
	if (sw==1) {
		if (width && height) {
			//var wcw = window.screen.width;
			//var wch = document.body.clientHeight;
			var wcw = (window.document.documentElement.clientWidth > 0 ? window.document.documentElement.clientWidth : window.document.body.clientWidth);
			var wch = (window.document.documentElement.clientHeight > 0 ? window.document.documentElement.clientHeight : window.document.body.clientHeight);
			var tx = (wcw - width) / 2;
			tx = (tx < 0 ? 0 : tx);
			var ty = (wch - height) / 2;
			//ty = (ty < 175 ? 175 : ty);
			ty = (ty < 0 ? 0 : ty);
			var sx = (document.compatMode == "CSS1Compat") ? document.documentElement.scrollLeft: document.body.scrollLeft;
			var st = (document.compatMode == "CSS1Compat") ? document.documentElement.scrollTop: document.body.scrollTop;
			var x = sx + tx;
			var y = st + ty;
			//IE8以上
			if (cxGetIeVersion() >= 8) {
				if ($(t)[0].parentNode.offsetParent) {
					y = (st + ty - $(t)[0].parentNode.offsetParent.offsetTop);
				}
			}
			if (navigator.userAgent.indexOf('Edge') > -1) {
				$(t)[0].style.left = window.pageXOffset + x + 'px';
				$(t)[0].style.top = window.pageYOffset + y + 'px';
			}
			else {
				$(t)[0].style.left = x + 'px';
				$(t)[0].style.top = y + 'px';
			}
			//ヘッダーより上に出す
			$(t)[0].style.zIndex = 99999;
			
			var cover = cxCreateCover(coverSetting);
			if (cover) {
				$(t)[0].parentNode.insertBefore(cover, $(t)[0]);
				$(t)[0].cover = cover;
			}
		}
		$(t)[0].style.display = 'block';
	} else {
		if ($(t)[0].cover != undefined) {
			if ($(t)[0].cover != null) {
				$(t)[0].cover.remove();
			}
			$(t)[0].cover = undefined;
		}
		$(t)[0].style.display = 'none';
	}
}

/**
 * Display dialog in iframe layer
 *
 * path: url to display in iframe
 * w: width
 * h: height
 * callback: call this func inside iframe to return data to parent window
 * name: iframe name
 * coverSetting: COVER_SETTING enum
 * addCloseBtn: add a close button for iframe
 * autoResize: adjust w and h after load
 * args: dialogArguments param
**/
function cxIframeLayer(path, w, h, coverSetting, name, callback, addCloseBtn, autoResize, args) {
	var iframe = top.document.createElement('iframe');
	iframe.setAttribute("id", "image_iframe");
	top.document.body.appendChild(iframe);
	$(iframe).css('position', 'absolute');
	$(iframe).css('left', '-1000px');
	var cover = cxCreateCover(coverSetting);
	if (cover) {
		iframe.parentNode.insertBefore(cover, iframe);
		iframe.cover = cover;
	}
	var cxIframeLayerCallback = function (retObj) {
		if (retObj != undefined && callback != undefined) {
			callback(retObj);
		}
		if (iframe.cover != undefined && iframe.cover != null) {
			$(iframe.cover).remove();
		}
		$(iframe).remove();
	};
	iframe.cxIframeLayerCallback = cxIframeLayerCallback;
	iframe.onload = function () {
		iframe.autoUpdateWH = function () {
			var iframeNewWidth = iframe.contentWindow.document.body.scrollWidth;
			if (iframeNewWidth > w) {
				iframeNewWidth = w;
			}
			var iframeNewHeight = iframe.contentWindow.document.body.scrollHeight + (addCloseBtn ? 40 : 0);
			if (iframeNewHeight > h) {
				iframeNewHeight = h;
			}
			updateWHForIframe(iframeNewWidth, iframeNewHeight);
		}
		if (autoResize) {
			iframe.autoUpdateWH();
		}
		
		if (iframe.contentWindow != undefined && iframe.contentWindow != null) {
			iframe.contentWindow.cxIframeLayerCallback = cxIframeLayerCallback
			var body = iframe.contentWindow.document.body;
			body.style.backgroundColor = '#fff';
			// if (addCloseBtn) {
				// var closeBtn = iframe.contentWindow.document.createElement('div');
				// closeBtn.innerHTML = '<table width="100%" border="0" cellspacing="0" cellpadding="0" class="layerheader"><tbody><tr><td align="left" valign="middle"></td><td width="78" align="right" valign="middle"><a href="javascript:cxIframeLayerCallback();"><img src="/ai/admin/images/btn/btn_close.jpg" alt="閉じる" width="58" height="19" border="0" style="margin: 4px 10px;"></a></td></tr></tbody></table>';
				// body.insertBefore(closeBtn, body.firstElementChild);
			// }
		}
		dragElement(iframe);
	}
	iframe.src = path;
	iframe.contentWindow.name = name;
	if (args) {
		iframe.args = args;
	}
	else {
		iframe.args = this;
	}
	if (!autoResize) {
		updateWHForIframe(w, h);
	}
	function updateWHForIframe(w, h) {
		if (w && h) {
			//var wcw = window.screen.width;
			//var wch = document.body.clientHeight;
			var wcw = (window.top.document.documentElement.clientWidth > 0 ? window.top.document.documentElement.clientWidth : window.top.document.body.clientWidth);
			var wch = (window.top.document.documentElement.clientHeight > 0 ? window.top.document.documentElement.clientHeight : window.top.document.body.clientHeight);
			var tx = (wcw - w) / 2;
			tx = (tx < 0 ? 0 : tx);
			var ty = (wch - h) / 2;
			//ty = (ty < 175 ? 175 : ty);
			ty = (ty < 0 ? 0 : ty);
			var sx = (top.document.compatMode == "CSS1Compat") ? top.document.documentElement.scrollLeft : top.document.body.scrollLeft;
			var st = (top.document.compatMode == "CSS1Compat") ? top.document.documentElement.scrollTop : top.document.body.scrollTop;
			var x = sx + tx;
			var y = st + ty;
			//IE8以上
			if (cxGetIeVersion() >= 8) {
				if (iframe.parentNode.offsetParent) {
					y = (st + ty - iframe.parentNode.offsetParent.offsetTop);
				}
			}

			iframe.style.position = 'absolute';
			if (navigator.userAgent.indexOf('Edge') > -1) {
				iframe.style.left = window.top.pageXOffset + x + 'px';
				iframe.style.top = window.top.pageYOffset + y + 'px';
			}
			else {
				iframe.style.left = x + 'px';
				iframe.style.top = y + 'px';
			}
			iframe.style.zIndex = 99999;
			iframe.style.borderColor = 'transparent';
			iframe.style.width = w + 'px';
			iframe.style.height = h + 'px';
		}
	}
	return iframe.contentWindow;
}

/**
 * 4バイト文字検知
 * @palam		$str チェック文字列
 * @return		検知した4バイト文字 / 4バイト文字を含まなければ空を返す
 */
function getFourByteString(str){
	var r4byte = str.replace(/^\s*[\r\n]+/gm, '');
	for (var i = 0; i < r4byte.length; i++) {
		try {
			encodeURIComponent(r4byte.charAt(i));
		} catch(e) {
			try {
				encodeURIComponent(r4byte.substring(i, i + 2).charAt(1));
			} catch (e) {
				return r4byte.substring(i, i + 2);
			}
		}
	}
	return "";
}
// 4バイト文字検知オブジェクト
var obj4byte = {
	errors: [],
	check: function(target, context){
		var getStr = getFourByteString(context);
		if (getStr != "") {
			this.errors.push(' ・'+target+'に「'+getStr+'」という文字が入力されています。');
			return false;
		}
		return true;
	},
	isError: function() {
		if (this.errors.length > 0) {
			return true;
		}
		return false;
	}
};

function dragElement(elmnt) {
	var pos1 = 0, pos2 = 0, pos3 = 0, pos4 = 0;
	if (elmnt.contentWindow.document.getElementById(elmnt.id + "_header")) {
		/* if present, the header is where you move the DIV from:*/
		elmnt.contentWindow.document.getElementById(elmnt.id + "_header").onmousedown = dragMouseDown;
	}

	function dragMouseDown(e) {
		e = e || window.event;
		e.preventDefault();
		// get the mouse cursor position at startup:
		pos3 = e.screenX;
		pos4 = e.screenY;
		elmnt.contentWindow.document.onmouseup = closeDragElement;
		// call a function whenever the cursor moves:
		elmnt.contentWindow.document.onmousemove = elementDrag;
	}

	function elementDrag(e) {
		e = e || window.event;
		e.preventDefault();
		// calculate the new cursor position:
		pos1 = pos3 - e.screenX;
		pos2 = pos4 - e.screenY;
		pos3 = e.screenX;
		pos4 = e.screenY;
		
		// set the element's new position:

		if (elmnt.offsetTop - pos2 < 0) {
			elmnt.style.top = "0px";
		} else if ((elmnt.offsetTop - pos2)  > window.parent.innerHeight - elmnt.offsetHeight) {
			elmnt.style.top = window.parent.innerHeight - elmnt.offsetHeight + "px";
		} else {
			elmnt.style.top = elmnt.offsetTop - pos2 + "px"
		}

		if (elmnt.offsetLeft - pos1 < 0) {
			elmnt.style.left = "0px";
		} else if ((elmnt.offsetLeft - pos1) > window.parent.innerWidth - elmnt.offsetWidth) {
			elmnt.style.left = window.parent.innerWidth - elmnt.offsetWidth + "px";
		} else {
			elmnt.style.left = elmnt.offsetLeft - pos1 + "px"
		}
	}

	function closeDragElement() {
		/* stop moving when mouse button is released:*/
		elmnt.contentWindow.document.onmouseup = null;
		elmnt.contentWindow.document.onmousemove = null;
	}
}