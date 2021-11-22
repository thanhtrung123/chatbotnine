/**
 * bind.js
 * バインド関連共通処理
 */
((base) => {
    base.bind = {};

    /**
     * bind container
     */
    base.bind.container = {
        datatables: (p = $(document)) => {
            base.dataTables.bind(p);
        },
        modal: (p = $(document)) => {
            p.on('click', '[data-modal]', function (e) {
                base.modal.showDataModal($(this));
            });
        },
        select2: (p = $(document)) => {
            p.find('.select2').each(function () {
                let option = {}, data = $(this).data();
                if (data.width) {
                    option.width = data.width;
                }
                $(this).select2(option);
            });
        },
        datepicker: (p = $(document)) => {
            p.find('.datepicker').each(function () {
                $(this).datepicker({
                    format: "yyyy/mm/dd",
                    language: 'ja',
                    autoclose: true
                });
            });
        },
        tooltip: (p = $(document)) => {
            p.find('[data-toggle="tooltip"]').tooltip();
        },
        omit_message: (p = $(document)) => {
            p.find('.omit_message').each(function () {
                $(this).off('dblclick').on('dblclick', function (e) {
                    let ostr = $(this), str = ostr.attr('title'), p = ostr.parent(),
                        w = p ? p.width() : 0, h = p ? p.height() : 0;
                    ostr.hide();
                    let inp = $('<input/>', {
                        type: 'text',
                        value: str,
                        // 'class': 'form-control',
                        style: 'width:' + (w ? w + 'px' : '100%') + ';height:' + (h ? h + 'px' : '100%') + ';',
//                        readonly: ''
                    });
                    ostr.after(inp);
                    inp.focus().select();
                    inp.blur(function (e) {
                        inp.remove();
                        ostr.show();
                    });
                });
            });
        },
        input_toggle: (p = $(document)) => {
            base.form.bindInputToggle(p);
        },
        replace_input: (p = $(document)) => {
            base.form.bindReplaceInput(p);
        },
    };

    /**
     * 全イベントバインド
     * @param p
     */
    base.bind.all = (p = $(document), isInit = false) => {
        $.each(base.bind.container, (k, f) => {
            f(p, isInit);
        });
    };

    /**
     * datatables draw時　バインド
     * @returns {undefined}
     */
    base.bind.drawDatatables = (p = $(document)) => {
        base.bind.container.tooltip(p);
        base.bind.container.omit_message(p);
    };

    /**
     * Dom loaded
     * @returns {undefined}
     */
    $(() => {
        base.bind.all($(document), true);
    });


})(window.base);