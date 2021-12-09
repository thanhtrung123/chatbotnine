<?php
return [
    // フォーム
    'form' => [
        // アンケート
        config('const.enquete.form_id.user_form.id') => [
            'title' => 'Enquete',
            'description' => '<p> Please let us know what you think in order to improve the AI chatbot. </ p> <p> There are 13 questions. </ p>',
            'question_prefix' => 'Q',
            'items' => [
                10 => [
                    'question' => 'Please tell me your age.',
                    'type' => config('const.enquete.form.radio.id'),
                    'items' => __('const.enquete_items.age'),
                    'is_first_check' => false,
                    'is_crypt' => false,
                ],
                20 => [
                    'question' => 'Please tell me your gender.',
                    'type' => config('const.enquete.form.radio.id'),
                    'items' => __('const.enquete_items.sex'),
                    'is_first_check' => false,
                    'is_crypt' => false,
                ],
                30 => [
                    'question' => 'Please tell me your profession.',
                    'type' => config('const.enquete.form.radio.id'),
                    'items' => __('const.enquete_items.job'),
                    'is_first_check' => false,
                    'is_crypt' => false,
                ],
                40 => [
                    'question' => 'Please tell me where you live.',
                    'type' => config('const.enquete.form.radio.id'),
                    'items' => __('const.enquete_items.home'),
                    'is_first_check' => false,
                    'is_crypt' => false,
                ],
                50 => [
                    'question' => 'How did you find out about this service?',
                    'type' => config('const.enquete.form.radio.id'),
                    'items' => __('const.enquete_items.know_service'),
                    'is_first_check' => false,
                    'is_crypt' => false,
                ],
                60 => [
                    'question' => 'Please tell me the terminal device when using this service.',
                    'type' => config('const.enquete.form.radio.id'),
                    'items' => __('const.enquete_items.device'),
                    'is_first_check' => false,
                    'is_crypt' => false,
                ],
                70 => [
                    'question' => 'Did you get the information you requested using this service?',
                    'type' => config('const.enquete.form.radio.id'),
                    'items' => __('const.enquete_items.get_select'),
                    'is_first_check' => false,
                    'is_crypt' => false,
                ],
                80 => [
                    'question' => 'Did you find this service convenient?',
                    'type' => config('const.enquete.form.radio.id'),
                    'items' => __('const.enquete_items.service_convenient'),
                    'is_first_check' => false,
                    'is_crypt' => false,
                ],
                90 => [
                    'question' => 'Please tell us the good points of this service.',
                    'type' => config('const.enquete.form.checkbox.id'),
                    'items' => __('const.enquete_items.service_good_point'),
                    'is_crypt' => false,
                ],
                100 => [
                    'question' => 'Please tell me the bad points of this service.',
                    'type' => config('const.enquete.form.checkbox.id'),
                    'items' => __('const.enquete_items.service_bad_point'),
                    'is_crypt' => false,
                ],
                110 => [
                    'question' => 'Please tell us what you would like us to improve this service.',
                    'type' => config('const.enquete.form.checkbox.id'),
                    'items' => __('const.enquete_items.service_improvement'),
                    'is_crypt' => false,
                ],
                120 => [
                    'question' => 'Would you like to use a chatbot in the future?',
                    'type' => config('const.enquete.form.radio.id'),
                    'items' => __('const.enquete_items.yes_no'),
                    'is_first_check' => false,
                    'is_crypt' => false,
                ],
                130 => [
                    'question' => 'Please let us know if you have any suggestions or suggestions for improvement.',
                    'placeholder' => 'Please enter a comment.',
                    'type' => config('const.enquete.form.textarea.id'),
                    'is_crypt' => true,
                ],
            ],
        ],
    ],
    'text' => [
        'thanks_help'=>'Thank you for your help.'
    ],
];