function detectBrowser() {
    window.gc = false;
    window.ff = false;
    window.ie = false;

    if (navigator.userAgent.indexOf('Chrome') >= 0) {
        window.gc = true;
        return;
    }
    if (navigator.userAgent.indexOf('Firefox') >= 0) {
        window.ff = true;
        return;
    }
    window.ie = true;
    //隠しDIV貼り付け使用フラグ（IE9以降）
    window.iePasteDivFlag = !!window.navigator.userAgent.match(/msie 9\.|msie 10\.|trident.*rv:11\.|edge\/\d/i);
    
    window.ie_lt11 = window.navigator.userAgent.match(/msie 9\.|msie 10\.\/\d/i);
    return;
}
detectBrowser();

if (window.ie_lt11) {
    function FCKCsutomSelectionTagName() {
        var a = CKEDITOR.currentInstance.window.$.document.body.innerHTML;
        a = a.toLowerCase();
        if (a == '<p>&nbsp;</p>')
            return 'BODY';
        var s = CKEDITOR.currentInstance.window.$.document.selection;
        var c = s.createRange();
        var t = s.type;
        var p = "";
        if (t == 'Control') {
            if (c && c.item)
                p = c.item(0).tagName;
        }
        else
            p = c.parentElement().tagName;
        return p;
    }
    function FCKCsutomSelectionElement() {
        var s = CKEDITOR.currentInstance.window.$.document.selection;
        // 20070521 ADD START
        var t = s.type;
        if (t == 'Control') {
            var r = s.createRange();
            if (r && r.item)
                var i = s.createRange().item(0);
            return i;
        }
        // 20070521 ADD END
        var c = s.createRange();
        var p = s.createRange().parentElement();
        return p;
    }

    var FCKRegexLib = new Object();
    FCKRegexLib.AposEntity = /&apos;/gi;
    FCKRegexLib.ObjectElements = /^(?:IMG|TABLE|TR|TD|TH|INPUT|SELECT|TEXTAREA|HR|OBJECT|A|UL|OL|LI)$/i;
    FCKRegexLib.BlockElements = /^(?:P|DIV|H1|H2|H3|H4|H5|H6|ADDRESS|PRE|OL|UL|LI|TD|TH)$/i;
    FCKRegexLib.EmptyElements = /^(?:BASE|META|LINK|HR|BR|PARAM|IMG|AREA|INPUT)$/i;
    FCKRegexLib.NamedCommands = /^(?:Cut|Copy|Paste|Print|SelectAll|RemoveFormat|Unlink|Undo|Redo|Bold|Italic|Underline|StrikeThrough|Subscript|Superscript|JustifyLeft|JustifyCenter|JustifyRight|JustifyFull|Outdent|Indent|InsertOrderedList|InsertUnorderedList|InsertHorizontalRule|CreateDiv|YouTube)$/i;
    FCKRegexLib.BodyContents = /([\s\S]*\<body[^\>]*\>)([\s\S]*)(\<\/body\>[\s\S]*)/i;
    FCKRegexLib.ToReplace = /___fcktoreplace:([\w]+)/ig;
    FCKRegexLib.MetaHttpEquiv = /http-equiv\s*=\s*["']?([^"' ]+)/i;
    FCKRegexLib.HasBaseTag = /<base /i;
    FCKRegexLib.HeadOpener = /<head\s?[^>]*>/i;
    FCKRegexLib.HeadCloser = /<\/head\s*>/i;
    FCKRegexLib.TableBorderClass = /\s*FCK__ShowTableBorders\s*/;
    FCKRegexLib.ElementName = /(^[A-Za-z_:][\w.\-:]*\w$)|(^[A-Za-z_]$)/;
    FCKRegexLib.ForceSimpleAmpersand = /___FCKAmp___/g;
    FCKRegexLib.SpaceNoClose = /\/>/g;
    FCKRegexLib.EmptyParagraph = /^<(p|div)>\s*<\/\1>$/i;
    FCKRegexLib.TagBody = /></;
    FCKRegexLib.StrongOpener = /<STRONG([ \>])/gi;
    FCKRegexLib.StrongCloser = /<\/STRONG>/gi;
    FCKRegexLib.EmOpener = /<EM([ \>])/gi;
    FCKRegexLib.EmCloser = /<\/EM>/gi;
    FCKRegexLib.GeckoEntitiesMarker = /#\?-\:/g;
    FCKRegexLib.ProtectUrlsAApo = /(<a\s.*?href=)("|')([^"']*?)("|')/gi;
    FCKRegexLib.ProtectUrlsANoApo = /(<a\s.*?href=)([^"'][^ >]+)/gi;
    FCKRegexLib.ProtectUrlsImgApo = /(<img\s.*?src=)("|')([^"']*?)("|')/gi;
    FCKRegexLib.ProtectUrlsImgNoApo = /(<img\s.*?src=)([^"'][^ >]+)/gi;
    FCKRegexLib.Html4DocType = /HTML 4\.0 Transitional/i;

    FCKTools = {};
    FCKTools.CreateXmlObject = function (A) {
        var B;
        switch (A) {
            case 'XmlHttp':
                B = ['MSXML2.XmlHttp', 'Microsoft.XmlHttp'];
                break;
            case 'DOMDocument':
                B = ['MSXML2.DOMDocument', 'Microsoft.XmlDom'];
                break;
        }
        ;
        for (var i = 0; i < 2; i++) {
            try {
                return new ActiveXObject(B[i]);
            } catch (e) {
            }
        }
        ;
        // if (FCKLang.NoActiveX) {
        // alert(FCKLang.NoActiveX);
        // FCKLang.NoActiveX = null;
        // }
    };


    var FCKXml = function () {
        this.Error = false;
    };
    FCKXml.prototype.LoadUrl = function (A) {
        this.Error = false;
        var B = FCKTools.CreateXmlObject('XmlHttp');
        if (!B) {
            this.Error = true;
            return;
        }
        ;
        B.open("GET", A, false);
        B.send(null);
        if (B.status == 200 || B.status == 304)
            this.DOMDocument = B.responseXML;
        else if (B.status == 0 && B.readyState == 4) {
            this.DOMDocument = FCKTools.CreateXmlObject('DOMDocument');
            this.DOMDocument.async = false;
            this.DOMDocument.resolveExternals = false;
            this.DOMDocument.loadXML(B.responseText);
        } else {
            this.Error = true;
            alert('Error loading "' + A + '"');
        }
    };
    FCKXml.prototype.SelectNodes = function (A, B) {
        if (this.Error)
            return new Array();
        if (B)
            return B.selectNodes(A);
        else
            return this.DOMDocument.selectNodes(A);
    };
    FCKXml.prototype.SelectSingleNode = function (A, B) {
        if (this.Error)
            return;
        if (B)
            return B.selectSingleNode(A);
        else
            return this.DOMDocument.selectSingleNode(A);
    }
    FCKXml.TransformToObject = function (A) {
        if (!A)
            return null;
        var B = {};
        var C = A.attributes;
        for (var i = 0; i < C.length; i++) {
            var D = C[i];
            B[D.name] = D.value;
        }
        ;
        var E = A.childNodes;
        for (i = 0; i < E.length; i++) {
            var F = E[i];
            if (F.nodeType == 1) {
                var G = '$' + F.nodeName;
                var H = B[G];
                if (!H)
                    H = B[G] = [];
                H.push(this.TransformToObject(F));
            }
        }
        ;
        return B;
    };


    var FCKStyleDef = function (A, B) {
        this.Name = A;
        this.Element = B.toUpperCase();
        this.IsObjectElement = FCKRegexLib.ObjectElements.test(this.Element);
        this.Attributes = new Object();
    };
    FCKStyleDef.prototype.AddAttribute = function (A, B) {
        this.Attributes[A] = B;
    };
    FCKStyleDef.prototype.GetOpenerTag = function () {
        var s = '<' + this.Element;
        for (var a in this.Attributes)
            s += ' ' + a + '="' + this.Attributes[a] + '"';
        return s + '>';
    };
    FCKStyleDef.prototype.GetCloserTag = function () {
        return '</' + this.Element + '>';
    };
    FCKStyleDef.prototype.RemoveFromSelection = function () {
        if (FCKSelection.GetType() == 'Control')
            this._RemoveMe(FCK.ToolbarSet.CurrentInstance.Selection.GetSelectedElement());
        else
            this._RemoveMe(FCK.ToolbarSet.CurrentInstance.Selection.GetParentElement());
    }
    FCKStyleDef.prototype.ApplyToSelection = function () {
        //Non Validate Mode
        if (FCKConfig.Validation != true) {
            var A = FCK.ToolbarSet.CurrentInstance.EditorDocument.selection;
            if (A.type == 'Text') {
                var B = A.createRange();
                var e = document.createElement(this.Element);
                e.innerHTML = B.htmlText;
                this._AddAttributes(e);
                this._RemoveDuplicates(e);
                B.pasteHTML(e.outerHTML);
            } else if (A.type == 'Control') {
                var C = FCK.ToolbarSet.CurrentInstance.Selection.GetSelectedElement();
                if (C.tagName == this.Element)
                    this._AddAttributes(C);
            }
            return;
        }

        //Validate Mode
        var A = FCK.ToolbarSet.CurrentInstance.EditorDocument.selection;
        var B = A.createRange();
        if (A.type == 'Text') {
            //GD Customize//////////////////////////////////////////////////////////
            var T = B.parentElement();
            var U = this.Element.toUpperCase();
            if (T.tagName.toLowerCase() == "body" || T.tagName.toLowerCase() == "td" || T.tagName.toLowerCase() == "th") {
                //BODY,TDに対するスタイル適用
                if (U.match(/div/i)) {
                    var e = document.createElement(this.Element);
                    e.innerHTML = B.htmlText;
                    this._AddAttributes(e);
                    this._RemoveDuplicates(e);
                    B.pasteHTML(e.outerHTML);
                    if (e.children[0]) {
                        var ft = e.children[0].tagName;
                        var fa = FCK.EditorDocument.getElementsByTagName(ft);
                        for (var i = 0; i < fa.length; i++) {
                            if (fa[i].children[0]) {
                                fa[i].outerHTML = fa[i].innerHTML;
                            }
                        }
                    }
                } else if (U.match(/^p$|h[1-6]|div|address|pre/i)) {
                    alert('指定されたスタイルは、現在の選択範囲に適用できません。');
                } else {
                    var e = document.createElement(this.Element);
                    e.innerHTML = B.htmlText;
                    if (e.innerHTML.match(/<(p|h[1-6]|div|address|pre|table)/i)) {
                        alert('指定されたスタイルは、選択範囲中にブロック要素があるため適用できません。');
                    } else {
                        this._AddAttributes(e);
                        this._RemoveDuplicates(e);
                        B.pasteHTML(e.outerHTML);
                    }
                }
            } else if (T.tagName.toLowerCase() == "div") {
                //DIVに対するスタイル適用
                if (U.match(/div/i)) {
                    var e = document.createElement(this.Element);
                    e.innerHTML = T.innerHTML;
                    this._AddAttributes(e);
                    this._RemoveDuplicates(e);
                    T.outerHTML = e.outerHTML;
                } else {
                    if (U.match(/^p$|h[1-6]|address|pre/i)) {
                        alert('指定されたスタイルは、現在の選択範囲に適用できません。');
                    } else {
                        var e = document.createElement(this.Element);
                        e.innerHTML = B.htmlText;
                        if (e.innerHTML.match(/<(p|h[1-6]|div|address|pre|table)/i)) {
                            alert('指定されたスタイルは、選択範囲中にブロック要素があるため適用できません。');
                        } else {
                            this._AddAttributes(e);
                            this._RemoveDuplicates(e);
                            B.pasteHTML(e.outerHTML);
                        }
                    }
                }
            } else if (T.tagName.match(/^p$|h[1-6]|address|pre|ul|ol|li/i)) {
                //DOCブロック要素に対するスタイル適用
                if (U.match(/^p$|h[1-6]|div|address|pre|ul|ol|li/i)) {
                    while (T) {
                        tag = T.tagName;
                        if (tag) {
                            if (tag == U) {
                                break;
                            }
                        } else {
                            T = false;
                            break;
                        }
                        T = T.parentNode;
                        if (T.tagName == 'BODY')
                            break;
                    }
                }
                if (this.Element.match(/^p$|h[1-6]|div|address|pre|ul|ol|li/i)) {
                    var e = document.createElement(this.Element);
                    e.innerHTML = T.innerHTML;
                    this._AddAttributes(e);
                    this._RemoveDuplicates(e);
                    T.outerHTML = e.outerHTML;
                } else {
                    var e = document.createElement(this.Element);
                    var html = B.htmlText;
                    e.innerHTML = B.htmlText;

                    // ▼IE9対応▼
                    //改行コードを削除
                    var reg = new RegExp("\\n", "gm");
                    e.innerHTML = e.innerHTML.replace(reg, "");
                    // ▲IE9対応▲

                    var elementName = T.tagName.toLowerCase();
                    switch (elementName) {
                        case "p":
                            e.innerHTML = e.innerHTML.replace(/^<p[^>]*>/i, '');
                            e.innerHTML = e.innerHTML.replace(/<\/p>$/i, '');
                            e.innerHTML = e.innerHTML.replace(/<p><\/p>/i, "");
                            break;
                        case "h1":
                            e.innerHTML = e.innerHTML.replace(/^<h1[^>]*>/i, "");
                            e.innerHTML = e.innerHTML.replace(/<\/h1>$/i, "");
                            e.innerHTML = e.innerHTML.replace(/<h1><\/h1>/i, "");
                            break;
                        case "h2":
                            e.innerHTML = e.innerHTML.replace(/<h2[^>]*>/i, '');
                            e.innerHTML = e.innerHTML.replace(/<\/h2>/i, '');
                            e.innerHTML = e.innerHTML.replace(/<h2><\/h2>/i, "");
                            break;
                        case "h3":
                            e.innerHTML = e.innerHTML.replace(/<h3[^>]*>/i, '');
                            e.innerHTML = e.innerHTML.replace(/<\/h3>/i, '');
                            e.innerHTML = e.innerHTML.replace(/<h3><\/h3>/i, "");
                            break;
                        case "h4":
                            e.innerHTML = e.innerHTML.replace(/<h4[^>]*>/i, '');
                            e.innerHTML = e.innerHTML.replace(/<\/h4>/i, '');
                            e.innerHTML = e.innerHTML.replace(/<h4><\/h4>/i, "");
                            break;
                        case "h5":
                            e.innerHTML = e.innerHTML.replace(/<h5[^>]*>/i, '');
                            e.innerHTML = e.innerHTML.replace(/<\/h5>/i, '');
                            e.innerHTML = e.innerHTML.replace(/<h5><\/h5>/i, "");
                            break;
                        case "h6":
                            e.innerHTML = e.innerHTML.replace(/<h6[^>]*>/i, '');
                            e.innerHTML = e.innerHTML.replace(/<\/h6>/i, '');
                            e.innerHTML = e.innerHTML.replace(/<h6><\/h6>/i, "");
                            break;
                        case "div":
                            e.innerHTML = e.innerHTML.replace(/<div[^>]*>/i, '');
                            e.innerHTML = e.innerHTML.replace(/<\/div>/i, '');
                            e.innerHTML = e.innerHTML.replace(/<div><\/div>/i, "");
                            break;
                        case "address":
                            e.innerHTML = e.innerHTML.replace(/<address[^>]*>/i, '');
                            e.innerHTML = e.innerHTML.replace(/<\/address>/i, '');
                            e.innerHTML = e.innerHTML.replace(/<address><\/address>/i, "");
                            break;
                        case "pre":
                            e.innerHTML = e.innerHTML.replace(/<pre[^>]*>/i, '');
                            e.innerHTML = e.innerHTML.replace(/<\/pre>/i, '');
                            e.innerHTML = e.innerHTML.replace(/<pre><\/pre>/i, "");
                            break;
                        default:
                            break;
                    }
                    this._AddAttributes(e);
                    this._RemoveDuplicates(e);
                    if (html.match(/^<p|h[1-6]|div|address|pre|ul|ol|li[^>]*>/i)) {
                        if (e.innerHTML.match(/<p|h[1-6]|div|address|pre|ul|ol|li[^>]*>/i)) {
                            alert('指定されたスタイルは、選択範囲中にブロック要素があるため適用できません。');
                        } else {
                            T.innerHTML = e.outerHTML;
                        }
                    } else {
                        if (e.innerHTML.match(/<p|h[1-6]|div|address|pre|ul|ol|li[^>]*>/i)) {
                            alert('指定されたスタイルは、選択範囲中にブロック要素があるため適用できません。');
                        } else {
                            B.pasteHTML(e.outerHTML);
                        }
                    }
                }
            } else {
                //インライン要素に対するスタイル適用
                if (!U.match(/^p$|h[1-6]|div|address|pre|ul|ol|li/i)) {
                    var e = document.createElement(this.Element);
                    //e.innerHTML=B.htmlText;
                    var html = B.htmlText;
                    var current = FCKCsutomSelectionElement();
                    ////// 061211 ////////////////////////////////////////////////////
                    if (html == current.outerHTML) {
                        html = current.innerHTML;	//選択した範囲と取得範囲が同一であれば、innerHTMLのみを適用範囲とする
                    } else {
                        html = B.text;				//選択した範囲と取得範囲が同一でなければ、テキストにのみスタイルを適用する
                    }
                    ////// 061211 ////////////////////////////////////////////////////
                    e.innerHTML = html;
                    this._AddAttributes(e);
                    this._RemoveDuplicates(e);
                    if (e.innerHTML.match(/<ul|li[^>]*>/i)) {
                        alert('指定されたスタイルは、選択範囲中にブロック要素があるため適用できません。');
                        return;
                    }
                    if (e.innerHTML.match(/^<(P|h[1-6]|div|address|pre|ul|ol|li)/i)) {
                        e.innerHTML = e.children[0].innerHTML;
                        B.pasteHTML(e.outerHTML);
                    } else {
                        B.pasteHTML(e.outerHTML);
                    }
                } else {
                    while (T) {
                        tag = T.tagName;
                        if (tag) {
                            if (tag == U) {
                                break;
                            }
                        } else {
                            T = false;
                            break;
                        }
                        T = T.parentNode;
                        if (T.tagName == 'BODY')
                            return;
                    }
                    var e = document.createElement(this.Element);
                    e.innerHTML = T.innerHTML;
                    this._AddAttributes(e);
                    this._RemoveDuplicates(e);
                    T.outerHTML = e.outerHTML;
                }
            }
        } else if (A.type == 'Control') {
            var C = FCK.ToolbarSet.CurrentInstance.Selection.GetSelectedElement();
            if (C.tagName == this.Element)
                this._AddAttributes(C);
        }
    };
    FCKStyleDef.prototype._AddAttributes = function (A) {
        for (var a in this.Attributes) {
            switch (a.toLowerCase()) {
                case 'style':
                    A.style.cssText = this.Attributes[a];
                    break;
                case 'class':
                    //A.setAttribute('className',this.Attributes[a],0);
                    // ▼IE9対応▼
                    A.className = this.Attributes[a];
                    // ▲IE9対応▲
                    break;
                case 'src':
                    A.setAttribute('_fcksavedurl', this.Attributes[a], 0);
                default:
                    A.setAttribute(a, this.Attributes[a], 0);
            }
        }
    };
    FCKStyleDef.prototype._RemoveDuplicates = function (A) {
        for (var i = 0; i < A.children.length; i++) {
            var B = A.children[i];
            this._RemoveDuplicates(B);
            if (this.IsEqual(B))
                FCKTools.RemoveOuterTags(B);
        }
    };
    FCKStyleDef.prototype.IsEqual = function (e) {
        if (e.tagName != this.Element)
            return false;

        for (var a in this.Attributes) {
            switch (a.toLowerCase()) {
                case 'style':
                    if (e.style.cssText.toLowerCase() != this.Attributes[a].toLowerCase())
                        return false;
                    break;
                case 'class':
                    //if (e.getAttribute('className',0)!=this.Attributes[a]) return false;
                    // ▼IE9対応▼
                    if (e.className != this.Attributes[a])
                        return false;
                    // ▲IE9対応▲
                    break;
                default:
                    if (e.getAttribute(a, 0) != this.Attributes[a])
                        return false;
            }
        }
        ;
        return true;
    };
    FCKStyleDef.prototype._RemoveMe = function (A) {
        if (!A)
            return;
        var B = A.parentElement;
        if (this.IsEqual(A)) {
            if (this.IsObjectElement) {
                for (var a in this.Attributes) {
                    switch (a.toLowerCase()) {
                        case 'class':
//						A.removeAttribute('className',0);
                            // ▼IE9対応▼
                            A.removeAttribute('class');
                            // ▲IE9対応▲
                            break;
                        default:
                            A.removeAttribute(a, 0);
                    }
                }
                ;
                return;
            } else
                FCKTools.RemoveOuterTags(A);
        }
        ;
        this._RemoveMe(B);
    }


    var FCKStylesLoader = function () {
        this.Styles = new Object();
        this.StyleGroups = new Object();
        this.Loaded = false;
        this.HasObjectElements = false;
    };
    FCKStylesLoader.prototype.Load = function (A) {
        var B = new FCKXml();
        B.LoadUrl(A);
        var C = B.SelectNodes('Styles/Style');
        for (var i = 0; i < C.length; i++) {
            var D = C[i].attributes.getNamedItem('element').value.toUpperCase();
            var E = new FCKStyleDef(C[i].attributes.getNamedItem('name').value, D);
            if (E.IsObjectElement)
                this.HasObjectElements = true;
            var F = B.SelectNodes('Attribute', C[i]);
            for (var j = 0; j < F.length; j++) {
                var G = F[j].attributes.getNamedItem('name').value;
                var H = F[j].attributes.getNamedItem('value').value;
                if (G.toLowerCase() == 'style') {
                    var I = document.createElement('SPAN');
                    I.style.cssText = H;
                    H = I.style.cssText;
                }
                ;
                E.AddAttribute(G, H);
            }
            ;
            this.Styles[E.Name] = E;
            var J = this.StyleGroups[D];
            if (J == null) {
                this.StyleGroups[D] = new Array();
                J = this.StyleGroups[D];
            }
            ;
            J[J.length] = E;
        }
        ;
        this.Loaded = true;
    }

}
else {
    function FCKCsutomSelectionTagName() {
        var e = FCKCsutomSelectionElement();
        return e.tagName;
    }
    function FCKCsutomSelectionElement() {
        isStart = true;
        var range, sel, container;
        sel = CKEDITOR.currentInstance.window.$.getSelection();
        if (sel.getRangeAt) {
            if (sel.rangeCount > 0) {
                range = sel.getRangeAt(0);
            }
        } else {
            // Old WebKit
            range = CKEDITOR.currentInstance.window.$.document.createRange();
            range.setStart(sel.anchorNode, sel.anchorOffset);
            range.setEnd(sel.focusNode, sel.focusOffset);

            // Handle the case when the selection was selected backwards (from the end to the start in the document)
            if (range.collapsed !== sel.isCollapsed) {
                range.setStart(sel.focusNode, sel.focusOffset);
                range.setEnd(sel.anchorNode, sel.anchorOffset);
            }
        }

        if (range) {
            container = range[isStart ? "startContainer" : "endContainer"];

            // Check if the container is a text node and return its parent if so
            return container.nodeType === 3 ? container.parentNode : container;
        }
    }

    var FCKRegexLib = {
        AposEntity: /&apos;/gi,
        ObjectElements: /^(?:IMG|TABLE|TR|TD|TH|INPUT|SELECT|TEXTAREA|HR|OBJECT|A|UL|OL|LI)$/i,
        NamedCommands: /^(?:Cut|Copy|Paste|Print|SelectAll|RemoveFormat|Unlink|Undo|Redo|Bold|Italic|Underline|StrikeThrough|Subscript|Superscript|JustifyLeft|JustifyCenter|JustifyRight|JustifyFull|Outdent|Indent|InsertOrderedList|InsertUnorderedList|InsertHorizontalRule|YouTube)$/i,
        BeforeBody: /(^[\s\S]*\<body[^\>]*\>)/i,
        AfterBody: /(\<\/body\>[\s\S]*$)/i,
        ToReplace: /___fcktoreplace:([\w]+)/ig,
        MetaHttpEquiv: /http-equiv\s*=\s*["']?([^"' ]+)/i,
        HasBaseTag: /<base /i,
        HasBodyTag: /<body[\s|>]/i,
        HtmlOpener: /<html\s?[^>]*>/i,
        HeadOpener: /<head\s?[^>]*>/i,
        HeadCloser: /<\/head\s*>/i,
        FCK_Class: /\s*FCK__[^ ]*(?=\s+|$)/,
        ElementName: /(^[a-z_:][\w.\-:]*\w$)|(^[a-z_]$)/,
        ForceSimpleAmpersand: /___FCKAmp___/g,
        SpaceNoClose: /\/>/g,
        EmptyParagraph: /^<(p|div|address|h\d|center)(?=[ >])[^>]*>\s*(<\/\1>)?$/,
        EmptyOutParagraph: /^<(p|div|address|h\d|center)(?=[ >])[^>]*>(?:\s*|&nbsp;|&#160;)(<\/\1>)?$/,
        TagBody: /></,
        GeckoEntitiesMarker: /#\?-\:/g,
        ProtectUrlsImg: /<img(?=\s).*?\ssrc=((?:(?:\s*)("|').*?\2)|(?:[^"'][^ >]+))/gi,
        ProtectUrlsA: /<a(?=\s).*?\shref=((?:(?:\s*)("|').*?\2)|(?:[^"'][^ >]+))/gi,
        ProtectUrlsArea: /<area(?=\s).*?\shref=((?:(?:\s*)("|').*?\2)|(?:[^"'][^ >]+))/gi,
        Html4DocType: /HTML 4\.0 Transitional/i,
        DocTypeTag: /<!DOCTYPE[^>]*>/i,
        HtmlDocType: /DTD HTML/,
        TagsWithEvent: /<[^\>]+ on\w+[\s\r\n]*=[\s\r\n]*?('|")[\s\S]+?\>/g,
        EventAttributes: /\s(on\w+)[\s\r\n]*=[\s\r\n]*?('|")([\s\S]*?)\2/g,
        ProtectedEvents: /\s\w+_fckprotectedatt="([^"]+)"/g,
        StyleProperties: /\S+\s*:/g,
        InvalidSelfCloseTags: /(<(?!base|meta|link|hr|br|param|img|area|input)([a-zA-Z0-9:]+)[^>]*)\/>/gi,
        StyleVariableAttName: /#\(\s*("|')(.+?)\1[^\)]*\s*\)/g,
        RegExp: /^\/(.*)\/([gim]*)$/,
        HtmlTag: /<[^\s<>](?:"[^"]*"|'[^']*'|[^<])*>/
    };

    FCKDomTools = {};
    FCKDomTools.RemoveNode = function (A, B) {
        if (B) {
            var C;
            while ((C = A.firstChild))
                A.parentNode.insertBefore(A.removeChild(C), A);
        }
        ;
        return A.parentNode.removeChild(A);
    };

    FCKTools = {};
    FCKTools.CreateXmlObject = function (A) {
        switch (A) {
            case 'XmlHttp':
                return new XMLHttpRequest();
            case 'DOMDocument':
                var B = (new DOMParser()).parseFromString('<tmp></tmp>', 'text/xml');
                FCKDomTools.RemoveNode(B.firstChild);
                return B;
        }
        ;
        return null;
    };

    var FCKXml = function () {
        this.Error = false;
    };
    FCKXml.GetAttribute = function (A, B, C) {
        var D = A.attributes.getNamedItem(B);
        return D ? D.value : C;
    };
    FCKXml.TransformToObject = function (A) {
        if (!A)
            return null;
        var B = {};
        var C = A.attributes;
        for (var i = 0; i < C.length; i++) {
            var D = C[i];
            B[D.name] = D.value;
        }
        ;
        var E = A.childNodes;
        for (i = 0; i < E.length; i++) {
            var F = E[i];
            if (F.nodeType == 1) {
                var G = '$' + F.nodeName;
                var H = B[G];
                if (!H)
                    H = B[G] = [];
                H.push(this.TransformToObject(F));
            }
        }
        ;
        return B;
    };
    FCKXml.prototype = {
        LoadUrl: function (A) {
            this.Error = false;
            var B;
            var C = FCKTools.CreateXmlObject('XmlHttp');
            C.open('GET', A, false);
			// Reference: https://stackoverflow.com/questions/17743534/need-to-work-around-selectsinglenode-in-ie10
			C.responseType = 'msxml-document';
            C.send(null);
            if (C.status == 200 || C.status == 304 || (C.status == 0 && C.readyState == 4)) {
                B = C.responseXML;
                if (!B)
                    B = (new DOMParser()).parseFromString(C.responseText, 'text/xml');
            } else
                B = null;
            if (B) {
                try {
                    var D = B.firstChild;
                } catch (e) {
                    B = (new DOMParser()).parseFromString(C.responseText, 'text/xml');
                }
            }
            ;
            if (!B || !B.firstChild) {
                this.Error = true;
                if (window.confirm('Error loading "' + A + '" (HTTP Status: ' + C.status + ').\r\nDo you want to see the server response dump?'))
                    alert('a1' + C.responseText);
            }
            ;
            this.DOMDocument = B;
        },
        SelectNodes: function (A, B) {
            if (this.Error)
                return [];
            if (window.ie) {
                if (B)
            		return B.selectNodes(A);
            	else
            		return this.DOMDocument.selectNodes(A);
            } else {
                var C = [];
                var D = this.DOMDocument.evaluate(A, B ? B : this.DOMDocument, this.DOMDocument.createNSResolver(this.DOMDocument.documentElement), XPathResult.ORDERED_NODE_ITERATOR_TYPE, null);
                if (D) {
                    var E = D.iterateNext();
                    while (E) {
                        C[C.length] = E;
                        E = D.iterateNext();
                    }
                }
                ;
                return C;
            }
        },
        SelectSingleNode: function (A, B) {
            if (this.Error)
                return null;
            var C = this.DOMDocument.evaluate(A, B ? B : this.DOMDocument, this.DOMDocument.createNSResolver(this.DOMDocument.documentElement), 9, null);
            if (C && C.singleNodeValue)
                return C.singleNodeValue;
            else
                return null;
        }
    };

    function FCKStyleDef(A, B) {
        this.Name = A;
        this.Element = B.toUpperCase();
        this.IsObjectElement = FCKRegexLib.ObjectElements.test(this.Element);
        this.Attributes = new Object();
    }
    ;
    FCKStyleDef.prototype.AddAttribute = function (A, B) {
        this.Attributes[A] = B;
    };
    FCKStyleDef.prototype.GetOpenerTag = function () {
        var s = '<' + this.Element;
        for (var a in this.Attributes)
            s += ' ' + a + '="' + this.Attributes[a] + '"';
        return s + '>';
    };
    FCKStyleDef.prototype.GetCloserTag = function () {
        return '</' + this.Element + '>';
    };
    FCKStyleDef.prototype.RemoveFromSelection = function () {
        if (FCKSelection.GetType() == 'Control')
            this._RemoveMe(FCK.ToolbarSet.CurrentInstance.Selection.GetSelectedElement());
        else
            this._RemoveMe(FCK.ToolbarSet.CurrentInstance.Selection.GetParentElement());
    }
    FCKStyleDef.prototype.ApplyToSelection = function () {
        //Non Validate Mode
        if (FCKConfig.Validation != true) {
            var A = FCK.ToolbarSet.CurrentInstance.EditorDocument.selection;
            if (A.type == 'Text') {
                var B = A.createRange();
                var e = document.createElement(this.Element);
                e.innerHTML = B.htmlText;
                this._AddAttributes(e);
                this._RemoveDuplicates(e);
                B.pasteHTML(e.outerHTML);
            } else if (A.type == 'Control') {
                var C = FCK.ToolbarSet.CurrentInstance.Selection.GetSelectedElement();
                if (C.tagName == this.Element)
                    this._AddAttributes(C);
            }
            return;
        }

        //Validate Mode
        var A = FCK.ToolbarSet.CurrentInstance.EditorDocument.selection;
        var B = A.createRange();
        if (A.type == 'Text') {
            //GD Customize//////////////////////////////////////////////////////////
            var T = B.parentElement();
            var U = this.Element.toUpperCase();
            if (T.tagName.toLowerCase() == "body" || T.tagName.toLowerCase() == "td" || T.tagName.toLowerCase() == "th") {
                //BODY,TDに対するスタイル適用
                if (U.match(/div/i)) {
                    var e = document.createElement(this.Element);
                    e.innerHTML = B.htmlText;
                    this._AddAttributes(e);
                    this._RemoveDuplicates(e);
                    B.pasteHTML(e.outerHTML);
                    if (e.children[0]) {
                        var ft = e.children[0].tagName;
                        var fa = FCK.EditorDocument.getElementsByTagName(ft);
                        for (var i = 0; i < fa.length; i++) {
                            if (fa[i].children[0]) {
                                fa[i].outerHTML = fa[i].innerHTML;
                            }
                        }
                    }
                } else if (U.match(/^p$|h[1-6]|div|address|pre/i)) {
                    alert('指定されたスタイルは、現在の選択範囲に適用できません。');
                } else {
                    var e = document.createElement(this.Element);
                    e.innerHTML = B.htmlText;
                    if (e.innerHTML.match(/<(p|h[1-6]|div|address|pre|table)/i)) {
                        alert('指定されたスタイルは、選択範囲中にブロック要素があるため適用できません。');
                    } else {
                        this._AddAttributes(e);
                        this._RemoveDuplicates(e);
                        B.pasteHTML(e.outerHTML);
                    }
                }
            } else if (T.tagName.toLowerCase() == "div") {
                //DIVに対するスタイル適用
                if (U.match(/div/i)) {
                    var e = document.createElement(this.Element);
                    e.innerHTML = T.innerHTML;
                    this._AddAttributes(e);
                    this._RemoveDuplicates(e);
                    T.outerHTML = e.outerHTML;
                } else {
                    if (U.match(/^p$|h[1-6]|address|pre/i)) {
                        alert('指定されたスタイルは、現在の選択範囲に適用できません。');
                    } else {
                        var e = document.createElement(this.Element);
                        e.innerHTML = B.htmlText;
                        if (e.innerHTML.match(/<(p|h[1-6]|div|address|pre|table)/i)) {
                            alert('指定されたスタイルは、選択範囲中にブロック要素があるため適用できません。');
                        } else {
                            this._AddAttributes(e);
                            this._RemoveDuplicates(e);
                            B.pasteHTML(e.outerHTML);
                        }
                    }
                }
            } else if (T.tagName.match(/^p$|h[1-6]|address|pre|ul|ol|li/i)) {
                //DOCブロック要素に対するスタイル適用
                if (U.match(/^p$|h[1-6]|div|address|pre|ul|ol|li/i)) {
                    while (T) {
                        tag = T.tagName;
                        if (tag) {
                            if (tag == U) {
                                break;
                            }
                        } else {
                            T = false;
                            break;
                        }
                        T = T.parentNode;
                        if (T.tagName == 'BODY')
                            break;
                    }
                }
                if (this.Element.match(/^p$|h[1-6]|div|address|pre|ul|ol|li/i)) {
                    var e = document.createElement(this.Element);
                    e.innerHTML = T.innerHTML;
                    this._AddAttributes(e);
                    this._RemoveDuplicates(e);
                    T.outerHTML = e.outerHTML;
                } else {
                    var e = document.createElement(this.Element);
                    var html = B.htmlText;
                    e.innerHTML = B.htmlText;

                    // ▼IE9対応▼
                    //改行コードを削除
                    var reg = new RegExp("\\n", "gm");
                    e.innerHTML = e.innerHTML.replace(reg, "");
                    // ▲IE9対応▲

                    var elementName = T.tagName.toLowerCase();
                    switch (elementName) {
                        case "p":
                            e.innerHTML = e.innerHTML.replace(/^<p[^>]*>/i, '');
                            e.innerHTML = e.innerHTML.replace(/<\/p>$/i, '');
                            e.innerHTML = e.innerHTML.replace(/<p><\/p>/i, "");
                            break;
                        case "h1":
                            e.innerHTML = e.innerHTML.replace(/^<h1[^>]*>/i, "");
                            e.innerHTML = e.innerHTML.replace(/<\/h1>$/i, "");
                            e.innerHTML = e.innerHTML.replace(/<h1><\/h1>/i, "");
                            break;
                        case "h2":
                            e.innerHTML = e.innerHTML.replace(/<h2[^>]*>/i, '');
                            e.innerHTML = e.innerHTML.replace(/<\/h2>/i, '');
                            e.innerHTML = e.innerHTML.replace(/<h2><\/h2>/i, "");
                            break;
                        case "h3":
                            e.innerHTML = e.innerHTML.replace(/<h3[^>]*>/i, '');
                            e.innerHTML = e.innerHTML.replace(/<\/h3>/i, '');
                            e.innerHTML = e.innerHTML.replace(/<h3><\/h3>/i, "");
                            break;
                        case "h4":
                            e.innerHTML = e.innerHTML.replace(/<h4[^>]*>/i, '');
                            e.innerHTML = e.innerHTML.replace(/<\/h4>/i, '');
                            e.innerHTML = e.innerHTML.replace(/<h4><\/h4>/i, "");
                            break;
                        case "h5":
                            e.innerHTML = e.innerHTML.replace(/<h5[^>]*>/i, '');
                            e.innerHTML = e.innerHTML.replace(/<\/h5>/i, '');
                            e.innerHTML = e.innerHTML.replace(/<h5><\/h5>/i, "");
                            break;
                        case "h6":
                            e.innerHTML = e.innerHTML.replace(/<h6[^>]*>/i, '');
                            e.innerHTML = e.innerHTML.replace(/<\/h6>/i, '');
                            e.innerHTML = e.innerHTML.replace(/<h6><\/h6>/i, "");
                            break;
                        case "div":
                            e.innerHTML = e.innerHTML.replace(/<div[^>]*>/i, '');
                            e.innerHTML = e.innerHTML.replace(/<\/div>/i, '');
                            e.innerHTML = e.innerHTML.replace(/<div><\/div>/i, "");
                            break;
                        case "address":
                            e.innerHTML = e.innerHTML.replace(/<address[^>]*>/i, '');
                            e.innerHTML = e.innerHTML.replace(/<\/address>/i, '');
                            e.innerHTML = e.innerHTML.replace(/<address><\/address>/i, "");
                            break;
                        case "pre":
                            e.innerHTML = e.innerHTML.replace(/<pre[^>]*>/i, '');
                            e.innerHTML = e.innerHTML.replace(/<\/pre>/i, '');
                            e.innerHTML = e.innerHTML.replace(/<pre><\/pre>/i, "");
                            break;
                        default:
                            break;
                    }
                    this._AddAttributes(e);
                    this._RemoveDuplicates(e);
                    if (html.match(/^<p|h[1-6]|div|address|pre|ul|ol|li[^>]*>/i)) {
                        if (e.innerHTML.match(/<p|h[1-6]|div|address|pre|ul|ol|li[^>]*>/i)) {
                            alert('指定されたスタイルは、選択範囲中にブロック要素があるため適用できません。');
                        } else {
                            T.innerHTML = e.outerHTML;
                        }
                    } else {
                        if (e.innerHTML.match(/<p|h[1-6]|div|address|pre|ul|ol|li[^>]*>/i)) {
                            alert('指定されたスタイルは、選択範囲中にブロック要素があるため適用できません。');
                        } else {
                            B.pasteHTML(e.outerHTML);
                        }
                    }
                }
            } else {
                //インライン要素に対するスタイル適用
                if (!U.match(/^p$|h[1-6]|div|address|pre|ul|ol|li/i)) {
                    var e = document.createElement(this.Element);
                    //e.innerHTML=B.htmlText;
                    var html = B.htmlText;
                    var current = FCKCsutomSelectionElement();
                    ////// 061211 ////////////////////////////////////////////////////
                    if (html == current.outerHTML) {
                        html = current.innerHTML;	//選択した範囲と取得範囲が同一であれば、innerHTMLのみを適用範囲とする
                    } else {
                        html = B.text;				//選択した範囲と取得範囲が同一でなければ、テキストにのみスタイルを適用する
                    }
                    ////// 061211 ////////////////////////////////////////////////////
                    e.innerHTML = html;
                    this._AddAttributes(e);
                    this._RemoveDuplicates(e);
                    if (e.innerHTML.match(/<ul|li[^>]*>/i)) {
                        alert('指定されたスタイルは、選択範囲中にブロック要素があるため適用できません。');
                        return;
                    }
                    if (e.innerHTML.match(/^<(P|h[1-6]|div|address|pre|ul|ol|li)/i)) {
                        e.innerHTML = e.children[0].innerHTML;
                        B.pasteHTML(e.outerHTML);
                    } else {
                        B.pasteHTML(e.outerHTML);
                    }
                } else {
                    while (T) {
                        tag = T.tagName;
                        if (tag) {
                            if (tag == U) {
                                break;
                            }
                        } else {
                            T = false;
                            break;
                        }
                        T = T.parentNode;
                        if (T.tagName == 'BODY')
                            return;
                    }
                    var e = document.createElement(this.Element);
                    e.innerHTML = T.innerHTML;
                    this._AddAttributes(e);
                    this._RemoveDuplicates(e);
                    T.outerHTML = e.outerHTML;
                }
            }
        } else if (A.type == 'Control') {
            var C = FCK.ToolbarSet.CurrentInstance.Selection.GetSelectedElement();
            if (C.tagName == this.Element)
                this._AddAttributes(C);
        }
    };
    FCKStyleDef.prototype._AddAttributes = function (A) {
        for (var a in this.Attributes) {
            switch (a.toLowerCase()) {
                case 'style':
                    A.style.cssText = this.Attributes[a];
                    break;
                case 'class':
                    //A.setAttribute('className',this.Attributes[a],0);
                    // ▼IE9対応▼
                    A.className = this.Attributes[a];
                    // ▲IE9対応▲
                    break;
                case 'src':
                    A.setAttribute('_fcksavedurl', this.Attributes[a], 0);
                default:
                    A.setAttribute(a, this.Attributes[a], 0);
            }
        }
    };
    FCKStyleDef.prototype._RemoveDuplicates = function (A) {
        for (var i = 0; i < A.children.length; i++) {
            var B = A.children[i];
            this._RemoveDuplicates(B);
            if (this.IsEqual(B))
                FCKTools.RemoveOuterTags(B);
        }
    };
    FCKStyleDef.prototype.IsEqual = function (e) {
        if (e.tagName != this.Element)
            return false;

        for (var a in this.Attributes) {
            switch (a.toLowerCase()) {
                case 'style':
                    if (e.style.cssText.toLowerCase() != this.Attributes[a].toLowerCase())
                        return false;
                    break;
                case 'class':
                    //if (e.getAttribute('className',0)!=this.Attributes[a]) return false;
                    // ▼IE9対応▼
                    if (e.className != this.Attributes[a])
                        return false;
                    // ▲IE9対応▲
                    break;
                default:
                    if (e.getAttribute(a, 0) != this.Attributes[a])
                        return false;
            }
        }
        ;
        return true;
    };
    FCKStyleDef.prototype._RemoveMe = function (A) {
        if (!A)
            return;
        var B = A.parentElement;
        if (this.IsEqual(A)) {
            if (this.IsObjectElement) {
                for (var a in this.Attributes) {
                    switch (a.toLowerCase()) {
                        case 'class':
                            //						A.removeAttribute('className',0);
                            // ▼IE9対応▼
                            A.removeAttribute('class');
                            // ▲IE9対応▲
                            break;
                        default:
                            A.removeAttribute(a, 0);
                    }
                }
                ;
                return;
            } else
                FCKTools.RemoveOuterTags(A);
        }
        ;
        this._RemoveMe(B);
    }

    function FCKStylesLoader() {
        this.Styles = new Object();
        this.StyleGroups = new Object();
        this.Loaded = false;
        this.HasObjectElements = false;
    }
    ;
    FCKStylesLoader.prototype.Load = function (A) {
        var B = new FCKXml();
        B.LoadUrl(A);
        var C = B.SelectNodes('Styles/Style');
        for (var i = 0; i < C.length; i++) {
            var D = C[i].attributes.getNamedItem('element').value.toUpperCase();
            var E = new FCKStyleDef(C[i].attributes.getNamedItem('name').value, D);
            if (E.IsObjectElement)
                this.HasObjectElements = true;
            var F = B.SelectNodes('Attribute', C[i]);
            for (var j = 0; j < F.length; j++) {
                var G = F[j].attributes.getNamedItem('name').value;
                var H = F[j].attributes.getNamedItem('value').value;
                if (G.toLowerCase() == 'style') {
                    var I = document.createElement('SPAN');
                    I.style.cssText = H;
                    H = I.style.cssText;
                }
                ;
                E.AddAttribute(G, H);
            }
            ;
            this.Styles[E.Name] = E;
            var J = this.StyleGroups[D];
            if (J == null) {
                this.StyleGroups[D] = new Array();
                J = this.StyleGroups[D];
            }
            ;
            J[J.length] = E;
        }
        ;
        this.Loaded = true;
    }

}

