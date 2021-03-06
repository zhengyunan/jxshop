<?php
namespace models;
use PDO;
class Model{
    protected $_db;
    // 操作的表单 具体的值由子类确定
    protected $table;
    // 操作的数据 具体的值由子类确定
    protected $data;

    public function __construct(){
         $this->_db = \libs\Db::make();
    }


    protected function _before_write(){}
    protected function _after_write(){}
    protected function _before_delete(){}
    protected function _after_delete(){}
    
    public function insert(){
        
        // // 取出数组中所有的键  组成新的数组
        // $keys = array_keys($this->data);
        // // 将新生成的数组转为字符串
        // $keys = implode(',',$keys);
        // // 取出数组中所有的键  组成新的数组
        // $values = array_values($this->data);
        // // 将新生成的数组转为字符串
        // $values = implode("','",$values);
        $this->_before_write();
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
        // var_dump($sql);die;
        $stmt = $this->_db->prepare($sql);
        // var_dump($stmt);die;
        $stmt->execute($values);
         
        $this->data['id']= $this->_db->lastInsertId();
        $this->_after_write();
        // die;
        // $this->_db->exec($sql);
    }
    public function update($id){
        $this->_before_write();

        $set = [];
        $token = [];

        foreach($this->data as $k => $v)
        {
            $set[] = "$k=?";
            $values[] = $v;
            $token[] = '?';
        }

        $set = implode(',', $set);

        $values[] = $id;

        $sql = "UPDATE {$this->table} SET $set WHERE id=?";
        // var_dump($sql);die;
        $stmt = $this->_db->prepare($sql);
        $stmt->execute($values);
        $this->_after_write();
    }
    public function delete($id){
        $this->_before_delete();
        $stmt = $this->_db->prepare("DELETE FROM {$this->table} WHERE id=?");
        $stmt->execute([$id]);
        $this->_after_delete();
    }
    public function findAll($options = []){
        $_option = [
            'fields' => '*',
            'where' => 1,
            'order_by' => 'id',
            'order_way' => 'desc',
            'per_page'=>20,
            'join'=>'',
            'groupby'=>'',
        ];
 
        // 合并用户的配置
        if($options)
        {
            $_option = array_merge($_option, $options);
        }

        /**
         * 翻页
         */
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $offset = ($page-1)*$_option['per_page'];
        
        $sql = "SELECT {$_option['fields']}
                 FROM {$this->table}
                 {$_option['join']}
                 WHERE {$_option['where']} 
                 {$_option['groupby']}
                 ORDER BY {$_option['order_by']} {$_option['order_way']} 
                 LIMIT $offset,{$_option['per_page']}";

        $stmt = $this->_db->prepare($sql);
        $stmt->execute();
        $data = $stmt->fetchAll( PDO::FETCH_ASSOC );

        /**
         * 获取总的记录数
         */
        $stmt = $this->_db->prepare("SELECT COUNT(*) FROM {$this->table} WHERE {$_option['where']}");
        $stmt->execute();
        $count = $stmt->fetch( PDO::FETCH_COLUMN );
        $pageCount = ceil($count/$_option['per_page']);

        $page_str = '';
        if($pageCount>1)
        {
            for($i=1;$i<=$pageCount;$i++)
            {
                $page_str .= '<a href="?page='.$i.'">'.$i.'</a> ';
            }
        }
        

        return [
            'data' => $data,
            'page' => $page_str,
        ];
    }
    public function findOne($id){
        $stmt = $this->_db->prepare("SELECT * FROM {$this->table} WHERE id=?");
        $stmt->execute([$id]);
        return $stmt->fetch( PDO::FETCH_ASSOC );
    }
    public function fill($data){
        foreach($data as $k=>$v){
            if(!in_array($k,$this->fillable)){
                unset($data[$k]);
            }
        }
        $this->data = $data;
        // var_dump($this->data);
    }

    // 递归排序c
    // 参数1  排序的数据
    // 参数2  上级的id
    // 参数3  第几级
    protected function _tree($data,$parent_id=0,$level=0){
        static $_ret = [];
        foreach($data as $v){
            if($v['parent_id']==$parent_id){
                // 标签他的级别
                $v['level'] = $level;
                // 挪到排列之后的数组中
                $_ret[]=$v;
                // 找到$v的子分类
                $this->_tree($data,$v['id'],$level+1);
            }
        }
        return $_ret;
    }
}