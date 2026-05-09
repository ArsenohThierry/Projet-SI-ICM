<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'AuthController::login');

$routes->get('/login', 'AuthController::login');
$routes->post('/login', 'AuthController::authenticate');
$routes->post('/register', 'AuthController::register');
$routes->get('/logout', 'AuthController::logout');

$routes->get('/imc', 'UserController::getIMC', ['filter' => 'auth']);
$routes->post('/imc', 'UserController::IMCresult', ['filter' => 'auth']);
$routes->get('/profile', 'UserController::userProfile', ['filter' => 'auth']);
$routes->post('/upgrade/gold', 'UserController::upgradeToGold', ['filter' => 'auth']);
