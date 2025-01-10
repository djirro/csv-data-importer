<?php

class Database
{
    private $host = 'csv-data-importer-db-1';
    private $dbName = 'test_db';
    private $username = 'test_user';
    private $password = 'test_password';
    private $connection;

    public function __construct()
    {
        try {
            $this->connection = new PDO(
                "mysql:host={$this->host};dbname={$this->dbName}",
                $this->username,
                $this->password
            );
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Ошибка подключения: " . $e->getMessage() . "\n";
            echo "Параметры подключения: " . "host={$this->host}; dbname={$this->dbName}" . "\n";
            echo "Стек ошибок: " . $e->getTraceAsString() . "\n";
            exit;
        }
    }

    public function getConnection()
    {
        return $this->connection;
    }
}
?>