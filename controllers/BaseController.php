<?php
namespace controllers;
class BaseController{
    public function __construct(){
        if(!isset($_SESSION['id']))
        {  
           
            redirect('/login/login');
        }

        // var_dump($_SESSION['url_path']);
    }
}