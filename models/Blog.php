<?php
namespace models;

class Blog extends model{

    protected $table = 'blog';
    protected $fillable = ['title','content','is_show'];
}