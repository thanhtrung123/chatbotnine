/**
 * util.js
 * ユーティリティ
 */
((base) => {

    base.util = {
        conf: {
            device_width: {
                smartphone: 640, //スマートフォンと識別する横幅
            },
        },
        device_type: null, //デバイスタイプ
        callback: {
            device_type_change: $.Callbacks(),
        },
    };

    let old_device_type = null

    //DOM Ready
    $(function () {
        //ウィンドウサイズ変更時
        $(window).resize(function () {
            updateDeviceType();
        });
    });

    /**
     * デバイスタイプの更新
     */
    let updateDeviceType = () => {
        let w = $(window).width();
        if (w > base.util.conf.device_width.smartphone) {
            base.util.device_type = COMMON_CONFIG.device.pc.id;
        } else {
            base.util.device_type = COMMON_CONFIG.device.smartphone.id;
        }
        if (old_device_type === null) old_device_type = base.util.device_type;
        if (old_device_type != base.util.device_type) {
            base.util.callback.device_type_change.fire(base.util.device_type);
            old_device_type = base.util.device_type;
        }
    };
    updateDeviceType();

    /**
     * Ajax
     * @param method
     * @param url
     * @param params
     * @param type
     * @param option
     * @returns {*|jQuery}
     */
    base.util.ajax = (method, url, params, type = 'json', option = {}) => {
        let dfd = $.Deferred(),
            setting = {
                type: method,
                url: url,
                data: params,
                dataType: type,
                beforeSend: function (xhr) {
                    if (window.Laravel && window.Laravel.apiToken)
                        xhr.setRequestHeader("Authorization", "Bearer " + window.Laravel.apiToken);
                },
            };
        $.extend(setting, option);
        let ajax = $.ajax(setting).done((ret, status, jqXHR) => {
            dfd.resolve(ret, status, jqXHR);
        }).fail((xh, ts, et) => {
            dfd.reject({xh: xh, ts: ts, et: et});
        });
        return dfd.promise({
            id: base.util.getRandStr(),
            ajax: ajax,
        });
    };

    /**
     * Ajax失敗時共通処理
     * @param ret
     */
    base.util.ajaxFail = (ret, msg = 'Ajax通信エラーが発生しました。') => {
        let emsg = ret.ts + '[' + ret.xh.status + "] " + ret.et;
        console.error(emsg);
        console.error(ret.xh);
        if (ret.xh.status === 401 && window.Laravel && window.Laravel.route) {
            base.util.showFlushError('認証エラー', `ログイン認証に失敗しました。再度ログインしてください。 <b><a href="${window.Laravel.route.login}">ログイン画面へ</a></b>`);
        } else {
            base.util.showFlushError('エラー', msg);
        }
    };

    /**
     * GETパラメータ→オブジェクト変換
     * @param str
     * @returns {{}}
     */
    base.util.parseParams = (str, decodeUri = true) => {
        let hash = str.split('&'), ret = {};
        for (let i = 0; i < hash.length; i++) {
            let para = hash[i].split('=');
            let key = decodeURIComponent(para[0]);

            if (key.match(/\[\]/)) {
                if (ret[key] == void 0) {
                    ret[key] = [];
                }
                ret[key].push((decodeUri) ? decodeURIComponent(para[1]) : para[1]);
            } else {
                ret[key] = (decodeUri) ? decodeURIComponent(para[1]) : para[1];
            }
        }
        return ret;
    };

    /**
     *
     * @param str
     * @param decode
     * @returns {*|jQuery}
     */
    base.util.htmlspecialchars = (str, decode = false) => {
        if (decode) return $('<div/>').html(str).text();
        else return $('<div/>').text(str).html().replace(/"/g, "&quot;").replace(/'/g, "&#039;");
    };

    /**
     * DataTables用　復元URL判定
     * @returns {{}|boolean}
     */
    base.util.isRestoreUrl = () => {
        let params = base.util.parseParams(location.search.slice(1));
        return (params && params.r == 1);
    };

    /**
     * URLパラメータ取得
     * @returns {undefined}
     */
    base.util.getUrlParams = () => {
        return base.util.parseParams(location.search.slice(1));
    };

    /**
     * ゼロ埋め
     * @param num
     * @param len
     * @param right
     * @returns {string}
     */
    base.util.zeroPad = (num, len, right) => {
        right = (right == void 0) ? false : right;
        return right ? (num + '0'.repeat(len)).substr(0, len) : ('0'.repeat(len) + num).slice(len * -1);
    };

    /**
     * URLをリンクに置換
     * @param {type} str
     * @returns {unresolved}
     */
    base.util.urlToLink = (str, target = '_blank') => {
        if (str.replace == void 0) return str;
        return str.replace(/((https?|ftp)(:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]+))/ig, `<a href="$1" target="${target}">$1</a>`);
    };

    /**
     * 改行をBRに変換
     * @param {type} str
     * @returns {unresolved}
     */
    base.util.nl2br = (str) => {
        if (str.replace == void 0) return str;
        return str.replace(/\r?\n/g, '<br />');
    };

    /**
     * 属性コピー
     * @param src
     * @param target
     */
    base.util.copyAttr = (src, target) => {
        let attr = src.get(0).attributes;
        for (let i = 0; i < attr.length; i++) {
            target.attr(attr[i].name, attr[i].value);
        }
    };

    /**
     * DATA属性コピー
     * @param src
     * @param target
     */
    base.util.copyData = (src, target) => {
        let data = src.data();
        base.util.bindData(data, target);
    };

    /**
     * DATA属性バインド
     * @param data
     * @param target
     */
    base.util.bindData = (data, target) => {
        $.each(data, (k, v) => {
            target.data(k, v);
        });
    };

    /**
     * DOM検索
     * @param str
     * @param p
     * @returns {T}
     */
    base.util.findDom = (str, p = $(document)) => {
        if (typeof str != 'string' || str === '')
            return $();
        if (str.match(/^#|^\.|^\[/)) {
            return p.find(str);
        } else {
            if (!str.match(/[!"#$%&'()*+,./:;<=>?@[\]^`{|}~]/)) {
                if (p.find('#' + str).length)
                    return p.find('#' + str);
                if (p.find('[name="' + str + '"]').length)
                    return p.find('[name="' + str + '"]');
            } else if (str.match(/[\[\]]/)) {
                str = str.replace(/\[/g, '\\[').replace(/\]/g, '\\]');
                if (p.find('#' + str).length)
                    return p.find('#' + str);
                if (p.find('[name="' + str + '"]').length)
                    return p.find('[name="' + str + '"]');
            }
        }
        return $();
    };

    /**
     * DOM検索（複数）
     * @param ary
     * @param p
     * @returns {Array}
     */
    base.util.findDoms = (ary, p = $(document)) => {
        let doms = [];
        $.each(ary, (i, v) => {
            doms.push(base.util.findDom(v, p));
        });
        return doms;
    };

    /**
     * 値セット
     * @param val
     * @param str
     * @param obj
     */
    base.util.setValue = (val, str, obj = $(document), ev = ['change']) => {
        let dom = base.util.findDom(str, obj);
        if (dom.filter(':input').length) {
            dom.val(val);
            if ($.inArray('change', ev) !== -1)
                dom.change();
            if ($.inArray('blur', ev) !== -1)
                dom.blur();
        } else {
            dom.html(val);
        }
    };

    /**
     * 値ゲット
     * @param str
     * @param obj
     */
    base.util.getValue = (str, obj = $(document)) => {
        let dom = base.util.findDom(str, obj);
        if (dom.filter(':input').length) {
            return dom.val();
        } else {
            return dom.html();
        }
    };

    /**
     * 金額 → Number
     * @param str
     * @returns {number}
     */
    base.util.moneyToNumber = (str, empty = '') => {
        if (!str)
            return empty;
        return Number(str.replace(/,/g, ''));
    };

    /**
     *　Number → 金額
     * @param str
     * @param fix
     * @returns {*}
     */
    base.util.numberToMoney = (str, fix = void 0, empty = '') => {
        if (!str)
            return empty;
        str = str.toString().replace(/[Ａ-Ｚａ-ｚ０-９]/g, function (s) {
            return String.fromCharCode(s.charCodeAt(0) - 0xFEE0);
        }).replace(/[^\d.-]+/g, '');
        if (fix == void 0 || fix == 0) {
            var m = str.match(/^\-?\d+/);
            str = m ? Number(m[0]).toLocaleString() : empty;
        } else {
            var df = str.split('.');
            if (df.length > 2) {
                str = empty;
            } else {
                str = df[0].match(/^\-?\d+$/) ? Number(df[0]).toLocaleString() : empty;
                if (df[1] != void 0) {
                    str = str + "." + base.util.zeroPad(df[1], fix, true);
                }
            }
        }
        return str;
    };

    /**
     * money_inputの内容切替（カンマ有無）
     * @param p
     * @param flg
     */
    base.util.toggleMoneyInputComma = (commaFlg, p = $(document)) => {
        p.find('.money_input').each(function () {
            let d = $(this).data() || {};
            if (commaFlg)
                $(this).val(base.util.numberToMoney($(this).val(), d.fix, d.empty));
            else
                $(this).val(base.util.moneyToNumber($(this).val(), d.empty));
        })
    };

    /**
     * Viewから指定されたパラメータを変換
     * @param params
     * @param source
     * @returns {{}}
     */
    base.util.getMappedParams = (params, source = {}) => {
        let ret = {};
        $.each(params, (k, v) => {
            if (source[v]) {
                ret[k] = source[v];
            } else {
                let iv = base.util.getValue(v);
                ret[k] = (iv === void 0) ? v : iv;
            }
        });
        return ret;
    };

    /**
     * テンプレートの値を置換
     * @param {type} html
     * @param {type} data
     * @returns {unresolved}
     */
    base.util.replaceTemplateValue = (html, data) => {
        $.each(data, (k, v) => {
            let reg = new RegExp(`{${k}}|%${k}%|%25${k}%25`, 'g');
            html = html.replace(reg, v);
        });
        return html;
    };

    /**
     * Disabledセット
     * @param btn
     * @param disabled
     */
    base.util.setDisabled = (btn, disabled = true, cls = null) => {
        if (disabled) {
            btn.addClass('disabled');
            if (!btn.filter('[type="submit"]').length) {
                btn.prop('disabled', true);
            }
            if (cls !== null) {
                btn.addClass(cls);
            }
        } else {
            btn.removeClass('disabled').prop('disabled', false);
            if (cls !== null) {
                btn.removeClass(cls);
            }
        }
    };

    /**
     * ランダム文字列（半角英数記号）取得
     * @param n
     * @returns {string}
     */
    base.util.getRandStr = (n = 10, symbol = false) => {
        let base_w = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
            strs = symbol ? base_w + '!#$%&_-+*=~^' : base_w,
            len = strs.length, nstr = '';
        for (var i = 0; i < n; i++)
            nstr += strs[Math.floor(Math.random() * len)];
        return nstr;
    };

    /**
     * 強制シリアライズ（Disabledの値を取得）
     * @param form
     * @returns {*}
     */
    base.util.forceSerialize = (form) => {
        let disabled = form.find(':input:disabled').removeAttr('disabled'),
            ret = form.serialize();
        disabled.attr('disabled', 'disabled');
        return ret;
    };

    /**
     * Formタグ以外用シリアライズ
     * @param noform
     * @returns {*}
     */
    base.util.serializeFromNoForm = (noform) => {
        noform.wrap('<form/>');
        let para = noform.parent().serialize();
        noform.unwrap();
        return para;
    };

    /**
     * インプットタグをラベルに変換
     * @param {type} input
     * @returns {$|_$|Element|$.el|_$.el}
     */
    base.util.inputToLabel = (input) => {
        let label = $('<label/>').addClass('control-label');
        if (input.length == 1) {
            //単体
            let tag = input.prop('tagName').toLowerCase();
            label.text(base.util.getInputConfirmValue(input));
        } else {
            //複数(checkbox,radio)
            let vals = [];
            input.filter(':checked').each(function () {
                vals.push(base.util.getInputConfirmValue($(this)));
            });
            label.text(vals.join(','));
        }
        return label;
    };

    /**
     * 入力から確認用の値を取得
     * @param {type} input
     * @returns {String}
     */
    base.util.getInputConfirmValue = (input) => {
        let value = '';
        switch (input.prop('tagName').toLowerCase()) {
            case 'select':
                value = input.children().filter(':selected').text();
                break;
            default:
                if (input.data('confirmValue')) {
                    value = input.data('confirmValue');
                } else if (input.parent().prop('tagName').toLowerCase() == 'label') {
                    value = input.parent().text();
                } else {
                    value = input.val();
                }
                break;
        }
        return value;
    };

    /**
     * THから対応するTDを取得
     * @param {type} th
     * @param {type} rowIdx
     * @returns {Array}
     */
    base.util.getTdFromTh = (th, rowIdx = null) => {
        let tbl = th.closest('table'), idx = th.parent().children().index(th), td;
        if (rowIdx === null) {
            td = tbl.find('tbody>tr').eq(rowIdx).children().eq(idx);
        } else {
            td = [];
            tbl.find('tbody>tr').each(function () {
                td.push($(this).eq(idx));
            });
        }
        return td;
    };

    /**
     * TDから対応するTHを取得
     * @param {type} td
     * @returns {unresolved}
     */
    base.util.getThFromTd = (td) => {
        let tbl = td.closest('table'), idx = td.parent().children().index(td);
        return tbl.find('thead>tr>th').eq(idx);
    };

    /**
     * 確認用データを行から取得
     * @param {type} row
     * @returns {util=>#5.base.util.getConfirmFromTable.ret}
     */
    base.util.getConfirmFromTable = (row) => {
        let tbl = row.closest('table'), ret = {};
        row.children().each(function () {
            let td = $(this), th = base.util.getThFromTd(td), hd = th.text().replace(/\r?\n|\s+/g, '');
            if (!hd.length)
                return true;
            ret[hd] = td.text();
        });
        return ret;
    };

    /**
     *
     * @param {type} title
     * @param {type} msg
     * @returns {undefined}
     */
    base.util.showFlush = (title, msg) => {
        let area = $('.flush_message_area'), tpl = $(area.find('template').html());
        tpl.find('.flush_message').append(`<b>${title}</b> ${msg}`);
        area.append(tpl);
    };

    /**
     *
     * @param title
     * @param msg
     * @param scroll
     */
    base.util.showFlushError = (title, msg, scroll = true) => {
        let area = $('.flush_message_area'), tpl = $(area.find('template').html().replace('alert-info', 'alert-danger'));
        tpl.find('.flush_message').append(`<b>${title}</b> ${msg}`);
        area.append(tpl);
        if (scroll) {
            base.util.scrollToObject(tpl, 10);
        }
    };

    /**
     *
     * @param dom
     */
    base.util.scrollToObject = (dom, offset = 0) => {
        $('html,body').scrollTop(dom.offset().top - offset);
    };

    /*
     * polyfill
     */
    //String.repeat (IE用)
    if (!String.prototype.repeat) {
        String.prototype.repeat = function (count) {
            return Array(count * 1 + 1).join(this);
        };
    }

})(window.base);