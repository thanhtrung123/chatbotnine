/**
 * datatables.js
 * DataTables関連共通処理
 */
((base) => {
    base.dataTables = {};
    let tables = {}, formMapping = {}, ids = {}, details = {}, detailsCache = {};
    /**
     * コールバック
     */
    base.dataTables.callback = {
        //ajax取得
        ajax: {
            //完了時
            done: $.Callbacks(),
        },
        //描画時
        onDraw: $.Callbacks('unique'),
    };

    /**
     * 内部処理（外部からも呼んでるが…）
     */
    base.dataTables.private = {
        /**
         * TDに対応したTHのデータを取得
         * @param td
         * @returns {{data: *, chk: *, ridx: *, cidx: *}}
         */
        getThData: (td) => {
            let table = td.closest('table'), td_tr = td.parent(),
                ridx = table.find('tbody').find('tr').index(td_tr), cidx = td_tr.children().index(td),
                th_tr = table.find('thead').find('tr'), th = th_tr.find('th').eq(cidx);
            return {
                data: th.data(),
                chk: td_tr.find('[id^="_checkbox"]'),
                ridx: ridx,
                cidx: cidx,
            };
        },
        /**
         * 描画時実行
         * @param e
         * @param s
         */
        onDraw: (e, s) => {
            let table = $(e.target), tid = table.prop('id');
            //無効行設定
            let data = table.data('tables');
            if (data.ignore) {
                $.each(data.ignore, (clm, sel) => {
                    $.each(s.json.data, (i, row) => {
                        base.util.findDom(sel).each(function () {
                            if (row[clm] == $(this).val()) {
                                table.find('#_checkbox_' + i).prop('disabled', true);
                            }
                        });
                    });
                });
            }
            //data-templateが設定されているTHをループ
            $.each(table.find('th[data-template="true"]'), function () {
                let $this = $(this), th_tr = table.find('thead').find('tr'), th_idx = th_tr.find('th').index($this.get(0));
                $.each(table.find('tbody').find('tr'), function (k, v) {
                    let td = $(this).find('td').eq(th_idx);
                    td.html(base.util.replaceTemplateValue($this.find('template').html(), s.json.data[k]));
                });
            });
            //
            detailsCache[tid] = {};
            table.find('.details-control-button').each(function () {
                let key = base.util.getRandStr();
                $(this).attr('data-key', key);
            });
            //bind
            base.bind.drawDatatables(table);
        },
        /**
         * Ajax取得前データ加工
         * @param d
         * @param id
         */
        dataSrc: (d, id) => {
            if (d.extend && d.extend.ids) {
                base.dataTables.private.margeIds(d.extend.ids, id);
            }
        },
        /**
         * ID配列用セッションストレージ名取得
         * @param id
         * @returns {string}
         */
        getIdsItemName: (id) => {
            return 'AllIds_' + id + location.pathname;
        },
        /**
         * ID配列をマージ
         * @param ids
         * @param id
         */
        margeIds: (ids, id) => {
            let nowIds = JSON.parse(sessionStorage.getItem(base.dataTables.private.getIdsItemName(id))),
                newIds = $.extend(ids, nowIds);
            sessionStorage.setItem(base.dataTables.private.getIdsItemName(id), JSON.stringify(newIds));
        },
        /**
         * ID配列存在チェック
         * @param id
         * @returns {boolean}
         */
        existsIds: (id) => {
            return !!sessionStorage.getItem(base.dataTables.private.getIdsItemName(id));
        },
        /**
         * ID配列取得
         * @param id
         * @returns {any}
         */
        getIds: (id) => {
            return JSON.parse(sessionStorage.getItem(base.dataTables.private.getIdsItemName(id)));
        },
        /**
         * ID配列セット
         * @param id
         * @param ids
         * @returns {boolean}
         */
        setIds: (id, ids) => {
            let nowIds = JSON.parse(sessionStorage.getItem(base.dataTables.private.getIdsItemName(id))), ac = true;
            $.each(ids, (k, f) => {
                nowIds[k] = f ? 1 : 0;
            });
            $.each(nowIds, (k, f) => {
                if (!f)
                    ac = false;
            });
            sessionStorage.setItem(base.dataTables.private.getIdsItemName(id), JSON.stringify(nowIds));
            return ac;
        },
        /**
         * 全ID配列セット
         * @param id
         * @param flag
         */
        setAllIds: (id, flag) => {
            let nowIds = JSON.parse(sessionStorage.getItem(base.dataTables.private.getIdsItemName(id)));
            $.each(nowIds, (id, f) => {
                nowIds[id] = flag ? 1 : 0;
            });
            sessionStorage.setItem(base.dataTables.private.getIdsItemName(id), JSON.stringify(nowIds));
        },
        /**
         * ID配列削除
         * @param id
         */
        removeIds: (id) => {
            sessionStorage.removeItem(base.dataTables.private.getIdsItemName(id));
        },
        /**
         * Datatablesのオプション columnsを生成
         * @param id
         * @param isAjax
         * @returns {Array}
         */
        getColumnsNames: (id, isAjax = true) => {
            let cols = [];
            $('#' + id).find('th').each(function (i, o) {//thをループ
                let data = $(this).data(), setting = {};
                if (isAjax && data.name == void 0)
                    data.name = '_empty';
                if (data.name) {
                    setting.data = data.name;
                    //data-nameが_で始まる場合はソート・検索無効
                    if (data.name.match(/^_/)) {
                        setting.orderable = false;
                        setting.searchable = false;
                    }
                    //data-nameがdateで始まるか終わる場合、検索無効
                    if (data.name.match(/^date|date$/i)) {
                        setting.searchable = false;
                    }
                    //data-nameが_で始まる場合、センタリング
                    if (data.name.match(/^_/)) {
                        setting.className = 'dt-body-center';
                    }
                }
                if (data.search != void 0) {    //data-searchがある場合
                    setting.searchable = data.search;
                }
                if (data.sort != void 0) {      //data-sortがある場合
                    setting.orderable = data.sort;
                }
                if (data.detail != void 0) {
                    setting.className = 'details-control';
                    setting.data = null;
                    setting.name = null;
                    setting.orderable = false;
                    setting.defaultContent = '<a class="details-control-button"><span class="glyphicon glyphicon-plus-sign"></span></a>';
                    details[id] = data.detail;
                }
                //フォーマット設定
                if (data.format) {
                    let formatFunc, ft = data.format.split(':'), fset = null;
                    if (ft.length == 2) {   //オプションがある場合、分離
                        data.format = ft[0];
                        fset = ft[1];
                    }
                    //フォーマット変更用関数
                    switch (data.format) {
                        case 'datetime':    //日付日時
                            formatFunc = (val, type, row, meta) => {
                                if (val == null)
                                    return '';
                                let mt = val.match(/\d{4}(.)\d{2}(.)\d{2}(\s)\d{2}(.)\d{2}(.)\d{2}/), dt = mt[0].replace(mt[3], '<br>');
                                if (fset == 'ja')
                                    dt = dt.replace(mt[1], '年').replace(mt[2], '月').replace(mt[3], '日<br>').replace(mt[4], '時').replace(mt[5], '分') + '秒';
                                return dt;
                            };
                            break;
                        case 'date':        //日付
                            formatFunc = (val, type, row, meta) => {
                                if (val == null)
                                    return '';
                                let mt = val.match(/\d{4}(.)\d{2}(.)\d{2}/), dt = mt[0];
                                if (fset == 'ja')
                                    dt = dt.replace(mt[1], '年').replace(mt[2], '月') + '日';
                                return dt;
                            };
                            break;
                        case 'date_ym':     //年月
                            formatFunc = (val, type, row, meta) => {
                                if (val == null)
                                    return '';
                                let mt = val.match(/\d{4}(.)\d{2}/), dt = mt[0];
                                if (fset == 'ja')
                                    h
                                dt = dt.replace(mt[1], '年') + '月';
                                return dt;
                            };
                            break;
                        case 'money':       //金額
                            formatFunc = (val, type, row, meta) => {
                                if (val == null)
                                    return '';
                                return base.util.numberToMoney(val, fset, 0);
                            };
                            setting.className = 'dt-body-right';
                            break;
                        case 'money_split': //金額（複数行）
                            formatFunc = (val, type, row, meta) => {
                                if (val == null)
                                    return '';
                                return val.replace(/(\d+(\.\d+)?)/g, function (m, c, o, i) {
                                        return base.util.numberToMoney(m, fset, 0);
                                    }
                                );
                            };
                            setting.className = 'dt-body-right';
                            break;
                        case 'text':        //テキスト
                            formatFunc = (val, type, row, meta) => {
                                if (val == null)
                                    return '';
                                return val.replace(/\n/g, '<br/>');
                            };
                            break;
                        case 'default':
                            formatFunc = (val, type, row, meta) => {
                                return val || fset;
                            };
                            break;
                        default:
                            formatFunc = () => {
                            };
                            break;
                    }
                    setting.render = (val, type, row, meta) => {
                        return formatFunc(val, type, row, meta);
                    };
                }
                cols.push(setting);
            });
            return cols;
        },
    };

    /**
     * DataTables生成
     * @param id
     * @param extend
     */
    base.dataTables.create = (id, extend = {}) => {
        let tbl = $('#' + id),
            form = $('#' + tbl.data('form')),
            conf = {
                "columns": base.dataTables.private.getColumnsNames(id, false),
            };
        tables[id] = tbl.DataTable($.extend(true, conf, extend));
        tables[id].on('draw', function (e, s) {
            base.dataTables.private.onDraw(e, s);
        });
        tables[id].draw();
    };

    /**
     * Ajax対応DataTablesを生成
     * @param id
     * @param url
     * @param extend
     */
    base.dataTables.createAjax = (id, url, extend = {}, exParams = {}) => {
        let tbl = $('#' + id),
            form = base.dataTables.getFormFromTable(id),
            conf = {
                "searching": false,
                "processing": true,
                "serverSide": true,
                "scrollX": true,
                "ajax": {
                    url: url,
                    data: function (d) {
                        d.params = form.serialize();
                        d.extend = base.util.getMappedParams(exParams);
                        d.page = (d.start / d.length) + 1;
                    },
                    dataSrc: function (d) {
                        base.dataTables.private.dataSrc(d, id);
                        base.dataTables.callback.ajax.done.fire(tbl, d);
                        return d.data;
                    },
                    error: function (xh, ts, et) {
                        base.util.ajaxFail({xh: xh, ts: ts, et: et});
                    },
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader("Authorization", "Bearer " + Laravel.apiToken);
                    },
                },
                "stateSave": true,
                stateSaveCallback: function (settings, data) {
                    data.params = form.serialize();
                    sessionStorage.setItem('DataTables_' + settings.sInstance + location.pathname, JSON.stringify(data))
                },
                stateLoadCallback: function (settings) {
                    let data = JSON.parse(sessionStorage.getItem('DataTables_' + settings.sInstance + location.pathname))
                    if (base.util.isRestoreUrl() && data != null) {
                        let params = base.util.parseParams(data.params);
                        $.each(params, (k, v) => {
                            if (typeof v == 'object') {
                                $('[name="' + k + '"]').val(v).change();
                            } else {
                                base.util.setValue(v, k, $(document));
                            }
                        });
                        return data;
                    } else {
                        return {};
                    }
                },
                "columns": base.dataTables.private.getColumnsNames(id),
            };
        tables[id] = tbl.DataTable($.extend(true, conf, extend));
        tables[id].on('draw', function (e, s) {
            base.dataTables.private.onDraw(e, s);
            base.dataTables.callback.onDraw.fire(e, s);
        });
        if (exParams.key != void 0)
            tables[id].key = exParams.key;
    };

    /**
     * datatables取得
     * @param id
     * @returns {*}
     */
    base.dataTables.getTable = (id) => {
        return tables[id];
    };

    /**
     * datatabels全て取得
     * @returns {{}}
     */
    base.dataTables.getTables = () => {
        return tables;
    };

    /**
     * FormIdからdatatablesを取得（複数の可能性有）
     * @param formId
     * @returns {{}}
     */
    base.dataTables.getTableFromForm = (formId) => {
        let tbls = {}, ids = formMapping[formId];
        $.each(ids, (id, v) => {
            tbls[id] = base.dataTables.getTable(id);
        });
        return tbls;
    };

    /**
     * datatabelsのIDからFormを取得（jQuery複数オブジェクト）
     * @param id
     * @returns {*|jQuery|HTMLElement}
     */
    base.dataTables.getFormFromTable = (id) => {
        let fids = [];
        $.each(formMapping, (fid, ids) => {
            if (ids[id] === 1)
                fids.push('#' + fid);
        });
        return $(fids.join(','));
    };

    /**
     * チェックボックスでチェックされているものの値を取得
     * @param id
     */
    base.dataTables.getCheckedIds = (id, emptyCheck = false) => {
        let dtbl = base.dataTables.getTable(id),
            ids = {};
        if (base.dataTables.private.existsIds(id)) {
            $.each(base.dataTables.private.getIds(id), (key, v) => {
                if (v == 0)
                    return true;
                ids[key] = 1;
            });
        } else {
            const lists = dtbl.$('input:checked');
            $.each(lists, function () {
                ids[this.value] = 1;
            });
        }
        if (emptyCheck) {
            let allFalse = true;
            $.each(ids, (id, v) => {
                if (v == 1) {
                    allFalse = false;
                    return false;
                }
            });
            if (allFalse)
                return false;
        }
        return ids;
    };

    /**
     * 初期化
     */
    base.dataTables.init = () => {
        let datatables_language = {
            "emptyTable": "データが登録されていません。",
            "info": " 全_TOTAL_ 件中 _START_ ～ _END_ 件  表示",
            "infoEmpty": " 0 件中 0 から 0 まで表示",
            "infoFiltered": "（全 _MAX_ 件より抽出）",
            "infoPostFix": "",
            "thousands": ",",
            "lengthMenu": "_MENU_ 件表示",
            "loadingRecords": "ロード中",
//             "processing": "<div class='datatable-loading'><img src='/assets/img/loader.gif'> 処理中...</div>",
//             processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> ',
            "search": "検索",
            "zeroRecords": "データはありません。",
            "paginate": {
                "first": "先頭",
                "previous": "<<",
                "next": ">>",
                "last": "末尾"
            }
        };
        base.dataTables.config = {};
        base.dataTables.config.default = {
            "order": [0, 'desc'],
            "language": datatables_language,
            // dom: "<'container-fluid datatable-item'<'col-sm-6 inline-flex'il><'col-sm-6'f>>" +
            //     "<'x-scroll-auto'<'w-100p'tr>>" +
            //     "<'w-100p text-center'p>"
        };
        base.dataTables.config.column = {
            "language": datatables_language,
            ordering: false,
            // dom: "<'container-fluid datatable-item'<'col-sm-6 inline-flex'il><'col-sm-6'f>>" +
            //     "<'x-scroll-auto'<'w-100p'tr>>" +
            //     "<'w-100p text-center'p>"
        };
        $.extend(true, $.fn.dataTable.defaults, base.dataTables.config.default);
        //詳細
        $(document).on('click', '.details-control>a', function (e) {
            let id = $(this).closest('table').attr('id'), tbl = base.dataTables.getTable(id), tr = $(this).closest('tr'),
                row = tbl.row(tr), setting = details[id], data = $(this).data();
            if (row.child.isShown()) {
                row.child.hide();
                tr.removeClass('shown');
                $(this).children().addClass('glyphicon-plus-sign').removeClass('glyphicon-minus-sign');
            } else {
                let get_detail_flag = false;
                if (setting.cache) {
                    if (detailsCache[id][data.key] == void 0) {
                        get_detail_flag = true;
                        detailsCache[id][data.key] = true;
                    } else {
                        row.child.show();
                    }
                } else {
                    get_detail_flag = true;
                }
                if (get_detail_flag) {
                    setting.key = data.key;
                    row.child(base.dataTables.getDetail(row, setting, function () {
                        base.bind.container.omit_message();
                    })).show();
                }
                tr.addClass('shown');
                $(this).children().removeClass('glyphicon-plus-sign').addClass('glyphicon-minus-sign');
            }
        });
    };

    /**
     *
     * @param row
     * @param settings
     * @returns {jQuery.fn.init|*|jQuery|HTMLElement}
     */
    base.dataTables.getDetail = (row, settings, callback = null) => {
        let div = $('<div/>'), data = row.data(), params = {};
        let getDetailData = (setting, i) => {
            setting.key = `${settings.key}_${i}`;
            let tmp_div = $('<div/>');
            tmp_div.html('Loading...');
            div.append(tmp_div);
            if (setting.columns != void 0) {
                $.each(setting.columns, (k, v) => {
                    params[v] = data[v];
                });
            }
            $.extend(params, setting.params);
            base.util.ajax('GET', setting.ajax.url, params).done((ret) => {
                tmp_div.html('');
                tmp_div.append(createDetail(ret, setting));
                base.dataTables.bind(tmp_div);
                if (typeof callback == 'function')
                    callback(ret, setting);
            }).fail((ret) => {
                base.util.ajaxFail(ret);
            });
        };
        for (let i = 0; i < settings.data.length; i++) {
            getDetailData(settings.data[i], i);
        }
        return div;
    };

    /**
     *
     * @param data
     * @param setting
     * @returns {jQuery.fn.init|*|jQuery|HTMLElement}
     */
    let createDetail = (data, setting) => {
        let tbl = $('<table/>', {
            'class': 'table data-table details-table', id: `dtable_${setting.key}`, 'data-tables': JSON.stringify({
                setting: {
                    //FIXME:必要な詳細もあるかも？
                    paging: false,
                    searching: false,
                    ordering: false,
                    info: false,
                }
            })
        }), tr = $('<tr/>'), tbody = $('<tbody/>');
        $.each(setting.header, (key, val) => {
            tr.append($('<th/>').html(val));
        });
        tbl.append($('<thead/>').append(tr));
        $.each(data, (i, row) => {
            let r_tr = $('<tr/>');
            $.each(setting.header, (key) => {
                r_tr.append($('<td/>').html(row[key]));
            });
            tbody.append(r_tr);
        });
        tbl.append(tbody);
        return tbl;
    };

    /**
     * 全バインド
     * @param p
     */
    base.dataTables.bind = (p = $(document)) => {
        p.find('[data-tables]').each(function () {
            let data = $(this).data('tables');
            if (data.onload !== void 0 && !data.onload)
                return true;
            base.dataTables.bindOne($(this));
        });
    };

    /**
     * テーブルオブジェクトにDatatablesをバインド
     * @param tbl
     */
    base.dataTables.bindOne = (tbl, rebind = false) => {
        let data = tbl.data('tables'), id = tbl.prop('id');
        if (base.dataTables.getTable(id) && !rebind) {
            //再描画
            return false;//多重バインド
        }
        tbl.css('width', '100%');
        if (data.form) {
            $.extend(true, formMapping, base.form.mapping(data.form, id));
        }
        base.form.bindSearch(data.form, function (form) {
            let tbls = base.dataTables.getTableFromForm(form.prop('id'));
            $.each(tbls, (id, tbl) => {
                tbl.draw();
            });
        });
        detailsCache[id] = {};
        if (data.ajax != void 0) {
            base.dataTables.private.removeIds(id);
            base.dataTables.createAjax(id, data.ajax.url, data.setting, data.extend);
        } else {
            base.dataTables.create(id, data.setting);
        }
    };

    /**
     * ヘッダーの位置調整
     * @param {type} id
     * @returns {undefined}
     */
    base.dataTables.adjustHeader = (id) => {
        base.dataTables.getTable(id).columns.adjust();
    };

})(window.base);

//初期化
base.dataTables.init();