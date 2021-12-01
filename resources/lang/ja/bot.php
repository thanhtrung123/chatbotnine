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
                'nonautonomy' => '非自立',
                'noun' => '名詞',
                'noun_connect' => '名詞接続',
                'prefix' => '接頭詞',
                'suffix' => '接尾',//名詞
                'verb' => '動詞',
                'adjective' => '形容詞',
                'particle' => '助詞',
                'symbol' => '記号',
                'auxiliary_verb' => '助動詞',
                'case_particle' => '格助詞',
                'sbs_particle' => '並立助詞',
                'adverb' => '副詞',
                'adverb_possible' => '副詞可能',
                'other' => '以外',
                'verb_suffix' => '接尾',//（動詞）
                'comma' => '読点',
                'period' => '句点',
                'sa_connect' => 'サ変接続',
                'sa_do' => 'サ変・スル',
                'sp_masu' => '特殊・マス',
                'verb_do' => 'する',
                'sp_not' => '特殊・ナイ',
                'sp_ta' => '特殊・タ',
                'sp_nu' => '特殊・ヌ',
                'standard' => '基本形',
                'no_change' => '不変化型',
                'renyo' => '連用形',
                'undefined' => '名詞 サ変接続 * * * * *',
                'negative_prefix' => ['非', '不', '無', '未', '反', '異'],
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
            'default' => "エラーが発生したため、ページの表示ができませんでした。<br />しばらく時間をおいてから、再度お試しください。",
            'exp' => "アンケート回答ページの有効期限が切れました。<br />本ページよりアンケートに回答いただくことはできません。<br /><br />再度チャットボットにお問い合わせいただき、表示された「アンケートに回答する」ボタンよりアンケートの回答をおこなってください。",

        ],
        'messages' => [
            'send_complete' => "回答の送信が完了しました。\nご協力いただきありがとうございました。",
            'send_complete_sns' => "回答の送信が完了しました。\nご協力いただきありがとうございました。\n\nチャット画面に戻るには、アンケート画面を閉じてください。",
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
            'user_input' => '入力キーワード',
            'status' => 'ステータス',
            'result_status' => '結果ステータス',
            'select_id' => '選択された質問文のID',
            'select_message' => '選択された文字列',
            'choice_key_phrase' => '返答候補キーフレーズ',
            'choice_question' => '選択候補の質問',
            'chat_used' => '有人チャットフラグ',
            'select_feedback' => '解決/未解決フラグ',
            'scenario_used' => 'シナリオ発動フラグ',
            'channel' => 'チャンネル',
            'action_datetime' => '年月日時',
            'load_datetime' => 'load年月日時',
            'close_datetime' => '離脱年月日時',
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
