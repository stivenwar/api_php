<?php

class Database {
    private $host = "db";
    private $db_name = "mi_base_datos";
    private $user = "root";
    private $password = "secret";
    private $port = "3360";
    public $conn;
    
    public function getConnection() {
        
        $this->conn = null;

    try {
    $this->conn = new PDO(
        "mysql:host=" . $this->host . ";port=" . $this->port . ";dbname=" . $this->db_name,
        $this->user,
        $this->password
    );
         $this->conn->exec("set names utf8");
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}    catch (PDOException $e) {
    // Evitar imprimir directamente para no enviar salida antes de headers
    error_log("Error en la conexión: " . $e->getMessage());
    throw $e; // O maneja el error como prefieras
}

        return $this->conn;


    }


}