<?php

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  header('Access-Control-Allow-Origin: *');
  header('Access-Control-Allow-Methods: POST, GET, DELETE, PUT, PATCH, OPTIONS');
  header('Access-Control-Allow-Headers: token, Content-Type');
  header('Access-Control-Max-Age: 1728000');
  header('Content-Length: 0');
  header('Content-Type: text/plain');
  die();
}
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, DELETE, PUT, PATCH, OPTIONS');
header('Content-Type: application/json');


$arrayRutas = explode("/", $_SERVER['REQUEST_URI']);

if(count(array_filter($arrayRutas)) == 2){

  if(array_filter($arrayRutas)[1] == "bluetech"){
    require_once __DIR__."/bluetech/Controllers/RutasController.php";
    $router = new controllers\RutasBluetechController();
    $router->index();
  }

  if(array_filter($arrayRutas)[1] == "istore"){
    require_once __DIR__."/istore/Controllers/RutasController.php";
    $router = new controllers\RutasBluetechController();
    $router->index();
  }

  if(array_filter($arrayRutas)[1] == "istore_two"){
  require_once __DIR__."/istore_two/Controllers/RutasController.php";
    $router = new controllers\RutasBluetechController();
    $router->index();
  }

  if(array_filter($arrayRutas)[1] == "istore_three"){
    require_once __DIR__."/istore_three/Controllers/RutasController.php";
    $router = new controllers\RutasBluetechController();
    $router->index();
  }

  if(array_filter($arrayRutas)[1] == "infotechcorp"){
    require_once __DIR__."/infotechcorp/Controllers/RutasController.php";
    $router = new controllers\RutasInfotechcorpController();
    $router->index();
  }
}
