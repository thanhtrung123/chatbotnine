<?php
/**
 * ACL SETTING
 */
return [
    //リソース
    'resources' => [
        'learning' => '学習データ',
        'synonym' => '類義語データ',
        'variant' => '異表記データ',
        'user' => 'アカウント情報',
        'role' => '特権情報',
        'key_phrase' => 'キーフレーズ',
        'category' => 'カテゴリ',
        'scenario' => 'シナリオ',
        'proper_noun' => '固有名詞',
        'learning_relation' => '関連質問',
    ],
    //特権
    'privileges' => [
        'create' => '新規追加',
        'edit' => '修正',
        'destroy' => '削除',
        'import' => 'インポート',
        'export' => 'エクスポート',
    ],
    //ロール
    'roles' => [
        'admin' => '管理者',
    ],
    //スーパーユーザー(消せないように)
    'admin' => [
        'role' => 'admin',
        'user' => 'admin',
    ],
];
