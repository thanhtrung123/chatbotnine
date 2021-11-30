<?php
return  [
    //タイトル
    'bot_title' => env('CLIENT_NAME') . (env('CLIENT_NAME') != '' ? ' ' : '') . 'AIチャットボット',
    //コピーライト
    'bot_copyright' => env('CLIENT_COPYRIGHT'),
    //ステータス
    'bot_status_question' => 'question',                            // 通常の質問
    'bot_status_select' => 'select',                                // 質問の選択肢を選択
    'bot_status_feedback' => 'select_feedback',                     // フィードバッグ選択
    'bot_status_select_keyword' => 'select_keyword',                // キーフレーズ選択
    'bot_status_select_keyword_other' => 'select_keyword_other',    // キーフレーズ選択(その他)
    'bot_status_select_keyword_none' => 'select_keyword_none',      // キーフレーズ選択(該当なし)
    'bot_status_show_category' => 'show_category',                  // カテゴリ表示
    'bot_status_select_category' => 'select_category',              // カテゴリ選択
    'bot_status_select_scenario' => 'select_scenario',              // シナリオ選択
    'bot_status_select_answer' => 'select_answer',                  // 回答選択
    'bot_status_select_no_answer' => 'select_no_answer',            // 回答中の「この中ない」を選択
    'bot_status_related_answer' => 'related_answer',                // 関連する質問を選択
    'bot_status_show_hint' => 'show_hint',                          // 質問の選択肢中の「この中にない」を選択
    'bot_status_chat_call' => 'chat_call',                          // チャット呼び出し

    //結果ステータス
    'bot_result_status_answer' => 'result_answer',          //回答
    'bot_result_status_feedback' => 'result_feedback',      //フィードバック
    'bot_result_status_yn' => 'result_yn',                  //「はい・いいえ」の選択肢（質問）
    'bot_result_status_select' => 'result_select',          //質問の選択肢
    'bot_result_status_keyword' => 'result_keyword',        //キーフレーズ
    'bot_result_status_scenario' => 'result_scenario',      //シナリオ
    'bot_result_status_category' => 'result_category',      //カテゴリ
    'bot_result_status_no_answer' => 'result_no_answer',    //回答なし
    'bot_result_status_chat_call' => 'result_chat_call',    //チャット呼び出し

    //メッセージ
    'bot_message_start' => '質問を入力してください。',
    'bot_message_start_category' => '質問を入力してください。<br />選択肢を選んで、質問を探すこともできます。',
    'bot_message_no_answer' => '申し訳ございません。ご質問の内容が分かりませんでした。お手数ですが、別の言葉でもう一度質問していただけないでしょうか。',
    'bot_message_feedback_msg' => '問題は解決しましたか？<br />送信いただいたフィードバックの内容で学習を行っています。ご協力をお願いします。',
    'bot_message_feedback_yes' => 'ご回答ありがとうございます。お役に立てて何よりです。',
    'bot_message_feedback_no' => 'お役に立てず申し訳ございません。ご面倒でなければ、別の言葉でもう一度質問していただけないでしょうか。',
    'bot_message_chat_call_prev' => '解決できない場合は、チャットオペレーターに直接お問い合わせいただけます。（受付時間：●●時から●●時）',
    'bot_message_chat_call' => 'オペレーターにおつなぎしますか？',
    'bot_message_enquete' => 'ただいま、サービス向上のためにアンケートを実施しています。<br />アンケートへのご協力をお願いします。',
    'bot_message_hear_back_one' => 'ご質問の内容は「{msg}」ということでしょうか？',
    'bot_message_hear_back_many' => 'こちらの中にあなたのお知りになりたいことはありますか？<br />選択肢からお選びください。',
    'bot_message_hear_back_keyword' => '答えが多く絞り込めませんでした。<br />追加のキーワードをお選びください。',
    'bot_message_scenario_select' => 'こちらの中にあなたのお知りになりたいことはありますか？<br />選択肢からお選びください。',
    'bot_message_repeat' => '「{msg}」についてのご質問ですね。<br /><br />',
    'bot_message_repeat_stack' => '「{msg}」で検索します。<br /><br />',
    'bot_message_related_answer' => 'この答えに関連した質問があります。<br />お知りになりたいことがございましたら、以下からお選びください。',
    'bot_message_fail' => '通信に失敗しました。<br />時間をおいて再度お試しください。',
    'bot_message_api_fail' => '通信に失敗しました。<br />時間をおいて再度お試しください。',
    'bot_message_bot_loading' => '<span style="color:gray;">回答入力中…</span>',
    'bot_message_unknown_type' => "申し訳ございません。\n質問の内容が分かりませんでした。\n\n質問はテキスト形式で入力してください。",
    'bot_message_sns_join' => "ご質問についてお答えいたします。\n聞きたいことを質問してください。",
    //symbol
    'bot_symbol_yes' => 'はい', //FIXME:はい いいえ を変えてしまうとconstと紐づかなくなる（configの読み込み順を変える必要）
    'bot_symbol_no' => 'いいえ',
    'bot_symbol_feedback_yes' => '解決した',
    'bot_symbol_feedback_no' => '解決しなかった',
    'bot_symbol_not' => '×',
    'bot_symbol_other_hint' => 'ほかの選択肢を表示',
    'bot_symbol_not_in' => '該当するものがありません',
    'bot_symbol_reset' => '新しい質問をする',
    'bot_symbol_chat_call' => 'オペレーターにつなぐ（別ウィンドウで開く）',
    'bot_symbol_enquete' => 'アンケートに回答する（別ウィンドウで開く）',
    //string
    'bot_str_bot' => "【BOT】\n",
    'bot_str_user' => "【USER】\n",
    'bot_str_sel' => "\t- ",
    //button class
    'bot_button_classes' => [
        'select_feedback' => [
            'はい' => 'answers-settled',
            'いいえ' => 'answers-resolve',
        ],
        'select' => [
            'はい' => 'col-6',
            'いいえ' => 'col-6',
        ],
    ],
    //dialog
    'bot_dialog_voice_api_loading' => '解析中・・',
    'bot_dialog_alert_title' => '確認',
    'bot_dialog_alert_voiceapi_fail' => '音声の入力に失敗しました',
];