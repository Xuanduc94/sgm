<?php

// Tự động load các file cần thiết
spl_autoload_register(function ($class) {
    if (file_exists("core/$class.php")) {
        require "core/$class.php";
    } elseif (file_exists("controllers/$class.php")) {
        require "controllers/$class.php";
    } elseif (file_exists("models/$class.php")) {
        require "models/$class.php";
    }
});

// Lấy controller và action từ URL, mặc định: UserController@index
$controllerName;
$actionName;
if (isset($_GET['c']) && isset($_GET['a'])) {
    $controllerName = $_GET['c'];
    $actionName = $_GET['a'];
    $controllerClass = $controllerName . "Controller";
    $controller = new $controllerClass;
    $controller->$actionName();
} else {
    echo "SGM API version 1.0";
}
