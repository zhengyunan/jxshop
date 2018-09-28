<?php
namespace models;

class Brand extends Model
{
    // 设置这个模型对应的表
    protected $table = 'brand';
    // 设置允许接收的字段
    protected $fillable = ['brand_name','logo'];
    
    // 会在添加和修改之前被调用
    public function _before_write(){
        // 如果修改就删除原图片
        $this->_delete_logo();
         $uploader = \libs\Uploader::make();
         $logo='/uploads/'.$uploader->upload('logo','brand');
         $this->data['logo']=$logo;
    }

    // 在删除之前被调用
    public function _before_delete(){
        $this->_delete_logo();
    }

    public function _delete_logo(){
        $id = $_GET['id'];
        if(isset($id)){
           // 从数据库取出原来的logo
           $logo = $this->findOne($id);
           // 删除
           @unlink(ROOT.'public'.$logo['logo']);
        }
    }
}

