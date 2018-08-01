<?php
namespace app\app\controller;
use think\Controller;
use think\Db;

class Common extends Controller
{
    protected function _initialize(){
        header('content-type:text/html;charset=utf-8');
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
        header('Access-Control-Allow-Methods: GET, POST, PUT');
        ksort($_POST);
        ksort($_GET);
    }
}
