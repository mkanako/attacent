<?php

return [
    'fileTypes' => [
        'image' => '/^(gif|png|jpe?g)$/i',
        'video' => '/^(og?|mp4|webm|mp?g|mov|3gp)$/i',
        'audio' => '/^(og?|mp3|mp?g|wav)$/i',
    ],
    'disk' => [
        'driver' => 'local',
        'root' => storage_path('app/attachments'),
        'url' => './attachments',
        'visibility' => 'public',
    ],
];
