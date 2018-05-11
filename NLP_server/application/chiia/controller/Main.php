<?php
/**
 * Created by PhpStorm.
 * User: mateng
 * Date: 20/03/2018
 * Time: 4:09 PM
 */

namespace app\chiia\Controller;

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
        $id = isset($data['articleID'])? (int)$data['articleID'] : 0;
        $method = isset($data['method']) ? $data['method'] : '';

        //$max_query='db.getCollection(\'ARTICLE\').aggregate({"$group":{id : \'max\',max:value:{"$max":"id"}}})';
        //$min_query='db.getCollection(\'ARTICLE\').aggregate({"$group":{id : \'min\',max:value:{"$min":"id"}}})';

        $max = Db::table('NLP_ARTICLE')->max('articleID');
        $min = Db::table('NLP_ARTICLE')->min('articleID');

        if($id < $min){
            $id = $min;
        }elseif($id >= $max){
            $id = $max;
        }

        if($method == ''){
            $result = Db::table('NLP_ARTICLE')->where('articleID',$id)->select();

            if (count($result) != 0) {
                $file_path = '../article/'.$result[0]["content"];
            }else
            {
                $file_path = '';
            }

            if($result){
                $this->assign('article', $result);
                $this->assign('file_path',$file_path);
                return $this->fetch();
            }
        }else{
            while(true){
                $result = Db::table('NLP_ARTICLE')->where('articleID',$id)->select();

                if (count($result) != 0) {
                    $file_path = '../article/'.$result[0]["content"];
                }else
                {
                    $file_path = '';
                }

                if($result){
                    $this->assign('article', $result);
                    $this->assign('file_path',$file_path);
                    return $this->fetch();
                }

                if($method == 'next'&& $id<$max){
                    $id = $id+1;
                }elseif($method == 'last' && $id>$min){
                    $id = $id-1;
                }
            }
        }
    }

    public function labelRelevent(){
        $data = input('get.');
        $id = $data['articleID'];
        $user = Session::get('username');

        $result = Db::table('NLP_ARTICLE')->where('articleID',$id)->update(['status'=>1, 'assign'=>'1',
            'labeledby'=> $user,'labeledtime' => date("Y-m-d")]);

        if($result){
            return $this->success('Labeled success');
        }
        else{
            return $this->error('Labeled failed');
        }
    }

    public function labelIrrelevent(){
        $data = input('get.');
        $id = $data['articleID'];
        $user = Session::get('username');

        $result = Db::table('NLP_ARTICLE')->where('articleID',$id)->update(['status'=>2, 'assign'=>'1',
            'labeledby'=> $user,'labeledtime' => date("Y-m-d")]);

        if($result){
            return $this->success('Labeled success');
        }
        else{
            return $this->error('Labeled failed');
        }
    }



    public function logout(){
        session(null);
        return $this->success('Logout success.','chiia/index/login');
    }



}


