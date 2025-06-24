<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/Productos.php';

class ProductController {

    private $db;
    private $productController;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->productController = new Product($this->db);
    }

    public function create($data){
           
        $todosInsertados = true;
        $data = json_decode($data['data'],true);
      
        foreach ($data as $key ) {
         
            if (!empty($key['supplierId']) &&!empty($key['name'])) {
                  $this->productController->id_supplier = $key['supplierId'];
                  $this->productController->name_product = $key['name'];
                $insertado = $this->productController->createProducts();
                if (!$insertado) {
                    $todosInsertados = false; // si alguno falla, marcamos como falso
                    }
           
            } else {

               $todosInsertados = false;
            
            }

           
          
        }
    
        return $todosInsertados;

    }
    
           

   public function read(): array
{
    $stmt = $this->productController->readController();
    $num  = $stmt->rowCount();

    if ($num > 0) {
        $registros = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
           
            $products[] = [
                'id_product'  => $row['id_product'],
                 'id_supplier'  => $row['id_supplier'],
                'name_product'    => $row['name_product'],
                'created_at' => $row['created_at'],
            ];
        }
        
        // Devolvemos un array; quien llame decidirá status code y cabeceras
        return ['products' => $products];
    }

    // Lista vacía → el caller podrá decidir si responde 200 o 404
    return [];
}
}
?>