<?php

// Tự động load các file cần thiết
// spl_autoload_register(function ($class) {
//     if (file_exists("core/$class.php")) {
//         require "core/$class.php";
//     } elseif (file_exists("controllers/$class.php")) {
//         require "controllers/$class.php";
//     } elseif (file_exists("models/$class.php")) {
//         require "models/$class.php";
//     }
// });

// Lấy controller và action từ URL, mặc định: UserController@index
// $controllerName;
// $actionName;
// try {
//     if (isset($_GET['c']) && isset($_GET['a'])) {
//         $controllerName = $_GET['c'];
//         $actionName = $_GET['a'];
//         $controllerClass = $controllerName . "Controller";
//         $controller = new $controllerClass;
//         $controller->$actionName();
//     } else {
//         echo "SGM API version 1.0";
//     }
// } catch (\Throwable $th) {
//     throw $th;
// }
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require 'vendor/autoload.php';

$app = AppFactory::create();

$app->get('/', function (Request $request, Response $response, $args) {
    ob_start();
    include __DIR__ . '/views/index.php';
    $output = ob_get_clean();
    $response->getBody()->write($output);
    return $response;
});

$app->get('/all', function (Request $request, Response $response, $args) {
    $controller = new ContrucstionController();
    $data = $controller->getAll();
    return $response->getBody()->write($data);
});
$app->get('/upload', function (Request $request, Response $response, $args) {
    $controller = new ContrucstionController();
    $data = $controller->getAll();
    return $response->getBody()->write($data);
});
$app->run();
