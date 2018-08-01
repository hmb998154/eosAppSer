<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use util\Tree;
use app\admin\model\Category as CategoryModel;

class Category extends Common
{
    public function index($tab = 1, $id = 0){
        if(request()->isPost()){
            foreach (input('post.sort/a') as $key => $value) {
                Db::name('category')->where('id',$key)->update(['sort'=>$value]);
            }
            return success('排序更新成功!',url('index',['tab'=>$tab]));
        }else{
            $category = Db::name('category')->order('sort')->select();
            $tree = new Tree();
            $tree->tree($category,'id','pid','name');
            $categoryArray = $tree->getArray();
            $this->assign('category',$categoryArray);

            // 编辑分类
            if( 3 == $tab ){
                // 获取所要编辑分类的信息
                $category_info = Db::name('category')->where('id',$id)->find();
                if($category_info!=null && is_array($category_info)){
                    $this->assign('category_info',$category_info);
                }
            }
        }
        return view();
    }

    public function add($tab = 1){
        if(request()->isPost()){
            // 检查分类名称和分类目录是否重名
            $count = Db::name('category')->where('name',input('post.name'))->count();
            if($count){
                return error('分类名称重名!');
            }
            $categoryModel = new CategoryModel;
            if($categoryModel->allowField(true)->save(input('post.'))){
                return success('分类添加成功!',url('index',['tab'=>1]));
            }else{
                return error('分类添加失败!',url('index',['tab'=>$tab]));
            }
        }
    }

    public function edit($tab = 1, $id = 0){
        if(request()->isPost()){
            // 检查分类名称和分类目录是否重名
            $count = Db::name('category')->where('id','<>',$id)->where('name',input('post.name'))->count();
            if($count){
                return error('分类名称重名!');
            }
            // 编辑分类
            $categoryModel = new CategoryModel;
            if($categoryModel->allowField(true)->isUpdate()->save(input('post.'))){
                return success('分类编辑成功!',url('index',['tab'=>1]));
            }else{
                return error('分类信息未修改或编辑失败!');
            }
        }
    }

}
