<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

return [
    // 生成应用公共文件
    '__file__' => ['common.php', 'config.php', 'database.php'],

    // 定义admin模块的自动生成 （按照实际定义的文件名生成）
    'admin'     => [
        '__file__'   => ['common.php'],
        '__dir__'    => ['controller', 'model', 'view'],
        'controller' => ['Index', 'Category', 'Article', 'Slider'],
        'model'      => ['Category', 'Article'],
        'view'       => ['index/index', 'index/content', 'category/index', 'article/index', 'slider/index'],
    ],

    // 定义app接口模块
    'app'     => [
        '__file__'   => ['common.php'],
        '__dir__'    => ['controller'],
        'controller' => ['Index', 'Common', 'Category', 'Article', 'User'],
    ],
];
