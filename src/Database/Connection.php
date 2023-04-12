<?php

namespace Drobotik\Eav\Database;
use Doctrine\DBAL\Connection as DBALConnection;
use Doctrine\DBAL\DriverManager;

class Connection
{
    protected static DBALConnection|null $conn = null;

    public static function getConnection(array $params = null) : DBALConnection
    {
        if(!is_null(self::$conn)) {
            return self::$conn;
        }
        if(PHP_SAPI == 'cli' && str_contains($_SERVER['argv'][0], 'phpunit'))
        {
            $params = [
                'driver' => 'pdo_sqlite',
                'path' => dirname(__DIR__, 2) . '/tests/test.sqlite'
            ];
        }
        else {
            $config = [
                'driver' => $_ENV['DB_DRIVER'],
                'host' => $_ENV['DB_HOST'],
                'port' => $_ENV['DB_PORT'],
                'dbname' => $_ENV['DB_DATABASE'],
                'user' => $_ENV['DB_USERNAME'],
                'password' => $_ENV['DB_PASSWORD']
            ];
            $params = is_null($params)
                ? $config
                : array_merge($config, $params);
        }
        self::$conn = DriverManager::getConnection($params);
        return self::$conn;
    }
}