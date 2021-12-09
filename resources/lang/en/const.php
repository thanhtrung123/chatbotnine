<?php
return [
    //操作
    'function' => [
        //管理画面
        'admin' => [
            //ログイン
            'login' => [
                'id' => 1001,
                'name' => 'login',
            ],
            'logout' => [
                'id' => 1002,
                'name' => 'logout',
            ],
            //学習データ
            'learning_store' => [
                'id' => 1101,
                'name' => 'New learning data added',
            ],
            'learning_update' => [
                'id' => 1102,
                'name' => 'Training data correction',
            ],
            'learning_destroy' => [
                'id' => 1103,
                'name' => 'Learning data deletion',
            ],
            'learning_import' => [
                'id' => 1104,
                'name' => 'Training data import',
            ],
            'learning_export' => [
                'id' => 1105,
                'name' => 'Training data export',
            ],
            //類義語
            'synonym_store' => [
                'id' => 1201,
                'name' => 'Addition of new synonym data',
            ],
            'synonym_update' => [
                'id' => 1202,
                'name' => 'Synonym data correction',
            ],
            'synonym_destroy' => [
                'id' => 1203,
                'name' => 'Delete synonym data',
            ],
            'synonym_import' => [
                'id' => 1204,
                'name' => 'Synonym data import',
            ],
            'synonym_export' => [
                'id' => 1205,
                'name' => 'Synonym data export',
            ],
            //異表記
            'variant_store' => [
                'id' => 1301,
                'name' => 'New addition of different notation data',
            ],
            'variant_update' => [
                'id' => 1302,
                'name' => 'Correction of different notation data',
            ],
            'variant_destroy' => [
                'id' => 1303,
                'name' => 'Delete variant data',
            ],
            'variant_import' => [
                'id' => 1304,
                'name' => 'Variant data import',
            ],
            'variant_export' => [
                'id' => 1305,
                'name' => 'Variant data export',
            ],
            //ユーザ
            'user_store' => [
                'id' => 1401,
                'name' => 'Add new user information',
            ],
            'user_update' => [
                'id' => 1402,
                'name' => 'User information correction',
            ],
            'user_destroy' => [
                'id' => 1403,
                'name' => 'Delete user information',
            ],
            //権限
            'role_store' => [
                'id' => 1501,
                'name' => 'Add new permission information',
            ],
            'role_update' => [
                'id' => 1502,
                'name' => 'Authority information correction',
            ],
            'role_destroy' => [
                'id' => 1503,
                'name' => 'Delete permission information',
            ],
            //キーフレーズ
            'key_phrase_store' => [
                'id' => 1601,
                'name' => 'New key phrase data added',
            ],
            'key_phrase_update' => [
                'id' => 1602,
                'name' => 'Key phrase data correction',
            ],
            'key_phrase_destroy' => [
                'id' => 1603,
                'name' => 'Delete key phrase data',
            ],
            'key_phrase_import' => [
                'id' => 1604,
                'name' => 'Key phrase data import',
            ],
            'key_phrase_export' => [
                'id' => 1605,
                'name' => 'Key phrase data export',
            ],
            //カテゴリ
            'category_store' => [
                'id' => 1701,
                'name' => 'Add new category',
            ],
            'category_update' => [
                'id' => 1702,
                'name' => 'Category modification',
            ],
            'category_destroy' => [
                'id' => 1703,
                'name' => 'Category deletion',
            ],
            //シナリオ
            'scenario_store' => [
                'id' => 1801,
                'name' => 'シナリオ新規追加',
            ],
            'scenario_update' => [
                'id' => 1802,
                'name' => 'New scenario added',
            ],
            'scenario_destroy' => [
                'id' => 1803,
                'name' => 'Delete scenario',
            ],
            //固有名詞
            'proper_noun_store' => [
                'id' => 1901,
                'name' => 'Addition of new proper noun data',
            ],
            'proper_noun_update' => [
                'id' => 1902,
                'name' => 'Correcting proper noun data',
            ],
            'proper_noun_destroy' => [
                'id' => 1903,
                'name' => 'Delete proper noun data',
            ],
            'proper_noun_import' => [
                'id' => 1904,
                'name' => 'Proper noun data import',
            ],
            'proper_noun_export' => [
                'id' => 1905,
                'name' => 'Proper noun data export',
            ],
            // 関連質問
            'learning_relation_store' => [
                'id' => 2001,
                'name' => 'Added new relation question data',
            ],
            'learning_relation_update' => [
                'id' => 2002,
                'name' => 'relation question data correction',
            ],
            'learning_relation_destroy' => [
                'id' => 2003,
                'name' => 'Related question data deletion',
            ],
            'learning_relation_import' => [
                'id' => 2004,
                'name' => 'Related question data import',
            ],
            'learning_relation_export' => [
                'id' => 2005,
                'name' => 'Related Question Data Export',
            ],
            // アンケート
            'enquete_export' => [
                'id' => 2101,
                'name' => 'Questionnaire export',
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
                'name' => 'Yes',
            ],
            'no' => [
                'id' => 0,
                'name' => 'No',
            ],
        ],
        //
        'disabled' => [
            'no' => [
                'id' => 0,
                'name' => 'valid',
            ],
            'yes' => [
                'id' => 1,
                'name' => 'invalid',
            ],
        ],
        //
        'available' => [
            'yes' => [
                'id' => 1,
                'name' => 'be',
            ],
            'no' => [
                'id' => 0,
                'name' => 'No',
            ],
        ],

        //
        'gender' => [
            'man' => [
                'id' => 1,
                'name' => 'Man',
            ],
            'woman' => [
                'id' => 2,
                'name' => 'Woman',
            ],
            'other' => [
                'id' => 3,
                'name' => 'other',
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
                'name' => 'WEB bot',
            ],
            'line' => [
                'id' => 2,
                'name' => 'LINE bot',
            ],
        ],
        'user_or_sys' => [
            'user' => [
                'id' => 0,
                'name' => 'A user',
            ],
            'sys' => [
                'id' => 1,
                'name' => 'system',
            ],
        ],
        //ステータス
        'status' => [

            'question_input' => [
                'id' => 100,
                'name' => 'Question input',
            ],
            'question_answer' => [
                'id' => 110,
                'name' => 'Questions answered',
            ],
            'no_answer' => [
                'id' => 120,
                'name' => 'No answer',
            ],

            'response_select' => [
                'id' => 200,
                'name' => 'Candidate answer',
            ],
            'response_other' => [
                'id' => 210,
                'name' => 'Candidate answer (other)',
            ],

            //MEMO:フィードバックはステータスで管理する？
            'feedback_yes' => [
                'id' => 400,
                'name' => 'feedback: Useful',
                'name2' => 'Useful',
            ],
            'feedback_no' => [
                'id' => 410,
                'name' => 'feedback：Not useful',
                'name2' => 'Not useful',
            ],

            'chat_start' => [
                'id' => 500,
                'name' => '有人チャット開始',
            ],

            //
            'scenario_answer' => [
                'id' => 130,
                'name' => 'Scenario(answered)',
            ],
            'scenario_no_answer' => [
                'id' => 140,
                'name' => 'Scenario(no answer)'
            ],

            'related_answer' => [
                'id' => 150,
                'name' => 'Related question selection'
            ],

        ],
                    
        'search_target' =>[
            "user_input" => "Question text(input value)",
            "api_question" => "Question text(learning data)",
            "api_answer" => "Answer text"
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
                'name' => 'all',
            ],
            'learning' => [
                'id' => 1,
                'name' => 'Training data ID',
            ],
            'question' => [
                'id' => 2,
                'name' => 'Question text',
            ],
        ],
        //種類
        'type' => [
            'user' => [
                'id' => 10,
                'name' => 'Number of users',
            ],
            'question' => [
                'id' => 20,
                'name' => 'Number of questions',
            ],
            'one_time_answer' => [
                'id' => 30,
                'name' => 'Number of times you could answer at once',
            ],
            'no_one_time_answer' => [
                'id' => 40,
                'name' => 'Number of times I could not answer at once',
            ],
            'hear_back' => [
                'id' => 50,
                'name' => 'Number of times to listen back',
            ],
            'hear_back_no_answer' => [
                'id' => 50,
                'name' => 'The number of times I could not answer by listening back',
            ],
            'feedback_yes' => [
                'id' => 60,
                'name' => 'feedback：Useful',
            ],
            'feedback_no' => [
                'id' => 70,
                'name' => 'feedback：Not useful',
            ],
            'error' => [
                'id' => 900,
                'name' => 'error',
            ]
        ],
        //集計タブ
        'tab' => [
            'many_question' => [
                'id' => 1,
                'name' => 'Frequently asked questions',
            ],
            'no_answer_question' => [
                'id' => 2,
                'name' => "I couldn't answer",
            ],
            'feedback_yes_question' => [
                'id' => 3,
                'name' => 'Answers that helped with feedback',
            ],
            'feedback_no_question' => [
                'id' => 4,
                'name' => "Answers that didn't help with feedback",
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
                'name' => 'Automatic',
            ],
            'user_add' => [
                'id' => 1,
                'name' => 'addition',
            ],
        ],

    ],
    //アンケート
    'enquete' => [
        //フォームID
        'form_id' => [
            'user_form' => [
                'id' => 'user_form',
                'name' => 'questionnaire',
            ],
        ],

        //フォームタイプ
        'form' => [
            'textarea' => [
                'id' => 1,
                'name' => 'textarea',
            ],
            'text' => [
                'id' => 2,
                'name' => 'text',
            ],
            'checkbox' => [
                'id' => 3,
                'name' => 'Checkbox',
            ],
            'select' => [
                'id' => 4,
                'name' => 'Select box',
            ],
            'radio' => [
                'id' => 5,
                'name' => 'Radio button',
            ],
            'file' => [
                'id' => 6,
                'name' => 'File',
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
                'name' => 'Teens and younger',
            ],
            'a20' => [
                'id' => 2,
                'name' => "20's",
            ],
            'a30' => [
                'id' => 3,
                'name' => "30's",
            ],
            'a40' => [
                'id' => 4,
                'name' => "40's",
            ],
            'a50' => [
                'id' => 5,
                'name' => "50's",
            ],
            'a60' => [
                'id' => 6,
                'name' => "60's",
            ],
            'a70' => [
                'id' => 7,
                'name' => '70s and over',
            ],
        ],
        // 性別
        'sex' => [
            'a10' => [
                'id' => 1,
                'name' => 'male',
            ],
            'a20' => [
                'id' => 2,
                'name' => 'woman',
            ],
            'a30' => [
                'id' => 3,
                'name' => 'others',
            ],
        ],
        // 職業
        'job' => [
            'employee' => [
                'id' => 1,
                'name' => 'Employee',
            ],
            'civil_service' => [
                'id' => 2,
                'name' => 'Civil servant',
            ],
            'self_employed' => [
                'id' => 3,
                'name' => 'Self employed',
            ],
            'housewife' => [
                'id' => 4,
                'name' => 'Full-time housewife (husband)',
            ],
            'student' => [
                'id' => 5,
                'name' => 'student',
            ],
            'part_time_job' => [
                'id' => 6,
                'name' => 'part-time job',
            ],
            'neet' => [
                'id' => 7,
                'name' => 'Unemployed',
            ],
            'other' => [
                'id' => 8,
                'name' => 'other',
            ],
        ],
        // 住まい
        'home' => [
            'in_city' => [
                'id' => 1,
                'name' => 'Lives in the city',
            ],
            'in_city_other' => [
                'id' => 2,
                'name' => 'Living outside the city, but working or attending school in the city',
            ],
            'out_city' => [
                'id' => 3,
                'name' => 'Living outside the city',
            ],
        ],
        // サービス認知
        'know_service' => [
            'homepage' => [
                'id' => 1,
                'name' => 'City homepage',
            ],
            'pr_magazine' => [
                'id' => 2,
                'name' => 'Public relations magazine',
            ],
            'press_release' => [
                'id' => 3,
                'name' => 'Press release material',
            ],
            'search_engine' => [
                'id' => 4,
                'name' => 'Search with an external search engine',
            ],
            'municipal_office' => [
                'id' => 5,
                'name' => 'City facilities such as city hall',
            ],
            'sns' => [
                'id' => 6,
                'name' => 'SNS',
            ],
            'other' => [
                'id' => 7,
                'name' => 'other',
            ],
        ],
        // 端末
        'device' => [
            'smartphone' => [
                'id' => 1,
                'name' => 'Smartphone',
            ],
            'tablet' => [
                'id' => 2,
                'name' => 'tablet',
            ],
            'pc' => [
                'id' => 3,
                'name' => 'Computer',
            ],
            'other' => [
                'id' => 4,
                'name' => 'other',
            ],
        ],
        // 求める情報が得られたか　選択
        'get_select' => [
            1 => 'Obtained',
            2 => 'Mostly obtained',
            3 => "I didn't get much",
            4 => 'Did not get',
        ],
        // 便利だったか　選択
        'service_convenient' => [
            1 => 'I find it very convenient',
            2 => 'I find it rather convenient',
            3 => "I don't find it convenient",
            4 => 'Not convenient',
        ],
        // 良かった点　選択
        'service_good_point' => [
            1 => 'Available 24 hours',
            2 => 'You can ask questions more easily than by phone or at the counter',
            3 => 'Shows related questions',
            4 => 'Quick answer to questions',
            5 => 'The answer to the question is accurate',
            6 => 'Not particularly',
        ],
        // 悪かった点　選択
        'service_bad_point' => [
            1 => 'Difficult to understand how to use',
            2 => 'The answer to the question is not accurate',
            3 => 'Answers are difficult to understand without reference to other information',
            4 => "It's harder to ask questions than by phone or at the counter",
            5 => "Chatbots can't be trusted",
            6 => 'Not particularly',
        ],
        // 改善点　選択
        'service_improvement' => [
            1 => 'I want you to improve the accuracy of the answer',
            2 => 'I want you to increase the number of questions you can answer',
            3 => 'I want you to be able to use it at the counter etc.',
            4 => 'I want you to have a familiar design',
            5 => 'I want you to be able to have conversations other than questions',
            6 => 'I want you to be able to use it with tools such as LINE',
            7 => 'Not particularly',
        ],
        // はい・いいえ　選択
        'yes_no' => [
            1 => 'Yes',
            2 => 'No',
        ],
        // ある・ない　選択
        'available_select' => [
            1 => 'I think there is',
            2 => 'I think there is something',
            3 => "I don't think it's rather",
            4 => "I don't think",
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
