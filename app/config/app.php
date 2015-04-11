<?php

return [
    'debug'    => true,
    'doctrine' => [
        'dbs.options' => [
            'default' => [
                'driver'   => 'pdo_mysql',
                'host'     => '127.0.0.1',
                'dbname'   => 'promoted_listings',
                'user'     => 'root',
                'password' => 'root',
                'charset'  => 'utf8',
            ]
        ]
    ]
];
