<?php

namespace Jidaikobo\Kontiki\Core;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Connection;

class Database
{
    private static ?Database $instance = null;
    protected Connection $connection;

    private function __construct(array $connection)
    {
        $capsule = new Capsule();
        $capsule->addConnection($connection);
        $capsule->setAsGlobal();
        $capsule->bootEloquent();
        $this->connection = $capsule->getConnection();
    }

    public static function setInstance(array $connection): void
    {
        if (self::$instance === null) {
            self::$instance = new Database($connection);
        }
    }

    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            throw new \RuntimeException("Call setInstance() first.");
        }
        return self::$instance;
    }

    public function getConnection(): Connection
    {
        return $this->connection;
    }
}
