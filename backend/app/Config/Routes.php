<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// ─────────────────────────────────────────────────────────────────────────────
// Global options — all routes return JSON, no redirect on 404
// ─────────────────────────────────────────────────────────────────────────────
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override(function () {
    $request = service('request');
    $response = service('response');
    $path = trim((string) $request->getPath(), '/');
    $isApiRoute = $path === 'api' || str_starts_with($path, 'api/');

    if ($isApiRoute) {
        $response->setStatusCode(404);
        $response->setContentType('application/json');

        return json_encode([
            'status' => false,
            'message' => 'The requested API endpoint was not found.',
            'data' => null,
        ], JSON_UNESCAPED_SLASHES);
    }

    $response->setStatusCode(404);
    $response->setContentType('text/html');

    return view('errors/html/error_404', [
        'message' => 'The page you are looking for could not be found.',
    ]);
});

$routes->options('(:any)', function () {
    return response()->setStatusCode(200);
});

// ─────────────────────────────────────────────────────────────────────────────
// Public Routes (no auth needed)
// ─────────────────────────────────────────────────────────────────────────────
$routes->get('/', 'WebController::home');
$routes->get('about', 'WebController::about');
$routes->get('articles', 'WebController::articles');
$routes->get('articles/(:segment)', 'WebController::articleDetail/$1');
$routes->group('api', function($routes) {

    // Auth
    $routes->group('auth', function ($routes) {
        $routes->post('login',  'AuthController::login');
        $routes->post('logout', 'AuthController::logout');
    });

    // log
    $routes->post('log', 'LogController::create'); // untuk log sisi client / FE

    // ─────────────────────────────────────────────────────────────────
    // Protected Routes — requires valid Bearer token
    // ─────────────────────────────────────────────────────────────────
    $routes->group('', ['filter' => 'auth'], function($routes) {
        
        // Access
        $routes->group('access', function ($routes) {
            $routes->get('menu',            'AccessController::menu');
            $routes->get('control/(:any)',  'AccessController::control/$1');
        });

        // Dropdown
        $routes->group('dropdown', function ($routes) {
            $routes->get('role', 'DropdownController::role');
            $routes->get('user', 'DropdownController::user');
        });

        // User
        $routes->group('user', function ($routes) {
            $routes->post('/',          'UserController::create');
            $routes->get('/',           'UserController::index');
            $routes->get('(:any)',      'UserController::detail/$1');
            $routes->put('(:any)',      'UserController::edit/$1');
            $routes->delete('(:any)',   'UserController::destroy/$1');
        });

        // Role
        $routes->group('role', function ($routes) {
            $routes->post('/',          'RoleController::create');
            $routes->get('/',           'RoleController::index');
            $routes->get('(:any)',      'RoleController::detail/$1');
            $routes->put('(:any)',      'RoleController::edit/$1');
            $routes->delete('(:any)',   'RoleController::destroy/$1');
        });
    
        // Parameter / app support
        $routes->group('parameter', function ($routes) {
            $routes->post('/',          'AppSupportController::create');
            $routes->get('/',           'AppSupportController::index');
            $routes->get('(:any)',      'AppSupportController::detail/$1');
            $routes->put('(:any)',      'AppSupportController::edit/$1');
            $routes->delete('(:any)',   'AppSupportController::destroy/$1');
        });

        // Menu
        $routes->group('menu', function ($routes) {
            $routes->post('/',          'MenuController::create');
            $routes->get('/',           'MenuController::index');
            $routes->get('(:any)',      'MenuController::detail/$1');
            $routes->put('(:any)',      'MenuController::edit/$1');
            $routes->delete('(:any)',   'MenuController::destroy/$1');
        });

        // Menu Control
        $routes->group('menu-control', function ($routes) {
            $routes->post('/',          'MenuControlController::create');
            $routes->get('(:any)',      'MenuControlController::index/$1');
            $routes->put('(:any)',      'MenuControlController::edit/$1');
            $routes->delete('(:any)',   'MenuControlController::destroy/$1');
        });

        // Role Menu Control
        $routes->group('role-menu-control', function ($routes) {
            $routes->post('/',          'RoleMenuControlController::create');
            $routes->get('(:any)',      'RoleMenuControlController::index/$1');
        });

        // Log
        $routes->group('log', function ($routes) {
            $routes->get('/',       'LogController::index');
            $routes->delete('/',    'LogController::clear');
        });
    });
});
