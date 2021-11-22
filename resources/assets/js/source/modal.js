/**
 * modal.js
 * Modal関連共通処理
 */
((base) => {
    base.modal = {};

    let conf = {};

    /**
     * モーダルオープン
     * @param id
     * @returns {T}
     */
    base.modal.showModal = (id) => {
        let obj = base.util.findDom(id);
        obj.modal('show');
        return obj;
    };

    /**
     *
     * @param data
     */
    base.modal.showDataModal = (btn) => {
        let data = btn.data('modal'), modal;
        switch (data.type) {
            case 'ajax':
                modal = base.modal.showModal('ajaxModal');
                modal.find('.ajax_exec_button').data('ajax', data.ajax);
                modal.find('.ajax_exec_button').data('chain', data.chain);
                modal.find('.modal-title').html(data.message.title);
                modal.find('.modal-message').html(data.message.body);
                modal.find('.modal-status').hide().find('.modal-status-message').text('');
                break;
            case 'choice':
                modal = base.modal.showModal('choiceModal');
                base.util.bindData(data, modal.find('.choice_select_button'));
                let cbody = modal.find('#choice_body'), ctitle = data.title || '選択';
                modal.find('#choice_title').html(ctitle);
                cbody.html('<p style="color:gray;">読込中…</p>');
                if (data.ajax) {
                    base.util.ajax('get', data.ajax.url).done((ret) => {
                        // console.log(ret);
                        let sel = $('<select/>', {'class': 'form-control select2', 'data-width': '100%'});
                        $.each(ret.data, (k, v) => {

                            let opt = $('<option/>', {value: k,}).text(v);
                            if (data.choice_text != void 0) {
                                let ct = base.util.getValue(data.choice_text);
                                if (ct == v) {
                                    opt.attr('selected', 'selected');
                                }
                            }
                            sel.append(opt);
                        });
                        cbody.html('');
                        cbody.append(sel);
                        base.bind.container.select2(cbody);
                    }).fail((ret) => {
                        base.util.ajaxFail(ret);
                    });
                }
                // modal.find('.choice_select_button').data('ajax', data.ajax);
                break;
            case 'chat':
                modal = base.modal.showModal('chatModal');
                let iframe = modal.find('iframe');
                if (!iframe.attr('src')) {
                    iframe.attr('src', iframe.data('src'));
                } else {
                    let content = iframe.contents();
                    modal.off('shown.bs.modal');
                    modal.on('shown.bs.modal', function (e) {
                        $('body,html', content).animate({scrollTop: content.find('#msg_area').get(0).scrollHeight});
                    });
                }
                break;
            case 'csvImport':
                modal = base.modal.showModal('csvImportModal');
                modal.find('#csv_import_form').attr('action', data.params.action);
                break;
            case 'delete':
                modal = base.modal.showModal('deleteModal');
                if (btn.closest('.dataTable ').length) {
                    let confirm = base.util.getConfirmFromTable(btn.closest('tr')), dfb = modal.find('#deleteModalBody');
                    dfb.html("");
                    modal.find('#delete_form').attr('action', data.params.action);
                    $.each(confirm, (k, v) => {
                        let tpl = $(modal.find('#deleteModalTemplate').html());
                        tpl.find('label:eq(0)').html(k);
                        tpl.find('label:eq(1)').html(v);
                        dfb.append(tpl);
                    });
                }
                break;
            case 'showReport':
                modal = base.modal.showModal('modalShow');
                modal.find('.modal-category').html(data.message.category);
                modal.find('.modal-question').html(data.message.question);
                modal.find('.modal-answer').html(data.message.answer);
                break;
        }
    };

})(window.base);