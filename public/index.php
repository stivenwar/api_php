<?php
// Función para manejar CORS
// function handleCors(): void {
//     header("Access-Control-Allow-Origin: *");
//     header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
//     header("Access-Control-Allow-Headers: Content-Type, Authorization");
//     if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
//         http_response_code(204);
//         exit;
//     }
// }
// handleCors();

// Función para añadir CORS a cualquier objeto Response de Laminas
function addCorsToResponse($response) {
    return $response
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
        ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization');
}
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    $response = new Laminas\Diactoros\Response();
    $response = addCorsToResponse($response);
    http_response_code(204);
    foreach ($response->getHeaders() as $name => $values) {
        foreach ($values as $value) {
            header(sprintf('%s: %s', $name, $value), false);
        }
    }
    exit;
}

use Aura\Router\RouterContainer;

require_once __DIR__ . '/../src/ProductosController.php';
require_once __DIR__ . '/../src/QuantityController.php';
require_once __DIR__ . '/../src/SuppliersController.php';
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../routes/routesHandlers.php';

$productController = new ProductController();
$quantityController = new QuantityController();
$supplierController = new SuppliersController();

$routerContainer = new RouterContainer();
$map = $routerContainer->getMap();

// Rutas
$map->get('read', '/getList', 'getSupliersHandler');
$map->post('sendForm', '/createList', 'createListHandler');

$map->get('sendToUser', '/', function ($request, $response) {
    $file = __DIR__ . '/index.html';
    if (!file_exists($file)) {
        $response->getBody()->write("Archivo no encontrado");
        return $response->withStatus(404);
    }
    $html = file_get_contents($file);
    $response->getBody()->write($html);
    return $response->withHeader('Content-Type', 'text/html; charset=utf-8');
});

$map->get('getForm', '/getFormList', function ($request, $response) {
    $file = __DIR__ . '/../views/vistaProveedor.php';
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
        $response->getBody()->write("Datos no enviados");
        return $response->withStatus(400);
    }
    $allok = $productController->create($res);
    if (!$allok) {
        return $response->withStatus(302)->withHeader('Location','/getFormList?error=1');
    }
    return $response->withStatus(200)->withHeader('Location','/getFormList?success=1');
});

$map->post('sendQuantity', '/sendQuantity', function ($request, $response) use ($quantityController) {
    $json = json_decode($request->getBody()->getContents(), true);
    if (empty($json)) {
        $response->getBody()->write("Datos vacíos");
        return $response->withStatus(400);
    }
    $allok = $quantityController->createQuantity($json);
    if (!$allok) {
        $response->getBody()->write(json_encode(["success" => false]));
        return $response->withStatus(500);
    }
    $response->getBody()->write(json_encode(["success" => true]));
    return $response->withStatus(201);
});

$map->delete("deleteSupplier","/delete-supplier", function($request,$response) use ($supplierController){
    $json = json_decode($request->getBody()->getContents(), true);
    $allok = $supplierController->removeSupplierController($json["id_supplier"] ?? null);
    if (!$allok) {
        $response->getBody()->write("No se pudo eliminar");
        return $response->withStatus(404);
    }
    $response->getBody()->write("success");
    return $response->withStatus(200);
});

$map->delete("deleteProduct","/delete-product", function($request,$response) use ($productController){
    $json = json_decode($request->getBody()->getContents(), true);
    $allok = $productController->deleteProduct($json["id_product"] ?? null);
    if (!$allok) {
        $response->getBody()->write("No se pudo eliminar");
        return $response->withStatus(404);
    }
    $response->getBody()->write("success");
    return $response->withStatus(200);
});

$matcher = $routerContainer->getMatcher();
$request = Laminas\Diactoros\ServerRequestFactory::fromGlobals($_SERVER, $_GET, $_POST, $_COOKIE, $_FILES);
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

// 🚀 Forzamos CORS en todas las respuestas
$response = addCorsToResponse($response);

// Enviar respuesta
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
