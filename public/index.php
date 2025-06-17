<?php

require_once __DIR__ . '/../src/SuppliersController.php';

$method = $_SERVER["REQUEST_METHOD"];
$supplierController = new SuppliersController();

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

switch ($method) {
        case 'POST':
          $supplierController->create();
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

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);