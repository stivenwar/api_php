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

    public function create($data){
        $todosInsertados = false;
        // $dataJson = json_decode(file_get_contents("php://input"))
         if (!empty($data['supplier']) && !empty($data['description'])) {
            $this->supplierController->supplier = $data['supplier'];
             $this->supplierController->description = $data['description'];

            $todosInsertados = $this->supplierController->createSuppliers();
        } else {
            $todosInsertados = false;
             }

    return $todosInsertados;
    


    }
    
           
        


   public function read(): array
{
    $stmt = $this->supplierController->readSuppliers();
    $num  = $stmt->rowCount();

    if ($num > 0) {
         $registros = [];
     
    }
   $row = $stmt->fetch(PDO::FETCH_ASSOC); // Solo un resultado

if ($row && isset($row['suppliers_with_products'])) {
    // Decodifica el JSON directamente
  $registros = json_decode($row['suppliers_with_products'], true); // ← Decodifica correctamente

    return ['registros' => $registros];
}

    return [];
}
}
?>