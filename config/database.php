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

           $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name , $this->user, $this->password);
           $this->conn->exec("set names utf8");

        } catch (\Throwable $th) {
            echo "Error en la conexion: " .$th->getMessage();
        }

        return $this->conn;


    }


}