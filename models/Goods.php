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
        // 如果上传了新图片
        if($_FILES['logo']['error']==0){

        // 如果修改就删除原图片
        $this->_delete_logo();
         $uploader = \libs\Uploader::make();
         $logo='/uploads/'.$uploader->upload('logo','goods');
         $this->data['logo']=$logo;
    }
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
        //    构造数据
        $data = [
            'logo'=>$this->data['logo'],
            'id'=>$this->data['id'],
            'table'=>'goods',
            'column'=>'logo'
         ];
          // 把消息放到消息队列里面
          $client = new \Predis\Client([
              'scheme'=>'tcp',
              'host'=>'localhost',
              'port'=>6379,
          ]);
          $client->lpush('jxshop:niqui',serialize($data));


        $goodsId = isset($_GET['id'])?$_GET['id']:$this->data['id'];
        // 先删除原来的属性
        $stmt=$this->_db->prepare("DELETE FROM goods_attribute WHERE goods_id=?");
        $stmt->execute([$goodsId]);
        $stmt = $this->_db->prepare("INSERT INTO goods_attribute(attr_name,attr_value,goods_id) VALUES(?,?,?) ");
        // 处理商品属性
        foreach($_POST['attr_name'] as $k=>$v){
            
            $stmt->execute([
                $v,
                $_POST['attr_value'][$k],
                $goodsId,
            ]);
            // $this->_db->insert([
            //     'attr_name'=>$v,
            //     'attr_value'=>$_POST['attr_value'][$k],
            //     'goods_id'=>$this->data['id'],
            // ],'goods_attribute');
        }
        $stmt=$this->_db->prepare("DELETE FROM goods_sku WHERE goods_id=?");
        $stmt->execute([$goodsId]);
        $stmt = $this->_db->prepare("INSERT INTO goods_sku
                (goods_id,sku_name,stock,price) VALUES(?,?,?,?)");

        foreach($_POST['sku_name'] as $k => $v)
        {
            $stmt->execute([
                $goodsId,
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
        if(isset($_POST['del_image'])&&$_POST['del_image']!=''){
            // 现根据id把图片路径取出来
        $stmt=$this->_db->prepare("SELECT path FROM goods_image WHERE id IN({$_POST['del_image']})");
        $stmt->execute();
        $path = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        // 循环每个图片路径删除
        foreach($path as $v){
            @unlink(ROOT.'public/'.$v['path']);
        }
        // 从数据库把图片删除
        $stmt = $this->_db->prepare("DELETE FROM goods_image WHERE id IN({$_POST['del_image']})");
        $stmt->execute();
        }
        $uploader = \libs\uploader::make();
        $stmt = $this->_db->prepare("INSERT INTO goods_image
                (goods_id,path) VALUES(?,?)");
        $_tmp = [];
        foreach($_FILES['image']['name'] as $k=>$v){
            if($_FILES['image']['error'][$k]==0){
         
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
                $goodsId,
                $path,
            ]);
            $id = $this->_db->lastInsertId();
            echo $id;
            $client->lpush('jxshop:niqui',serialize([
                'logo'=>$path,
                'id'=>$id,
                'table'=>'goods_image',
                'column'=>'path'
            ]));
        }
               
    }
    }

    public function getFullInfo($id){
        // 获取商品的基本信息
       $info= $this->findOne($id);
        // 获取商品属性信息
       $stmt = $this->_db->prepare("SELECT * FROM goods_attribute WHERE goods_id=?");
       $stmt->execute([$id]);
       $attrs = $stmt->fetchAll(\PDO::FETCH_ASSOC);
       // 获取商品图片
       $stmt = $this->_db->prepare("SELECT * FROM goods_image WHERE goods_id=?");
       $stmt->execute([$id]);
       $images = $stmt->fetchAll(\PDO::FETCH_ASSOC);
           // 获取商品sku
       $stmt = $this->_db->prepare("SELECT * FROM goods_sku WHERE goods_id=?");
       $stmt->execute([$id]);
       $skus = $stmt->fetchAll(\PDO::FETCH_ASSOC);
       
    //    返回所有数据
    return [
        'info'=>$info,
        'images'=>$images,
        'skus'=>$skus,
        'attrs'=>$attrs
    ];
    }
}