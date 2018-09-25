<?php
namespace controllers;
class categoryController{
    public function index(){
        view('category/index');
    }
    // 显示添加表单
    public function create(){
        view('category/create');
    }
    //添加表单
    public function insert(){

    }
    // 显示修改表单
    public function edit(){
        view('category/edit');
    }
    // 修改表单
    public function update(){

    }
    public function delete(){

    }
}