<?php
namespace app\app\controller;
use think\Db;

class Index extends Common
{
    public function index($page=1)
    {
    	$config = ['page'=>$page, 'list_rows'=>5];
        $data['slider'] = Db::name('slider')->field('title,url')->limit(3)->order('sort')->select();
        $data['article'] = Db::name('article')
        ->field('id,title,url,author,create_time')
        ->limit(6)
        ->order('id desc')
        ->paginate(null,false,$config)
        ->toArray();
        ajaxmsg('ok', 1, $data);
    }

}
