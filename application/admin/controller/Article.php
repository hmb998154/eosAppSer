<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use app\admin\model\Category;
use think\Config;
use Qiniu\Auth;
use Qiniu\Storage\UploadManager;

class Article extends Common
{
    public function index()
    {
        //获取分类树
        $categoryArray = Db::name('category')->order('sort')->select();
        $categoryList = Category::tree($categoryArray);
        $this->assign('categoryList',$categoryList);

        return view();
    }

    public function content($id = 0)
    {
        if($id){
            $infoList = Db::name('article')->where('cid',$id)->order('id desc')->select();
            $catname = getCatInfoById($id, 'name');
            $this->assign('name',$catname);
            $this->assign('infoList',$infoList);
        }

        return view();
    }

    public function delete($cid= 0, $id = 0){
        if(Db::name('article')->where('id',$id)->delete()){
            return success('删除成功!',url('content',['id'=>$cid]));
        }else{
            return error('删除失败!');
        }
    }

    // 批量删除
    public function deleteAll(){
        $cid = input('post.cid');
        if (empty(input('post.ids/a'))) {
            return error('请选中需要删除的数据!');
        }
        foreach (input('post.ids/a') as $id => $value) {
            Db::name('article')->where('id',$id)->delete();
        }
        return success('删除成功!',url('content',['id'=>$cid]));
    }

    public function getDataTables($id = 0) {
        if($id){
            //获取请求过来的数据
            $getParam = request()->param();

            $draw = $getParam['draw'];

            //排序
            $orderSql = $getParam['order'][0]['dir'];

            //自定义查询参数
            $extra_search = $getParam['extra_search'];

            // 总记录数
            $recordsTotal = Db::name('article')->where('cid',$id)->count();
            //过滤条件后的总记录数
            $search = $getParam['search']['value'];
            $recordsFiltered = strlen($search) ?  Db::name('article')->where('cid',$id)->where($extra_search,'like','%'.$search.'%')->count() : $recordsTotal;

            //分页
            $start = $getParam['start']; //起始下标
            $length = $getParam['length']; //每页显示记录数

            //根据开始下标计算出当前页
            $page = intval($start/$length) + 1;
            $config = ['page'=>$page, 'list_rows'=>$length];
            $list = Db::name('article')->where('cid',$id)->where($extra_search,'like','%'.$search.'%')->order($orderSql)->paginate(null,false,$config);
            $lists = [];
            if(!empty($list)){
                foreach ($list as $key => $value) {
                    $lists[$key] = $value;
                    $lists[$key]['operate'] = "<a href='". url('edit',['id'=>$value['id'], 'cid'=>$value['cid']]) ."' title='编辑' target='_parent'><i class='fa fa-edit text-navy'></i></a>&nbsp;&nbsp;
                    <a name='delete' href='". url('delete',['id'=>$value['id'], 'cid'=>$value['cid']]) ."' title='删除'><i class='fa fa-trash-o text-navy'></i></a>";
                }
            }
        } else{
            $draw = 1;
            $recordsTotal = 0;
            $recordsFiltered = 0;
            $lists = [];
        }

        $data = array(
            "draw"=>$draw,
            "recordsTotal"=>$recordsTotal, //数据总数
            "recordsFiltered"=>$recordsFiltered, //过滤之后的记录总数
            "data"=>$lists
        );

        echo json_encode($data);
    }

    //信息发布
    public function add($id=0) {
        if(request()->isPost()){
            $config = Config::get('UPLOAD_QINIU_CONFIG');
            $data = input('post.');
            $data['url']=$config['domain'] . date('Y-m-d',time()) . '_' . substr($data['path'],9);
            Db::name('article')->strict(false)->insert($data);
            return success('文章添加成功!',url('index',['id'=>input('post.cid')]));
        }elseif($id == 0){
           return error ('请选择相应分类！');
        } else {
          //分类名称
          $catname = getCatInfoById($id,'name');
          $this->assign('name',$catname);
          return view();
        }
    }

    //编辑功能
    public function edit( $cid= 0, $id = 0) {
        if(request()->isPost()) {
           $config = Config::get('UPLOAD_QINIU_CONFIG');
           $data = input('post.');
           $data['url']=$config['domain'] . date('Y-m-d',time()) . '_' . substr($data['path'],9);
           Db::name('article')->strict(false)->update($data);
           return success('信息编辑成功!',url('index',['id'=>input('post.cid')]));
        }else {
          //获取数据
          $article = Db::name('article')->where('id',$id)->find();
          $this->assign('info',$article);
          //分类名称
          $catname = getCatInfoById($cid,'name');
          $this->assign('name',$catname);
          return view();
        }
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

}
