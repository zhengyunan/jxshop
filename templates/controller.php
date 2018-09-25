namespace controllers;
class <?=$cname?>{
    public function index(){
        view('<?=$tableName?>/index');
    }
    // 显示添加表单
    public function create(){
        view('<?=$tableName?>/create');
    }
    //添加表单
    public function insert(){

    }
    // 显示修改表单
    public function edit(){
        view('<?=$tableName?>/edit');
    }
    // 修改表单
    public function update(){

    }
    public function delete(){

    }
}