<?php

class Suppliers {

    private $conn;
    private $table_name = "suppliers";

    public $id_supplier;
    public $supplier;
    public $description;
    public $created_at;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function createSuppliers() {
        $query = "INSERT INTO " . $this->table_name . "(id_suppliers,supplier,description,created_at) values (null,:supplier,:description,NOW())";
        #var_dump($query);
        $stmt = $this->conn->prepare($query);
        
        $this->supplier  = htmlspecialchars(strip_tags($this->supplier));
        $this->description = htmlspecialchars(strip_tags($this->description));
    
        $stmt->bindParam(":supplier",$this->supplier);
        $stmt->bindParam(":description",$this->description);

        if($stmt->execute()){
            return true;

        }
        return false;
    }
    public function readSuppliers (){
        $query = "SELECT * FROM " . $this->table_name;
    
        $stmt = $this->conn->prepare($query);
     
        $stmt->execute();

        return $stmt;
    
    }
}