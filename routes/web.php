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
$router->group(['middleware' => 'center'], function () use ($router) {
    $router->get('/user_center',['uses'=>'IndexController@center']);
});
//商品列表
$router->get('/goods','GoodsController@goods');
//商品详情
$router->post('/detail','GoodsController@detail');
//购物车
$router->post('/cart','CartController@cart');
//购物车列表
$router->get('/cartlist','CartController@cartlist');
//商品结算生成订单
$router->post('/order','OrderController@order');
//订单列表
$router->get('/orderlist','OrderController@orderlist');
//支付页面
$router->get('/ali_pay','PayController@ali_pay');
//支付异步回调
$router->get('/notify_url','PayController@notify_url');
//支付同步回调
$router->get('/return_url','PayController@return_url');
