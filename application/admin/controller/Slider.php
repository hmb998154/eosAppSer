<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\Config;
use Qiniu\Auth;
use Qiniu\Storage\UploadManager;

class Slider extends Common
{
    public function index($tab=1,$id=0){
        if(request()->isPost()){
            foreach (input('post.sort/a') as $key => $value) {
                Db::name('slider')->where('id',$key)->update(['sort'=>$value]);
            }
            return success('排序更新成功!',url('index',['tab'=>$tab]));
        }else{
            //获取模型信息
            $sliderlist = Db::name('slider')->order('sort')->select();
            $this->assign('sliderlist',$sliderlist);

            // 编辑模型
            if(3==$tab){
                $info = Db::name('slider')->where('id',$id)->find();
                if($info!=null && is_array($info)){
                    $this->assign('info',$info);
                }
            }
        }

        return view();
    }

    public function upload_image(){
        //图片上传
        $file = request()->file(input('name'));
        $info = $file->move(ROOT_PATH . 'public/uploads');
        if($info) {
            // 上传到七牛
            require_once EXTEND_PATH.'Qiniu/autoload.php';
            $config = Config::get('UPLOAD_QINIU_CONFIG');
            $accessKey = $config['accessKey'];
            $secretKey = $config['secretKey'];
            $auth = new Auth($accessKey, $secretKey);

            $bucket = $config['bucket'];// 要上传的空间
            $token = $auth->uploadToken($bucket);// 生成上传 Token

            // 要上传文件的本地路径
            $filePath = ROOT_PATH . 'public/uploads/' . $info->getSaveName();

            // 上传到七牛后保存的文件名
            $key = date('Y-m-d',time()).'_'.$info->getFilename();
            // 初始化 UploadManager 对象并进行文件的上传
            $uploadMgr = new UploadManager();

            // 调用 UploadManager 的 putFile 方法进行文件的上传
            list($ret, $err) = $uploadMgr->putFile($token, $key, $filePath);

            return json_encode($info->getSaveName());
        }
    }

    public function add($tab = 1){
        if(request()->isPost()){
            $config = Config::get('UPLOAD_QINIU_CONFIG');
            $data = input('post.');
            $data['url']=$config['domain'] . date('Y-m-d',time()) . '_' . substr($data['path'],9);
            Db::name('slider')->strict(false)->insert($data);
            return success('幻灯片添加成功!',url('index',['tab'=>1]));
        }
    }

    public function edit($tab = 1){
        if(request()->isPost()){
            $config = Config::get('UPLOAD_QINIU_CONFIG');
            $data = input('post.');
            $data['url']=$config['domain'] . date('Y-m-d',time()) . '_' . substr($data['path'],9);
            Db::name('slider')->strict(false)->update($data);
            return success('幻灯片更新成功!',url('index',['tab'=>1]));
        }
    }
}
