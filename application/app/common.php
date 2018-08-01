<?php
use think\Config;

function ajaxmsg($msg = '', $status = 1, $data = '') {
    $info['msg'] = $msg;
    $info['status'] = $status;
    $info['data'] = $data;
    echo json_encode($info, true);
    exit;
}

function sendAlidayu($mobile, $code) {
    include EXTEND_PATH . 'Alidayu/TopSdk.php'; //载入阿里大于
    $c = new \TopClient;
    $c->appkey = Config::get('alidayu.app_key') ;
    $c->secretKey = Config::get('alidayu.app_secret') ;
    $req = new \AlibabaAliqinFcSmsNumSendRequest;
    $req->setSmsType( "normal" );
    $req->setSmsFreeSignName( Config::get('alidayu.signature') );
    $req->setSmsParam( "{code:'". $code ."'}" );
    $req->setRecNum( $mobile );
    $req->setSmsTemplateCode( Config::get('alidayu.templatecode') );
    $resp = $c->execute( $req );
    return $resp;
}
