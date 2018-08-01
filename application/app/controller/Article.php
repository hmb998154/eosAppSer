<?php
namespace app\app\controller;
use think\Db;

class Article extends Common
{
    //特定分类下的文章
    public function index($cid = 0, $page = 1) {
        $config = ['page'=>$page, 'list_rows'=>5];
        $article = Db::name('article')
            ->where('cid',$cid)
            ->field('id,title,url,author,create_time')
            ->order('id desc')
            ->paginate(null,false,$config)
            ->toArray();
        $data['article'] = $article['data'];
        $data['count'] = $article['total'];
        ajaxmsg('ok', 1, $data);
    }

    public function info($id = 0){
        //获取域名或IP
        $domain = input('server.HTTP_HOST');
        $data['info'] = Db::name('article')
            ->where('id',$id)
            ->field('id,title,abstract,content,author,create_time')
            ->find();
        $data['info']['content'] = str_replace("/uploads","http://".$domain."/uploads",$data['info']['content']);
        ajaxmsg('ok', 1, $data);
    }
}
