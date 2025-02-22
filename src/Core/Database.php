<?php

namespace Jidaikobo\Kontiki\Core;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Connection;

class Database {
    private static ?Database $instance = null;
    protected Connection $connection;

    private function __construct() {
        $capsule = new Capsule();
        $capsule->addConnection([
            'driver' => 'sqlite',
            'database' => env('PROJECT_PATH', '') . '/' . env('DB_DATABASE', ''),
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => '',
        ]);
        $capsule->setAsGlobal();
        $capsule->bootEloquent();
        $this->connection = $capsule->getConnection();
    }

    public static function getInstance(): Database {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection(): Connection {
        return $this->connection;
    }
}
