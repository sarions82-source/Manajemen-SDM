<?php

return [
    'default' => 'mysql',

    'connections' => [
        // Aplikasi ini hanya memakai MySQL / MariaDB. Wasmer Edge mengisi DB_HOST, DB_PORT, DB_NAME, DB_USERNAME, DB_PASSWORD secara otomatis.
        'mysql' => [
            'driver' => 'mysql',
            'url' => env('DB_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_NAME', env('DB_DATABASE', 'psikotes')),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
            // TLS tanpa verifikasi rantai sertifikat (sertifikat Wasmer dari CA privat). Matikan dengan DB_SSL=false.
            'options' => extension_loaded('pdo_mysql') && env('DB_SSL', true) ? array_filter([
                \PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
                \PDO::MYSQL_ATTR_SSL_CA => env('DB_SSL_CA') ?: null,
            ], fn ($v) => $v !== null) : [],
        ],
    ],

    'migrations' => [
        'table' => 'migrations',
        'update_date_on_publish' => true,
    ],
];
