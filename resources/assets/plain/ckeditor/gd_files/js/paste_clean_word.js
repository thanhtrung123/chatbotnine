CKEDITOR.CleanWord = function(html) {
  //GD Customize
  if (CKEDITOR.currentInstance.config.Validation == true) {
    var valid = html.match(/<(p|h[1-6]|div|address|table|pre|hr|ul|ol)[^>]*>/i);
    //var e     = FCKCustomEvent.tagName;
    var e = FCKCsutomSelectionTagName().toLowerCase();
    var r = FCKCsutomSelectionElement();
    if (!e.match(/^p$|h[1-6]|div|address|td|th|pre|hr|body|li/i)) {
      while (r) {
        temp = r;
        r = r.parentElement;
        e = r.tagName.toLowerCase();
        if (temp.innerHTML.length == 0)
          temp.outerHTML = '';
        if (e.match(/^p$|h[1-6]|div|address|td|th|pre|hr|body|li/i))
          break;
      }
    }
    if (e == '') {
      alert('挿入したいポイントをクリックして選択してから行ってください。');
      return;
    }
    if (valid) {
      if (e == "p") {
        r.innerHTML = '';
        c = r.innerHTML.toLowerCase();
        if (c.length != 0) {
          alert('現在コピーされている領域は、指定された場所に貼り付けることはできません。');
          return;
        }
      } else if (!e.match(/div|td|th|body/i)) {
        alert('現在コピーされている領域は、指定された場所に貼り付けることはできません。');
        return;
      }
    }
  }
  if (!CKEDITOR.currentInstance.config.ShiftOver) {

    // 070525 Wordの箇条書き対応 START
    // リスト化するタグに特殊属性「_gdlist」を付加
    html = html.replace(/style=('|")[^']*mso-list[^'"]*level(\d+)[^'"]*('|")/gi,
            function($0, $1, $2) {
              if ($2) {
                return '_gdlist="' + $2 + '"';
              } else {
                return '';
              }
            }
    );
    // 070525 Wordの箇条書き対応 END

    ////一太郎対応/////////////////////////////////////////////////////////////////////////////////////////
    //**一太郎改ページコードの削除/////////////////////////////////////////////////////////////////////////
    html = html.replace(//gi, '');
    //**一太郎HRタグCOLOR属性の削除////////////////////////////////////////////////////////////////////////
    html = html.replace(/<(hr[^>]*) color="?[^\s]*"?([^>]*>)/gi, '<$1$2');
    ///////////////////////////////////////////////////////////////////////////////////////////////////////

    html = html.replace(/<o:p>\s*<\/o:p>/g, '');
    html = html.replace(/<o:p>.*?<\/o:p>/g, '&nbsp;');
    // <rt>..</rt>, <rp>..</rp>を削除（まず閉じタグの後ろに改行を入れる）
    html = html.replace(/<\/(rt|rp)>/gi, '<\/$1>\n');
    html = html.replace(/(<rt>.*<\/rt>|<rp>.*<\/rp>)\n/gi, '');
    //colgroupタグ,colタグ,rubyタグ,rbタグ,コロンが入るタグの削除
    html = html.replace(/<\/?(colgroup|col|ruby|rb|\??\w+:)[^>]*>/gi, '');

    //abbrタグの削除
    html = html.replace(/<\/?abbr([^>]*)?>/gi, '');

    // Remove mso-xxx styles.
    // ▼IE11ペースト対応▼
    // IE11のIE8互換モードだとstyleはシングルクォーテーションで囲まれる為、修正
    html = html.replace(/\s*mso-[^:]+:[^;"']+;?/gi, '');
    // ▲IE11ペースト対応▲

    // Remove margin styles.
    html = html.replace(/\s*margin: 0cm 0cm 0pt\s*;/gi, '');
    html = html.replace(/\s*margin: 0cm 0cm 0pt\s*"/gi, '"');

    html = html.replace(/\s*text-indent: 0cm\s*;/gi, '');
    html = html.replace(/\s*text-indent: 0cm\s*"/gi, '"');

    html = html.replace(/\s*text-align: [^\s;]+;?"/gi, '"');

    html = html.replace(/\s*page-break-before: [^\s;]+;?"/gi, '"');

    html = html.replace(/\s*font-variant: [^\s;]+;?"/gi, '"');

    html = html.replace(/\s*tab-stops:[^;"]*;?/gi, "");
    html = html.replace(/\s*tab-stops:[^"]*/gi, "");

    // Remove FONT face attributes.
    //	html = html.replace( /\s*face="[^"]*"/gi, "" ) ;
    //	html = html.replace( /\s*face=[^ >]*/gi, "" ) ;
    html = html.replace(/(<font[^>]*)\s*face=("[^"]*"|[^ >]*)/gi, '$1');

    html = html.replace(/\s*font-family:[^;"]*;?/gi, '');

    //リンク先の中身をチェックし、特定のパスが入ったアンカーなら修正
    html = html.replace(/(href|src)(=)(\"|\')?([^\"\' >]*\/FCKeditor\/editor\/fckeditor\.html[^\"\' >]*)(#[^\"\' >]*)(\"|\')?/gi, '$1$2$3$5$6');

    //Aタグを全て取得する（href属性を含まないAタグを削除する）
    if (ancer_tag = html.match(/<a\s[^>]*>/gi)) {
      for (i = 0; i < ancer_tag.length; i++) {
        //「href」属性が含まれていない場合
        if (!ancer_tag[i].match(/\shref=/i)) {
          //Aタグのみ削除する
          html = html.replace(new RegExp(ancer_tag[i] + '(.*?)<\/a>', 'gi'), '$1');
        }
      }
    }

    // Remove Class attributes
    //html = html.replace( /<(\w[^>]*) class=([^ |>]*)([^>]*)/gi, '<$1$3' ) ;
    html = html.replace(/class="?mso[a-zA-Z0-9]+"?/gi, '');
    html = html.replace(/class="?xl[0-9]+"?/gi, '');

    // Remove styles.
    html = html.replace(/<(\w[^>]*) style="([^\"]*)"([^>]*)/gi, '<$1$3');
    html = html.replace(/<(\w[^>]*) style='([^\']*)'([^>]*)/gi, '<$1$3');

    html = html.replace(/<span\s*[^>]*>\s*&nbsp;\s*<\/span>/gi, '&nbsp;');
    html = html.replace(/<span\s*[^>]*><\/span>/gi, '');

    // Remove id attributes
    html = html.replace(/<(\w[^>]*) id=([^ |>]*)([^>]*)/gi, '<$1$3');
    // Remove accesskey attributes(for mobile)
    html = html.replace(/<(\w[^>]*) accesskey=([^ |>]*)([^>]*)/gi, '<$1$3');

    // remove lang attributes
    html = html.replace(/<(\w[^>]*) lang=([^ |>]*)([^>]*)/gi, '<$1$3');

    html = html.replace(/<span\s*[^>]*>(.*?)<\/span>/gi, '$1');
    html = html.replace(/<span\s*[^>]*>(.*?)<\/span>/gi, '$1');
    html = html.replace(/<span\s*[^>]*>(.*?)<\/span>/gi, '$1');

    //html = html.replace( /<font\s*>(.*?)<\/font>/gi, '$1' ) ;
    html = html.replace(/<font[^>]*>(.*?)<\/font>/gi, '$1');

    // Remove XML elements and declarations
    html = html.replace(/<\\?\?xml[^>]*>/gi, '');

    // Remove Tags with XML namespace declarations: <o:p><\/o:p>
    html = html.replace(/<\/?\w+:[^>]*>/gi, '');

    //テーブルの置換
    //	html = html.replace( /<table[^>]*>/gi, '<table cellspacing="0" cellpadding="0" border="1">');
    //	html = html.replace( /(<(tr|td)[^>]*) width=[^ >]+/gi, '$1');
    //	html = html.replace( /(<(tr|td)[^>]*) height=[^ >]+/gi, '$1');
    html = html.replace(/(<(table)[^>]*) height=[^ >]+/gi, '$1');
    html = html.replace(/(<(tr)[^>]*) width=[^ >]+/gi, '$1');
    html = html.replace(/(<(tr)[^>]*) height=[^ >]+/gi, '$1');

    //コロンが入る属性の削除
    html = html.replace(/(<\w+[^>]*)(\s+\w+:\w+)(=("[^"]*"|[^ >]*))?/gi, '$1');

    html = html.replace(/<h\d>\s*<\/h\d>/gi, '');

    //見出しを置換
    //	html = html.replace( /<h1([^>]*)>/gi, '<div$1><b><font size="6">' ) ;
    //	html = html.replace( /<h2([^>]*)>/gi, '<div$1><b><font size="5">' ) ;
    //	html = html.replace( /<h3([^>]*)>/gi, '<div$1><b><font size="4">' ) ;
    //	html = html.replace( /<h4([^>]*)>/gi, '<div$1><b><font size="3">' ) ;
    //	html = html.replace( /<h5([^>]*)>/gi, '<div$1><b><font size="2">' ) ;
    //	html = html.replace( /<h6([^>]*)>/gi, '<div$1><b><font size="1">' ) ;
    //
    //	html = html.replace( /<\/h\d>/gi, '<\/font><\/b><\/div>' ) ;

    html = html.replace(/<\/?frameset[^>]*>/gi, '');
    html = html.replace(/<\/?frame[^>]*>/gi, '');
    html = html.replace(/<\/?noframes[^>]*>/gi, '');
    html = html.replace(/<\/?iframe[^>]*>/gi, '');

    html = html.replace(/<(u|i|strike|s|b)>&nbsp;<\/\1>/g, '&nbsp;');

    html = html.replace(/<\/?font[^>]*>/gi, '');

    //html = html.replace(/<\/?u[^>]*>/gi,'');
    html = html.replace(/<\/?u>/gi, '');

    html = html.replace(/<i(\s[^>]*)?>/gi, '<em$1>');
    html = html.replace(/<\/i>/gi, '</em>');
    html = html.replace(/<b(\s[^>]*)?>/gi, '<strong$1>');
    html = html.replace(/<\/b>/gi, '</strong>');

    html = html.replace(/<strike([^>]*>)/gi, '<del$1');
    html = html.replace(/<\/strike>/gi, '</del>');
    html = html.replace(/<s(\s[^>]*)?>/gi, '<del$1>');
    html = html.replace(/<\/s>/gi, '</del>');

    html = html.replace(/<(em|strong|del)>&nbsp;<\/\1>/g, '&nbsp;');

    // Remove empty tags (three times, just to be sure).
    html = html.replace(/<([^td>]+)(\s+[^>]*>|>)\s*<\/\1>/gi, '');
    html = html.replace(/<([^td>]+)(\s+[^>]*>|>)\s*<\/\1>/gi, '');
    html = html.replace(/<([^td>]+)(\s+[^>]*>|>)\s*<\/\1>/gi, '');

    // Transform <P> to <DIV>
    //	var re = new RegExp("(<P)([^>]*>.*?)(<\/P>)","gi") ;	// Different because of a IE 5.0 error
    //	html = html.replace( re, "<div$2<\/div>" ) ;
    //▼IE11ペースト対応▼
    //タグとタグの間が空白のみの場合削除（エクセル貼り付けで隙間ができるため）
    if (window.iePasteDivFlag) {
      html = html.replace(/<[^>]*?>|([^<]+)/g, function(a, b) {
        return (b === void 0) ? a : b.replace(/^\s+$/, '');
      });
    }
    //▲IE11ペースト対応▲
    html = html.replace(new RegExp('<span>', 'g'), '').replace(new RegExp('</span>', 'g'), '');
    //On paste, replace DIV => P
    var re = new RegExp("(<DIV)([^>]*>.*?)(<\/DIV>)","gi") ;
    html = html.replace( re, "<p$2</p>" ) ;
    CKEDITOR.currentInstance.insertHtml(html);

    // 070525 Wordの箇条書き対応 START
    while (getListFirstNode()) {
      var A = getListFirstNode();
      if (!A)
        break;
      var B = getListNodes(A);
      createList(B);
    }
    // 070525 Wordの箇条書き対応 END
  }

  if (CKEDITOR.currentInstance.config.ShiftOver) {
    //指定されたブロック要素を変換
    for (var i = 0; i < CKEDITOR.currentInstance.config.Rep_Tag_Ary.length; i++) {
      var RTA = CKEDITOR.currentInstance.config.Rep_Tag_Ary[i];
      //置換
      if (RTA['mode'] == 0) {
        //開始タグ
        if (RTA['target'] == 0 || RTA['target'] == 1) {
          var before = new RegExp('<' + RTA['tag'] + '(\\s[^>]*)?>', 'gi');
          var after = '<' + RTA['rep_tag'] + (RTA['del_attr'] == 1 ? '' : '$1') + '>';
          html = html.replace(before, after);
        }
        //終了タグ
        if (RTA['target'] == 0 || RTA['target'] == 2) {
          before = new RegExp('<\\/' + RTA['tag'] + '>', 'gi');
          after = '</' + RTA['rep_tag'] + '>';
          html = html.replace(before, after);
        }
      }
      //削除
      else if (RTA['mode'] == 1) {
        //開始・終了タグ
        var before = new RegExp('<' + (RTA['target'] == 1 ? '' : '\\/?') + RTA['tag'] + '(\\s[^>]*)?>', 'gi');
        var after = '';
        html = html.replace(before, after);
      }
    }

    //A,IMGのパス整形
    var targetAry = CKEDITOR.currentInstance.config.DressupPath;
    var pathReg;
    for (var i = 0; i < targetAry.length; i++) {
      pathReg = new RegExp('(="' + targetAry[i] + ')([^"]*")', 'gi');
      html = html.replace(pathReg, function($0) {
        var str = $0;
        //HTML拡張子変更
        if (CKEDITOR.currentInstance.config.HTMtoHTML) {
          var expReg = new RegExp('(="[^"]*\.)(htm)([^l"]*)?"', 'gi');
          str = str.replace(expReg, function($0, $1, $2, $3) {
            return $1 + $2 + 'l' + $3 + '"';
          });
        }
        str = str.replace(pathReg, function($0, $1, $2) {
          //置換フラグ（デフォルトtrue）
          var replaceFlg_def_true = true;
          //置換フラグ（デフォルトfalse）
          var replaceFlg_def_false = false;
          //短縮表示
          var p = window.parent;
          //削除しない拡張子
          var notDelExtensions = p.ALLOWED_EXTENSIONS_IMAGE.split(',');
          if (p.IKOU_MOBILE)
            notDelExtensions = notDelExtensions.concat(p.ALLOWED_EXTENSIONS_IMAGE_MOBILE.split(','));
          //削除する拡張子
          var delExtensions = p.DENIED_EXTENSIONS_FILE.split(',');

          // i-cityのURLかチェック
          if (!$2.match(new RegExp(CKEDITOR.currentInstance.config.i_city_url))) {
            //削除しない拡張子をチェック
            for (var i = 0; i < notDelExtensions.length; i++) {
              if ($2.match(new RegExp('.' + notDelExtensions[i] + '((\\?|#)[^\\.\\/]*?)?"$', 'gi'))) {
                replaceFlg_def_true = false;
                break;
              }
            }
            //削除する拡張子をチェック
            for (var i = 0; i < delExtensions.length; i++) {
              if ($2.match(new RegExp('.' + delExtensions[i] + '((\\?|#)[^\\.\\/]*?)?"$', 'gi'))) {
                replaceFlg_def_false = true;
                break;
              }
            }

            //最後が「/」の場合は、パスを削除する
            if ($2.match(/\/((\\?|#)[^\\.\\/]*?)?"$/gi) || ($2.match(/^"$/i) && $1.match(/\/$/i))) {
              replaceFlg_def_true = true;
              replaceFlg_def_false = true;
            }
          }
          // i-city
          else {
            replaceFlg_def_true = false;
            replaceFlg_def_false = false;
          }

          //置換フラグが両方trueの場合は置換を行なう
          if (replaceFlg_def_true && replaceFlg_def_false)
            return '="' + CKEDITOR.currentInstance.config.ReplacePath + $2;
          else
            return $0;
        });
        return str;
      });
    }

    //abbrタグの削除
    html = html.replace(/<\/?abbr([^>]*)?>/gi, '');
    //コメント要素を削除
    html = html.replace(/<!--(.|\n)*?-->/g, '');

    //HTMLを挿入
    html = html.replace(new RegExp('<span>', 'g'), '').replace(new RegExp('</span>', 'g'), '');
    //On paste, replace DIV => P
    var re = new RegExp("(<DIV)([^>]*>.*?)(<\/DIV>)","gi") ;
    html = html.replace( re, "<p$2</p>" ) ;
    CKEDITOR.currentInstance.insertHtml(html);

    //属性整形処理（対象：A,IMG,TABLE関連）
    var removeAttrElement = function(A, B) {
      //タグの数だけループ
      for (var i = 0; i < A.length; i++) {
        //属性の削除
        if (CKEDITOR.currentInstance.config.Del_Target_Attribute[B]) {
          for (var j = 0; j < CKEDITOR.currentInstance.config.Del_Target_Attribute[B].length; j++) {
            A[i].removeAttribute(CKEDITOR.currentInstance.config.Del_Target_Attribute[B][j], false);
          }
        }

        /**
         * 配列の値があるかチェック
         * class_nameにclass_aryに含まれる値があるかチェックする。
         * @param class_name 検索対象となる値
         * @param class_ary 検索する値が含まれる配列
         * @return class_nameでclass_aryに含まれる値が見つかった場合にTRUE、それ以外の場合は、FALSEを返す。
         */
        var in_class = function(class_name, class_ary) {
          for (var key = 0; key < class_ary.length; key++) {
            if (class_name.match(new RegExp(class_ary[key])))
              return true;
          }
          return false;
        }

        //------------------------------//
        //	↓各要素に対する特殊処理↓	//
        //------------------------------//

        //A要素
        if (B == 'a') {
          //アンカー要素の指定　条件：HREF属性値がない場合
          if (!A[i].href) {
            //ID属性が存在する場合は、NAME属性の削除
            if (A[i].id)
              A[i].removeAttribute("name");
            //ID属性が存在しない場合は、NAME属性をID属性に変換後、NAME属性の削除
            else if (A[i].name || A[i].name == "") {
              A[i].id = A[i].name;
              // A[i].className += ' FCK__AnchorC';
              //NAME属性削除
              A[i].removeAttribute("name");
            }
            //空のアンカー要素に空文字挿入
            if (A[i].tagName.toUpperCase() != 'AREA' && (!A[i].innerHTML || A[i].innerHTML == ""))
              A[i].innerHTML = '&nbsp;';
          }
          //リンクだった場合
          else {
            A[i].removeAttribute("name");
            A[i].removeAttribute("id");
          }
        }
        //TABLE要素
        if (B == 'table') {
          //データテーブルの宣言がある場合
          if (CKEDITOR.currentInstance.config.dataTableClass) {
            //CLASS属性値付加（対象：BORDDER属性値が1以上であり、削除しないCLASS属性値に宣言が無い場合）
            if (A[i].border && A[i].border > 0 && (!CKEDITOR.currentInstance.config.disabledClass[B] || CKEDITOR.currentInstance.config.disabledClass[B] && !in_class(A[i].className, CKEDITOR.currentInstance.config.disabledClass[B])))
              A[i].className = CKEDITOR.currentInstance.config.dataTableClass;
          }
        }

        //TABLEにCLASSが存在する場合は削除しない属性
        if (CKEDITOR.currentInstance.config.classCheckTableElement[B]) {
          //タグの取得
          var parentElmObj = A[i];
          var table_class_flg = true;
          //「classCheckTableClass」に「B(table th td)」の指定がある場合
          if (CKEDITOR.currentInstance.config.classCheckTableClass[B]) {
            //ループ
            while (1) {
              //タグがTABLEの場合
              if (parentElmObj.tagName == "TABLE") {
                //タグにクラス名がある場合
                if (parentElmObj.className) {
                  //タグのクラス名が「classCheckTableClass」にある場合
                  if (in_class(parentElmObj.className, CKEDITOR.currentInstance.config.classCheckTableClass[B])) {
                    table_class_flg = false;
                    break;
                  }
                }
                break;
              }
              //親タグが無くなった場合
              if (!parentElmObj.parentElement)
                break;
              //親タグの取得
              parentElmObj = parentElmObj.parentElement
            }
          }
          //親タグにテーブルがあり、消さないクラス名が無かった場合
          if (table_class_flg && parentElmObj.tagName == 'TABLE') {
            //属性の削除
            for (var j = 0; j < CKEDITOR.currentInstance.config.classCheckTableElement[B].length; j++) {
              A[i].removeAttribute(CKEDITOR.currentInstance.config.classCheckTableElement[B][j], false);
            }
          }
        }

        //------------------------------//
        //	↑各要素に対する特殊処理↑	//
        //------------------------------//

        //CKEDITOR.currentInstance.config.disabledClass[要素名]に宣言されていないCLASS属性値の場合、削除処理を行う
        if (CKEDITOR.currentInstance.config.disabledClass[B]) {
          var className_ary = A[i].className.split(' ');
          var nonDelClassStr = CKEDITOR.currentInstance.config.disabledClass[B].join(',');
          for (var j = 0; j < className_ary.length; j++) {
            if (className_ary[j] == "")
              continue;
            if (!in_class(nonDelClassStr, new Array(className_ary[j])))
              A[i].className = A[i].className.replace(className_ary[j], '');
          }
          // クラスの整形
          // 前後の空白を除去
          A[i].className = A[i].className.trim();
          // 複数の空白を一つにまとめる
          A[i].className = A[i].className.replace(/ +/g, ' ');
          // 空のクラスを削除
          if (A[i].className.trim() == "") {
            A[i].removeAttribute('className', false);
          }
        }
      }
    }
    var dom = CKEDITOR.currentInstance.window.$.document;
    removeAttrElement(dom.getElementsByTagName('TABLE'), 'table');
    removeAttrElement(dom.getElementsByTagName('THEAD'), 'thead');
    removeAttrElement(dom.getElementsByTagName('TBODY'), 'tbody');
    removeAttrElement(dom.getElementsByTagName('TFOOT'), 'tfoot');
    removeAttrElement(dom.getElementsByTagName('CAPTION'), 'caption');
    removeAttrElement(dom.getElementsByTagName('TR'), 'tr');
    removeAttrElement(dom.getElementsByTagName('TH'), 'th');
    removeAttrElement(dom.getElementsByTagName('TD'), 'td');
    removeAttrElement(dom.getElementsByTagName('IMG'), 'img');
    removeAttrElement(dom.getElementsByTagName('A'), 'a');
    removeAttrElement(dom.getElementsByTagName('AREA'), 'a');
  }

  //テーブルの中途半端要素を削除
  var removeTableElement = function(A, B) {
    //
    var p;
    switch (B) {
      case 'TBODY':
        p = 'table';
        break;
      case 'TR':
        p = 'table|tbody';
        break;
      case 'TH':
        p = 'tr';
        break;
      case 'TD':
        p = 'tr';
        break;
    }
    if (!p)
      return;
    //
    var reg, pe;
    var flg = 0;
    for (var i = 0; i < A.length; i++) {
      reg = new RegExp(p, 'i');
      pe = A[i].parentElement.tagName;
      if (!pe)
        continue;
      if (!pe.match(reg)) {
        A[i].parentNode.removeChild(A[i]);
        flg = 1;
        break;
      }
    }
    if (flg == 1) {
      var target = CKEDITOR.currentInstance.window.$.document.getElementsByTagName(B);
      removeTableElement(target, B);
    }
  }
  var tbody = CKEDITOR.currentInstance.window.$.document.getElementsByTagName('TBODY');
  removeTableElement(tbody, 'TBODY');
  var tr = CKEDITOR.currentInstance.window.$.document.getElementsByTagName('TR');
  removeTableElement(tr, 'TR');
  var th = CKEDITOR.currentInstance.window.$.document.getElementsByTagName('TH');
  removeTableElement(th, 'TH');
  var td = CKEDITOR.currentInstance.window.$.document.getElementsByTagName('TD');
  removeTableElement(td, 'TD');

  removeWordImg();
  
  return true;
};

// 070525 Wordの箇条書き対応 START
//** リスト化対象ノードの最初のノードを返す
function getListFirstNode() {
  var A = CKEDITOR.currentInstance.window.$.document.getElementsByTagName('P');
  for (var i = 0; i < A.length; i++) {
    if (A[i]._gdlist && A[i]._gdlist != "") {
      return A[i];
    }
  }
  return false;
}

//** リスト化するノードを返す
function getListNodes(obj) {
  var A = new Array();
  A.push(obj);
  while (obj) {
    obj = obj.nextSibling;
    if (!obj)
      break;
    if (obj.tagName == 'P' && obj._gdlist) {
      A.push(obj);
    } else {
      break;
    }
  }
  return A;
}

//** リストノードの作成と挿入
function createList(obj) {
  var UL = CKEDITOR.currentInstance.window.$.document.createElement('ul');
  var A = obj[0].parentNode;
  A.insertBefore(UL, obj[0]);
  var B = new Object();
  var C;
  B[obj[0]._gdlist] = UL;
  for (var i = 0; i < obj.length; i++) {
    var LI = CKEDITOR.currentInstance.window.$.document.createElement('li');
    LI.innerHTML = requireText(obj[i].innerHTML);
    if (B[obj[i]._gdlist]) {
      B[obj[i]._gdlist].appendChild(LI);
    } else {
      B[obj[i]._gdlist] = C;
      var ul = CKEDITOR.currentInstance.window.$.document.createElement('ul');
      ul.appendChild(LI);
      C.appendChild(ul);
    }
    C = LI;
    obj[i].outerHTML = "";
  }
}

//** 不要テキスト・スペースの削除
function requireText(text) {
  var A = text.replace(/^(l|Ø|²|n|u|¨|ü|・|○|●|□|■|◇|◆|△|▲|▽|▼|☆|★|◎)(&nbsp;|\s)+/gi, '');
  A = A.replace(/^([①-⑳])(&nbsp;|\s)+/gi,
          function($0, $1, $2) {
            var R = "";
            switch ($1) {
              case "①":
                R = 1;
                break;
              case "②":
                R = 2;
                break;
              case "③":
                R = 3;
                break;
              case "④":
                R = 4;
                break;
              case "⑤":
                R = 5;
                break;
              case "⑥":
                R = 6;
                break;
              case "⑦":
                R = 7;
                break;
              case "⑧":
                R = 8;
                break;
              case "⑨":
                R = 9;
                break;
              case "⑩":
                R = 10;
                break;
              case "⑪":
                R = 11;
                break;
              case "⑫":
                R = 12;
                break;
              case "⑬":
                R = 13;
                break;
              case "⑭":
                R = 14;
                break;
              case "⑮":
                R = 15;
                break;
              case "⑯":
                R = 16;
                break;
              case "⑰":
                R = 17;
                break;
              case "⑱":
                R = 18;
                break;
              case "⑲":
                R = 19;
                break;
              case "⑳":
                R = 20;
                break;
            }
            return R;
          });
  A = A.replace(/(\d+)(.|．)?(&nbsp;|\s)+/gi,
          function($0, $1, $2, $3) {
            var R = "";
            if ($1)
              R += $1;
            if ($2)
              R += $2;
            return R;
          }
  );
  return A;
}

// 070525 Wordの箇条書き対応 END

function removeWordImg() {
    var list = CKEDITOR.currentInstance.window.$.document.getElementsByTagName('img');
    for (var i = 0; i < list.length; i++) {
        if (!(list[i].src.indexOf('http') == 0 || list[i].hasAttribute('data-cke-real-element-type'))) {
	     list[i].parentNode.removeChild(list[i]);
        } 
    };
}
