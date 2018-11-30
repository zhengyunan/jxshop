<?php
require('./vendor/autoload.php');
use Qiniu\Storage\UploadManager;
use Qiniu\Auth;
    $pdo = new \PDO('mysql:host=127.0.0.1;dbname=jxshop', 'root','990607');
    $client = new \Predis\Client([
        'scheme'=>'tcp',
        'host'=>'localhost',
        'port'=>6379,
    ]);
    // 设置 socket 永不超时
    ini_set('default_socket_timeout', -1); 
    // 上传七牛云
    $accessKey = 'x13yAQwYC6FGVO4s2p7F_w8mhA8SSnk9SP9Vvi4i';
    $secretKey = 'L1WeGBuK2cgRxtlZuDyGrUnZOaVdKVEt3TTycM1t';
    $domain = 'http://piy4kjpnu.bkt.clouddn.com';
    // 配置参数
    $bucketName = 'vue-shop';   // 创建的 bucket(新建的存储空间的名字)

    $upManager = new UploadManager();

    // 登录获取令牌
    $expire = 86400 * 3650; // 令牌过期时间10年
    $auth = new Auth($accessKey, $secretKey);
    $token = $auth->uploadToken($bucketName, null, $expire);
    while(true){
       // 从队列中取数据，设置为永久不超时（如果队列里面是空的，就一直阻塞在这）
       $rawdata = $client->brpop('jxshop:niqui',0);
       //处理数据
       $data = unserialize($rawdata[1]); // 转成数组
    //    var_dump($data);die;
       //获取文件名
       $name = ltrim(strrchr($data['logo'],'/'),'/');
       // 上传的文件
       $file = './public'.$data['logo'];  
       list($ret, $error) = $upManager->putFile($token, $name, $file);
       // 判断是否成功
       if($error !== null) {
            $client->lpush('jxshop:niqui', $rawdata[1]); 
       }else {
            // 更新数据库
            $new = $domain.'/'.$ret['key'];
            $sql = "UPDATE ".$data['table']." SET ".$data['column']."='$new' WHERE id=".$data['id'];
            $pdo->exec($sql);
            // 删除本地文件
            @unlink($file);
            echo 'ok';
       }
    }
