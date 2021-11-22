<?php
return [
    // フォーム
    'form' => [
        // アンケート
        config('const.enquete.form_id.user_form.id') => [
            'title' => 'アンケートフォーム',
            'description' => '<p>AIチャットボットの改善のため、みなさまのご意見をお聞かせください。</p><p>質問は13問になります。</p>',
            'question_prefix' => 'Q',
            'items' => [
                10 => [
                    'question' => '年齢を教えてください。',
                    'type' => config('const.enquete.form.radio.id'),
                    'items' => config('const.enquete_items.age'),
                    'is_first_check' => false,
                    'is_crypt' => false,
                ],
                20 => [
                    'question' => '性別を教えてください。',
                    'type' => config('const.enquete.form.radio.id'),
                    'items' => config('const.enquete_items.sex'),
                    'is_first_check' => false,
                    'is_crypt' => false,
                ],
                30 => [
                    'question' => '職業を教えてください。',
                    'type' => config('const.enquete.form.radio.id'),
                    'items' => config('const.enquete_items.job'),
                    'is_first_check' => false,
                    'is_crypt' => false,
                ],
                40 => [
                    'question' => 'お住まいを教えてください。',
                    'type' => config('const.enquete.form.radio.id'),
                    'items' => config('const.enquete_items.home'),
                    'is_first_check' => false,
                    'is_crypt' => false,
                ],
                50 => [
                    'question' => '本サービスを何で知りましたか。',
                    'type' => config('const.enquete.form.radio.id'),
                    'items' => config('const.enquete_items.know_service'),
                    'is_first_check' => false,
                    'is_crypt' => false,
                ],
                60 => [
                    'question' => '本サービスを利用した際の端末機器を教えてください。',
                    'type' => config('const.enquete.form.radio.id'),
                    'items' => config('const.enquete_items.device'),
                    'is_first_check' => false,
                    'is_crypt' => false,
                ],
                70 => [
                    'question' => '本サービスを利用して求める情報は得られましたか。',
                    'type' => config('const.enquete.form.radio.id'),
                    'items' => config('const.enquete_items.get_select'),
                    'is_first_check' => false,
                    'is_crypt' => false,
                ],
                80 => [
                    'question' => '本サービスを便利だと感じましたか。',
                    'type' => config('const.enquete.form.radio.id'),
                    'items' => config('const.enquete_items.service_convenient'),
                    'is_first_check' => false,
                    'is_crypt' => false,
                ],
                90 => [
                    'question' => '本サービスの良かった点を教えてください。',
                    'type' => config('const.enquete.form.checkbox.id'),
                    'items' => config('const.enquete_items.service_good_point'),
                    'is_crypt' => false,
                ],
                100 => [
                    'question' => '本サービスの悪かった点を教えてください。',
                    'type' => config('const.enquete.form.checkbox.id'),
                    'items' => config('const.enquete_items.service_bad_point'),
                    'is_crypt' => false,
                ],
                110 => [
                    'question' => '本サービスの改善してほしい点を教えてください。',
                    'type' => config('const.enquete.form.checkbox.id'),
                    'items' => config('const.enquete_items.service_improvement'),
                    'is_crypt' => false,
                ],
                120 => [
                    'question' => '今後チャットボットを利用したいと思いますか。',
                    'type' => config('const.enquete.form.radio.id'),
                    'items' => config('const.enquete_items.yes_no'),
                    'is_first_check' => false,
                    'is_crypt' => false,
                ],
                130 => [
                    'question' => '改善してほしい点や、ご意見がありましたら教えてください。',
                    'placeholder' => 'コメントを入力してください。',
                    'type' => config('const.enquete.form.textarea.id'),
                    'is_crypt' => true,
                ],
            ],
        ],
    ],
];