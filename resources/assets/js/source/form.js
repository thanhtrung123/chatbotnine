/**
 * form.js
 * Form関連共通処理
 */
((base) => {
    base.form = {};

    /**
     * コールバック
     */
    base.form.callback = {
        //domCopy
        domCopy: {
            before: $.Callbacks(),
            after: $.Callbacks(),
        },
        inputToggle: $.Callbacks(),
    };

    let bindedForms = {}, replacedInputs = {};


    base.form.submitConfirmModal = (btn) => {

    };

    /**
     * 入力確認モーダル用HTML取得
     * @param id
     * @param target_id
     * @returns {*|jQuery}
     */
    base.form.getConfirmHtml = (id, target_ids = []) => {
        let form = base.util.findDom(id).clone(true);
        if (target_ids.length) {
            let cform = form.clone(true), targets = base.util.findDoms(target_ids, form);
            cform.children().remove();
            $.each(targets, (i, o) => {
                o.children().each(function () {
                    modConfirmFormChild($(this));
                });
                cform.append(o.children().clone(true));
            });
            form = cform;
        } else {
            form.children().each(function () {
                modConfirmFormChild($(this));
            });
        }
        form.attr('id', form.attr('id') + '_modal');
        return $('<p/>').append(form).html();
    };

    /**
     *
     * @param form_child
     */
    let modConfirmFormChild = (form_child) => {
        let input, label;
        if (form_child.attr('data-confirm') !== "") {
            form_child.remove();
        } else {
            form_child.find('[data-confirm-ignore]').remove();
            let names = {};
            form_child.find('[name]').each(function () {
                names[$(this).attr('name')] = 1;
            });
            let name_ary = Object.keys(names);
            $.each(names, (name, d) => {
                input = form_child.find(`[name="${name}"]`), label = base.util.inputToLabel(input).wrap('<div/>');
                if (name_ary.length > 1) {
                    input.parent().html(label.html());
                } else {
                    input.closest('div').html(label.html());
                }
            });
        }
    };

    /**
     * 入力確認モーダル表示
     * @param {type} id
     * @returns {undefined}
     */
    base.form.showConfirm = (id, type, target_ids) => {
        let html = base.form.getConfirmHtml(id, target_ids), modal = base.modal.showModal('confirmModal'), submit_btn = modal.find('.submit_button');
        modal.find('.modal-body').html(html);
        submit_btn.data('form', id);
        if (type == 'update')
            submit_btn.text('修正');
        if (type == 'delete') {
            submit_btn.removeClass('btn-primary').addClass('btn-danger').text('削除');
            modal.find('.modal-title').text('削除確認');
        }
    };

    /**
     *
     * @param formAry
     * @param id
     */
    base.form.mapping = (formAry, id) => {
        let formMapping = {};
        for (let i in formAry) {
            if (formMapping[formAry[i]]) {
                formMapping[formAry[i]][id] = 1;
            } else {
                formMapping[formAry[i]] = {};
                formMapping[formAry[i]][id] = 1;
            }
        }
        return formMapping;
    };

    /**
     *
     * @param formAry
     */
    base.form.bindSearch = (formAry, callback) => {
        for (let i in formAry) {
            if (bindedForms[formAry[i]] == void 0) {
                let form = base.util.findDom(formAry[i]);
                form.submit(function (e) {
                    e.preventDefault();
                    callback($(this));
                });
                bindedForms[formAry[i]] = form;
            }
        }
    };

    /**
     * インプット有効無効切替
     */
    let inputToggle = (chk) => {
        let data = chk.data('inputToggle'), readonly = false;
        let get_area = (obj) => {
            let data = obj.data('inputToggle');
            if (typeof data == 'object') {
                return data.area;
            }
            return data;
        };

        if (typeof data == 'object') {
            if (data.readonly) readonly = true;
        }
        let chk_flg = chk.prop('checked'), area_sel = get_area(chk), area = base.util.findDom(area_sel), prop = readonly ? 'readonly' : 'disabled', in_chk = [];
        $('[data-input-toggle]').each(function () {
            let o_area = get_area($(this));
            if (o_area == area_sel) return true;
            if (!area.find(o_area).length) return true;
            in_chk.push($(this));
        });

        if (chk_flg) {
            //有効化
            area.find(':input').not('[data-ignore],[data-ignore-tmp]').prop(prop, false);
            if (in_chk.length) {
                for (let i in in_chk) {
                    inputToggle(in_chk[i]);
                }
            }
        } else {
            //無効化
            area.find(':input').not('[data-ignore],[data-ignore-tmp]').prop(prop, true);
        }
        base.form.callback.inputToggle.fire(chk_flg, chk, area);
    };

    /**
     *
     * @param p
     */
    base.form.bindInputToggle = (p = $(document)) => {
        p.on('change', '[data-input-toggle]', function (e) {
            inputToggle($(this));
        });
        p.find('[data-input-toggle]').each(function () {
            inputToggle($(this));
        });
    };

    /**
     *
     * @type {jQuery.fn.init|*|jQuery|HTMLElement}
     */
    base.form.bindReplaceInput = (p = $(document)) => {
        p.on('click', '[data-replace-input]', function (e) {
            var cb_func = null;
            if (getFullscreenElement() != null && $('#mySelect2').length > 0) {
                cb_func = function() {
                    $('.select2').select2({
                        dropdownParent: $('#mySelect2')
                    });
                };
            }
            replaceInputProcess($(this), cb_func);
        });
    };
    let replaceInputProcess = (btn, callback) => {
        let data = btn.data('replaceInput');
        data.target = base.util.findDom(data.target);
        if (replacedInputs[data.target.attr('name')] || data.target.length == 0) {
            return;
        }
        data.btn = btn;
        btn.prop('disabled', true);
        //TODO:なんかロード中っぽくしたい
        data.target.hide();
        data.target.parent().append('<span>ロード中…</span>');
        if (data.ajax) {
            base.util.ajax('get', data.ajax.url, {}).done(function (ret) {
                data.options = ret.data;
                createReplaceInput(data);
                if (callback) callback();
            }).fail(function (ret) {
                base.util.ajaxFail(ret);
            });
        } else {
            createReplaceInput(data);
        }
    };
    let createReplaceInput = (data) => {
        let input, now_value = data.target.val(), wrap_target = data.target.parent(), target_name = data.target.attr('name');
        if (data.replace_name) {
            target_name = target_name.replace(data.replace_name[0], data.replace_name[1]);
        }
        wrap_target.find('span').remove();
        switch (data.type) {
            case 'select':
                input = $('<select/>', {'class': 'form-control select2', 'data-width': '100%'});
                input.attr('name', target_name);
                $.each(data.options, (k, v) => {
                    let opt = $('<option/>', {value: k,}).text(v);
                    if (v == now_value) {
                        opt.prop('selected', true);
                    }
                    input.append(opt);
                });
                break;
            default:
                break;
        }
        replacedInputs[target_name] = 1;
        wrap_target.append(input);
        base.bind.container.select2(wrap_target);
        data.target.remove();
        data.btn.prop('disabled', false);
    };
    let getFullscreenElement = () => {
        if (document.webkitFullscreenElement) {
            return document.webkitFullscreenElement;
        }
        else if (document.mozFullScreenElement) {
            return document.mozFullScreenElement;
        }
        else if (document.msFullscreenElement) {
            return document.msFullscreenElement;
        }
        else if (document.fullscreenElement) {
            return document.fullscreenElement;
        }
    };

    //DOM ロード後
    $(function () {

        /**
         * 登録ボタン押下時
         */
        $(document).on('click', '.submit_button', function (e) {
            $(this).prop('disabled', true);
            let id = $(this).data('form'), form = base.util.findDom(id);
            form.append($('<input/>', {
                type: 'hidden', name: 'store'
            }));
            form.submit();
        });

        /**
         * 多重クリック無効
         */
        $(document).on('submit', 'form', function (e) {
            let id = $(this).attr('id');
            if (!id.match(/entry|import/)) return;
            //送信ボタンの値を送れるようにする
            let btn = $(this).find('[type="submit"]');
            if (btn.attr('name'))
                $(this).append($('<input/>', {type: 'hidden', name: btn.attr('name'), value: btn.val()}));
            $(this).find('[type="submit"]').prop('disabled', true);
        });

        /**
         * Ajax実行ボタン押下時
         */
        $(document).on('click', '.ajax_exec_button', function (e) {
            //TODO:複数のAjaxに対応？
            let btn = $(this), data = btn.data(), modal = btn.closest('.modal'), btns = modal.find('button'), count = 0,
                is_chain = (data.chain != void 0);
            btns.attr('disabled', true);
            if (modal.length) {
                let step = is_chain ? '(1/' + (data.chain.length + 1) + ')' : '';
                modal.find('.modal-status').show().find('.modal-status-message').html('処理中…' + step).addClass('active');
            }
            let execAjax = (url) => {
                base.util.ajax('GET', url, data.params).done((ret) => {
                    let end = () => {
                        if (modal.length) {
                            modal.find('.modal-status-message').text('処理完了').removeClass('active');
                            setTimeout(() => {
                                modal.modal('hide');
                                if ($("#sysn").hasClass("button-glow")) {
                                    $('#sysn').addClass('btn-default').removeClass('button-glow');
                                }
                                base.util.showFlush('情報', modal.find('.modal-title').text() + 'が完了しました。');
                            }, 500);
                        }
                        btns.attr('disabled', false);
                    };
                    if (data.chain == void 0) {
                        //通常
                        end();
                    } else {
                        if (data.chain[count] == void 0) {
                            end();
                        } else {
                            modal.find('.modal-status-message').html('処理中…' + '(' + (count + 2) + '/' + (data.chain.length + 1) + ')');
                            execAjax(data.chain[count++]);
                        }
                    }
                }).fail((ret) => {
                    modal.modal('hide');
                    base.util.ajaxFail(ret, modal.find('.modal-title').text() + 'に失敗しました。');
                    btns.attr('disabled', false);
                });
            };
            execAjax(data.ajax.url);
        });

        /**
         * 確認リンク押下時
         */
        $(document).on('click', '[data-button-confirm]', function (e) {
            let data = $(this).data('buttonConfirm');
            if (!confirm(data.message)) {
                e.preventDefault();
            }
        });

        /**
         * 選択ボタン押下時
         */
        $(document).on('click', '.choice_select_button', function (e) {
            let btn = $(this), data = btn.data(), modal = btn.closest('.modal');
            if (data.map_text != void 0) {
                let sel_text = modal.find('.select2>option[value="' + modal.find('.select2').val() + '"]').text();
                base.util.setValue(sel_text, data.map_text);
                modal.modal('hide');
            }
        });

        /**
         * 汎用DOM削除ボタン
         */
        $(document).on('click', '[data-dom-delete]', function (e) {
            let data = $(this).data('domDelete'), target = base.util.findDom(data.target), count = 0;
            if (data.parent != void 0) {
                count = base.util.findDom(data.parent).length;
            }
            if (data.limit != void 0) {
                if (count == data.limit) return false;
            }
            target.remove();
        });

        /**
         * 汎用DOMコピーボタン
         */
        $(document).on('click', '[data-dom-copy]', function (e) {
            let data = $(this).data('domCopy'), tpl_html = base.util.findDom(data.src).clone(true).html();
            if (data.idx_src != void 0) {
                let idx_src = base.util.findDom(data.idx_src), next_idx = 0;
                idx_src.each(function () {
                    if (next_idx < $(this).val() - 0) next_idx = $(this).val() - 0;
                });
                tpl_html = base.util.replaceTemplateValue(tpl_html, {idx: next_idx + 1});
            }
            let src = $('<div/>').html(tpl_html).children(), area = base.util.findDom(data.area);
            base.form.callback.domCopy.before.fire(src, area);
            if (data.type && data.type == 'prepend') area.prepend(src);
            else area.append(src);
            base.form.callback.domCopy.after.fire(src, area);
        });

        /**
         * ダイレクトエラー表示
         */
        if (typeof direct_errors != 'undefined') {
            $.each(direct_errors, (title, msg) => {
                base.util.showFlushError(title, msg);
            });
        }

    });

})(window.base);