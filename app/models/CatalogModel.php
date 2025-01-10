<?php

require_once __DIR__ . '/../config/Database.php';

class CatalogModel
{
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
    }

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
?>