<?php
    require "Controller.php";
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: *');
    header('Access-Control-Allow-Methods: *');
    header('Access-Control-Allow-Credentials: true');
    header('Content-type: json/application');
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $request = explode( '/', $uri );
    //var_dump($request);
    $controller = new Controller();
    $controller->setRequest($request);
    $response = $controller->processRequest();
?>