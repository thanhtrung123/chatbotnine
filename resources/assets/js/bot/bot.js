/**
 * bot.js
 * チャットボット用js
 */
(function ($) {
    /*
     * 定数
     */
    let CHAT_BOT = window.CHAT_BOT;
    let conf = {
        message_area: '#msg_area',
        message_wrap: '.message-container',
        message_cnt: '.cnt',
        chat_form: "#chat_form",
        chat_text: "#txt_input",
        reset_button: '#bot_reset_btn',
        select_button: '.sel_btn',
        select_def_button: '.sel_def_btn',
        chat_call_button: '.chat_call_btn',
        disabled_cls: 'disabled',
        disabled_lock_cls: 'disabled_lock',
        btn_disabled_cls: 'btn_disable',
        btn_other_cls: 'btn_other',
        btn_active_cls: 'active',
        loading_cls: 'loading',
        user_msg_cls: 'self-message',
        bot_msg_cls: 'bot-message',
        disp_info: '#disp_info',
        storage_key: 'chat_bot_key',
        templates: {
            user_message: 'tpl_msg_user',
            bot_message: 'tpl_msg_bot',
            button_select_def: 'tpl_sel_def_btn',
            button_select_def_list: 'tpl_sel_def_btn_list',
            link_select_def_list: 'tpl_sel_def_link_list',
            image_message: 'tpl_msg_img',
        },
    };
    let chat_id, disp_id, prev_talk_id, now_talk_stack = [], now_bot_msg_box = [], last_bot_msg_box = [], ajax_processing = false, last_self_message_box, suggest_use = false, tmp_text;

    /**
     * DOM Ready...
     */
    $(() => {
        //bind select button
        $(document).on('click', conf.select_def_button, function (e) {
            let btn = $(this), data = btn.data(), msgbox = btn.closest(conf.message_wrap);
            if (btn.hasClass(conf.disabled_cls) || ajax_processing) return false;
            btn.parent().addClass(conf.btn_active_cls).removeClass(conf.btn_other_cls);
            let talk_id = msgbox.data('talkId'), talk_group = $(`[data-talk-id="${talk_id}"]`),
                sel_talk_idx = talk_group.index(msgbox), prev_talk_info = null;
            if (sel_talk_idx != (talk_group.length - 1) && data.option && data.option.prev_talk_info) {
                prev_talk_info = data.option.prev_talk_info;
                talk_group.each(function (i) {
                    if (i <= sel_talk_idx) return true;
                    $(this).find(conf.select_button).each(function () {
                        disableButton($(this).addClass(conf.disabled_lock_cls));
                    });
                });
            }
            postMessage(data.symbol, data.status, btn.text(), prev_talk_info);
        });

        //reset
        $(document).on('click', conf.reset_button, function (e) {
            if (ajax_processing) return false;
            postMessage(CHAT_BOT.bot_const.bot_symbol_reset, CHAT_BOT.bot_const.bot_status_show_category);
        });

        //bind post message
        $(conf.chat_form).submit(function (e) {
            e.preventDefault();
            let msg = $(conf.chat_text).val().trim(), status = CHAT_BOT.bot_const.bot_status_question;
            postMessage(msg, status);
            $(conf.chat_text).val('');
        });

        //chat id
        chat_id = (CHAT_BOT.id) ? CHAT_BOT.id : base.util.getRandStr(16);
        disp_id = chat_id;
        if (localStorage) {
            let ls_chat_id = localStorage.getItem(conf.storage_key);
            if (ls_chat_id) {
                chat_id = ls_chat_id;
            } else {
                localStorage.setItem(conf.storage_key, chat_id);
            }
        }

        //chat init
        _chaq.onAibisLoad = function () {
            // チャット画面を非表示
            $aibis.api.widget.hide();
            $aibis.api.user.setCustomerid(chat_id);
        };

        //start
        init();
        main();
    });

    //
    let close_logged = false;
    $(window).on(base.user.getCloseEvent(), function (e) {
        if (close_logged) return false;
        close_logged = true;
        closeLog();
    });

    /**
     *
     */
    let closeLog = () => {
        base.user.userLog(chat_id, CHAT_BOT.route.user_log, CHAT_BOT.ua_status.close.id, true);
    }

    /**
     * Initialize
     */
    let init = () => {
        last_bot_msg_box.push(showInitMessage(CHAT_BOT.init_data));
        base.user.userLog(chat_id, CHAT_BOT.route.user_log, CHAT_BOT.ua_status.load.id);
    };

    /**
     * main
     */
    let main = () => {
        //追加の処理は以下に追記
    };

    /**
     *
     * @param data
     */
    let showInitMessage = (data) => {
        if (Object.keys(data.category).length) {
            return createSelectButton(data.select_button.default, CHAT_BOT.bot_const.bot_message_start_category);
        } else {
            return viewBotMessage(CHAT_BOT.bot_const.bot_message_start);
        }
    };

    /**
     *
     * @returns {boolean}
     */
    let isAiBisOnline = () => {
        let state = $aibis.api.chat.getState();
        return (state === 'online');
    };

    /**
     * APIにメッセージを送信
     * @param {type} msg
     * @param {type} status
     * @returns {Boolean}
     */
    let postMessage = (msg = '', status = CHAT_BOT.bot_const.bot_status_question, disp_msg = null, prev_talk_info = null) => {
        if (msg.length == 0) {
            return false;
        }
        //ボタンの制御
        buttonProcess();
        //入力無効
        ajaxProcessing(true);
        last_self_message_box = viewUserMessage(base.util.htmlspecialchars(disp_msg || msg));
        if (status == CHAT_BOT.bot_const.bot_status_select) {
            msg = msg.replace(/^\[([A-Za-z0-9])\].+$/, "\$1");
        }
        //Ajax post
        let load_bot_msg = viewBotMessage(CHAT_BOT.bot_const.bot_message_bot_loading);
        base.util.ajax('POST', CHAT_BOT.route.index, {
            message: msg,
            status: status,
            id: chat_id,
            disp_id: disp_id,
            prev_talk_info: prev_talk_info,
            disp_info: $(conf.disp_info).prop('checked') ? 1 : 0,
            disp_msg: disp_msg,
        }, 'json').done((data) => {
            scrollBottom(true);
            if (data.info && $(conf.disp_info).prop('checked')) {
                dispInfo(data);
            }
            if (data.err) {
                //エラー時
                viewBotMessage(data.err);
            } else {
                //オウム返し
                let repeat_msg = '';
                if (data.msg)
                    repeat_msg = base.util.replaceTemplateValue(CHAT_BOT.bot_const.bot_message_repeat, {msg: base.util.htmlspecialchars(data.msg)});
                else if (data.msg_stack)
                    repeat_msg = base.util.replaceTemplateValue(CHAT_BOT.bot_const.bot_message_repeat_stack, {msg: base.util.htmlspecialchars(data.msg_stack)});
                //回答
                let ca_result = createAnswer(data, repeat_msg);
                //追加ボタン
                //フィードバックボタン
                if (data.select_button && data.select_button.feedback) {
                    now_bot_msg_box.push(createSelectButton(data.select_button.feedback, CHAT_BOT.bot_const.bot_message_feedback_msg, 'double'));
                }
                //アンケートボタン
                if (data.select_button && data.select_button.enquete) {
                    now_bot_msg_box.push(createSelectButton(data.select_button.enquete, CHAT_BOT.bot_const.bot_message_enquete));
                }
                //有人チャットボタン
                if (data.select_button && data.select_button.chat_call && isAiBisOnline()) {
                    let prev_msg = now_bot_msg_box[now_bot_msg_box.length - 1].find(conf.message_cnt);
                    prev_msg.html(prev_msg.html() + '<br />' + CHAT_BOT.bot_const.bot_message_chat_call_prev);
                    now_bot_msg_box.push(createSelectButton(data.select_button.chat_call, CHAT_BOT.bot_const.bot_message_chat_call));
                }
                //ボタン制御
                if (data.selected_symbol) {
                    buttonProcess(data.selected_symbol, data.talk_id);
                }
                //キーフレーズ選択だったら
                if (ca_result.has_key_phrase && data.enabled_talk_prev) {
                    for (let i in now_bot_msg_box) {
                        now_bot_msg_box[i].attr('data-talk-id', data.talk_id);
                    }
                    $(`[data-talk-id="${data.talk_id}"] ${conf.select_button}`).not('.' + conf.disabled_lock_cls).removeClass(conf.disabled_cls).parent().removeClass(conf.btn_disabled_cls);
                }
                if (prev_talk_id != data.talk_id) now_talk_stack = [];
                now_talk_stack.push({status: data.input_status, hint_offset: data.hint_offset || 0});
                last_bot_msg_box = now_bot_msg_box;
                now_bot_msg_box = [];
            }
            scrollBottom();
            prev_talk_id = data.talk_id;
        }).fail((data) => {
            //ajax error
            viewBotMessage(CHAT_BOT.bot_const.bot_message_fail);
            last_bot_msg_box = now_bot_msg_box = [];
            scrollBottom();
        }).always((data) => {
            load_bot_msg.remove();
            ajaxProcessing(false);
            $(conf.chat_text).focus();
        });
        scrollBottom();
    };

    /**
     *
     * @param flg
     */
    let ajaxProcessing = (flg) => {
        ajax_processing = flg;
        $(conf.chat_text).attr(conf.disabled_cls, flg);
        let btns = $(conf.select_button).parent();
        if (flg) {
            btns.addClass(conf.loading_cls);
        } else {
            btns.removeClass(conf.loading_cls);
        }
    };

    /**
     * ボタンの処理
     */
    let buttonProcess = (selected_symbol = null) => {
        if (selected_symbol) {
            for (let i in last_bot_msg_box) {
                last_bot_msg_box[i].find(conf.select_button).each(function () {
                    let txt = $(this).text(), sym = $(this).data('symbol');
                    if (txt == selected_symbol || sym == selected_symbol) {
                        $(this).parent().addClass(conf.btn_active_cls).removeClass(conf.btn_disabled_cls).removeClass(conf.btn_other_cls);
                    }
                });
            }
        } else {
            $(conf.message_area).find(conf.select_def_button).not('.' + conf.disabled_cls).each(function () {
                let status = $(this).data('status');
                if (status == CHAT_BOT.bot_const.bot_status_chat_call ||
                    status == CHAT_BOT.bot_const.bot_status_select_answer ||
                    status == CHAT_BOT.bot_const.bot_status_select_category ||
                    status == CHAT_BOT.bot_const.bot_status_select_scenario) return true;
                disableButton($(this));
            });
        }
    };

    /**
     *
     * @param btn
     */
    let disableButton = (btn) => {
        btn.addClass(conf.disabled_cls)
            .parent().not('.' + conf.btn_active_cls).addClass(conf.btn_disabled_cls).removeClass(conf.btn_other_cls);
    };

    /**
     * 回答生成処理
     * @param data
     */
    let createAnswer = (data, repeat_msg = '') => {
        let msg_box, result = {has_key_phrase: false,};
        switch (data.result_status) {
            case CHAT_BOT.bot_const.bot_result_status_no_answer:
                //回答なし
                msg_box = viewBotMessage(CHAT_BOT.bot_const.bot_message_no_answer);
                break;
            case CHAT_BOT.bot_const.bot_result_status_answer :
                //回答文表示
                let ans = base.util.nl2br(data.qa[0].answer), meta = data.qa[0].metadata;
                viewBotMessage(repeat_msg + ans);
                msg_box = createRelatedAnswer(data.select_button.related_answer);
                if (meta && meta.meta && meta.meta.match(/^image/)) {
                    let img_path = meta.meta.replace('image=', ''),
                        obj = viewImageMessage(`./img/images/${img_path}`);
                    //画像ロード後スクロール
                    obj.find('img').on('load', () => {
                        scrollBottom();
                    });
                }
                break;
            case CHAT_BOT.bot_const.bot_result_status_feedback :
                //feedback
                msg_box = viewBotMessage(data.feedback ? CHAT_BOT.bot_const.bot_message_feedback_yes : CHAT_BOT.bot_const.bot_message_feedback_no);
                break;
            case CHAT_BOT.bot_const.bot_result_status_yn :
                //はい/いいえ
                msg_box = createSelectButton(data.select_button.default,
                    base.util.replaceTemplateValue(repeat_msg + CHAT_BOT.bot_const.bot_message_hear_back_one, {msg: data.qa[0].question_str}), 'double');
                break;
            case CHAT_BOT.bot_const.bot_result_status_select :
                //選択肢
                msg_box = createSelectButton(data.select_button.default, repeat_msg + CHAT_BOT.bot_const.bot_message_hear_back_many);
                result.has_key_phrase = true;
                addOtherBtnClass(msg_box);
                break;
            case CHAT_BOT.bot_const.bot_result_status_keyword :
                //キーフレーズ
                msg_box = createSelectButton(data.select_button.default, repeat_msg + CHAT_BOT.bot_const.bot_message_hear_back_keyword, 'horizontal medium_width');
                addOtherBtnClass(msg_box);
                result.has_key_phrase = true;
                break;
            case CHAT_BOT.bot_const.bot_result_status_scenario:
                //シナリオ
                msg_box = createSelectButton(data.select_button.default, CHAT_BOT.bot_const.bot_message_scenario_select);
                addOtherBtnClass(msg_box);
                break;
            case CHAT_BOT.bot_const.bot_result_status_category:
                //カテゴリ
                //今までのボタンを無効化
                $(conf.message_area).find(conf.select_button).addClass(conf.disabled_cls).parent().addClass(conf.btn_disabled_cls);
                msg_box = showInitMessage(data);
                break;
            case CHAT_BOT.bot_const.bot_result_status_chat_call:
                //チャット呼び出し
                $aibis.api.user.setMemo(messagesToMemo());
                $aibis.api.widget.expand();
                $aibis.api.widget.show();
                break;
        }
        if (msg_box) {
            now_bot_msg_box.push(msg_box);
        }
        return result;
    };

    /**
     *
     * @param msg_box
     */
    let addOtherBtnClass = (msg_box) => {
        msg_box.find(conf.select_def_button).each(function () {
            if ($(this).text() == CHAT_BOT.bot_const.bot_symbol_other_hint || $(this).text() == CHAT_BOT.bot_const.bot_symbol_not_in) {
                $(this).parent().addClass(conf.btn_other_cls);
            }
        });
    };

    /**
     *
     * @param data
     * @returns {undefined}
     */
    let createRelatedAnswer = (data) => {
        if (!data) return;
        return createSelectButton(data, CHAT_BOT.bot_const.bot_message_related_answer);
    };

    /**
     * 選択ボタン生成
     * @param data
     * @param message
     * @param answer_type
     * @returns {*}
     */
    let createSelectButton = (data, message = '', answer_type = 'horizontal long_width') => {
        let q_list = [];
        for (let i in data) {
            if (data[i].status)
                q_list.push(getReplaceTemplate(conf.templates.button_select_def_list, createBtnData(data[i].status, data[i].message, data[i].symbol, data[i].option)));
            else if (data[i].href)
                q_list.push(getReplaceTemplate(conf.templates.link_select_def_list, createLinkData(data[i].href, data[i].message, data[i].target, data[i].option)));
        }
        return viewBotMessage(message,
            '',
            getReplaceTemplate(conf.templates.button_select_def, {select_btn_list: q_list.join("\n"), answer_type: answer_type}));
    };

    /**
     * Botのメッセージ表示
     * @param {type} msg
     * @param {type} sel_q
     * @param {type} sel_b
     * @returns {undefined}
     */
    let viewBotMessage = (msg, sel_q = '', sel_b = '', id = null) => {
        return viewMessage(conf.templates.bot_message, {msg: msg, select_q: sel_q, select_btn: sel_b}, id);
    };

    /**
     * 画像表示
     * @param {type} img_path
     * @returns {undefined}
     */
    let viewImageMessage = (img_path, id = null) => {
        return viewMessage(conf.templates.image_message, {img_path: img_path}, id);
    };

    /**
     * 利用者のメッセージ表示
     * @param {type} msg
     * @returns {undefined}
     */
    let viewUserMessage = (msg, id = null) => {
        return viewMessage(conf.templates.user_message, {msg: msg}, id);
    };

    /**
     * メッセージを表示(テンプレート指定)
     * @param tpl_id
     * @param data
     * @param id
     * @returns {*|jQuery|undefined}
     */
    let viewMessage = (tpl_id, data, id = null) => {
        id = (id === null) ? 'msg_' + base.util.getRandStr() : id;
        let obj = $(getReplaceTemplate(tpl_id, data)).attr('id', id);
        $(conf.message_area).append(obj);
        return obj;
    };

    /**
     * 置き換えたテンプレートを取得
     * @param {type} tpl_id
     * @param {type} data
     * @returns {unresolved}
     */
    let getReplaceTemplate = (tpl_id, data) => {
        let tpl = $(`#${tpl_id}`).clone(true);
        return base.util.replaceTemplateValue(tpl.html(), data);
    };

    /**
     * 汎用選択ボタン用データ
     * @param status
     * @param message
     * @param symbol
     * @returns {{symbol: *, message: *, status: *}}
     */
    let createBtnData = (status, message, symbol = null, option = {}) => {
        return {
            status: status,
            message: message,
            symbol: (((symbol === null) ? message : symbol) + '').replace(/"/, '&quot;'),
            btn_cls: option.class || 'col-6',
            option: base.util.htmlspecialchars(JSON.stringify(option)),
        };
    };

    /**
     * 汎用リンクボタン用データ
     * @param href
     * @param message
     * @param target
     * @param option
     * @returns {{href: *, message: *, target: *, option: *}}
     */
    let createLinkData = (href, message, target = null, option = {}) => {
        return {
            href: href,
            message: message,
            target: target || '_blank',
            option: base.util.htmlspecialchars(JSON.stringify(option)),
        };
    };

    /**
     * メッセージエリアを最後の自分のメッセージにスクロール
     * @param is_stop スクロール停止
     */
    let scrollBottom = (is_stop = false) => {
        let target = $("html,body");
        if (is_stop) {
            target.stop();
            return;
        }
        let scroll_top, offset = last_self_message_box.offset(), box_h = last_self_message_box.outerHeight(true),
            hh = $('#header').outerHeight(), fh = $('#footer>form').outerHeight(), wh = $(window).height();
        if (last_self_message_box.height() > wh - (hh + fh)) {
            scroll_top = offset.top - hh;
        } else {
            scroll_top = offset.top - box_h;
        }
        // 10ピクセル引いてスペースを入れる
        target.animate({scrollTop: scroll_top - 10}, 700);
    };

    /**
     *
     * @returns {string}
     */
    let messagesToMemo = () => {
        let memo = '';
        $(conf.message_wrap).each(function () {
            let wrap = $(this);
            if (wrap.hasClass(conf.bot_msg_cls)) {
                //BOTメッセージ取得
                let bot_msg = CHAT_BOT.bot_const.bot_str_bot + $.trim(wrap.find(conf.message_cnt).text()), sel_msg = '';
                wrap.find('li').each(function () {
                    sel_msg += "\n" + CHAT_BOT.bot_const.bot_str_sel + $.trim($(this).text());
                });
                memo += bot_msg + sel_msg;
            } else {
                //USERメッセージ取得
                memo += CHAT_BOT.bot_const.bot_str_user + $.trim(wrap.text());
            }
            memo += "\n\n";
        });
        return memo;
    };

    /**
     * 情報表示用
     * @param data
     */
    let dispInfo = (data) => {
        let info = data.info;
        let tbl = $('<table/>');
        let add_row = (th, td) => {
            let tr = $('<tr/>');
            tr.append($('<th/>').html(th).css({'border-bottom': '1px solid gray', 'border-right': '1px solid gray', 'padding': '0px 3px'}));
            tr.append($('<td/>').html(td).css({'border-bottom': '1px solid gray', 'padding': '0px 3px'}));
            tbl.append(tr);
        };
        add_row('RESULT_STATUS', data.result_status);
        if (info.meta && info.meta.talk_id)
            add_row('TALK_ID', info.meta.talk_id);
        add_row('MESSAGE', info.init.message);
        add_row('STATUS', info.init.status);
        if (info.exec) {
            if (info.init.message != info.exec.message)
                add_row('EX_MESSAGE', info.exec.message);
            if (info.init.status != info.exec.status)
                add_row('EX_STATUS', info.exec.status);
        }
        if (info.truth_input_words) {
            for (let i in info.truth_input_words) {
                let num = (i - 0 + 1);
                add_row('TRUTH_INPUT' + num, info.truth_input_words[i].join(' '));
                if (info.truth_result.info[i]) {
                    if (info.truth_result.info[i].match_status) add_row('TRUTH_REFINE_STATUS' + num, info.truth_result.info[i].match_status);
                    if (info.truth_result.info[i].perfect.length) {
                        let msg = '';
                        $.each(info.truth_result.info[i].perfect, function (i, api_id) {
                            msg += `<span style="display: inline-block;margin: 0px 3px;"><a href="./admin/learning/edit/${api_id}" target="_blank">${api_id}</a></span>`;
                        });
                        add_row(`PERFECT${num}`, msg);
                    }
                    $.each(info.truth_result.info[i].debug_ary, function (k, row) {
                        let msg = '';
                        $.each(row.api_ids, function (api_id, d) {
                            msg += `<span style="display: inline-block;margin: 0px 3px;"><a href="./admin/learning/edit/${api_id}" target="_blank">${api_id}</a></span>`;
                        });
                        add_row(`WORD${num}_[<a href="./admin/key_phrase/edit/${row.key_phrase_id}" target="_blank">${k}</a>](${row.priority.toFixed(2)})`, msg);
                    });
                }
            }
        }

        let disp_qa = (title, qa) => {
            for (let i in qa) {
                let res = qa[i];
                if (!res.id) continue;
                let msg = '';
                if (res.score) msg += `<div> SCORE : ${res.score} </div>`;
                msg += `<div> Q : ${res.question_str} </div><div> QM : ${res.questions[0]} </div>`;
                add_row(`${title}[<a href="./admin/learning/edit/${res.id}" target="_blank">${res.id}</a>]`, msg);
            }
        };
        if (info.try_api) {
            add_row('TRY_API_MESSAGE', info.try_api_message);
            disp_qa('TRY_API', [info.try_api]);
        }
        if (info.api_result) {
            add_row('API_RESULT_MESSAGE', info.api_result_message);
            disp_qa('API_RESULT', info.api_result);
        } else {
            if (data.qa) {
                disp_qa('QA_RESULT', data.qa);
            }
        }
        viewBotMessage($('<div/>').append(tbl).html());
    };

    // Start Fixed Iphone 5
    if (checkVersion() <= 11) {
        $(window).on('load resize', fix_ios_11);
        $('#footer .text').on('focusin keydown', function () {
            if ($(this).val()) {
                $('#footer').css({
                    'height': 'auto',
                    'padding-bottom': 20,
                    'transform': 'translate(0,-20px)'
                });
            }
        });
        $('#footer .text').on('focusout', function () {
            $('#footer').css({
                'transform': 'translate(0,0)',
                'padding-bottom': 0,
            })
        })
    }

    function fix_ios_11() {
        $('#footer').css({
            'position': 'relative',
            'margin-top': window.innerHeight - $('#footer').outerHeight() - $('#header').outerHeight()
        });
        $('#header').css({
            'position': 'relative'
        })
        $('#main-content').css({
            'height': '100%',
            'top': 0,
            'overflow': 'auto',
            'position': 'absolute',
            'padding-bottom': 140
        });
        $('#wrapper,body,html').css({
            'height': '100%',
            'overflow': 'hidden'
        });
    }

    function checkVersion() {
        var agent = window.navigator.userAgent,
            start = agent.indexOf('OS ');
        if ((agent.indexOf('iPhone') > -1 || agent.indexOf('iPad') > -1) && start > -1) {
            return window.Number(agent.substr(start + 3, 3).replace('_', '.'));
        }
        return 10000;
    }

    // End Fixed Iphone 5

})(jQuery);