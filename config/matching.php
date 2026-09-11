<?php

return [
    'email_notifications' => env('MATCHING_EMAIL_NOTIFICATIONS', false),
    'social_links' => [
        ['label' => 'Official Website', 'code' => 'WEB', 'url' => env('MATCHING_SOCIAL_WEBSITE', 'https://highclass-inc.com/')],
        ['label' => 'LINE Official', 'code' => 'LINE', 'url' => env('MATCHING_SOCIAL_LINE', 'https://lin.ee/BSWbDgn')],
        ['label' => 'Instagram', 'code' => 'IG', 'url' => env('MATCHING_SOCIAL_INSTAGRAM')],
        ['label' => 'X', 'code' => 'X', 'url' => env('MATCHING_SOCIAL_X')],
        ['label' => 'YouTube', 'code' => 'YT', 'url' => env('MATCHING_SOCIAL_YOUTUBE')],
    ],
    'application_statuses' => [
        'applied' => '応募済み',
        'organization_review' => '団体確認中',
        'interview' => '面談・調整中',
        'accepted' => '採用',
        'rejected' => '不採用',
        'withdrawn' => '辞退',
        'completed' => '完了',
        'canceled' => 'キャンセル',
    ],
    'offer_statuses' => [
        'sent' => '送信済み',
        'accepted' => '受諾済み',
        'declined' => '辞退',
        'withdrawn' => '取り下げ',
    ],
    'prefectures' => [
        '北海道', '青森', '岩手', '宮城', '秋田', '山形', '福島',
        '茨城', '栃木', '群馬', '埼玉', '千葉', '東京', '神奈川',
        '新潟', '富山', '石川', '福井', '山梨', '長野',
        '岐阜', '静岡', '愛知', '三重',
        '滋賀', '京都', '大阪', '兵庫', '奈良', '和歌山',
        '鳥取', '島根', '岡山', '広島', '山口',
        '徳島', '香川', '愛媛', '高知',
        '福岡', '佐賀', '長崎', '熊本', '大分', '宮崎', '鹿児島', '沖縄',
    ],
    'fields' => ['競技指導', 'メンタル', 'トレーニング', 'リハビリ', '栄養', 'アナリスト'],
];
