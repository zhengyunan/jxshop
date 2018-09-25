<?php

define('POOT',__DIR__.'/../');


// 类的自动加载
function load($class){
    $path = str_repeat('\\','/',$class);
    require(ROOT . $path . '.php');
}

spl_autoload_register('load');


// 路由解析
$controller = '\controllers\IndexController';
$active = 'index';
if(isset($_SERVER['PATH_INFO'])){
    $router = explode('/',$_SERVER['PATH_INFO']);
    $controller = '\controller\\'.ucfirst($router[1]).'Controller';
    $action = $router[2];
}

$c = new $controller;
$c->$active();