<?php

class Database {
    private $host =getenv("HOST")?: "db";
    private $db_name = getenv("DATABASE")?:"mi_base_datos";
    private $user =getenv("USER")?: "root";
    private $password =getenv("PASSWORD")?: "secret";
    private $port =getenv("PORT")?: "3360";
    public $conn;
    
    public function getConnection() {
        
         $this->conn = null;

//     try {
//     $this->conn = new PDO(
//         "mysql:host=" . $this->host . ";port=" . $this->port . ";dbname=" . $this->db_name,
//         $this->user,
//         $this->password
//     );
//          $this->conn->exec("set names utf8");
//         $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
// }    catch (PDOException $e) {
//     // Evitar imprimir directamente para no enviar salida antes de headers
//     error_log("Error en la conexión: " . $e->getMessage());
//     throw $e; // O maneja el error como prefieras
// }

  try {
            $dsn = "pgsql:host={$this->host};port={$this->port};dbname={$this->db_name}";
            $this->conn = new PDO($dsn, $this->user, $this->password);
            // En PostgreSQL no hace falta "set names utf8" porque viene por defecto en UTF-8
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            error_log("Error en la conexión: " . $e->getMessage());
            throw $e;
        }

        return $this->conn;


    }


}