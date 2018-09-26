<?php
namespace controllers;
class Controller{
    public function index(){
        view('/index');
    }
    // 显示添加表单
    public function create(){
        view('/create');
    }
    //添加表单
    public function insert(){

    }
    // 显示修改表单
    public function edit(){
        view('/edit');
    }
    // 修改表单
    public function update(){

    }
    public function delete(){

    }
}