<?php
namespace models;

class Role extends Model
{
    // 设置这个模型对应的表
    protected $table = 'role';
    // 设置允许接收的字段
    protected $fillable = ['role_name'];
    // 添加、修改角色之后自动被执行
    // 获取添加完之后的ID； $this->data['id']
    // 修改之后的id $_GET['id'];
    protected function _after_write()
    {
        $roleId = isset($_GET['id'])?$_GET['id']:$this->data['id'];
        $stmt = $this->_db->prepare("DELETE FROM role_privlege WHERE role_id=?");
        $stmt->execute([
            $roleId
        ]);
        $stmt = $this->_db->prepare("INSERT INTO role_privlege(pri_id,role_id) VALUES(?,?)");
        // 循环所有勾选的权限ID插入到中间表
        foreach($_POST['pri_id'] as $v)
        {
            $stmt->execute([
                $v,   
                $roleId,
            ]);
        }
    }

    protected function _before_delete(){
        $stmt = $this->_db->prepare("delete from role_privlege where role_id=?");
        $stmt->execute([
            $_GET['id']
        ]);
    }
    public function getPriIds($roleId){
        $stmt = $this->_db->prepare('SELECT pri_id FROM role_privlege WHERE role_id=?');
        $stmt->execute([
            $roleId
        ]);
        $data=$stmt->fetchAll(\PDO::FETCH_ASSOC);
        $_ret=[];
        foreach($data as $k=>$v){
            $_ret[] = $v['pri_id'];
        }
        return $_ret;
    }
}