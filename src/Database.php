<?php
namespace App;

use PDO; // PHP Data Objects for database access

class Database {
    public static function connect() {
        $dbType = $_ENV['DB_CONNECTION'];
        if ($dbType === 'pgsql') {
            $dsn = "pgsql:host={$_ENV['DB_HOST']};port={$_ENV['DB_PORT']};dbname={$_ENV['DB_DATABASE']}";
        } else {
            $dsn = "sqlite:" . __DIR__ . "/../tasks.db";
        }

        return new PDO($dsn, $_ENV['DB_USERNAME'] ?? null, $_ENV['DB_PASSWORD'] ?? null);
    }
}
