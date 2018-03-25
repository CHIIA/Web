<?php
/**
 * Created by PhpStorm.
 * User: mateng
 * Date: 20/03/2018
 * Time: 4:09 PM
 */

namespace app\chiia\Controller;

use app\chiia\model\Article;
use think\Controller;
use think\Db;
use app\chiia\controller\Base;
use think\Session;
use app\chiia\model\Article as ArticleModel;
use think\Url;

class Main extends Base{

    public function articleList($result){
        $this->assign('articleData',$result);
        return $this->fetch();
    }

    public function unLabeledArticleList(){
        $result = Db::table('NLP_ARTICLE')->where('status',0)->select();
        return action('articleList',['result'=>$result]);
    }

    public function allArticleList(){
        $result = Db::table('NLP_ARTICLE')->select();
        return action('articleList',['result'=>$result]);
    }

    public function labeledArticleList(){
        $result = Db::table('NLP_ARTICLE')->where('status',1)->whereOr('status',2)->select();
        return action('articleList',['result'=>$result]);
    }
    
    public function statistic(){
        $count_article = Db::table('NLP_ARTICLE')->count();
        $count_labeled = Db::table('NLP_ARTICLE')->where('status',1)->whereOr('status',2)->count();
        $count_unlabeled = Db::table('NLP_ARTICLE')->where('status',0)->count();
        $this->assign('count_article',$count_article);
        $this->assign('count_labeled',$count_labeled);
        $this->assign('count_unlabeled',$count_unlabeled);
        return $this->fetch();
    }

    public function task(){
        $data= input('get.');
        $id = isset($data['id'])? $data['id'] : 0;
        $count = Db::table('NLP_ARTICLE')->count();
        if($id < 1){
            $id = 1;
        }elseif($id >= $count){
            $id = $count;
        }
        $result = Db::table('NLP_ARTICLE')->where('id',$id)->select();
        $this->assign('article', $result);
        return $this->fetch();
    }

    public function labelRelevent(){
        $data = input('get.');
        $id = $data['id'];

        $result = Db::table('NLP_ARTICLE')->where('id',$id)->update(['status'=>1]);
        $str = 'task?id='.$id;

        if($result){
            return $this->success('Labeled success');
        }
        else{
            return $this->error('Labeled failed');
        }
    }

    public function labelIrrelevent(){
        $data = input('get.');
        $id = $data['id'];

        $result = Db::table('NLP_ARTICLE')->where('id',$id)->update(['status'=>2]);
        $str = 'task?id='.$id;

        if($result){
            return $this->success('Labeled success');
        }
        else{
            return $this->error('Labeled failed');
        }
    }



    public function logout(){
        session(null);
        return $this->success('Logout success.','index/login');
    }



}


