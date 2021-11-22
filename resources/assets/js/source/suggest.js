/**
 * suggest.js
 * サジェスト用JS
 */
((base) => {

    base.suggest = {}

    let key_map = {
        TAB: 9,
        ENTER: 13,
        ESC: 27,
        UP: 38,
        DOWN: 40
    };

    let data = {};

    /**
     * 実行
     * @param id
     */
    let exec = (id) => {
        let obj = data[id].obj, txt = obj.val();
        if (txt === '') {
            clear(id);
            return;
        }
        if (data[id].length) toggleList(id, true);
        if (txt === data[id].txt) return;
        data[id].txt = txt;
        if (data[id].tm) clearTimeout(data[id].tm);
        data[id].tm = setTimeout(function () {
            getData(data[id].url, txt).done(function (ret) {
                if (data[id].obj.val() == '') {
                    clear(id);
                    return;
                }
                let ul = createList(ret.list);
                data[id].length = ret.list.length;
                toggleList(id, !!ret.list.length);
                data[id].area.html('');
                data[id].area.append(ul);
            });
        }, data[id].wait);
    };

    /**
     * リスト破棄
     * @param id
     */
    let clear = (id) => {
        if (data[id].tm) clearTimeout(data[id].tm);
        data[id].area.html('');
        data[id].txt = '';
        data[id].length = 0;
        toggleList(id, false);
    };

    /**
     * Ajaxデータ取得
     * @param url
     * @param txt
     * @returns {jQuery}
     */
    let getData = (url, txt) => {
        let dfd = $.Deferred();
        base.util.ajax('get', url, {message: txt}).done(function (ret) {
            dfd.resolve(ret);
        }).fail(function (ret) {
            dfd.reject(ret);
        });
        return dfd.promise();
    };

    /**
     * リスト作成
     * @param list
     * @returns {jQuery|HTMLElement|*}
     */
    let createList = (list) => {
        let ul = $('<ul/>');
        $.each(list, function (i, row) {
            ul.append($('<li/>').text(row.label));
        });
        return ul;
    };

    /**
     * 特殊キー操作
     * @param id
     * @param key
     * @param e
     */
    let keyControl = (id, key, e) => {
        let li = data[id].area.find('li'), sel = li.filter('.selected'), txt = '';
        if ((key == 'UP' || key == 'DOWN') && data[id].length) {
            e.preventDefault();
            li.removeClass('selected');
            let max = li.length, now = li.index(sel), home = false;
            if (sel.length) {
                if (key == 'UP') {
                    now--;
                    if (now < 0) home = true;
                } else {
                    now++;
                    if (now >= max) home = true;
                }
                if (home) {
                    txt = data[id].txt;
                } else {
                    txt = li.eq(now).addClass('selected').text();
                }
            } else {
                if (key == 'UP') {
                    txt = li.eq(max - 1).addClass('selected').text();
                } else {
                    txt = li.eq(0).addClass('selected').text();
                }
            }
            data[id].obj.val(txt);
        } else if (key == 'ENTER') {

        } else if (key == 'TAB') {

        } else if (key == 'ESC') {
            // data[id].obj.blur();
        }


    };

    /**
     * リスト表示切替
     * @param id
     * @param flg
     */
    let toggleList = (id, flg) => {
        if (flg)
            data[id].area.show();
        else
            data[id].area.hide();
    };

    /**
     * サジェスト発火用
     * @param obj
     */
    let bindSuggest = (obj) => {
        let id = obj.prop('id');
        data[id] = obj.data('suggest');
        data[id].obj = obj;
        data[id].last = (new Date).getTime();
        data[id].txt = '';
        data[id].lock = false;
        let area_id = id + '_suggest';
        $('body').append($('<div/>', {id: area_id, 'class': 'autocomplete'}));
        data[id].area = $('#' + area_id);
        toggleList(id, false);

        obj.click(function (e) {
            exec(id);
        });
        obj.bind('paste', function (e) {
            exec(id);
        });
        obj.focus(function (e) {
            exec(id);
        });
        obj.keydown(function (e) {
            let kc = e.keyCode, hit = null, txt = obj.val();
            $.each(key_map, function (key, code) {
                if (kc !== code) return true;
                // e.preventDefault();
                hit = key;
                return false;
            });
            if (hit === null)
                setTimeout(() => {
                    exec(id)
                }, 100);
            else
                keyControl(id, hit, e);
        });
        obj.blur(function (e) {
            if (data[id].lock) return;
            toggleList(id, false);
        });
    };


    /**
     * DOM Ready
     */
    $(function () {
        //各イベントバインド
        $('[data-suggest]').each(function () {
            bindSuggest($(this));
        });
        $(document).on('click', '.autocomplete li', function (e) {
            let div = $(this).closest('div'), id = div.prop('id').replace(/_suggest$/, '');
            data[id].obj.val($(this).text()).focus();
            data[id].lock = false;
            toggleList(id, false);
        });
        $(document).on('mouseover', '.autocomplete', function (e) {
            let div = $(this), id = div.prop('id').replace(/_suggest$/, '');
            data[id].lock = true;
        });
        $(document).on('mouseout', '.autocomplete', function (e) {
            let div = $(this), id = div.prop('id').replace(/_suggest$/, '');
            data[id].lock = false;
        });

    })


})(window.base);