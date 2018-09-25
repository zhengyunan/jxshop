<?php
namespace controllers;
class GoodsController{
    public function index(){
        view('goods/index');
    }
    // 显示添加表单
    public function create(){
        view('goods/create');
    }
    //添加表单
    public function insert(){

    }
    // 显示修改表单
    public function edit(){
        view('goods/edit');
    }
    // 修改表单
    public function update(){

    }
    public function delete(){

    }
}