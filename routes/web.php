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

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->get('/info', function () use ($router) {
    phpinfo();
});

$router->post('/index','IndexController@index');
//注册
$router->post('/reg','IndexController@reg');
$router->post('/register','IndexController@register');
//登录
$router->post('/login','IndexController@login');
$router->post('/logindo','IndexController@logindo');
//个人中心
$router->get('/user_center','IndexController@center');