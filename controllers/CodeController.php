<?php
namespace controllers;
class CodeController{
    

    // 代码生成
    public function make(){
        // 1接收参数
        $tableName = $_GET['name'];
        // 2 生成控制器
        $cname = ucfirst($tableName).'Controller';
        //  echo $cname;
         // 加载模板
        ob_start();
        include(ROOT.'templates/controller.php');
        $str = ob_get_clean();
        file_put_contents(ROOT.'controllers/'.$cname.'.php',"<?php\r\n".$str);



        // 3生成模板
        $mname = ucfirst($tableName);
        //  echo $cname;
         // 加载模板
        ob_start();
        include(ROOT.'templates/model.php');
        $str = ob_get_clean();
        file_put_contents(ROOT.'models/'.$mname.'.php',"<?php\r\n".$str);
        

        // 4生成试图文件
        // 生成试图目录
        @mkdir(ROOT . 'views/'.$tableName, 0777);
        // 取出这个表中所有字段信息
        $sql = "SHOW FULL FIELDS FROM $tableName";
        $db = \libs\Db::make();
        // 预处理
        $stmt = $db->prepare($sql);
        // 执行语句
        $stmt->execute();
        // 取出字段数据 
        $fields = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        // echo '<pre>';
        // var_dump($fields);
        // create.html
        ob_start();
        include(ROOT . 'templates/create.html');
        $str = ob_get_clean();
        file_put_contents(ROOT.'views/'.$tableName.'/create.html', $str);
        // edit.html
        ob_start();
        include(ROOT . 'templates/edit.html');
        $str = ob_get_clean();
        file_put_contents(ROOT.'views/'.$tableName.'/edit.html', $str);
        // index.html
        ob_start();
        include(ROOT . 'templates/index.html');
        $str = ob_get_clean();
        file_put_contents(ROOT.'views/'.$tableName.'/index.html', $str);

    }
    
}