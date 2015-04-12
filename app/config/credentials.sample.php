<?php

return [
    'facebook_app' => [
        'app_id'       => 'APP_ID',
        'app_secret'   => 'APP_SECRET',
        'app_url'      => 'APP_URL',
        'scope'        => ['ads_management', 'ads_read'],
        'redirect_url' => 'APP_URL/settings/facebook/callback',
    ],
    'meli_app' => [
        'app_id'     => 'APP_ID',
        'app_secret' => 'APP_SECRET',
        'app_url'    => 'APP_URL/account/login',
    ],
];
