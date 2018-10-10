<?php
namespace models;

class Admin extends Model
{
    // 设置这个模型对应的表
    protected $table = 'admin';
    // 设置允许接收的字段
    protected $fillable = ['username','password'];

    public function _before_write(){
        $this->data['password'] = md5($this->data['password']);
    }
    public function _after_write(){
        $id = isset($_GET['id'])?$_GET['id']:$this->data['id'];
        // 如果修改
        $stmt = $this->_db->prepare("DELETE FROM admin_role WHERE admin_id=?");
        $stmt->execute([
            $id
        ]);
        // 重新添加新的勾选的角色
        $stmt = $this->_db->prepare("INSERT INTO admin_role(role_id,admin_id) VALUES(?,?) ");
        foreach($_POST['role_id'] as $v){
            $stmt->execute([
                
                $v,
                $id,
         ]);
        }
        
    }
    public function login($username,$password){
        $stmt = $this->_db->prepare("SELECT * FROM admin WHERE username=? AND password=?");
        // var_dump($stmt);die;
        $stmt->execute([
            $username,
            md5($password),
        ]);
        $info=$stmt->fetch(\PDO::FETCH_ASSOC);
        
        
        // var_dump($info);die;
        if($info){
          $_SESSION['id'] = $info['id'];
          $_SESSION['username'] = $info['username'];

        //查看该管理员是否是超级管理员
        $stmt = $this->_db->prepare('SELECT COUNT(*) FROM admin_role WHERE role_id=1 AND admin_id=?');
        $stmt->execute([$_SESSION['id']]);
        $c = $stmt->fetch(\PDO::FETCH_COLUMN);
        if($c>0){
            $_SESSION['root'] = true;

        }
        else
          $_SESSION['url_path'] = $this->getUalPath($_SESSION['id']);
         
        }else{
            throw new \Exception("用户名或密码错误");
        }
    }
    public function logout(){
        $_SESSION = [];
        session_destroy();
    }

    public function getUalPath($adminId){
        $sql = "SELECT c.url_path 
                FROM admin_role a
                LEFT JOIN role_privlege b ON a.role_id=b.role_id
                LEFT JOIN privilege c ON b.pri_id=c.id 
                WHERE a.admin_id = 7 AND C.url_path!=''";
        $stmt = $this->_db->prepare($sql);
        $stmt->execute([
            $adminId,
        ]);
        $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        // var_dump($data);die;
        // 把二维数组转为一维数组
        $_ret = [];
        foreach($data as $v){
            
            if(FALSE === strpos($v['url_path'],',')){
                 // 如果没有,，就直接拿过来
                 $_ret[] = $v['url_path'];
            }else{
                //  如果有就直接转为数组
                $_tt = explode(',',$v['url_path']);
                // 把转换完的数组合并到一维数组中
                $_ret = array_merge($_ret,$_tt);
            }
            
        }
        return $_ret;
    }
}