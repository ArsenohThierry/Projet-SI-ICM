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

$routes->get('/imc', 'UserController::getIMC', ['filter' => 'auth']);
$routes->get('/objectif', 'UserController::objectifUser', ['filter' => 'auth']);
$routes->post('/objectif', 'UserController::setObjectif', ['filter' => 'auth']);
$routes->get('/regime', 'UserController::regimeSelection', ['filter' => 'auth']);
$routes->post('/regime', 'UserController::setRegime', ['filter' => 'auth']);
$routes->get('/sport', 'UserController::sportSelection', ['filter' => 'auth']);
$routes->post('/sport', 'UserController::setSport', ['filter' => 'auth']);
$routes->get('/programme', 'UserController::programme', ['filter' => 'auth']);
$routes->get('/profile', 'UserController::userProfile', ['filter' => 'auth']);
$routes->get('/profile/export-pdf', 'UserController::exportPdf', ['filter' => 'auth']);

$routes->get('/codes/redeem', 'CodeController::redeemForm', ['filter' => 'user']);
$routes->post('/codes/redeem', 'CodeController::redeem', ['filter' => 'user']);
$routes->get('/codes/redeem-register', 'CodeController::redeemRegisterForm', ['filter' => 'user']);
$routes->post('/codes/redeem-register', 'CodeController::redeemRegister', ['filter' => 'user']);

$routes->get('/dashboard', 'DashboardController::showDashboard', ['filter' => 'auth']);
$routes->post('/dashboard/add-poids', 'DashboardController::addPoids', ['filter' => 'auth']);

$routes->get('/api/balance', 'UserController::apiBalance', ['filter' => 'auth']);

$routes->get('/abonnement', 'UserController::pageAbonnement', ['filter' => 'auth']);
$routes->post('/abonnement/gold', 'UserController::upgradeToGold', ['filter' => 'auth']);

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

	$routes->get('codes', 'Admin\CodeController::index');
	$routes->get('codes/new', 'Admin\CodeController::create');
	$routes->post('codes', 'Admin\CodeController::store');
	$routes->post('codes/(:num)/delete', 'Admin\CodeController::delete/$1');
});
