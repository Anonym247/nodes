<?php

namespace Src\Core;

use PDO;
use PDOException;

class DatabaseConnector
{
    /**
     * @var PDO|null
     */
    private ?PDO $connection = null;

    public function __construct()
    {
        $host = getenv('DB_HOST');
        $port = getenv('DB_PORT');
        $database = getenv('DB_DATABASE');
        $username = getenv('DB_USERNAME');
        $password = getenv('DB_PASSWORD');

        try {
            $this->connection = new PDO(
                "mysql:host=$host;port=$port;charset=utf8mb4;dbname=$database",
                $username,
                $password
            );
        } catch (PDOException $exception) {
            exit($exception->getMessage());
        }
    }

    /**
     * @return PDO|null
     */
    public function getConnection(): ?PDO
    {
        return $this->connection;
    }
}