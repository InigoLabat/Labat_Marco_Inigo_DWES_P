<?php

require_once __DIR__ . '/../app/core/Router.php';
require_once __DIR__ . '/../config/conf.php';

//Controlador frontal
// Recibimos la solicitud y la gestionamos llamando al metodo match del Router

// Nos cinstanciamos un objeto router
$router = new Router();

$router->match($_SERVER['REQUEST_URI']);
