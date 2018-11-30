<?php
session_start();
// phpinfo();
define('ROOT',__DIR__.'/../');

// 引入函数文件
require(ROOT.'libs/functions.php');

// 类的自动加载
function load($class){
    $path = str_replace('\\','/',$class);
    require(ROOT . $path . '.php');
}

spl_autoload_register('load');
require(ROOT.'vendor/autoload.php');

// 路由解析
$controller = '\controllers\IndexController';
$action ='index';

if(isset($_SERVER['PATH_INFO'])){

    $router = explode('/',$_SERVER['PATH_INFO']);
    $controller = '\controllers\\'.ucfirst($router[1]).'Controller';
    $action = $router[2];
}

$c = new $controller;
$c->$action();


// $memcache = new memcache; 
// //  创建一个Memcache对象  
// $memcache -> connect('127.0.0.1', 11211) or die("连接失败"); 
// // echo $memcache->getVersion();
// $memcache -> set('name', ['张三'=>13,'李四'=>15]); 
// $val = $memcache->get('name');
// print_r($val); 
// // $memcache->flush_all();
// $memcache -> close(); 