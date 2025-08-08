<?php

class Product{
    private $conn;
    private $table_name = "products";

    public $id_product;
    public $id_supplier;
    public $name_product;
    public $description_product;
    public $created_at;


  public function __construct($db)
    {
        $this->conn = $db;
    }

    public function createProducts(){

        $query = "INSERT INTO " .$this->table_name."(id_supplier,name_product,created_at) values (:id_supplier,:name_product,NOW())";

        $stmt = $this->conn->prepare($query);

        $this->id_supplier = htmlspecialchars(strip_tags($this->id_supplier));
        $this->name_product = htmlspecialchars(strip_tags($this->name_product));

            $stmt->bindParam(":id_supplier",$this->id_supplier);
             $stmt->bindParam(":name_product",$this->name_product);
         
        if($stmt->execute()){
            return true;

        }
        return false;   

    }

    public function readController() {
         $query = "SELECT * FROM " . $this->table_name;
    
        $stmt = $this->conn->prepare($query);
     
        $stmt->execute();

        return $stmt;
         
    }
    public function deleteProduct() {
        $query = "DELETE FROM " . $this->table_name. " WHERE id_product = :id_product ";
        $stmt = $this->conn->prepare($query);
  
         $this->id_product = htmlspecialchars(strip_tags($this->id_product));

       
            $stmt->bindParam(":id_product",$this->id_product);
         
        if($stmt->execute()){
            return true;

        }
        return false;   

    }


}