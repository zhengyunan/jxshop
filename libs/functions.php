<?php

// 加载视图
function view($file,$data=[]){
    // 压缩数组
    extract($data);
    include(ROOT.'views/'.$file.'.html');
}

function redirect($url)
{
    header('Location:'.$url);
    exit;
}