<?php

require "../src/bootstrap.php";

use Src\Controller\NodeController;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode('/', $uri);

if ($uri[1] !== 'nodes') {
    header('Location: index.html');

    exit();
}

$nodeId = null;

if (isset($uri[2])) {
    $nodeId = (int) $uri[2];
}

$requestMethod = $_SERVER["REQUEST_METHOD"];

$controller = new NodeController($databaseConnection, $requestMethod, $nodeId);
$controller->processRequest();

