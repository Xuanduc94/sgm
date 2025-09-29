<?php
error_reporting(0);

use controllers\ContrucstionController;
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
include_once 'controllers/ContrucstionController.php';

$app->get('/all', function (Request $request, Response $response, $args) {
    $controller = new ContrucstionController();
    $data = $controller->getAll();
    $response->getBody()->write(json_encode($data));
    return $response;
});
$app->post('/upload', function (Request $request, Response $response, $args) {
    // $file = $request->getUploadedFiles();
    // $controller = new ContrucstionController();
    // $data = $controller->getAll();
    $response->getBody()->write("Test");
    return $response;
});
$app->run();
