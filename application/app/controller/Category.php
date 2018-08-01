<?php
namespace app\app\controller;
use think\Db;

class Category extends Common
{
    public function index(){
        $data['category'] = Db::name('category')->where('pid',0)->order('sort')->select();
        ajaxmsg('ok', 1, $data);
    }

    public function sub($pid = 0){
        $data['sub'] = Db::name('category')->where('pid',$pid)->order('sort')->select();
        ajaxmsg('ok', 1, $data);
    }
}
