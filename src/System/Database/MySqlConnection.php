<?php
declare(strict_types=1);

namespace Api\System\Database;

use PDO;

final class MySqlConnection implements Connection
{
    /** @var PDO */
    private PDO $connection;

    public function __construct()
    {
        $host = getenv('DB_HOST');
        $port = getenv('DB_PORT');
        $dbname = getenv('DB_DATABASE');
        $username = getenv('DB_USERNAME');
        $password = getenv('DB_PASSWORD');
        $dsn = sprintf('mysql:host=%s;port=%d;charset=utf8mb4;dbname=%s', $host, $port, $dbname);
        $this->connection = new PDO($dsn, $username, $password);
    }

    /**
     * @return PDO
     */
    public function open(): PDO
    {
        return $this->connection;
    }

}