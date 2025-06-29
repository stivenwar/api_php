<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/Quantity.php';

class QuantityController {

    private $db;
    private $quantityController;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->quantityController = new Quantity($this->db);
    }

    public  function createQuantity($dataQuantity){

        $todosInsertados = true;
     
        foreach ($dataQuantity as $key ) {

    

            if(isset($key['id_product'])&& isset($key['quantity']) && $key['quantity'] > 0){

                $this->quantityController->id_product = $key['id_product'];
    
                 $this->quantityController->quantity = $key['quantity'];
                
                

                $resultado = $this->quantityController->sendQuantity();

                if (!$resultado) {
                     $todosInsertados = false; // alguna inserción falló
                }

            }else{
                
                 //error_log("Datos recibidos en sendQuantity: " . print_r("error", true));
            }
            
        }
        
        return $todosInsertados;

    }


}