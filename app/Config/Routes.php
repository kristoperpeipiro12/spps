<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->group('admin', function ($routes) {
    $routes->get('login', 'Admin\Admin::login');
    $routes->post('login', 'Admin\Admin::login');
    $routes->add('sukses', 'Admin\Admin::sukses');
    $routes->add('logout', 'Admin\Admin::logout');
    $routes->add('lupapassword', 'Admin\Admin::lupapassword');
    $routes->add('resetpassword', 'Admin\Admin::resetpassword');
});
// $routes->get('index', 'Artikel::index');
// File: app/Config/Routes.php

$routes->group('admin', ['namespace' => 'App\Controllers\Admin'], function ($routes) {
    $routes->get('artikel', 'Artikel::index');
});