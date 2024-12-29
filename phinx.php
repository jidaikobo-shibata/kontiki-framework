<?php
/*
Ussage:
/usr/bin/php vendor/bin/phinx migrate
/usr/bin/php vendor/bin/phinx rollback
php vendor/bin/phinx migrate
php vendor/bin/phinx rollback
php vendor/bin/phinx seed:run
*/

$env = file_exists(__DIR__ . '/.dev') ? 'development' : 'production';

return
[
    'paths' => [
        'migrations' => '%%PHINX_CONFIG_DIR%%/db/migrations',
        'seeds' => '%%PHINX_CONFIG_DIR%%/db/seeds'
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_environment' => 'development',
        'development' => [
            'suffix' => '',
            'adapter' => 'sqlite',
            'name' => 'db/development/database.sqlite3',
            'charset' => 'utf8',
        ],
    ],
    'version_order' => 'creation'
];
