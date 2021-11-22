<?php
return [
    'import_export' => [
        'enabled' => true,
        'dir_backup_name' => 'export/backup',
        'dir_import_name' => 'import',
        'name_zip' => 'scenario_backup_YYYYMMDDHHmmSS.zip',
        'name_file' => 'admin_scenario_YYYYMMDDHHmmSS.xlsx',
        'date_format' => 'YYYYMMDDHHmmSS',
        'post_max_size' => 102400,
        'file_json' => [
            'tbl_scenario.json',
            'tbl_scenario_relation.json',
            'tbl_scenario_keyword.json',
            'tbl_scenario_keyword_relation.json',
            'tbl_scenario_learning_relation.json'
        ],
        'message' => [
            'scenario_file_empty' => 'ファイルが選択されていません。',
            'scenario_download_zip_fail' => 'zipシナリオのダウンロードが失敗しました。',
            'scenario_upload_error' => 'シナリオ復元に失敗しました。<br />シナリオデータ(バックアップ用)ダウンロードで作成したファイルを選択してください。',
            'scenario_upload_zip_error' => 'シナリオデータ(バックアップ用)ダウンロードで作成したZIPファイルを選択してください。',
        ]
    ],
];
