<?php

return [
    'allowed_ext' => [
        'image' => 'gif|png|jpe?g',
        'video' => 'og?|mp4|webm|mp?g|mov|3gp',
        'audio' => 'og?|mp3|mp?g|wav',
    ],
    'disk' => [
        'driver' => 'local',
        'root' => storage_path('app/attachments'),
        'url' => env('APP_URL') . '/attachments',
        'visibility' => 'public',
    ],
];
