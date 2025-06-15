<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/Suppliers.php';

class SuppliersController {

    private $db;
    private $supplierController;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->supplierController = new Suppliers($this->db);
    }

    public function create(){
        $todosInsertados = true;
        $dataJson = json_decode(file_get_contents("php://input"));
        var_dump($dataJson);
        
    foreach($dataJson as $data){

      

        if (!empty($data->supplier)&& !empty($data->description)) {
            $this->supplierController->supplier = $data->supplier;
            $this->supplierController->description = $data->description;

            if(!$this->supplierController->createSuppliers()){
                $todosInsertados = false;
            }


        } else {
               $todosInsertados = false;
        }

    }
        

        if($todosInsertados){
             http_response_code(201);
             echo json_encode(["message"=>"Producto creado correctamente"]);
        }else{
            http_response_code(503);
            echo json_encode(["message"=>"Producto no creado"]);
        }
        
    }

    public function read(){
        $stmt = $this->supplierController->readSuppliers();
        $num= $stmt->rowCount();

        if($num>0){
            $supplier_arr = [];
            $supplier_arr["registros"] = [];

            while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                extract($row);

                $supplier_item = [
                    "supplier"=> $supplier,
                    "description"=> $description
                ];
                array_push($supplier_arr["registros"],$supplier_item);
                
            }

             http_response_code(200);
             echo json_encode($supplier_arr);

        }else {
              http_response_code(503);
              echo json_encode(["message"=>"Producto no creado"]);
        }

        
        
    }

}