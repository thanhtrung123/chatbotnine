<?php
return [
    //ブラウザ用
    'browser' => [
        [
            'id' => config('const.useragent.browser.opera.id'),
            'match' => '/OPR/i',
            'version' => ['/OPR\/([\d.]+)/i', 1],
        ],
        [
            'id' => config('const.useragent.browser.edge.id'),
            'match' => '/Edge/i',
            'version' => ['/Edge\/([\d.]+)/i', 1],
        ],
        [
            'id' => config('const.useragent.browser.chrome.id'),
            'match' => '/Chrome/i',
            'version' => ['/Chrome\/([\d.]+)/i', 1],
        ],
        [
            'id' => config('const.useragent.browser.chrome.id'),
            'match' => '/CriOS/i',
            'version' => ['/CriOS\/([\d.]+)/i', 1],
        ],
        [
            'id' => config('const.useragent.browser.firefox.id'),
            'match' => '/Firefox/i',
            'version' => ['/Firefox\/([\d.]+)/i', 1],
        ],
        [
            'id' => config('const.useragent.browser.firefox.id'),
            'match' => '/FxiOS/i',
            'version' => ['/FxiOS\/([\d.]+)/i', 1],
        ],
        [
            'id' => config('const.useragent.browser.safari.id'),
            'match' => '/Safari/i',
            'version' => ['/Version\/([\d.]+)/i', 1],
        ],
        [
            'id' => config('const.useragent.browser.ie.id'),
            'match' => '/MSIE/i',
            'version' => ['/msie\s([\d.]+)/i', 1],
        ],
        [
            'id' => config('const.useragent.browser.ie.id'),
            'match' => '/Trident/i',
            'version' => ['/rv:([\d.]+)/i', 1],
        ],
    ],

    //OS用
    'os' => [
        [
            'id' => config('const.useragent.os.win.id'),
            'match' => '/Windows\sNT/i',
            'version' => ['/Windows\sNT\s?([\d.]+)/i', 1],
        ],
        [
            'id' => config('const.useragent.os.win.id'),
            'match' => '/Windows\s\d/i',
            'version' => ['/Windows\s?([\d.]+)/i', 1],
        ],
        [
            'id' => config('const.useragent.os.mac.id'),
            'match' => '/Macintosh/i',
            'version' => ['/Mac[\s\w]+\s([\d._]+)/i', 1],
        ],
        [
            'id' => config('const.useragent.os.iphone.id'),
            'match' => '/iPhone/i',
            'version' => ['/iPhone[\s\w]+\s([\d._]+)/i', 1],
        ],
        [
            'id' => config('const.useragent.os.android.id'),
            'match' => '/Android/i',
            'version' => ['/Android\s([\d.]+)/i', 1],
        ],
        [
            'id' => config('const.useragent.os.android.id'),
            'match' => '/Android/i',
            'version' => ['/Android\s([\d.]+)/i', 1],
        ],
        [
            'id' => config('const.useragent.os.chrome.id'),
            'match' => '/CrOS/i',
            'version' => ['/Chrome\/([\d.]+)/i', 1],
        ],
        [
            'id' => config('const.useragent.os.linux.id'),
            'match' => '/Linux/i',
        ],
    ],

    //
    'check_close' => [
        'require_regexp' => ['Safari', 'i'],

        'target_regexp' => [
            'iPhone' => [
                'match' => ['iPhone', 'i'],
                'version' => ['iPhone[\\s\\w;]+\\s([\\d._]+)', 'i', 1, 9],
            ],
            'iPad' => [
                'match' => ['iPad', 'i'],
                'version' => ['iPad[\\s\\w;]+\\s([\\d._]+)', 'i', 1, 9],
            ],
            'Macintosh' => [
                'match' => ['Macintosh', 'i'],
                'version' => ['Macintosh[\\s\\w;]+\\s([\\d._]+)', 'i', 1, 10.10],
            ],
        ]
    ],
];