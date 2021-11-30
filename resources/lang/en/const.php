<?php
return  [
        //タイトル
        'bot_title' => env('CLIENT_NAME') . (env('CLIENT_NAME') != '' ? ' ' : '') . 'AI Chatbot',
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
        'bot_message_start' => 'Please enter your question.',
        'bot_message_start_category' => "Please enter your question.<br/> You can also choose an option and look for a question.",
        'bot_message_no_answer' => "I'm sorry. I didn't understand the content of your question. Sorry to trouble you, but could you ask me another question in another language?",
        'bot_message_feedback_msg' => "Is the problem solved? <br /> We are learning with the content of the feedback you sent. Thank you for your cooperation.",
        'bot_message_feedback_yes' => "Thank you for your reply. It is useful and above all.",
        'bot_message_feedback_no' =>"I'm sorry it didn't help. If you don't mind, could you ask me another question in another language?",
        'bot_message_chat_call_prev' => "If you cannot resolve it, you can contact the chat operator directly. (Reception hours: ●● hours to ●● hours)",
        'bot_message_chat_call' => "Do you want to connect to the operator?",
        'bot_message_enquete' => "We are currently conducting a questionnaire to improve our service. <br /> Thank you for your cooperation in the questionnaire.",
        'bot_message_hear_back_one' => "Does the content of your question mean 「{msg}」?",
        'bot_message_hear_back_many' => 'Is there anything you would like to know about here? <br /> Please choose from the options.',
        'bot_message_hear_back_keyword' => "I couldn't narrow down the answers. <br /> Please select an additional keyword.",
        'bot_message_scenario_select' => "Is there anything you would like to know about here? <br /> Please choose from the options.",
        'bot_message_repeat' => "You have a question about 「{msg}」. <br /> <br />",
        'bot_message_repeat_stack' => "Search for「{msg}」. <br /> <br />",
        'bot_message_related_answer' => 'I have a question related to this answer.<br />If you have any questions, please choose from the following.',
        'bot_message_fail' => 'Communication failed.<br />Please try again later.',
        'bot_message_api_fail' => 'Communication failed.<br />Please try again later.',
        'bot_message_bot_loading' => '<span style="color:gray;">Entering an answer ...</span>',
        'bot_message_unknown_type' => "I'm sorry.<br/>I didn't understand the content of the question.<br/><br/>Please enter your question in text format.",
        'bot_message_sns_join' => "We will answer your questions.<br/>Ask a question you want to ask.",
        //symbol
        'bot_symbol_yes' => 'Yes', //FIXME:はい いいえ を変えてしまうとconstと紐づかなくなる（configの読み込み順を変える必要）
        'bot_symbol_no' => 'No',
        'bot_symbol_feedback_yes' => 'Settled',
        'bot_symbol_feedback_no' => 'Did not solve',
        'bot_symbol_not' => '×',
        'bot_symbol_other_hint' => 'Show other options',
        'bot_symbol_not_in' => 'Not applicable',
        'bot_symbol_reset' => 'Ask a new question',
        'bot_symbol_chat_call' => 'Connect to the operator (open in a new window)',
        'bot_symbol_enquete' => 'Answer the questionnaire (open in a new window)',
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
        'bot_dialog_voice_api_loading' => 'Analyzing...',
        'bot_dialog_alert_title' => 'Verification',
        'bot_dialog_alert_voiceapi_fail' => 'Failed to input voice',
        '情報メッセージを表示する'=>"Display informational messages",
        '送信'=>'send',
    ];