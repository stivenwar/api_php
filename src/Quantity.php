<?php


class Quantity{
    private $conn;
    private $table_name = "products_quantity";

    public $id_quantity;
    public $id_product;
    public $quantity;
    public $created_at;


  public function __construct($db)
    {
        $this->conn = $db;
    }

    public function sendQuantity(){

       if ($this->quantity > 0) {
    $query = "INSERT INTO " . $this->table_name . " (id_product, quantity, created_at)
              VALUES (:id_product, :quantity, NOW())
              ON CONFLICT (id_product) DO UPDATE
              SET quantity = EXCLUDED.quantity,
                  created_at = NOW()";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(":id_product", $this->id_product);
    $stmt->bindParam(":quantity", $this->quantity);
    if ($stmt->execute()) {
        return true;
    } else {
        $errorInfo = $stmt->errorInfo();
        error_log("Error en query: " . implode(", ", $errorInfo));
        return false;
    }
} else {
    // Si quantity es 0, eliminamos el registro
    $query = "DELETE FROM " . $this->table_name . " WHERE id_product = :id_product";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(":id_product", $this->id_product);
    if ($stmt->execute()) {
        return true;
    } else {
        $errorInfo = $stmt->errorInfo();
        error_log("Error en DELETE: " . implode(", ", $errorInfo));
        return false;
    }
}

    }

    public function readQuantity() {
         $query = "SELECT * FROM " . $this->table_name;
    
        $stmt = $this->conn->prepare($query);
     
        $stmt->execute();

        return $stmt;
         
    }
}