<?php
return [
    //操作
    'function' => [
        //管理画面
        'admin' => [
            //ログイン
            'login' => [
                'id' => 1001,
                'name' => 'ログイン',
            ],
            'logout' => [
                'id' => 1002,
                'name' => 'ログアウト',
            ],
            //学習データ
            'learning_store' => [
                'id' => 1101,
                'name' => '学習データ新規追加',
            ],
            'learning_update' => [
                'id' => 1102,
                'name' => '学習データ修正',
            ],
            'learning_destroy' => [
                'id' => 1103,
                'name' => '学習データ削除',
            ],
            'learning_import' => [
                'id' => 1104,
                'name' => '学習データインポート',
            ],
            'learning_export' => [
                'id' => 1105,
                'name' => '学習データエクスポート',
            ],
            //類義語
            'synonym_store' => [
                'id' => 1201,
                'name' => '類義語データ新規追加',
            ],
            'synonym_update' => [
                'id' => 1202,
                'name' => '類義語データ修正',
            ],
            'synonym_destroy' => [
                'id' => 1203,
                'name' => '類義語データ削除',
            ],
            'synonym_import' => [
                'id' => 1204,
                'name' => '類義語データインポート',
            ],
            'synonym_export' => [
                'id' => 1205,
                'name' => '類義語データエクスポート',
            ],
            //異表記
            'variant_store' => [
                'id' => 1301,
                'name' => '異表記データ新規追加',
            ],
            'variant_update' => [
                'id' => 1302,
                'name' => '異表記データ修正',
            ],
            'variant_destroy' => [
                'id' => 1303,
                'name' => '異表記データ削除',
            ],
            'variant_import' => [
                'id' => 1304,
                'name' => '異表記データインポート',
            ],
            'variant_export' => [
                'id' => 1305,
                'name' => '異表記データエクスポート',
            ],
            //ユーザ
            'user_store' => [
                'id' => 1401,
                'name' => 'ユーザ情報新規追加',
            ],
            'user_update' => [
                'id' => 1402,
                'name' => 'ユーザ情報修正',
            ],
            'user_destroy' => [
                'id' => 1403,
                'name' => 'ユーザ情報削除',
            ],
            //権限
            'role_store' => [
                'id' => 1501,
                'name' => '権限情報新規追加',
            ],
            'role_update' => [
                'id' => 1502,
                'name' => '権限情報修正',
            ],
            'role_destroy' => [
                'id' => 1503,
                'name' => '権限情報削除',
            ],
            //キーフレーズ
            'key_phrase_store' => [
                'id' => 1601,
                'name' => 'キーフレーズデータ新規追加',
            ],
            'key_phrase_update' => [
                'id' => 1602,
                'name' => 'キーフレーズデータ修正',
            ],
            'key_phrase_destroy' => [
                'id' => 1603,
                'name' => 'キーフレーズデータ削除',
            ],
            'key_phrase_import' => [
                'id' => 1604,
                'name' => 'キーフレーズデータインポート',
            ],
            'key_phrase_export' => [
                'id' => 1605,
                'name' => 'キーフレーズデータエクスポート',
            ],
            //カテゴリ
            'category_store' => [
                'id' => 1701,
                'name' => 'カテゴリ新規追加',
            ],
            'category_update' => [
                'id' => 1702,
                'name' => 'カテゴリ修正',
            ],
            'category_destroy' => [
                'id' => 1703,
                'name' => 'カテゴリ削除',
            ],
            //シナリオ
            'scenario_store' => [
                'id' => 1801,
                'name' => 'シナリオ新規追加',
            ],
            'scenario_update' => [
                'id' => 1802,
                'name' => 'シナリオ修正',
            ],
            'scenario_destroy' => [
                'id' => 1803,
                'name' => 'シナリオ削除',
            ],
            //固有名詞
            'proper_noun_store' => [
                'id' => 1901,
                'name' => '固有名詞データ新規追加',
            ],
            'proper_noun_update' => [
                'id' => 1902,
                'name' => '固有名詞データ修正',
            ],
            'proper_noun_destroy' => [
                'id' => 1903,
                'name' => '固有名詞データ削除',
            ],
            'proper_noun_import' => [
                'id' => 1904,
                'name' => '固有名詞データインポート',
            ],
            'proper_noun_export' => [
                'id' => 1905,
                'name' => '固有名詞データエクスポート',
            ],
            // 関連質問
            'learning_relation_store' => [
                'id' => 2001,
                'name' => '関連質問データ新規追加',
            ],
            'learning_relation_update' => [
                'id' => 2002,
                'name' => '関連質問データ修正',
            ],
            'learning_relation_destroy' => [
                'id' => 2003,
                'name' => '関連質問データ削除',
            ],
            'learning_relation_import' => [
                'id' => 2004,
                'name' => '関連質問データインポート',
            ],
            'learning_relation_export' => [
                'id' => 2005,
                'name' => '関連質問データエクスポート',
            ],
            // アンケート
            'enquete_export' => [
                'id' => 2101,
                'name' => 'アンケートエクスポート',
            ],
        ],
    ],
    //共通
    'common' => [
        //
        'on_off' => [
            'on' => [
                'id' => 1,
                'name' => 'ON',
            ],
            'off' => [
                'id' => 0,
                'name' => 'OFF',
            ],
        ],
        //
        'yes_no' => [
            'yes' => [
                'id' => 1,
                'name' => 'はい',
            ],
            'no' => [
                'id' => 0,
                'name' => 'いいえ',
            ],
        ],
        //
        'disabled' => [
            'no' => [
                'id' => 0,
                'name' => '有効',
            ],
            'yes' => [
                'id' => 1,
                'name' => '無効',
            ],
        ],
        //
        'available' => [
            'yes' => [
                'id' => 1,
                'name' => 'ある',
            ],
            'no' => [
                'id' => 0,
                'name' => 'ない',
            ],
        ],

        //
        'gender' => [
            'man' => [
                'id' => 1,
                'name' => '男',
            ],
            'woman' => [
                'id' => 2,
                'name' => '女',
            ],
            'other' => [
                'id' => 3,
                'name' => 'その他',
            ],
        ],

        //
        'device' => [
            'pc' => [
                'id' => 1,
                'name' => 'PC',
            ],
            'smartphone' => [
                'id' => 2,
                'name' => 'SmartPhone',
            ],
        ],

    ],
    //chat bot関連
    'bot' => [
        //チャンネル
        'channel' => [
            'web' => [
                'id' => 1,
                'name' => 'WEBボット',
            ],
            'line' => [
                'id' => 2,
                'name' => 'LINEボット',
            ],
        ],
        'user_or_sys' => [
            'user' => [
                'id' => 0,
                'name' => 'ユーザ',
            ],
            'sys' => [
                'id' => 1,
                'name' => 'システム',
            ],
        ],
        //ステータス
        'status' => [

            'question_input' => [
                'id' => 100,
                'name' => '質問入力',
            ],
            'question_answer' => [
                'id' => 110,
                'name' => '質問回答済',
            ],
            'no_answer' => [
                'id' => 120,
                'name' => '回答無し',
            ],

            'response_select' => [
                'id' => 200,
                'name' => '回答候補',
            ],
            'response_other' => [
                'id' => 210,
                'name' => '回答候補(その他)',
            ],

            //MEMO:フィードバックはステータスで管理する？
            'feedback_yes' => [
                'id' => 400,
                'name' => 'フィードバック：役に立った',
                'name2' => '役に立った',
            ],
            'feedback_no' => [
                'id' => 410,
                'name' => 'フィードバック：役に立たない',
                'name2' => '役に立たない',
            ],

            'chat_start' => [
                'id' => 500,
                'name' => '有人チャット開始',
            ],

            //
            'scenario_answer' => [
                'id' => 130,
                'name' => 'シナリオ(回答済み)',
            ],
            'scenario_no_answer' => [
                'id' => 140,
                'name' => 'シナリオ(回答なし)'
            ],

            'related_answer' => [
                'id' => 150,
                'name' => '関連質問選択'
            ],

//            'error' => [
//                'id' => 900,
//                'name' => 'エラー',
//            ],
        ],
        //チャンネル
        'channel' => [
            'web' => [
                'id' => 1,
                'name' => 'WEB',
            ],
            'line' => [
                'id' => 2,
                'name' => 'LINE',
            ],
            'fb' => [
                'id' => 3,
                'name' => 'Facebook',
            ],
        ],
        //エラー
        'error' => [
            'old_button' => [
                'id' => 10,
                'name' => 'old_button',
            ],
        ],
    ],
    //集計用
    'aggregate' => [
        //基準
        'base' => [
            'all' => [
                'id' => 0,
                'name' => 'すべて',
            ],
            'learning' => [
                'id' => 1,
                'name' => '学習データID',
            ],
            'question' => [
                'id' => 2,
                'name' => '質問文',
            ],
        ],
        //種類
        'type' => [
            'user' => [
                'id' => 10,
                'name' => '利用者人数',
            ],
            'question' => [
                'id' => 20,
                'name' => '質問回数',
            ],
            'one_time_answer' => [
                'id' => 30,
                'name' => '一回で回答できた回数',
            ],
            'no_one_time_answer' => [
                'id' => 40,
                'name' => '一回で回答できなかった回数',
            ],
            'hear_back' => [
                'id' => 50,
                'name' => '聞き返し回数',
            ],
            'hear_back_no_answer' => [
                'id' => 50,
                'name' => '聞き返しで回答できなかった回数',
            ],
            'feedback_yes' => [
                'id' => 60,
                'name' => 'フィードバック：役に立った',
            ],
            'feedback_no' => [
                'id' => 70,
                'name' => 'フィードバック：役に立たない',
            ],
            'error' => [
                'id' => 900,
                'name' => 'エラー',
            ]
        ],
        //集計タブ
        'tab' => [
            'many_question' => [
                'id' => 1,
                'name' => '多かった質問',
            ],
            'no_answer_question' => [
                'id' => 2,
                'name' => '回答できなかった',
            ],
            'feedback_yes_question' => [
                'id' => 3,
                'name' => 'フィードバックで役に立った回答',
            ],
            'feedback_no_question' => [
                'id' => 4,
                'name' => 'フィードバックで役に立たなかった回答',
            ],
        ],
        //概況表示
        'overview' => [
            'week' => [
                'id' => 1,
                'name' => '週',
            ],
            'month' => [
                'id' => 2,
                'name' => '月',
            ],
        ],


    ],
    //真理表
    'truth' => [
        //キーフレーズタイプ
        'key_phrase_type' => [
            'auto' => [
                'id' => 0,
                'name' => '自動',
            ],
            'user_add' => [
                'id' => 1,
                'name' => '追加',
            ],
        ],

    ],
    //アンケート
    'enquete' => [
        //フォームID
        'form_id' => [
            'user_form' => [
                'id' => 'user_form',
                'name' => 'アンケート',
            ],
        ],

        //フォームタイプ
        'form' => [
            'textarea' => [
                'id' => 1,
                'name' => 'テキストエリア',
            ],
            'text' => [
                'id' => 2,
                'name' => 'テキスト',
            ],
            'checkbox' => [
                'id' => 3,
                'name' => 'チェックボックス',
            ],
            'select' => [
                'id' => 4,
                'name' => 'セレクトボックス',
            ],
            'radio' => [
                'id' => 5,
                'name' => 'ラジオボタン',
            ],
            'file' => [
                'id' => 6,
                'name' => 'ファイル',
            ],
        ],

        //エラー
        'error' => [
            'exp' => [
                'id' => 10,
                'name' => 'exp',
            ],
        ],

    ],
    //アンケート項目
    'enquete_items' => [
        // 年齢
        'age' => [
            'a10' => [
                'id' => 1,
                'name' => '10代以下',
            ],
            'a20' => [
                'id' => 2,
                'name' => '20代',
            ],
            'a30' => [
                'id' => 3,
                'name' => '30代',
            ],
            'a40' => [
                'id' => 4,
                'name' => '40代',
            ],
            'a50' => [
                'id' => 5,
                'name' => '50代',
            ],
            'a60' => [
                'id' => 6,
                'name' => '60代',
            ],
            'a70' => [
                'id' => 7,
                'name' => '70代以上',
            ],
        ],
        // 性別
        'sex' => [
            'a10' => [
                'id' => 1,
                'name' => '男性',
            ],
            'a20' => [
                'id' => 2,
                'name' => '女性',
            ],
            'a30' => [
                'id' => 3,
                'name' => 'その他',
            ],
        ],
        // 職業
        'job' => [
            'employee' => [
                'id' => 1,
                'name' => '会社員',
            ],
            'civil_service' => [
                'id' => 2,
                'name' => '公務員',
            ],
            'self_employed' => [
                'id' => 3,
                'name' => '自営業',
            ],
            'housewife' => [
                'id' => 4,
                'name' => '専業主婦（夫）',
            ],
            'student' => [
                'id' => 5,
                'name' => '学生',
            ],
            'part_time_job' => [
                'id' => 6,
                'name' => 'パート・アルバイト',
            ],
            'neet' => [
                'id' => 7,
                'name' => '無職',
            ],
            'other' => [
                'id' => 8,
                'name' => 'その他',
            ],
        ],
        // 住まい
        'home' => [
            'in_city' => [
                'id' => 1,
                'name' => '市内在住',
            ],
            'in_city_other' => [
                'id' => 2,
                'name' => '市外在住だが、市内在勤または在学',
            ],
            'out_city' => [
                'id' => 3,
                'name' => '市外在住',
            ],
        ],
        // サービス認知
        'know_service' => [
            'homepage' => [
                'id' => 1,
                'name' => '市ホームページ',
            ],
            'pr_magazine' => [
                'id' => 2,
                'name' => '広報誌',
            ],
            'press_release' => [
                'id' => 3,
                'name' => '記者発表資料',
            ],
            'search_engine' => [
                'id' => 4,
                'name' => '外部検索エンジンで検索',
            ],
            'municipal_office' => [
                'id' => 5,
                'name' => '市役所等の市施設',
            ],
            'sns' => [
                'id' => 6,
                'name' => 'SNS',
            ],
            'other' => [
                'id' => 7,
                'name' => 'その他',
            ],
        ],
        // 端末
        'device' => [
            'smartphone' => [
                'id' => 1,
                'name' => 'スマートフォン',
            ],
            'tablet' => [
                'id' => 2,
                'name' => 'タブレット',
            ],
            'pc' => [
                'id' => 3,
                'name' => 'パソコン',
            ],
            'other' => [
                'id' => 4,
                'name' => 'その他',
            ],
        ],
        // 求める情報が得られたか　選択
        'get_select' => [
            1 => '得られた',
            2 => 'だいたい得られた',
            3 => 'あまり得られなかった',
            4 => '得られなかった',
        ],
        // 便利だったか　選択
        'service_convenient' => [
            1 => '大変便利だと感じる',
            2 => 'どちらかというと便利だと感じる',
            3 => 'どちらかというと便利だと感じない',
            4 => '便利ではない',
        ],
        // 良かった点　選択
        'service_good_point' => [
            1 => '24時間利用できる',
            2 => '電話や窓口よりも気軽に質問できる',
            3 => '関連する質問を示してくれる',
            4 => '質問に対する回答が早い',
            5 => '質問への回答が的確である',
            6 => '特にない',
        ],
        // 悪かった点　選択
        'service_bad_point' => [
            1 => '使い方がわかりにくい',
            2 => '質問への回答が的確ではない',
            3 => '他の情報を参照しないと回答がわかりにくい',
            4 => '電話や窓口よりも質問しにくい',
            5 => 'チャットボットは信用できない',
            6 => '特にない',
        ],
        // 改善点　選択
        'service_improvement' => [
            1 => '回答の精度を上げてほしい',
            2 => '対応できる質問の数を増やしてほしい',
            3 => '窓口等でも使えるようにしてほしい',
            4 => '親しみのあるデザインにしてほしい',
            5 => '質問以外の会話もできるようにしてほしい',
            6 => 'LINEなどのツールで利用できるようにしてほしい',
            7 => '特にない',
        ],
        // はい・いいえ　選択
        'yes_no' => [
            1 => 'はい',
            2 => 'いいえ',
        ],
        // ある・ない　選択
        'available_select' => [
            1 => 'あると思う',
            2 => 'どちらかといえばあると思う',
            3 => 'どちらかといえばないと思う',
            4 => 'ないと思う',
        ],
    ],
    //UA用
    'useragent' => [
        //ブラウザ
        'browser' => [
            'safari' => [
                'id' => 1,
                'name' => 'Safari',
            ],
            'ie' => [
                'id' => 2,
                'name' => 'InternetExplorer',
            ],
            'edge' => [
                'id' => 3,
                'name' => 'Edge',
            ],
            'chrome' => [
                'id' => 4,
                'name' => 'Chrome',
            ],
            'opera' => [
                'id' => 5,
                'name' => 'OPERA',
            ],
            'firefox' => [
                'id' => 6,
                'name' => 'Firefox',
            ],
            'other' => [
                'id' => 999,
                'name' => 'Other',
            ],
        ],
        //OS
        'os' => [
            'mac' => [
                'id' => 1,
                'name' => 'Macintosh',
            ],
            'win' => [
                'id' => 2,
                'name' => 'Windows',
            ],
            'linux' => [
                'id' => 3,
                'name' => 'Linux',
            ],
            'iphone' => [
                'id' => 4,
                'name' => 'iPhone',
            ],
            'android' => [
                'id' => 5,
                'name' => 'Android',
            ],
            'chrome' => [
                'id' => 6,
                'name' => 'ChromeOS',
            ],
            'other' => [
                'id' => 999,
                'name' => 'Other',
            ],
        ],
        //ステータス
        'status' => [
            'load' => [
                'id' => 1,
                'name' => 'load',
            ],
            'close' => [
                'id' => 2,
                'name' => 'close',
            ],
            'enquete_load' => [
                'id' => 8,
                'name' => 'enquete_load',
            ],
            'enquete_close' => [
                'id' => 9,
                'name' => 'enquete_close',
            ],
        ],
    ],
    'dashboard' => [
        'number_enquete' => 12,
        'limit_answers' => 30,
        'date_limit' => 31,
        'week_limit' => 180,
        'month_limit' => 1095,
        'background_color_in_5' => [
            '#4169e1',
            '#ff6347',
            '#00215d',
            '#1C706A',
            '#0045FF',
        ],
        'background_color_in_20' => [
            '#002f8f',
            '#003898',
            '#003f9f',
            '#0048a8',
            '#004faf',
            '#0058b8',
            '#005fbf',
            '#0068c8',
            '#006fcf',
            '#0078d8',
            '#007fdf',
            '#0088e8',
            '#008fef',
            '#0098f8',
            '#009fff',
            '#00Afff',
            '#00Bfff',
            '#00Cfff',
            '#00Dfff',
            '#00Efff'
        ],
        'background_color_default' => '#a9a9a9'
    ],
];
