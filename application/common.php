<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

error_reporting(E_ALL ^ E_NOTICE);

use think\Db;

function success($msg = '成功', $url = '')
{
    $data['status'] = 200;
    $data['msg'] = $msg;
    $data['url'] = $url;
    return json($data);
}

function error($msg = '失败', $url = '')
{
    $data['status'] = 202;
    $data['msg'] = $msg;
    $data['url'] = $url;
    return json($data);
}

function getCatInfoById($id=0, $field=''){
    if($field == ''){
        //获取单条数据
        return Db::name('category')->where('id',$id)->find();
    }else{
        //获取某个字段
        return Db::name('category')->where('id',$id)->value($field);
    }
}
