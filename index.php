<?php
    require "Controller.php";
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: *');
    header('Access-Control-Allow-Methods: *');
    header('Access-Control-Allow-Credentials: true');
    header('Content-type: json/application');
    header("Access-Control-Allow-Methods: PUT");
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $request = explode( '/', $uri );
    $controller = new Controller();
    $controller->setRequest($request);
    $controller->processRequest();
?>