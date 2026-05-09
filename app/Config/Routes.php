<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'HomeController::login');

$routes->post('/imc', 'UserController::IMCresult');

$routes->get('/profile', 'UserController::userProfile');