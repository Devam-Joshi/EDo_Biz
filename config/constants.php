<?php

return [
    'empty_object' => new stdClass(),
    'google_map_key' => 'AIzaSyBRR40Ie35qkoC1F5-v3YsZ1eWt51F3Qqg',
    'asset_url' => env('APP_URL'),
    'upload_type' => 'local',
    'default' => [
        'image' => 'uploads/user/user.png',
        'user_image' => 'uploads/user/user.png',
        'no_image_available' => 'assets/general/image/no_image.jpg',
    ],
    'upload_paths' => [
        'exception_upload' => 'uploads/exception',
        'user_profile_image' => 'uploads/user',
        'admin_upload' => 'uploads/admin',
    ],
    'push_log' => true,
    'firebase_server_key' => '',
];
