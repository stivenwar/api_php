<?php

use Aura\Router\RouterContainer;

require_once __DIR__ . '/../src/SuppliersController.php';
require_once __DIR__ . '/../src/ProductosController.php';
require_once __DIR__ . '/../vendor/autoload.php';

//$method = $_SERVER["REQUEST_METHOD"];
$supplierController = new SuppliersController();
$productController = new ProductController();
//$requestUri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);


$routerContainer = new RouterContainer();
$map = $routerContainer->getMap();

$map->get('read', '/getList', function ($request, $response) use ($supplierController,$productController) {

    $data = $supplierController->read();    
  

       // array devuelto

    $json = json_encode($data);
  

    $response->getBody()->write($json);

    // Si la lista está vacía, eliges 404 o 200 según tu criterio:
    $status = empty($data) ? 404 : 200;

    return $response
        ->withHeader('Content-Type', 'application/json; charset=utf-8')
        ->withStatus($status);
});
$map->get('sendToUser', '/', function ($request, $response) use ($supplierController) {

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


$map->get('getForm', '/getFormList', function ($request, $response) use ($supplierController) {

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
$map->post('sendForm', '/createList', function ($request, $response) use ($supplierController) {

   
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



/*

switch ($method) {
        case 'POST':
          
        break;
        case 'GET':
          $supplierController->read();
        break;
        case 'PUT':
         #$supplierController->update();
        break;
        case 'DELETE':
        #$supplierController->delete();
        break;
    
    default:
         http_response_code(405);
         echo json_encode(["message" => "método no permitido"]);
        break;
}
*/
// Despachar ruta


function handleCors() {
    // Permitir acceso desde cualquier origen (o cambia por el dominio permitido)
    header("Access-Control-Allow-Origin: *");

    // Métodos permitidos
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");

    // Encabezados permitidos
    header("Access-Control-Allow-Headers: Content-Type, Authorization");

    // Si es preflight (OPTIONS), termina aquí
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(204); // No Content
        exit;
    }
}

handleCors();

$matcher = $routerContainer->getMatcher();

$request = Laminas\Diactoros\ServerRequestFactory::fromGlobals(
    $_SERVER,
    $_GET,
    $_POST,
    $_COOKIE,
    $_FILES
);

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