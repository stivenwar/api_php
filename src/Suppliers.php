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

        $query = "INSERT INTO " . $this->table_name . "(supplier,description,created_at) values (:supplier,:description,NOW())";
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
        $query = "SELECT
    json_agg(
        json_build_object(
            'id_suppliers', s.id_suppliers,
            'supplier', s.supplier,
            'description', s.description,
            'created_at', s.created_at,
            'products', COALESCE((
                SELECT json_agg(
                    json_build_object(
                         'id_supplier', p.id_supplier,
                        'name_product', p.name_product,
                        'description_product', p.description_product,
                        'created_at', p.created_at
                    )
                )
                FROM products p
                WHERE p.id_supplier = s.id_suppliers
            ), '[]'::json)
        )
    ) AS suppliers_with_products
FROM suppliers s";
    
        $stmt = $this->conn->prepare($query);
     
        $stmt->execute();

        return $stmt;
    
    }
}