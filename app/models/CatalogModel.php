<?php

require_once __DIR__ . '/../config/Database.php';

class CatalogModel
{
    private $db;

    /**
     * Конструктор, инициализирует соединение с базой данных.
     */
    public function __construct()
    {
        $this->db = (new Database())->getConnection();
    }

    /**
     * Возвращает объект подключения к базе данных.
     */
    public function getDb()
    {
        return $this->db;
    }

    /**
     * Вставляет или обновляет запись в таблице catalog.
     * Если запись с таким кодом уже существует, обновляется ее название.
     * 
     * @param string $code Код записи.
     * @param string $name Название записи.
     * @return bool true, если операция выполнена успешно, иначе false.
     */
    public function insertOrUpdateRecord($code, $name)
    {
        $query = "INSERT INTO catalog (code, name) VALUES (:code, :name)
              ON DUPLICATE KEY UPDATE name = :name";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':code', $code);
        $stmt->bindParam(':name', $name);

        return $stmt->execute();
    }
}