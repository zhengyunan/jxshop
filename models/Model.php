<?php
namespace models;

class Model{
    protected $_db;
    // 操作的表单 具体的值由子类确定
    protected $table;
    // 操作的数据 具体的值由子类确定
    protected $data;

    public function __construct(){
         $this->_db = \libs\Db::make();
    }

    public function insert(){
        // // 取出数组中所有的键  组成新的数组
        // $keys = array_keys($this->data);
        // // 将新生成的数组转为字符串
        // $keys = implode(',',$keys);
        // // 取出数组中所有的键  组成新的数组
        // $values = array_values($this->data);
        // // 将新生成的数组转为字符串
        // $values = implode("','",$values);
        $keys=[];
        $values = [];
        $token = [];
        foreach($this->data as $k=>$v){
            $keys[] = $k;
            $values[] = $v;
            $token[] = '?';
        }
         $keys = implode(',',$keys);
         $token = implode(',',$token);
        $sql = "INSERT INTO {$this->table}($keys) VALUES($token)";
        // var_dump($sql);
        $stmt = $this->_db->prepare($sql);
        return $stmt->execute($values);
        // $this->_db->exec($sql);
    }
    public function update(){

    }
    public function delete(){

    }
    public function findAll(){

    }
    public function findOne(){

    }
    public function fill($data){
        foreach($data as $k=>$v){
            if(!in_array($k,$this->fillable)){
                unset($data[$k]);
            }
        }
        $this->data = $data;
    }
}