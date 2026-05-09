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

$routes->get('/unauthorized', 'HomeController::unauthorized');

$routes->get('/imc', 'UserController::imcForm', ['filter' => 'auth']);
$routes->post('/imc', 'UserController::IMCresult', ['filter' => 'auth']);
$routes->get('/profile', 'UserController::userProfile', ['filter' => 'auth']);
$routes->post('/upgrade/gold', 'UserController::upgradeToGold', ['filter' => 'auth']);

$routes->group('admin', ['filter' => 'admin'], static function ($routes) {
	$routes->get('regimes', 'Admin\RegimeController::index');
	$routes->get('regimes/new', 'Admin\RegimeController::create');
	$routes->post('regimes', 'Admin\RegimeController::store');
	$routes->get('regimes/(:num)', 'Admin\RegimeController::show/$1');
	$routes->get('regimes/(:num)/edit', 'Admin\RegimeController::edit/$1');
	$routes->post('regimes/(:num)/update', 'Admin\RegimeController::update/$1');
	$routes->post('regimes/(:num)/delete', 'Admin\RegimeController::delete/$1');

	$routes->get('sports', 'Admin\SportController::index');
	$routes->get('sports/new', 'Admin\SportController::create');
	$routes->post('sports', 'Admin\SportController::store');
	$routes->get('sports/(:num)', 'Admin\SportController::show/$1');
	$routes->get('sports/(:num)/edit', 'Admin\SportController::edit/$1');
	$routes->post('sports/(:num)/update', 'Admin\SportController::update/$1');
	$routes->post('sports/(:num)/delete', 'Admin\SportController::delete/$1');
});
