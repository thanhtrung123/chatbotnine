/**
 * Replace special characters
 * @param {string} s
 */
function htmlEnc(s) {
    return s.replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/'/g, '&#39;')
    .replace(/"/g, '&#34;');
}


// Setting modal
var setting_modal = {
    backdrop: 'static',
    keyboard: false
}

/**
 * Show/hide load and disable/enable area editor
 * @param {boolean} load
 */
function setLoad(load) {
    if (load) {
        $('.lds-roller').css('display', 'block');
        $('.drawflow-area').css('opacity', '0.5');
        $('#drawflow-editor').css('pointer-events', 'none');
    } else {
        $('.lds-roller').css('display', 'none');
        $('.drawflow-area').css('opacity', '1');
        $('#drawflow-editor').css('pointer-events', 'all');
    }
}

/**
 * Search data QA learing
 */
function searchQa() {
    if (!$('#qaModal').hasClass('in')) {
        setLoad(true)
    }
    var category_id = Number($('.qaCategory ').val()),
        apiId = $('.apiId').val(),
        keyword = $('.qaKeyword').val(),
        arr_id_qa = new Array();
    category_id = (category_id == 0) ? null : category_id;
    $.ajax({
        url: urlQaSearch,
        method: 'GET',
        data: {
            apiId: apiId,
            keyword: keyword,
            category_id: category_id,
            _token: csrf_token
        }
    }).done(function(response) {
        $('#qaModal').modal(setting_modal);
        $('.drawflow').children().map(function(i, val) {
            if (val.children && val.children.length > 0 && val.children[0].tagName == 'DIV') {
                if (val.children[0].getAttribute('id').slice(5, 6) == 'q') {
                    arr_id_qa.push(Number(val.children[0].getAttribute('id').slice(6)));
                }
            }
        });
        var html = '<tr><td colspan="4" class="text-center">データが登録されていません。</td></tr>'
        if (response.datatable_qa.length > 0) {
            html = response.datatable_qa.map(function(element) {
                let colorBtn = (arr_id_qa.indexOf(element["id"]) != -1) ? 'btn-primary qa-Copy-Node' : 'btn-primary qa-Add-Node';
                var html = '<tr>';
                html += '<td class="id_qa_add">';
                html += element["api_id"];
                html += '</td>';
                html += '<td class="api_id_qa_td text-center"></td>';
                html += '<td>';
                html += htmlEnc(element["question"]);
                html += '</td>';
                html += '<td>';
                html += htmlEnc(element["answer"]);
                html += '</td>';
                html += '<td class="text-center">';
                html += '<button id="q' + element["id"] + '" type="button" class="btn ' + colorBtn + '" data-text-qa="' + htmlEnc(element["question"]) + '" api_id="' + element["api_id"] + '"> 追加 </button>';
                html += '</td>';
                html += '</tr>';
                return html;
            });
        }
        $('.table-scroll tbody').html(html);
        csrf_token = response.token;
    }).fail(function () {
        $('#scenarioEdit').modal(setting_modal);
        showMsg('QA登録', '接続できません。')
        $('.delete-scenario-all').css('display', 'none');
    }).always(function () {
        setLoad(false)
    })
}

function showMsg(modal_header, msg) {
    $('.modal-title').html(modal_header);
    $('.message_body').html(msg);
}

function addNodeElement(name, e) {
    var id = null;
    var api_id = null;
    var nameSubmit = $("#nameScenario").val();
    if (e !== undefined) {
        nameSubmit = e.getAttribute('data-text-qa');
        id = e.getAttribute('id');
        api_id = e.getAttribute('api_id');
    }
    
    var pos_x = ($('.parent-drawflow').scrollLeft() + 100 - editor.canvas_x) / editor.zoom;
    var pos_y = ($('.parent-drawflow').scrollTop() + 100 - editor.canvas_y) / editor.zoom;
    
    var title = nameSubmit;
    title = convertLongString(title);
    //validate
    var name_scenario = $('form#formScenario').find('input[name="name"]').val().trim(),
    order = $('form#formScenario').find('input[name="order"]').val(),
    keyword_select = $('#clone_area').find('select[name^=multi_data]'),
    type = 'create';

    if ((name == 'scenario') && !validateParams(name_scenario, order, keyword_select, type)) {
        return false;
    }
    if (name == 'scenario' && isNameExist(name_scenario, type)) {
        $('#confirmModal').modal('show');
        $('#confirmModalLabel').text('シナリオ 登録');
        $("#ok").unbind('click').bind('click', function() {
            addNodeProcess(name, nameSubmit, title, pos_x, pos_y, id, api_id, e);
            $('#confirmModal').modal('hide');
        })
    } else {
        addNodeProcess(name, nameSubmit, title, pos_x, pos_y, id, api_id, e);
    }
}

/**
 * Process add a node
 * @param name
 * @param nameSubmit
 * @param title
 * @param pos_x
 * @param pos_y
 * @param id
 * @param api_id
 * @param e
 */
function addNodeProcess(name, nameSubmit, title, pos_x, pos_y, id, api_id, e) {
    switch (name) {
        case 'scenario':
            var input = $("<input>").attr("type", "hidden").attr("name", "category_id").val($('.scenarioCategory').val());
            $('#formScenario').append(input);
            //Encode form elements for submission
            var form_data = $('form#formScenario').serialize();
            $.ajax({
                url: urlSenario,
                type: 'POST',
                data: {
                    data: form_data,
                    _token: csrf_token
                }
            }).done(function(response) {
                temp++;
                let id = 's' + temp + '-temp';
                var scenario = '<div><div class="title-box1 editor-scenario" title="' + nameSubmit + '">' + title + '</div></div>';
                response.id = id;
                editor.addNode(nameSubmit, 1, 1, pos_x, pos_y, 'editor-scenario', {}, scenario, !1, id, JSON.stringify(response));
                $(".keyword-add-scenario")[0].innerHTML = '';
                $("#nameScenario").val('');
                $("#order").val('');
                editor.showDataToLeft();
                $('#node-' + id)[0].setAttribute('onclick', 'editor.focusData(this, false, \'' + id + '\')');
                editor.focusData(e, false, id);
                $('#scenarioAdd').modal('hide');
            }).fail(function () {
                $('#scenarioEdit').modal(setting_modal);
                showMsg('シナリオ追加', 'シナリオ追加できません。');
                $('.delete-scenario-all').css('display', 'none');
            }).always(function () {
                setLoad(false)
            });
            break;
        case 'qa':
            $.ajax({
                url: urlQaSearch,
                method: 'GET',
                data: {
                    apiId: api_id,
                    type: 'add',
                    _token: csrf_token
                }
            }).done(function(response) {
                var qa = '<div><div class="title-box editor-qa-id" title="' + api_id + '">QA ID: ' + api_id + '</div><div class="title-box editor-qa" title="' + nameSubmit + '">' + title + '</div></div>';
                editor.addNode(nameSubmit, 1, 0, pos_x, pos_y, 'editor-qa', {}, qa, !1, id, JSON.stringify(response.datatable_qa[0]));
                $('.qaKeyword').val('');
                $('.qaId').val('');
                editor.showDataToLeft();
                $('#qaModal').modal('hide');
                editor.focusData(e, false, id);
            }).fail(function () {
                $('#scenarioEdit').modal(setting_modal);
                showMsg('QA登録', 'QA追加できません。');
                $('.delete-scenario-all').css('display', 'none');
            })
            break;
        default:
    }
}

/**
 * Check exist name scenario
 * @param name_scenario
 * @param type
 * @param id_scenario
 */
function isNameExist(name_scenario, type, id_scenario) {
    var obj_scenario = editor.drawflow.drawflow.Home.data;
    var is_name_exist = false;
    Object.keys(obj_scenario).map(key => {
        if (obj_scenario[key].class == "editor-scenario" && obj_scenario[key].name.trim() == name_scenario) {
            if (type == 'edit' && obj_scenario[key].id.trim() != id_scenario) {
                is_name_exist = true;
            }
            if (type == 'create') {
                is_name_exist = true;
            }
        }
    });
    return is_name_exist;
}

function loadScenario(params, id) {
    $('#drawflow-editor').css('pointer-events', 'none');
    editor.clear();
    editor.clearHistory();
    setLoad(true)
    $('.scenarioCategory').val(params).trigger('change');
    $('.qaCategory').val(params).trigger('change');
    $('.fillterCategory').attr('disabled', true);
    ShowDataQa([]);
    ShowDataScenario([]);
    $.ajax({
        url: urlScenarioFillter,
        timeout: 0,
        method: 'GET',
        data: {
            params: params,
            _token: csrf_token
        }
    }).done(function name(response) {
        $('.fillterCategory').removeAttr('disabled');
        var cloneData = JSON.parse(JSON.stringify(response));
        actionHistory = [].concat(cloneData);
        dataQa = response.answer;
        dataScenario = response.scenario;
        dataQaCP = response.answerCopy;

        //Set data on show drawjs
        result = setDataShowScenario(response)

        //Set location scenario
        setLocation(result, response.position);

        $('.drawflow-area').css('opacity', '1');
        if (id) {
            editor.focusData(this, false, id);
        }
        var overflow_w = 0,
            overflow_h = 0,
            container = $('#drawflow');
        container.find('.drawflow-node').each(function() {
            if ($(this).position().top + $(this).outerHeight() + 50 > container.children('.drawflow').outerHeight()) {
                overflow_h = container.children('.drawflow').outerHeight()
            }
            if ($(this).position().left + $(this).outerWidth() + 50 > container.children('.drawflow').outerWidth()) {
                overflow_w = container.children('.drawflow').outerWidth()
            }
        });
        if (overflow_h || overflow_w) {
            container.children('.drawflow').css({
                width: container.children('.drawflow').outerWidth() + overflow_w,
                height: container.children('.drawflow').outerHeight() + overflow_h
            });
        }
        editor.zoom_reset();

        //On show
        editor.load();
        editor.addHistory();
    }).fail(function() {
        alert( "接続できません。" );
    }).always(function() {
        $('.status-scenario').prop('disabled', false);
        $('#drawflow-editor').css('pointer-events', 'all');
        $('.lds-roller').css('display', 'none');
    });
}

function ShowDataQa(data, dataCP) {
    $('.content-qa').innerHTML = '';
    let html = new Array(),
        htmlCP = new Array();
    html = data.map(function(val, index) {
        if (val.scenario_id) {
            return '<p class="q' + val.answer_id + '" onclick="editor.focusData(this)">' + val.question + '</p>';
        }
    });
    if (dataCP) {
        htmlCP = Object.keys(dataCP).map(function(val, index) {
            if (dataCP[val].scenario_id) {
                return '<p class="' + val + '" onclick="editor.focusData(this)">' + dataCP[val].question + '</p>';
            }
        });
    }
    $('.content-qa').html(html.concat(htmlCP).join(''));
}

function ShowDataScenario(dataScenario) {
    $('.content-scenario').innerHTML = '';
    let html = dataScenario.map(function(val, index) {
        let id = editor.drawflow.drawflow.Home.data[val].data.id;
        let name = editor.drawflow.drawflow.Home.data[val].data.name;
        return '<p class="s' + id + '" onclick="editor.focusData(this)">' + name + '</p>';
    });
    $('.content-scenario').html(html);
}

function fillterDataScenario(params) {
    let datas = Object.keys(editor.drawflow.drawflow.Home.data).filter(function(val, i) {
        if (val.toLowerCase().indexOf('s') >= 0 && editor.drawflow.drawflow.Home.data[val].name.toLowerCase().indexOf(params.toLowerCase()) >= 0)  {
            return editor.drawflow.drawflow.Home.data[val];
        }
    });
    ShowDataScenario(datas)
}

function fillterDataQa(params) {
    var datas = [];
    dataQa.map(function(val, index) {
        datas.push({
            "api_id": val.api_id,
            "node_id":val.node_id,
            "question":  val.question,
            "scenario_id":  val.scenario_id,
        });
    });
    Object.keys(dataQaCP).filter(function(val, i) {
        datas.push({
            "api_id": dataQaCP[val].api_id,
            "node_id": dataQaCP[val].node_id,
            "question":  dataQaCP[val].question,
            "scenario_id":  dataQaCP[val].scenario_id,
        });
    });
    let datasQA= dataQa.filter(function(val, i) {
        return val["question"].toLowerCase().indexOf(params.toLowerCase()) >= 0;
    });
    let datasCP = Object.keys(dataQaCP).reduce(function(r, e) {
        if (dataQaCP[e].question.toLowerCase().indexOf(params.toLowerCase()) >= 0) r[e] = dataQaCP[e]
        return r;
    }, {})
    ShowDataQa(datasQA, datasCP);
}

function convertLongString(str) {
    if (str.length >= 11) {
        return str = str.substring(0, 11) + '...';
    }
    return str
}

function scenarioEdit() {
    setLoad(true)
    $('.edit-scenario-button').css('display', 'inline');
    let target = $('.drawflow').find('.editor-scenario.selected').attr('id');
    if (target && target.indexOf('node-s') != -1) {
        let idScenario = target;
        let data = $('#' + idScenario)[0].getAttribute('data');
        $.ajax({
            url: editScenario,
            method: 'GET',
            data: {
                data: data,
                _token: csrf_token
            }
        }).done(function(response) {
            $('#scenarioEdit').modal(setting_modal);
            $('#scenarioEdit').html(response);
            $('.fa-circle-o-notch').remove();
        }).fail(function() {
            $('#scenarioEdit').modal(setting_modal);
            showMsg('シナリオ編集', '接続できません。');
            $('.delete-scenario-all').css('display', 'none');
        }).always(function () {
            setLoad(false)
        });
    } else {
        $('#scenarioEdit').modal(setting_modal);
        showMsg('シナリオ編集', 'シナリオを選択してください。');
        $('.delete-scenario-all').css('display', 'none');
        $('.edit-scenario-button').css('display', 'none');
        setLoad(false)
    }
}


/**
 * Load modal detail scenario
 */
function scenarioDetail() {
    setLoad(true)
    $('.edit-scenario-button').css('display', 'inline');
    let target = $('.drawflow').find('.editor-scenario.selected').attr('id');
    if (target && target.indexOf('node-s') != -1) {
        let idScenario = target;
        let data = $('#' + idScenario)[0].getAttribute('data');
        $.ajax({
            url: detailScenario,
            method: 'GET',
            data: {
                data: data,
                _token: csrf_token
            }
        }).done(function(response) {
            $('#detailSenarioQA').modal(setting_modal);
            $('#detailSenarioQA').html(response);
            $('.fa-circle-o-notch').remove();
        }).fail(function() {
            $('#detailSenarioQA').modal(setting_modal);
            showMsg('シナリオ 情報', '問題が発生しました。シナリオ情報取得できません。');
            $('.delete-scenario-all').css('display', 'none');
        }).always(function () {
            setLoad(false)
        });
    } else {
        $('#detailSenarioQA').modal(setting_modal);
        showMsg('シナリオ 情報', 'シナリオを選択してください。');
        $('.delete-scenario-all').css('display', 'none');
        $('.edit-scenario-button').css('display', 'none');
        setLoad(false)
    }
}

/**
 * Load modal qa learning
 */
function qaDetail() {
    setLoad(true)
    $('.edit-scenario-button').css('display', 'inline');
    let target = $('.drawflow').find('.editor-qa.selected').attr('id');
    if (target && target.indexOf('node-q') != -1) {
        let idQA = target;
        let data = $('#' + idQA)[0].getAttribute('data');
        $.ajax({
            url: detailQA,
            method: 'GET',
            data: {
                data: data,
                _token: csrf_token
            }
        }).done(function(response) {
            $('#detailSenarioQA').modal(setting_modal);
            $('#detailSenarioQA').html(response);
            $('.fa-circle-o-notch').remove();
            setLoad(false)
        }).fail(function() {
            $('#detailSenarioQA').modal(setting_modal);
            showMsg('ＱＡデータ', '問題が発生しました。ＱＡ情報取得できません。');
            $('.delete-scenario-all').css('display', 'none');
            setLoad(false)
        }).always(function () {
        });
    } else {
        $('#detailSenarioQA').modal(setting_modal);
        showMsg('ＱＡデータ', 'ＱＡを選択してください。');
        $('.delete-scenario-all').css('display', 'none');
        $('.edit-scenario-button').css('display', 'none');
        setLoad(false)
    }
}

// Set location scenario
// @params result array(obj)
// @params position array(obj)
function setLocation(result, position) {
    Object.keys(result).map(function(val, index) {
        result[val].pos_x = position[val].x;
        result[val].pos_y = position[val].y;
    });
}

function EditProcess(order) {
    var input = $("<input>").attr("type", "hidden").attr("name", "category_id").val($('.scenarioCategory').val());
    $('#entry_form_edit').append(input);
    if (order) {
        order = parseInt(order);
        $('form#entry_form_edit').find('input[name="order"]').val(order);
    }
    var form_data = $('form#entry_form_edit').serialize();
    let id = $('.drawflow').find('.editor-scenario.selected').attr('id');
    $.ajax({
        url: urlSenario,
        type: 'POST',
        data: {
            data: form_data,
            _token: csrf_token
        }
    }).done(function(response) {
        $('#scenarioEdit').modal('hide');
        var title = response.name;
        title = convertLongString(title);
        let data = editor.drawflow.drawflow.Home.data;
        data[id.replace('node-', '')].data = response;
        data[id.replace('node-', '')].name = response.name;
        data[id.replace('node-', '')].html = '<div><div class="title-box1 editor-scenario" title="' + response.name + '">' + title + '</div></div>';
        editor.clear();
        editor.drawflow.drawflow.Home.data = data;
        editor.addHistory();
        editor.load();
        var elem = $('#' + 'node-s' + response.id.replace('s', ''));
        editor.focusData(elem, false, 's' + response.id.replace('s', ''));
    });
}

function EditNodeElement() {
    var name_scenario = $('form#entry_form_edit').find('input[name="name"]').val().trim(),
    order = $('form#entry_form_edit').find('input[name="order"]').val(),
    keyword_input = $('#clone_area_edit').find('input[name^=multi_data]'),
    keyword_select = $('#clone_area_edit').find('select[name^=multi_data] :selected'),
    type = 'edit',
    scenario_selected = $('.drawflow').find('.selected').attr('id');
    //validate
    if (!validateParams(name_scenario, order, keyword_input, keyword_select, type)) {
        return false;
    }
    var id_scenario = scenario_selected.toString().replace('node-', '');
    if (isNameExist(name_scenario, type, id_scenario)) {
        $('#confirmModal').modal('show');
        $('#confirmModalLabel').text('シナリオ 編集');
        $("#ok").unbind('click').bind('click', function() {
            EditProcess(order);
            $('#confirmModal').modal('hide');
        })
    } else {
        EditProcess(order);
    }
}

function setDataShowScenario(response, result) {
    var result = {};
    editor.drawflow = {
        "drawflow": {
            "Home": {
                "data": result
            }
        }
    }
    if (dataScenario.length > 0) {
        response.scenario.map(function(val, index) {
            let multi_data = {};
            if (val.keyword_groupno && val.keyword_id && val.keyword) {
                multi_data = setMultipleKeyword(val);
            }
            if (Object.keys(multi_data).length > 0) {
                val['multi_data'] = multi_data;
            }
            var title = val.name;
            title = convertLongString(title);
            var connections_out = [];
            var connections_int = [];
            if (val.parent_scenario_id != null) {
                var arrConnect = val.parent_scenario_id.split(",");
                arrConnect.map(function(item, i) {
                    connections_int.push({
                        "node": "s" + item.trim() + "",
                        "input": "output_1"
                    });
                });
            }
            if (val.learning_id != null) {
                connections_out.push({
                    "node": "q" + val.learning_id + "",
                    "input": "output_1"
                })
            }

            result["s" + val.scenario_id] = {
                "id": "s" + val.scenario_id,
                "name": val.name,
                "class": "editor-scenario",
                "data": val,
                "typenode": false,
                "html": "\n<div>\n<div class=\"title-box1 editor-scenario\" title=\"" + val.name + "\">" + title + "</div>\n",
                "inputs": {
                    "input_1": {
                        "connections": connections_int
                    }
                },
                "outputs": {
                    "output_1": {
                        "connections": connections_out
                    },
                },
                "pos_x": 30,
                "pos_y": 70 * index,
                "count_childr": []
            }
        });
    }
    if (dataQa.length > 0) {
        response.answer.map(function(val, index) {
            if (val.scenario_id) {
                var title = val.question;
                title = convertLongString(title);
                var connections_int = [];
                if (val.scenario_id != null) {
                    var arrConnect = val.scenario_id.toString().split(",");
                    const unique_arr_connect = arrConnect.filter((x, i, a) => a.indexOf(x) == i);
                    unique_arr_connect.map(function(item, i) {
                        connections_int.push({
                            "node": "s" + item.trim() + "",
                            "input": "output_1"
                        });
                    });
                }
                if (val.key_phrase) {
                    const array_key_phrase = val.key_phrase.toString().split(",");
                    const unique_array_key_phrase = array_key_phrase.filter((x, i, a) => a.indexOf(x) == i);
                    val.key_phrase = unique_array_key_phrase.join(',');
                }
                if (val.answer_id) {
                    result["q" + val.answer_id] = {
                        "id": "q" + val.answer_id,
                        "name": val.question,
                        "class": "editor-qa",
                        "data": val,
                        "typenode": false,
                        "html": "\n<div>\n<div class=\"title-box editor-qa-id\" title=\"" + val.api_id + "\">QA ID: " + val.api_id + "</div>\n\n<div class=\"title-box editor-qa\" title=\"" + val.question + "\">" + title + "</div>\n</div>\n",
                        "inputs": {
                            "input_1": {
                                "connections": connections_int
                            }
                        },
                        "outputs": {

                        },
                        "pos_x": 600,
                        "pos_y": 100 * index,
                        "count_childr": []
                    }
                }
            }
        });
    }
    if (Object.keys(response.answerCopy).length > 0) {
        Object.keys(response.answerCopy).map(function(val, index) {
            if (response.answerCopy[val].scenario_id) {
                var title = response.answerCopy[val].question;
                title = convertLongString(title);
                var connections_int = [];
                if (response.answerCopy[val].scenario_id != null) {
                    var arrConnect = response.answerCopy[val].scenario_id.toString().split(",");
                    const unique_arr_connect = arrConnect.filter((x, i, a) => a.indexOf(x) == i);
                    unique_arr_connect.map(function(item, i) {
                        connections_int.push({
                            "node": "s" + item.trim() + "",
                            "input": "output_1"
                        });
                    });
                }
                if (response.answerCopy[val].key_phrase) {
                    const array_key_phrase = response.answerCopy[val].key_phrase.toString().split(",");
                    const unique_array_key_phrase = array_key_phrase.filter((x, i, a) => a.indexOf(x) == i);
                    response.answerCopy[val].key_phrase = unique_array_key_phrase.join(',');
                }
                if (response.answerCopy[val].node_id) {
                    result[val] = {
                        "id": val,
                        "name": response.answerCopy[val].question,
                        "class": "editor-qa",
                        "data": response.answerCopy[val],
                        "typenode": false,
                        "html": "\n<div>\n<div class=\"title-box editor-qa-id\" title=\"" + response.answerCopy[val].api_id + "\">QA ID: " + response.answerCopy[val].api_id + "</div>\n\n<div class=\"title-box editor-qa\" title=\"" + response.answerCopy[val].question + "\">" + title + "</div>\n</div>\n",
                        "inputs": {
                            "input_1": {
                                "connections": connections_int
                            }
                        },
                        "outputs": {

                        },
                        "pos_x": 600,
                        "pos_y": 100 * index,
                        "count_childr": []
                    }
                }
            }
        });
    }

    return result;
}

/**
 * Set and sort keyword by order & group_no
 * @param val
 */
function setMultipleKeyword(val) {
    var list_item = {};
    var arr_keyword = val.keyword.split(',');
    var arr_keyword_order = val.keyword_order.split(',');
    val.keyword_groupno.toString().split(',').map(function(item, i) {
        var obj = {
            order: arr_keyword_order[i],
            keyword: arr_keyword[i]
        }
        if (list_item[item] !== undefined) {
            list_item[item].push(obj)
        } else {
            list_item[item] = [obj]
        }
    });
    Object.keys(list_item).map(function(data, i) {
        var arr_data_sort = list_item[data].sort((a, b) => (a.order > b.order) ? 1 : -1);
        var arr_value_keyword = [];
        Object.keys(arr_data_sort).map(function(key) {
            if (arr_value_keyword.indexOf(arr_data_sort[key].keyword) == -1) {
                arr_value_keyword.push(arr_data_sort[key].keyword);
            }
        })
        list_item[data] =arr_value_keyword;
    })
    return list_item;
}

function copyNodeElement(id) {
    if (!id) {
        let target = $('.drawflow').find('.selected').attr('id');
        if (!target) {
            $('#scenarioEdit').modal(setting_modal);
            showMsg('シナリオ複製','対象のノードを選択してください。');
            $('.delete-scenario-all').css('display', 'none');
        } else {
            pastElemCopy(target.substr(5));
        }
    } else {
        pastElemCopy(id, true);
    }
    
}

function deleteNodeElement() {
    $('.edit-scenario-button').css('display', 'none');
    let id = $('.drawflow').find('.selected').attr('id');
    if (!id) {
        $('#scenarioEdit').modal(setting_modal);
        showMsg('シナリオ削除','対象のノードを選択してください。');
        $('.delete-scenario-all').css('display', 'none');
    } else {
        var ids = id;
        var eventScenario = document.getElementById(id);
        ids && editor.removeNodeId(ids),
            editor.connection_selected && editor.removeConnection(),
            null != ids && (eventScenario.classList.remove("selected"), (eventScenario = null)),
            null != editor.connection_selected && (editor.connection_selected.classList.remove("selected"), editor.removeReouteConnectionSelected(), (editor.connection_selected = null));
    }
}

function deleteAllNodeElement(e) {
    e.preventDefault();
    $( ".delete-scenario-all" ).unbind();
    $('#scenarioEdit').modal(setting_modal);
    showMsg('シナリオ削除','すべてのノードを削除します。</br> 削除してよろしいですか。');
    $('.edit-scenario-button').css('display', 'none');
    if ($('#scenarioEdit .modal-footer').find('.edit-scenario-button').length > 0) {
        var btn = $('#scenarioEdit .modal-footer').find('.edit-scenario-button')[0];
        $(btn).attr('class', 'btn btn-danger delete-scenario-all');
        $(btn).text('削除');
    }
    $('#scenarioEdit .delete-scenario-all').css('display', 'inline');
    var arr_id = new Array();
    $('.drawflow').children().map(function(i, val) {
        if (val && val.childNodes.length > 0 && val.childNodes[0].tagName == 'DIV') {
            arr_id.push(val.childNodes[0].getAttribute('id').replace('node-', ''));
        }
    });
    $('.delete-scenario-all').on('click', function() {
        setLoad(true)
        $('.status-scenario').prop('disabled', true);
        $.ajax({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            timeout: 0,
            url: urlScenarioDelete,
            method: 'DELETE',
            data: {
                id: arr_id,
                type: 'deleteAll',
            }
        }).done(function(response) {
            if (response) {
                if (arr_id.length > 200) {
                    window.location.reload();
                }
                loadScenario($('.fillterCategory').val());
            } else {
                $('#scenarioEdit').modal('hide');
                alert('ノードの削除に失敗しました');
                setLoad(false)
                $('.delete-scenario-all').css('display', 'none');
            }
            $('.search-scenario').val('');
            $('.search-QA').val('');
        }).fail(function(jqXHR, textStatus, errorThrown) {
            alert('ノードの削除に失敗しました');
        }).always(function () {
            $('#scenarioEdit').modal('hide');
            $('.delete-scenario-all').css('display', 'none');
            setLoad(false)
            $('.status-scenario').prop('disabled', false);
        });
    });
}

function saveScenarioEditor() {
    setLoad(true)
    $('.status-scenario').prop('disabled', true);
    var arr_id_scenario = '',
        arr_relate_scenario = '',
        arr_qa_str = '',
        scenario_id_selector = $('.drawflow-node').has('.editor-scenario');
    connection_selector = $('.connection');
    arr_qa = new Array(),
        url = storeScenario;

    scenario_id_selector.map(function (i, val) {
        if (val.id.indexOf('node-s') > -1) {
            arr_id_scenario += val.getAttribute('data');
            if (i != (scenario_id_selector.length - 1)) {
                arr_id_scenario += '@@add_item_node@@';
            }
        }
    });

    connection_selector.map(function (i, val) {
        let classList = $(val).attr('class').split(' ');
        if (classList[1] && classList[2]) {
            arr_relate_scenario += classList[2].split("node_out_node-").join("") + ',' + classList[1].split("node_in_node-").join("");
            if (i != (connection_selector.length - 1)) {
                arr_relate_scenario += '@@add_item_relation@@';
            }
        }
    });

    $('.editor-qa').map(function name(i, val) {
        if (val.id.indexOf('node-q') > -1) {
            let position = $(val).position().top;
            arr_qa.push({ 'id': val.id.toString(), 'position': position });
        }
    });

    if (arr_qa.length > 0) {
        arr_qa.sort(function (a, b) { return a.position - b.position });
        $.each(arr_qa, function (index, value) {
            let temp = index + 1;
            arr_qa[index]['order'] = temp;
            arr_qa_str += JSON.stringify(arr_qa[index]);
            if (index != arr_qa.length - 1) {
                arr_qa_str += '@@add_item_qa@@';
            }
        });
    }
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: url,
        method: 'POST',
        timeout: 0,
        data: {
            category_id: ($('.fillterCategory').val()) ? $('.fillterCategory').val() : null,
            id_scenario: arr_id_scenario,
            relate_scenario: arr_relate_scenario,
            arr_qa_position: arr_qa_str,
        }
    }).done(function (response) {
        if (response == true) {
            temp = 0;
            editor.clearHistory();
            $('.search-scenario').val('');
            $('.search-QA').val('');
            if (arr_id_scenario.length < 200) {
                loadScenario($('.fillterCategory').val());
                $('.status-scenario').prop('disabled', false);
            } else {
                window.location.reload();
            }
        } else {
            alert('データの保存に失敗しました。');
            setLoad(false)
            $('.status-scenario').prop('disabled', false);
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        alert('データの保存に失敗しました。');
        setLoad(false)
        $('.status-scenario').prop('disabled', false);
    });
}

var ctrlDown = false,
    ctrlKey = 17,
    cmdKey = 91,
    vKey = 86,
    copy_id = 0,
    undoKey = 90,
    redoKey = 89,
    sKey = 83,
    cKey = 67;
$('#drawflow').keydown(function(e) {
    if (e.keyCode == ctrlKey || e.keyCode == cmdKey) ctrlDown = true;
}).keyup(function(e) {
    if (e.keyCode == ctrlKey || e.keyCode == cmdKey) ctrlDown = false;
});
// '#drawflow' Ctrl + C/V
var id_target = null;
$('#drawflow').keydown(function(e) {
    let target = $('.drawflow').find('.selected').attr('id');
    if (ctrlDown) {
        switch (e.keyCode) {
                // Ctrl + C
            case cKey:
                if (!target) return !1;
                id_target = target.toString().replace('node-', '');
                break;
                // Ctrl + V
            case vKey:
                if (id_target == null) {
                    return false;
                }
                pastElemCopy(id_target);
                break;
                // Ctrl + Z
            case undoKey:
                editor.undo();
                break;
                // Ctrl + Y
            case redoKey:
                editor.redo();
                break;
             // Ctrl + Y
            case sKey:
                e.preventDefault();
                saveScenarioEditor();
                break;
            default:
                break;
        }
    }
});

function pastElemCopy(id, copyNodeQa) {
    let target = editor.drawflow.drawflow.Home.data[id];
    let pos_x = target.pos_x + 10;
    let pos_y = target.pos_y + 10;
    if (copyNodeQa) {
        pos_x = $('#drawflow').scrollLeft() + 100;
        pos_y = $('#drawflow').scrollTop() + 100;
    }
    if (id.indexOf('s') > -1) {
        var response = JSON.parse($('#node-' + id).attr('data'));
        var title = response.name;
        title = convertLongString(title);
        temp++;
        var idScenario = 's' + temp + '-temp';
        response.id = idScenario;
        var scenario = '<div><div class="title-box1 editor-scenario" title="' + response.name + '">' + title + '</div></div>';
        editor.addNode(response.name, 1, 1, pos_x, pos_y, 'editor-scenario', {}, scenario, !1, idScenario, JSON.stringify(response));
        var elem = document.getElementById('node-' + idScenario);
        var html = '<p class="' + idScenario + '" onclick="editor.focusData(this)">' + response.name + '</p>';
        $('.content-scenario').append(html);
        editor.focusData(elem, false, idScenario);
    }

    if (id.indexOf('qc') > -1) {
        var response = JSON.parse($('#node-' + id).attr('data'));
        var nameSubmit = $('#node-' + id).children().find('.editor-qa').attr('title');
        var title = nameSubmit;
        title = convertLongString(title);
        temp++;
        if (id.indexOf('-temp-') > -1) {
            var idQaLearning = id.slice(0, id.lastIndexOf("-temp")) + '-temp-' + temp;
        } else {
            var idQaLearning = id + '-temp-' + temp;
        }

        var qa = '<div><div class="title-box editor-qa-id" title="' + $('#node-' + id).children().find('.editor-qa-id').attr('title') + '">QA ID:' + $('#node-' + id).children().find('.editor-qa-id').attr('title') + '</div><div class="title-box editor-qa" title="' + nameSubmit + '">' + title + '</div></div>';
        editor.addNode(nameSubmit, 1, 0, pos_x, pos_y, 'editor-qa', {}, qa, !1, idQaLearning, JSON.stringify(response));
        var elem = document.getElementById('node-' + idQaLearning);
        var html = '<p class="' + idQaLearning + '" onclick="editor.focusData(this)">' + title + '</p>';
        $('.content-qa').append(html);
        editor.focusData(elem, false, idQaLearning);
    } else if (id.indexOf('q') > -1) {
        var response = JSON.parse($('#node-' + id).attr('data'));
        var nameSubmit = $('#node-' + id).children().find('.editor-qa').attr('title');
        var title = nameSubmit;
        title = convertLongString(title);
        temp++;
        if (id.indexOf('-temp-') > -1) {
            var idQaLearning = id.slice(0, id.lastIndexOf("-temp")) + '-temp-' + temp;
        } else {
            var idQaLearning = id + '-temp-' + temp;
        }

        var qa = '<div><div class="title-box editor-qa-id" title="' + $('#node-' + id).children().find('.editor-qa-id').attr('title') + '">QA ID:' + $('#node-' + id).children().find('.editor-qa-id').attr('title') + '</div><div class="title-box editor-qa" title="' + nameSubmit + '">' + title + '</div></div>';
        editor.addNode(nameSubmit, 1, 0, pos_x, pos_y, 'editor-qa', {}, qa, !1, idQaLearning, JSON.stringify(response));
        var elem = document.getElementById('node-' + idQaLearning);
        var html = '<p class="' + idQaLearning + '" onclick="editor.focusData(this)">' + title + '</p>';
        $('.content-qa').append(html);
        editor.focusData(elem, false, idQaLearning);
    }
}

function clearValidate() {
    $('form#entry_form_edit').find('span.help-block').remove();
    $('form#entry_form_edit').find('div.has-error').removeClass('has-error');
    $('form#formScenario').find('span.help-block').remove();
    $('form#formScenario').find('div.has-error').removeClass('has-error');
    $('.err-add-scenario').find('span.help-block').remove();
    $('.err-add-scenario').find('span.help-block-keyword').remove();
    $('.err-edit-scenario').find('span.help-block').remove();
    $('.err-edit-scenario').find('span.help-block-keyword').remove();
    $('#clone_area').find('.has-error-empty').removeClass('has-error-empty');
    $('#clone_area').find('.has-error').removeClass('has-error');
    $('#clone_area_edit').find('.has-error-empty').removeClass('has-error-empty');
    $('#clone_area_edit').find('.has-error').removeClass('has-error');
}

function validateParams(name, order, keyword_select, type) {
    var check_validate = true;
    clearValidate();
    // ナリオ is required
    if (name.trim() === '') {
        if (type == 'edit') {
            $('form#entry_form_edit').find('input[name="name"]').parent().addClass('has-error').append('<span class="help-block"><strong>シナリオは必須です。</strong></span>');
        }
        if (type == 'create') {
            $('form#formScenario').find('input[name="name"]').parent().addClass('has-error').append('<span class="help-block"><strong>シナリオは必須です。</strong></span>');
        }
        check_validate = false;
    }
    // 表示順 is numberic
    if (order) {
        if (!(/^\d+$/.test(order))) {
            if (type == 'edit') {
                $('form#entry_form_edit').find('input[name="order"]').parent().addClass('has-error').append('<span class="help-block"><strong>表示順には0以上の数値を入力してください。</strong></span>');
            }
            if (type == 'create') {
                $('#order').parent().addClass('has-error');
                $('#order').parent().append('<span class="help-block"><strong>表示順には0以上の数値を入力してください。</strong></span>');
            }
            check_validate = false;
        }
    }
    
    if (keyword_select.length > 0) {
        for (let i = 0; i < keyword_select.length; i++) {
            if (keyword_select[i].value.trim() == '') {
                $(keyword_select[i]).parent().parent().find('span.select2-selection').addClass('has-error-empty');
                check_validate = false;
                continue;
            }
        }
    }
    if (type == 'create') {
        if ($('#clone_area').find('.has-error').length > 0) {
            $('.err-add-scenario').append('<span class="help-block-keyword col-md-12"><strong>関連キーワードが重複しています。</strong></span>')
        }
        if ($('#clone_area').find('.has-error-empty').length > 0) {
            $('.err-add-scenario').append('<span class="help-block-keyword col-md-12"><strong>関連キーワードは必須です。</strong></span>')
        }
    }

    if (type == 'edit') {
        if ($('#clone_area_edit').find('.has-error').length > 0 && type == 'edit') {
            $('.err-edit-scenario').append('<span class="help-block-keyword col-md-12"><strong>関連キーワードが重複しています。</strong></span>')
        }
        if ($('#clone_area_edit').find('.has-error-empty').length > 0 && type == 'edit') {
            $('.err-edit-scenario').append('<span class="help-block-keyword col-md-12"><strong>関連キーワードは必須です。</strong></span>')
        }
    }
    
    if (check_validate) {
        return true;
    }
    return false;
}

function fullScreenElem(fullScreen) {
    var elem = $('#drawflow-editor');
    if (elem[0].requestFullscreen) {
        elem[0].requestFullscreen();
    } else if (elem[0].mozRequestFullScreen) { /* Firefox */
        elem[0].mozRequestFullScreen();
    } else if (elem[0].webkitRequestFullscreen) { /* Chrome, Safari and Opera */
        elem[0].webkitRequestFullscreen();
    } else if (elem[0].msRequestFullscreen) { /* IE/Edge */
        elem[0].msRequestFullscreen();
    }
}

document.addEventListener('fullscreenchange', exitHandler);
document.addEventListener('webkitfullscreenchange', exitHandler);
document.addEventListener('mozfullscreenchange', exitHandler);
document.addEventListener('MSFullscreenChange', exitHandler);

function exitHandler() {
    if (!document.fullscreenElement && !document.webkitIsFullScreen && !document.mozFullScreen && !document.msFullscreenElement) {
        /*style*/
        $('.bar-zoom-close').find('i.fa-compress').removeClass('fa-compress').addClass('fa-expand')
        $('.bar-zoom-close').removeClass('bar-zoom-close').addClass('bar-zoom-fill');
        $('.editor-body').css('height', 'calc(100vh - 250px)');
        $('.col-left-btn').css('min-width', '198px');
        editor.update_container_size(null, true);
    } else {
        $('.bar-zoom-fill').find('i.fa-expand').removeClass('fa-expand').addClass('fa-compress');
        $('.bar-zoom-fill').removeClass('bar-zoom-fill').addClass('bar-zoom-close');
        $('.editor-body').css('height', 'calc(100vh - 32px)');
        $('.col-left-btn').css('width', '200px');
        editor.update_container_size(null, true);
    }
}

function closeFullScreenElem(fullScreen) {
    if (document.exitFullscreen) {
        document.exitFullscreen();
    } else if (document.mozCancelFullScreen) { /* Firefox */
        document.mozCancelFullScreen();
    } else if (document.webkitExitFullscreen) { /* Chrome, Safari and Opera */
        document.webkitExitFullscreen();
    } else if (document.msExitFullscreen) { /* IE/Edge */
        document.msExitFullscreen();
    }
}

(function($) {

    $( document ).ready(function() {
        $('.select2').select2({
            dropdownParent: $('#mySelect2')
        });
    });

    $(".add-qa").on('click', function() {
        searchQa();
    });

    $(document).on('click', ".qa-Add-Node", function() {
        addNodeElement('qa', this);
        $('#qaModal').modal('hide');
    });

    $(document).on('click', ".qa-Copy-Node", function() {
        let id = this.getAttribute('id');
        copyNodeElement(id);
        $('#qaModal').modal('hide');
    });

    /**
     * Double click class editor-scenario
     */
    $(document).on('dblclick', ".editor-scenario", function(event) {
        event.preventDefault();
        scenarioDetail();
    });

    /**
     * Double click class editor-qa
     */
    $(document).on('dblclick', ".editor-qa", function(event) {
        event.preventDefault();
        qaDetail();
    });

    $(".edit-scenario").on('click', function() {
        scenarioEdit();
    });

    $(".copy-node").on('click', function() {
        copyNodeElement();
    });

    $(".delete-node").on('click', function() {
        deleteNodeElement();
    });

    $(".save-scenario").on('click', function() {
        saveScenarioEditor();
    });

    $(".delete-all-scenario").on('click', function(e) {
        deleteAllNodeElement(e);
    });

    $(".search-scenario").on('keyup', function() {
        fillterDataScenario(this.value);
    });

    $(".search-QA").on('keyup', function() {
        fillterDataQa(this.value);
    });

    $(".qa-seach").on('click', function() {
        searchQa();
    });

    $(".add-node-element-scenario").on('click', function() {
        addNodeElement('scenario');
    });

    $(document).on('click', '.edit-scenario-button', function() {
        EditNodeElement();
    });

    $(".fillterCategoryChange").on('change', function() {
        localStorage.setItem('category', this.value);
        loadScenario(this.value);
    });

    $(document).on('click', '.bar-zoom-fill', function() {
        fullScreenElem();
    });

    $(document).on('click', '.bar-zoom-close', function() {
        closeFullScreenElem();
    });

    if (localStorage.getItem("category") === null) {
        loadScenario(null);
    } else {
        $('.fillterCategoryChange').val(localStorage.getItem("category")).trigger('change');
    }

    $('.closeModalQa').click(function() {
        $('.qaKeyword').val('');
        $('.qaId').val('');
    });
    $('.closeModalAddSc').click(function() {
        $(".keyword-add-scenario")[0].innerHTML = '';
        $("#nameScenario").val('');
        $("#order").val('');
        $('#scenarioAdd').modal('hide');
        clearValidate();
    });
})(jQuery);