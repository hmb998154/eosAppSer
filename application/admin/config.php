<?php
//配置文件
return [
    // 七牛上传驱动配置
    'UPLOAD_QINIU_CONFIG' => array(
        'secretKey' => 'N88xe0bInu13Sk7uHZKdt3wbJqjD1HFeBk105fnk', //七牛服务器
        'accessKey' => 'i9p7uHpiaqFCnCRY_eahatvUcnfYvXAYiesqE-fN', //七牛用户
        'domain'    => 'http://oql4bb2rx.bkt.clouddn.com/', //七牛域名
        'bucket'    => 'newsapp', //空间名称
        'timeout'   => 300, //超时时间
    ),
];
