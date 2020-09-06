<?php

use Api\Controller\PersonController;

require_once __DIR__ . '/../bootstrap.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$url = $_SERVER['REQUEST_URI'];
$uri = parse_url($url, PHP_URL_PATH);
$uriParts = explode('/', $uri);
$queryParts = explode('&', parse_url($url, PHP_URL_QUERY));

if ('persons' !== $uriParts[1]) {
    header('HTTP/1.1 404 Not Found');
    exit;
}

$userId = $uriParts[2] ?? null;
if (null !== $userId) {
    $userId = (int)$userId;
}
$requestMethod = $_SERVER['REQUEST_METHOD'];
$controller = new PersonController($connection, $requestMethod, $userId);
$controller->processRequest();