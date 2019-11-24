<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

/** @var Router $router */

use Laravel\Lumen\Routing\Router;

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix' => 'vk-callback', 'middleware' => 'vk-callback-auth'], function(Router $router) {
    $router->post('/', 'VkCallbackController');
    $router->get('/', 'VkCallbackController');
});
