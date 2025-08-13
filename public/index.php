<?php
function handleCors(): void {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(204);
        exit;
    }
}
handleCors();

use Aura\Router\RouterContainer;

require_once __DIR__ . '/../src/ProductosController.php';
require_once __DIR__ . '/../src/QuantityController.php';
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ .'/../routes/routesHandlers.php';

//$method = $_SERVER["REQUEST_METHOD"];

$productController = new ProductController();
$quantityController = new QuantityController();
$supplierController = new SuppliersController();
//$requestUri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);


$routerContainer = new RouterContainer();
$map = $routerContainer->getMap();

$map->get('read', '/getList', 'getSupliersHandler' );
$map->post('sendForm', '/createList', 'createListHandler' );

$map->get('sendToUser', '/', function ($request, $response) {

     $file = __DIR__ . '/index.html';  // Ajusta la ruta según tu estructura


    if (!file_exists($file)) {
        http_response_code(404);
        $response->getBody()->write("Archivo no encontrado");
        return $response->withStatus(404);
    }

    $html = file_get_contents($file);

    $response->getBody()->write($html);
    return $response->withHeader('Content-Type', 'text/html; charset=utf-8');
});


$map->get('getForm', '/getFormList', function ($request, $response) {

     $file =  __DIR__ . '/../views/vistaProveedor.php';  // Ajusta la ruta según tu estructura

    if (!file_exists($file)) {
    
        $response->getBody()->write("Archivo no encontrado");
        return $response->withStatus(404);
    }

    ob_start();
    include $file;
    $html = ob_get_clean();

    $response->getBody()->write($html);
    return $response->withHeader('Content-Type', 'text/html; charset=utf-8');
});


$map->post('sendProducts', '/createProducts', function ($request, $response) use ($productController) {

 
    $res = $request->getParsedBody();
    if (empty($res)) {
      
        $response->getBody()->write("Archivo no encontrado");
        return $response->withStatus(404);
    }



    $allok = $productController->create($res);

    if(!$allok){
         return $response->withStatus(302)
            ->withHeader('Location','/getFormList?error=1');
    }

    return $response->withStatus(200)
            ->withHeader('Location','/getFormList?success=1');
  
    
});

$map->post('sendQuantity', '/sendQuantity', function ($request, $response) use ($quantityController) {

 
    $json = json_decode($request->getBody()->getContents(), true);

    if (empty($json)) {
      
        $response->getBody()->write("Archivo no encontrado");
        return $response->withStatus(404);
    }



    $allok = $quantityController->createQuantity($json);

    if(!$allok){
         $response->getBody()->write("Archivo no encontrado");
        return $response->withStatus(404);
            
    }

      $response->getBody()->write(json_encode(["success" => true]));
        return $response->withStatus(201);
  
    
});
$map->delete("deleteSupplier","/delete-supplier", function($request,$response) use ($supplierController){
$json = json_decode($request->getBody()->getContents(), true);
    var_dump($json["id_supplier"]);

    $allok = $supplierController->removeSupplierController($json["id_supplier"]);
    if(!$allok){
         $response->getBody()->write("Archivo no encontrado");
        return $response->withStatus(404);
            
    }

      $response->getBody()->write("success");
        return $response->withStatus(201);

});

$map->delete("deleteProduct","/delete-product", function($request,$response) use ($productController){
$json = json_decode($request->getBody()->getContents(), true);

    $allok = $productController->deleteProduct($json["id_product"]);

    if(!$allok){
         $response->getBody()->write("Archivo no encontrado");
        return $response->withStatus(404);
            
    }

      $response->getBody()->write("success");
        return $response->withStatus(201);

});



$matcher = $routerContainer->getMatcher();

$request = Laminas\Diactoros\ServerRequestFactory::fromGlobals(
    $_SERVER,
    $_GET,
    $_POST,
    $_COOKIE,
    $_FILES
);
error_log("Requested URI: " . $request->getUri()->getPath());
error_log("Requested method: " . $request->getMethod());
$route = $matcher->match($request);


if (!$route) {
    http_response_code(404);
    echo json_encode(["message" => "Ruta no encontrada"]);
    exit;
}

foreach ($route->attributes as $key => $val) {
    $request = $request->withAttribute($key, $val);
}

$callable = $route->handler;
$response = new Laminas\Diactoros\Response();

$response = $callable($request, $response);

// Enviar respuesta al navegador
http_response_code($response->getStatusCode());
foreach ($response->getHeaders() as $name => $values) {
    foreach ($values as $value) {
        header(sprintf('%s: %s', $name, $value), false);
    }
}
echo $response->getBody();


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);