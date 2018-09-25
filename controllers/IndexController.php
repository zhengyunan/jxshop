<?php
namespace controllers;

class IndexController{
    public function top(){
        view('index/top');
    }
    public function main(){
        view('index/main');
    }
    public function menu(){
        view('index/menu');
    }
    public function index(){
        view('index/index');
    }

}