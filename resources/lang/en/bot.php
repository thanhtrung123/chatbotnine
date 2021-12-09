<?php
/**
 * BOT SETTING
 */
return [
   //API
   'api' => [
    //使用するAPI
    'use' => env('USE_API', 'qna'),
    //APIから返却される回答を学習データのIDとする ※変更した場合、要再同期
    'answer_is_id' => true,
    //同じ質問をAPIに登録する際、連番を付ける（※QNAでNo good match found in KBと出る対策）
    'use_seq_duplicate_question' => true,
    /*
     * QnA Maker関係設定
     */
    'qna' => [
        //サービスクラス
        'service' => App\Services\Bot\Api\QnaService::class,
        //名称
        'name' => 'QnA Maker',
        //エンドポイント（QnA MakerのエンドポイントURL）
        'endpoint' => env('API_KNOWLEDGEBASE_ENDPOINT_URL'),
        /*
         * Knowledge Base関係設定
         */
        'knowledge' => [
            //名称
            'name' => 'QnA KB',
            //answer
            'answer' => '/knowledgebases/' . env('API_KNOWLEDGEBASE_ID') . '/generateAnswer',
            //download
            'download' => '/knowledgebases/' . env('API_KNOWLEDGEBASE_ID') . '/' . env('API_KNOWLEDGEBASE_ENV') . '/qna',
            //version
            'version' => '/endpointkeys',
            //update
            'update' => '/knowledgebases/' . env('API_KNOWLEDGEBASE_ID'),
            //publish
            'publish' => '/knowledgebases/' . env('API_KNOWLEDGEBASE_ID'),
            //HOST（AzureのAppServiceのURL）
            'host' => env('API_KNOWLEDGEBASE_HOST'),
            //エンドポイント
            'endpoint' => env('API_KNOWLEDGEBASE_ENDPOINT'),
            //サブスクリプションキー
            'subscription' => env('API_KNOWLEDGEBASE_SUBSCRIPTION'),
        ],
    ],
    /*
     * Demo
     */
    'demo' => [
        //サービスクラス
        'service' => App\Services\Bot\Api\DemoService::class,
        //名称
        'name' => 'Demo',
    ],
    /**
     * 規定値(オーバーライド用)
     */
    'default' => [
        'service' => '',
        'query_top' => 5,
        'morph_enabled' => true,
    ],
],
//Morph
'morph' => [
    //使用するMorph
    'use' => env('USE_MORPH', 'mecab'),
    //形態素解析結果をクエリとする
    'enabled' => true,
    //連続した名詞を一つをして扱う
    'consecutive_noun_enabled' => true,
    //異表記処理
    'variant_process_enabled' => true,
    //類語処理
    'thesaurus_process_enabled' => true,
    //固有名詞処理
    'proper_noun_process_enabled' => true,
    //スペースでの区切りを許可（注：記号として認識される）
    'enable_space_span' => true,
    //動詞を除外する
    'ignore_verb' => false,
    /*
     * MeCab 関係設定
     */
    'mecab' => [
        //サービスクラス
        'service' => App\Services\Bot\Morph\MecabService::class,
        'main_dic' => '/opt/mecab/lib/mecab/dic/neologd',
        // MeCab用辞書ファイル（「mecab-ipadic-neologd」を使用）
    //            'main_dic' => '/usr/lib64/mecab/dic/mecab-ipadic-neologd',
        'user_dic' => resource_path('dic/original.dic'),
        //依存キーワード（上書き用）
        'keywords' => [
        ],
    ],
    /**
     * 規定値(オーバーライド用)
     */
    'default' => [
        'service' => '',
        'keywords' => [
            'nonautonomy' => 'Non-independent',
            'noun' => 'N   oun',
            'noun_connect' => 'Noun connect',
            'prefix' => 'prefix',
            'suffix' => 'suffix',//名詞
            'verb' => 'verb',
            'adjective' => 'adjective',
            'particle' => 'particle',
            'symbol' => 'symbol',
            'auxiliary_verb' => 'auxiliary_verb',
            'case_particle' => 'Case particles',
            'sbs_particle' => 'Parallel particles',
            'adverb' => 'adverb',
            'adverb_possible' => 'Adverbs possible',
            'other' => 'other',
            'verb_suffix' => 'suffix',//（動詞）
            'comma' => 'The comma',
            'period' => 'period',
            'sa_connect' => 'Change connection',
            'sa_do' => 'Sahen Suru',
            'sp_masu' => 'Special mass',
            'verb_do' => 'do',
            'sp_not' => 'Special Nai',
            'sp_ta' => 'Special',
            'sp_nu' => 'Special',
            'standard' => 'Basic form',
            'no_change' => 'Particle type',
            'renyo' => 'Conjunctive form',
            'undefined' => 'Noun s-irregular connection * * * * *',
            'negative_prefix' => ['Non-', 'Unlucky', 'Nothing', 'Not yet', 'Anti', 'Different'],
            'filler' => 'フィラー'
        ],
    ],
],
//集計
'aggregate' => [
    'overview' => [
        //USA(0=SUN)で指定
        'base_week_day' => 0,
        //5週間分表示
        'display_week_num' => 6,
        //5ヶ月分表示
        'display_month_num' => 6,
    ],
    //response info 12ヵ月保持
    'info_max_month' => '12',
    //response aggregate 12ヵ月保持
    'aggregate_max_month' => '12',
],
//真理表
'truth' => [
    //真理表を使用する
    'enabled' => true,
    //動詞を除外する
    'ignore_verb' => false,
    //許可する記号
    'symbol_list' => [
        '-',
    ],
    //全体の質問数の10%使用されていたらストップワード
    'stop_word_rate' => 100,
    //聞き返しを行うキーフレーズ数(閾値)
    'hear_back_word_cnt' => 2,
    //↑閾値を超えた場合にAPIで問い合わせした結果、聞き返しを行わなくて良いと判断するスコア
    'api_no_hear_back_score' => 70,
    //聞き返しを行う回数
    'hear_back_cnt' => 4,
    //一件に絞り込めなかった場合の最大表示件数
    'no_one_refine_max_answer' => 5,
    //ヒントワードの最大表示件数
    'hint_word_max' => 5,
    //マッチ数が最大の真理表のワードをヒントの候補ワードにする
    'hint_use_max_match_truth' => true,
    //キーフレーズマッチングで一致したと見なす割合
    'hint_word_hit_rate' => 80,
    //キーフレーズマッチングで他の候補がすべて使っているワードを除去(除去後消えたものはマッチしたと判定)
    'ignore_all_equals_word' => true,
    //入力したワードからキーフレーズを生成する際に、類語変換前のワードを含める
    'add_before_synonym_key_phrase' => true,
    //キーフレーズ候補生成時に分かち書きしたものがヒットしたキーフレーズを含める
    'add_morph_key_phrase' => true,
    //キーフレーズ候補生成時に検索用ワードでヒットしたキーフレーズを含める
    'add_search_only_key_phrase' => true,
    //会話が続いている場合、候補キーフレーズを戻って選択できるようにする
    'enabled_talk_prev' => false,
    //キーフレーズ抽出が出来ない場合に、そのままAPIに問い合わせる
    'no_key_phrase_inquiry_api' => false,
],
//シナリオ
'scenario' => [
    //シナリオモードを使用する
    'enabled' => true,
    //一件に絞り込めなかった場合の最大表示件数
    'no_one_refine_max_scenario' => 8,
],
//有人チャット
'human_chat' => [
    //有人チャットを使用する
    'enabled' => true,
],
//アンケート
'enquete' => [
    //アンケートを有効にする
    'enabled' => true,
    //アンケートフォームID
    'form_id' => 'user_form',
    //URI有効期限
    'expiration' => 7200,
    //エラーメッセージ
    'error_messages' => [
        'default' => "The page could not be displayed due to an error. <br /> Please wait a while and try again.",
        'exp' => 'The survey response page has expired. <br /> You cannot answer the questionnaire from this page. <br /> <br /> Please contact the chatbot again and answer the questionnaire by clicking the "Answer Questionnaire" button displayed.',

    ],
    'messages' => [
        'send_complete' => "Your reply has been sent. \n Thank you for your cooperation.",
        'send_complete_sns' => "Your reply has been sent. \n Thank you for your cooperation. \n \nPlease close the survey screen to return to the chat screen.",
    ],
],
//サジェスト
'suggest' => [
    'enabled' => true,
    'pc_max_sentence' => 5,
    'mobile_max_sentence' => 5,
],
    //BIツール用ログ
    'bi_log' => [
        'enabled' => true,
        'path' => 'app/bi_log/log_{Ym}.csv', //※必ず{Ym}をファイル名のどこかに指定してください
        'rotate_dir' => 'app/bi_log_bk',
        'char_code' => 'UTF-8BOM',
        'newline' => "\r\n",
        'date_format' => 'Y-m-d H:i:s',
        'header' => [
            'chat_id' => 'CHAT_ID',
            'talk_id' => 'TALK_ID',
            'user_input' => 'Input keyword',
            'status' => 'status',
            'result_status' => 'Result status',
            'select_id' => 'ID of the selected question',
            'select_message' => 'Selected string',
            'choice_key_phrase' => 'Candidate key phrase',
            'choice_question' => 'Candidate questions',
            'chat_used' => 'Manned chat flag',
            'select_feedback' => 'Resolved / unresolved flag',
            'scenario_used' => 'Scenario activation flag',
            'channel' => 'Channel',
            'action_datetime' => 'Date and time',
            'load_datetime' => 'load date',
            'close_datetime' => 'Withdrawal date and time',
        ]
    ],

    //共通
    'common' => [
        'sync_memory' => '768M',
        'sync_timeout' => 600,
        'str_omit_length' => 15,
    ],
    //定数
    'const' => [
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
        'bot_message_start_category' => 'Please enter your question. <br /> You can also choose an option to search for a question.',
        'bot_message_no_answer' => "I'm sorry. I didn't understand the content of your question. Sorry to trouble you, but could you ask me another question in another language?",
        'bot_message_feedback_msg' => 'Is the problem solved? <br /> We are learning with the content of the feedback you sent. Thank you for your cooperation.',
        'bot_message_feedback_yes' => 'Thank you for your reply. It is useful and above all.',
        'bot_message_feedback_no' => "We apologize for the inconvenience. If you don't mind, could you ask me another question in another language?",
        'bot_message_chat_call_prev' => 'If you cannot resolve it, you can contact the chat operator directly. (Reception hours: ●● hours to ●● hours)',
        'bot_message_chat_call' => 'Would you like to connect to the operator?',
        'bot_message_enquete' => 'We are currently conducting a questionnaire to improve our service. <br /> Thank you for your cooperation in the questionnaire.',
        'bot_message_hear_back_one' => 'Does the content of your question mean 「{msg}」?',
        'bot_message_hear_back_many' => 'Is there anything you would like to know about here? <br /> Please choose from the options.',
        'bot_message_hear_back_keyword' => "I couldn't narrow down the answers. <br /> Please select an additional keyword.",
        'bot_message_scenario_select' => 'Is there anything you would like to know about here? <br /> Please choose from the options.',
        'bot_message_repeat' => 'You have a question about 「{msg}」. <br /> <br />',
        'bot_message_repeat_stack' => 'Search for 「{msg}」. <br /> <br />',
        'bot_message_related_answer' => 'I have a question related to this answer. <br /> If you have any questions, please choose from the following.',
        'bot_message_fail' => 'Communication failed. <br /> Please try again later.',
        'bot_message_api_fail' => 'Communication failed. <br /> Please try again later.',
        'bot_message_bot_loading' => '<span style="color: gray;"> Entering answer ...</ span>',
        'bot_message_unknown_type' => "I'm sorry. <br /> I didn't understand the content of the question. <br /><br />Please enter your question in text format.",
        'bot_message_sns_join' => "We will answer your questions. <br /> Please ask what you want to ask.",
        //symbol
        'bot_symbol_yes' => 'Yes', //FIXME:はい いいえ を変えてしまうとconstと紐づかなくなる（configの読み込み順を変える必要）
        'bot_symbol_no' => 'No',
        'bot_symbol_feedback_yes' => 'Settled',
        'bot_symbol_feedback_no' => 'Did not solve',
        'bot_symbol_not' => '×',
        'bot_symbol_other_hint' => 'Show other options',
        'bot_symbol_not_in' => 'Not applicable',
        'bot_symbol_reset' => 'Clear Filter',
        'bot_symbol_chat_call' => 'Connect to the operator (open in a new window)',
        'bot_symbol_enquete' => 'Answer the questionnaire (open in a new window)',
        //string
        'bot_str_bot' => "【BOT】\n",
        'bot_str_user' => "【USER】\n",
        'bot_str_sel' => "\t- ", 
        //button class
        'bot_button_classes' => [
            'select_feedback' => [
                'yes' => 'answers-settled',
                'no' => 'answers-resolve',
            ],
            'select' => [
                'yes' => 'col-6',
                'no' => 'col-6',
            ],
        ],
        //dialog
        'bot_dialog_voice_api_loading' => 'Analyzing ...',
        'bot_dialog_alert_title' => 'Verification',
        'bot_dialog_alert_voiceapi_fail' => 'Failed to input voice',
    ],
    // Speech to text
    'speech' => [
        'enabled' => TRUE,
        'timeout' => 3000,
        'during' => 10000,
        'sampling_rate' => 16000,
        'url_speech_azure' => 'https://'.env('API_SPEECH_REGION').'.stt.speech.microsoft.com/speech/recognition/conversation/cognitiveservices/v1?language=ja-JP.'
    ],
];
