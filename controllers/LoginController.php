<?php
namespace controllers;
use models\Admin;
class LoginController {
    public function login(){
        view('login/login');
    }
    public function dologin(){
        $username = $_POST['username'];
        $password = $_POST['password'];
        // var_dump($_POST);die;
        $a = $model = new Admin;
        
        try{
            $a = $model->login($username,$password);
            // var_dump($a);
            // die;
            // echo "123";die;
            redirect('/');
        }catch(\Exception $e){
            // echo "asc";
            redirect('/login/login');
        }
    }
    public function logout(){
        $model = new Admin;
        $model->logout();
        redirect('/login/login');
    }
}