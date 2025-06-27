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

        $todosInsertados = false;
      
        foreach ($dataQuantity as $key ) {
         
            if(!empty($key['id_product'])&& !empty($key['quantity'])){
                $this->quantityController->id_product = $key['id_product'];
                $this->quantityController->quantity = $key['quantity'];

                $todosInsertados = $this->quantityController->sendQuantity();
            }else{
                $todosInsertados = false;
            }
            
        }
    
        return $todosInsertados;

    }


}