<?php


require_once __DIR__ . '/../src/SuppliersController.php';

function getSupliersHandler($request,$response){
 $supplierController = new SuppliersController();

    $data = $supplierController->read();    
  
       // array devuelto

    $json = json_encode($data);
  

    $response->getBody()->write($json);

    // Si la lista está vacía, eliges 404 o 200 según tu criterio:
    $status = empty($data) ? 404 : 200;

    return $response
        ->withHeader('Content-Type', 'application/json; charset=utf-8')
        ->withStatus($status);
}
function createListHandler($request, $response){
$supplierController = new SuppliersController();

    $res = $request->getParsedBody();
    if (empty($res)) {
      
        $response->getBody()->write("Archivo no encontrado");
        return $response->withStatus(404);
    }



    $allok = $supplierController->create($res);

    if(!$allok){
         return $response->withStatus(302)
            ->withHeader('Location','/getFormList?error=1');
    }

    return $response->withStatus(200)
            ->withHeader('Location','/getFormList?success=1');
}