<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;

class Common extends Controller
{
    public function delete($id = 0, $tab = 1){
        // 获取当前数据表名（控制器名）
        $table = request()->controller();
        if(Db::name($table)->where('id', $id)->delete()){
            return success('删除成功!',url('index',['tab'=>$tab]));
        }else{
            return error('删除失败!',url('index',['tab'=>$tab]));
        }
    }
}
