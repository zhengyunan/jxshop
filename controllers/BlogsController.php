<?php
namespace controllers;
class BlogsController{
    public function index(){
        view('blogs/index');
    }
    // 显示添加表单
    public function create(){
        view('blogs/create');
    }
    //添加表单
    public function insert(){

    }
    // 显示修改表单
    public function edit(){
        view('blogs/edit');
    }
    // 修改表单
    public function update(){

    }
    public function delete(){

    }
}