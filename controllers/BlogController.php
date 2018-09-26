<?php
namespace controllers;
class BlogController{
    public function index(){
        view('blog/index');
    }
    // 显示添加表单
    public function create(){
        view('blog/create');
    }
    //添加表单
    public function insert(){
           $blog = new \models\blog;
           $blog->fill($_POST);
           $blog->insert();
    }
    // 显示修改表单
    public function edit(){
        view('blog/edit');
    }
    // 修改表单
    public function update(){

    }
    public function delete(){

    }
}