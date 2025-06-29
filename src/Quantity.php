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

        $query = "INSERT INTO " .$this->table_name."(id_product,quantity,created_at) values (:id_product,:quantity,NOW()) ON CONFLICT (id_product) DO UPDATE 
          SET quantity = EXCLUDED.quantity,
              created_at = NOW()";

        $stmt = $this->conn->prepare($query);

        $this->id_product = htmlspecialchars(strip_tags($this->id_product));
        $this->quantity = htmlspecialchars(strip_tags($this->quantity));

            $stmt->bindParam(":id_product",$this->id_product);
            $stmt->bindParam(":quantity",$this->quantity);
         
        if($stmt->execute()){
                $errorInfo = $stmt->errorInfo();
                error_log("Error en query: " . implode(", ", $errorInfo));
            return true;

        }
        return false;   

    }

    public function readQuantity() {
         $query = "SELECT * FROM " . $this->table_name;
    
        $stmt = $this->conn->prepare($query);
     
        $stmt->execute();

        return $stmt;
         
    }
}