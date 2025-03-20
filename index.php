<?php

header("Access-Control-Allow-Origin: *"); // Permite requisições de qualquer origem
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS"); // Permite métodos HTTP
header("Access-Control-Allow-Headers: Content-Type, Authorization"); // Permite headers necessários

// Se for uma requisição OPTIONS (preflight), responder e sair
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}
require_once __DIR__ ."/vendor/autoload.php";
require_once __DIR__ ."/src/routes/main.php";

use App\core\core;
use App\http\route;

core::dispatch(route::Routes());
