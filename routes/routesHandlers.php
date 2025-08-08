<?php


require_once __DIR__ . '/../src/SuppliersController.php';

function getSupliersHandler($request,$response){
    $supplierController = new SuppliersController();

    try {
        $data = $supplierController->read();
        $status = 200;

        $payload = [
            'success' => true,
            'data' => $data
        ];

    } catch (Exception $e) {
        $status = 500;
        $payload = [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }

    $response->getBody()->write(json_encode($payload));

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