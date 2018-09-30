<?php
namespace models;

class Goods extends Model
{
    // 设置这个模型对应的表
    protected $table = 'goods';
    // 设置允许接收的字段
    protected $fillable = ['goods_name','logo','is_on_sale','description','cat1_id','cat2_id','cat3_id','brand_id'];
    

    // 会在添加和修改之前被调用
    public function _before_write(){
        // 如果修改就删除原图片
        $this->_delete_logo();
         $uploader = \libs\Uploader::make();
         $logo='/uploads/'.$uploader->upload('logo','goods');
         $this->data['logo']=$logo;
    }

    // 在删除之前被调用
    public function _before_delete(){
        $this->_delete_logo();
    }

    public function _delete_logo(){
        // var_dump($_GET);
        // die;
        $id = $_GET['id'];
        
        if(isset($id)){
           // 从数据库取出原来的logo
           $logo = $this->findOne($id);
           // 删除
           @unlink(ROOT.'public'.$logo['logo']);
        }
    }

    public function _after_write(){
        // $data = "123123";
        // echo "<pre>";
        // var_dump($_POST);
        // die;
        $stmt = $this->_db->prepare("INSERT INTO goods_attribute(attr_name,attr_value,goods_id) VALUES(?,?,?) ");
        // 处理商品属性
        foreach($_POST['attr_name'] as $k=>$v){
            
            $stmt->execute([
                $v,
                $_POST['attr_value'][$k],
                $this->data['id'],
            ]);
            // $this->_db->insert([
            //     'attr_name'=>$v,
            //     'attr_value'=>$_POST['attr_value'][$k],
            //     'goods_id'=>$this->data['id'],
            // ],'goods_attribute');
        }

        $stmt = $this->_db->prepare("INSERT INTO goods_sku
                (goods_id,sku_name,stock,price) VALUES(?,?,?,?)");

        foreach($_POST['sku_name'] as $k => $v)
        {
            $stmt->execute([
                $this->data['id'],
                $v,
                $_POST['stock'][$k],
                $_POST['price'][$k],
            ]);
        
        
            // $this->_db->insert([
            //     'attr_name'=>$v,
            //     'attr_value'=>$_POST['attr_value'][$k],
            //     'goods_id'=>$this->data['id'],
            // ],'goods_attribute');
        }
        // var_dump($_FILES);
        // die;
        $uploader = \libs\uploader::make();
        $stmt = $this->_db->prepare("INSERT INTO goods_image
                (goods_id,path) VALUES(?,?)");
        $_tmp = [];
        foreach($_FILES['image']['name'] as $k=>$v){
            $_tmp['name'] = $v;
            $_tmp['type'] = $_FILES['image']['type'][$k];
            $_tmp['size'] = $_FILES['image']['size'][$k];
            $_tmp['tmp_name'] = $_FILES['image']['tmp_name'][$k];
            $_tmp['error'] = $_FILES['image']['error'][$k];
            // 放到$_FILES中
            $_FILES['tmp']=$_tmp;
            
            // 调用函数
            $path = '/uploads/'.$uploader->upload('tmp','goods');
            $stmt->execute([
                $this->data['id'],
                $path,
            ]);
        }
    }
}