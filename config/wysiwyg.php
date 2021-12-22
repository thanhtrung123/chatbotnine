<?php
// Define
return [
    'toolbar' => [
        'source' => [
            'enabled' => true,
            'options' => [
                'Source' => true,
            ],
        ],
        'document' => [
            'enabled' => false,
            'options' => [
                'Save' => false,
                'NewPage' => true,
                'ExportPdf' => true,
                'Preview' => true,
                '-' => true,
                'Print' => false,
                'Templates' => true,
            ],
        ],
        'clipboard' => [
            'enabled' => false,
            'options' => [
                'Cut' => true,
                'Copy' => true,
                'PasteText' => true,
                'PasteFromWord' => true,
                '-' => true,
                'Undo' => true,
                'Redo' => true,
            ],
        ],
        'editing' => [
            'enabled' => false,
            'options' => [
                'Find' => true,
                '-' => true,
                'Replace' => true,
                'SelectAll' => true,
                'Scayt' => true,
            ],
        ],
        'tools' => [
            'enabled' => false,
            'options' => [
                'Maximize' => true,
                'ShowBlocks' => true,
                'Zoom' => true,
            ],
        ],
        'insert' => [
            'enabled' => true,
            'options' => [
                'Slideshow' => false,
                'Youtube' => false,
                '-' => false,
                'Leaflet' => false,
                'Iframe' => false,
                '-' => false,
                'GdImage' => true,
                'Flash' => false,
                'Table' => false,
                'HorizontalRule' => true,
                '-' => false,
                'Smiley' => false,
                'SpecialChar' => false,
                'PageBreak' => false,
            ],
        ],
        'basicstyles' => [
            'enabled' => false,
            'options' => [
                'Bold' => true,
                'Italic' => true,
                'Underline' => true,
                'Strike' => true,
                '-' => true,
                'Subscript' => true,
                'Superscript' => true,
                'CopyFormatting' => true,
                'RemoveFormat' => true,
            ],
        ],
        'paragraph' => [
            'enabled' => true,
            'options' => [
                'NumberedList' => true,
                'BulletedList' => true,
                '-' => true,
                'Outdent' => false,
                'Indent' => false,
                'Blockquote' => false,
                'CreateDiv' => false,
                '-' => false,
                'JustifyLeft' => false,
                'JustifyCenter' => false,
                'JustifyRight' => false,
                'JustifyBlock' => false,
                '-' => false,
                'BidiLtr' => false,
                'BidiRtl' => false,
                '-' => false,
                'Language' => false,
            ],
        ],
        'forms' => [
            'enabled' => false,
            'options' => [
                'Form' => true,
                'Checkbox' => true,
                'Radio' => true,
                'TextField' => true,
                'Textarea' => true,
                'Select' => true,
                'Button' => true,
                'ImageButton' => true,
                'HiddenField' => true,
            ],
        ],
        'styles' => [
            'enabled' => false,
            'options' => [
                'Styles' => true,
                'Format' => true,
                'Font' => true,
                'FontSize' => true,
            ],
        ],
        'colors' => [
            'enabled' => false,
            'options' => [
                'TextColor' => true,
                'BGColor' => true,
            ],
        ],
        'links' => [
            'enabled' => true,
            'options' => [
                'lightbox' => false,
                'Link' => true,
                'Unlink' => true,
                'Anchor' => false,
            ],
        ],
        'about' => [
            'enabled' => false,
            'options' => [
                'About' => true,
            ],
        ],
    ],
    'config' => [
        'height' => 280,
        'skin' => 'moono-lisa',
        'uiColor' => '#dbdbdb',
        'language' => 'ja',
        'type_file_image' => ['png', 'jpg', 'gif'],
        'max_image_width' => 1000, // 640px
        'max_image_height' => 900, // 480px
        'max_image_capacity' => 10240, // 10MB,
        'post_max_size' => 102400, // 100MB
        'image_list_width' => '64',
        'image_list_height' => '64',
        'flag_off' => 0,
        'flag_on' => 1,
        'resize_mode' => 'RESIZE',
        'trimming_mode' => 'TRIMMING',
        'cancel_mode' => 'CANCEL',
        'decision_mode' => 'DECISION',
        'source_mode' => 'Source',
        // sort
        'max_images_displayed' => [
            "10" => "１０件表示",
            "30" => "３０件表示",
            "50" => "５０件表示",
            "100" => "１００件表示",
        ]
    ]
];
