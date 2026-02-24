<?php

return [
    'default' => env('FILESYSTEM_DRIVER', 'public'),

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL') . '/storage',
            'visibility' => 'public',
        ],

        'akunners_files' => [
            'driver' => 'local',
            'root' => env('AKUNNERS_STORAGE_PATH'),
            'visibility' => 'private',
        ],
    ],

];
