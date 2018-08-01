<?php
namespace app\app\controller;
use think\Db;

class User extends Common
{
    //阿里大于短信注册接口
    public function sendsms() {
        //手机号
        $mobile = input('mobile',0);
        //正则简单判断
        if(preg_match("/^1\d{10}$/", $mobile)){
            $count = Db::name('user')->where('username',$mobile)->count();
            if($count){
                ajaxmsg("手机号已被注册",0);
            }else{
                //验证码，随机4位
                $smscode = mt_rand(1111,9999);
                $resp = sendAlidayu($mobile, $smscode);
                if($resp->result->success) {
                    session('mobile', $mobile);
                    session('smscode', $smscode);
                    ajaxmsg("发送成功".$smscode,1); //方便调试
                }else{
                    ajaxmsg("发送失败".$resp->sub_msg,0);
                }
            }
        }else{
            ajaxmsg("手机号格式不正确",0);
        }
    }

    //会员注册接口
    public function register(){
        $mobile = input('mobile',0);
        $smscode = input('smscode',0);
        $password = input('password','');
        $deviceid = input('deviceid',0); //设备ID，在APP生成

        if($smscode != session('smscode') || $mobile != session('mobile')){
            ajaxmsg('验证码不正确!',0);
        }
        //添加user表
        $data['mobile'] = $mobile;
        $data['username'] = $mobile;
        $data['password'] = md5($password);
        $uid = Db::name('user')->insertGetId($data);
        if($uid){
            //生成令牌
            $token = $this->getToken($uid, $mobile);
            $tid = Db::name('tokens')->insertGetId(['uid'=>$uid,'deviceid'=>$deviceid,'token'=>$token]);
            if($tid){
                $user_info['username'] = $username;
                $user_info['mobile'] = $mobile;
                $user_info['token'] = $token;
                ajaxmsg('注册成功', 1, $user_info);
            }else{
                //删除注册的用户
                Db::name('user')->where('id',$uid)->delete();
                ajaxmsg('注册失败',0);
            }
        }else{
            ajaxmsg('注册失败',0);
        }
    }

    //生成令牌
    public function getToken($uid, $mobile){
        $auth = array('uid'=>$uid, 'mobile'=>$mobile, 'time'=>time());
        //排序
        ksort($auth);
        //url编码并生成query字符串
        $code = http_build_query($auth);
        //生成签名
        $token = sha1($code);
        return $token;
    }

    //会员登录接口
    public function login(){
        $mobile = input('mobile',0);
        $password = input('password','');
        $deviceid = input('deviceid',0); //设备ID，在APP生成
        $where['mobile'] = $mobile;
        $where['password'] = md5($password);
        $user_info =Db::name('user')->where($where)->find();
        if($user_info){
            $uid = $user_info['id'];
            $token = $this->getToken($uid,$mobile);
            $count = Db::name('tokens')->where('uid', $uid)->count();
            if($count){
                //存在则更新
                Db::name('tokens')->where('uid', $uid)->update(['token'=>$token,'deviceid'=>$deviceid]);
            }else{
                //不存在则新增
                Db::name('tokens')->insert(['uid'=>$uid,'token'=>$token,'deviceid'=>$deviceid]);
            }
            $user_info['token'] = $token;
            ajaxmsg('登录成功', 1, $user_info);
        }else{
            ajaxmsg('手机号或密码错误',0);
        }
    }

    //会员退出接口
    public function login_out(){
        $uid = $this->is_login();
        if($uid){
            $res = Db::name('tokens')->where('uid',$uid)->delete();
            if($res){
                ajaxmsg('退出成功!',1);
            }else{
                ajaxmsg('操作失败',0);
            }
        }else{
            ajaxmsg('会员未登录',0);
        }
    }

    //判断会员是否登录
    public function is_login(){
        $token = input('token',0);
        $uid = Db::name('tokens')->where('token',$token)->value('uid');
        if($uid!=null && $uid>0){
            return $uid;
        }else{
            return 0;
        }
    }
}
