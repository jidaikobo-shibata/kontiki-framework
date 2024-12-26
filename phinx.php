<?php
/*
Ussage:
php vendor/bin/phinx migrate
php vendor/bin/phinx rollback

php vendor/bin/phinx seed:run
php vendor/bin/phinx seed:run -s PostsSeeder
*/

$env = file_exists(__DIR__ . '/.dev') ? 'development' : 'production';

return [
    'paths' => [
        'migrations' => '%%PHINX_CONFIG_DIR%%/db/migrations',
        'seeds' => '%%PHINX_CONFIG_DIR%%/db/seeds',
    ],
    'environments' => [
        'default_environment' => $env,
        'development' => [
            'suffix' => '',
            'adapter' => 'sqlite',
            'name' => 'db/development/database.sqlite3',
            'charset' => 'utf8',
        ],
        'production' => [
            'suffix' => '',
            'adapter' => 'sqlite',
            'name' => 'db/production/database.sqlite3',
            'charset' => 'utf8',
        ],
    ],
];
