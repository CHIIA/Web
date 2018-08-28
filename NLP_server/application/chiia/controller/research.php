<?php
/**
 * Created by PhpStorm.
 * User: mateng
 * Date: 20/05/2018
 * Time: 11:26 PM
 */

namespace app\chiia\controller;

use think\Controller;
use think\Db;
use app\chiia\controller\Base;
use think\Session;
use app\chiia\model\Article as ArticleModel;
use think\Url;

class research extends Base{

    public function logout(){
        session(null);
        return $this->success('Logout success.','chiia/index/login');
    }

    public function statistic(){
        $userID = Session::get('userID');
        $count_article = Db::query('SELECT COUNT(articleID) AS article_count FROM NLP_ARTICLE NATURAL JOIN NLP_JOBLIST WHERE userID=?',[$userID]);
        $count_labeled = Db::query('SELECT COUNT(articleID) AS article_count FROM NLP_ARTICLE NATURAL JOIN NLP_JOBLIST WHERE userID=? AND status <> 0',[$userID]);
        $count_unlabeled = Db::query('SELECT COUNT(articleID) AS article_count FROM NLP_ARTICLE NATURAL JOIN NLP_JOBLIST WHERE userID=? AND status = 0',[$userID]);

        $count_article = $count_article[0]['article_count'];
        $count_labeled = $count_labeled[0]['article_count'];
        $count_unlabeled = $count_unlabeled[0]['article_count'];

        $this->assign('count_article',$count_article);
        $this->assign('count_labeled',$count_labeled);
        $this->assign('count_unlabeled',$count_unlabeled);

        return $this->fetch();
    }

    public function articlelist($result){
        $this->assign('articleData',$result);
        return $this->fetch();
    }

    public function unlabeledarticlelist(){
        $userID = Session::get('userID');
        $result = Db::query('SELECT * FROM NLP_ARTICLE NATURAL JOIN NLP_JOBLIST WHERE userID=? AND status = 0',[$userID]);
        return action('articleList',['result'=>$result]);
    }

    public function allarticlelist(){
        $userID = Session::get('userID');
        $result = Db::query('SELECT * FROM NLP_ARTICLE NATURAL JOIN NLP_JOBLIST WHERE userID=?',[$userID]);
        return action('articleList',['result'=>$result]);
    }

    public function labeledarticlelist(){
        $userID = Session::get('userID');
        $result = Db::query('SELECT * FROM NLP_ARTICLE NATURAL JOIN NLP_JOBLIST WHERE userID=? AND status <> 0',[$userID]);
        return action('articleList',['result'=>$result]);
    }

    public function task(){
        $userID = Session::get('userID');
        $data= input('get.');
        $id = isset($data['articleID'])? (int)$data['articleID'] : 0;
        $method = isset($data['method']) ? $data['method'] : '';

        $max= Db::query('SELECT MAX(articleID) AS maxID FROM NLP_ARTICLE NATURAL JOIN NLP_JOBLIST WHERE userID=?',[$userID]);
        $min= Db::query('SELECT MIN(articleID) AS minID FROM NLP_ARTICLE NATURAL JOIN NLP_JOBLIST WHERE userID=?',[$userID]);
        $max = $max[0]['maxID'];
        $min = $min[0]['minID'];

//        $max = Db::table('NLP_ARTICLE')->max('articleID');
//        $min = Db::table('NLP_ARTICLE')->min('articleID');

        if($id < $min){
            $id = $min;
        }elseif($id >= $max){
            $id = $max;
        }

        if($method == ''){
            $result = Db::query('SELECT * FROM NLP_ARTICLE NATURAL JOIN NLP_JOBLIST WHERE userID=? AND articleID=?',[$userID,$id]);

//            $result = Db::table('NLP_ARTICLE')->where('articleID',$id)->select();

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
                $result = Db::query('SELECT * FROM NLP_ARTICLE NATURAL JOIN NLP_JOBLIST WHERE userID=? AND articleID=?',[$userID,$id]);

//                $result = Db::table('NLP_ARTICLE')->where('articleID',$id)->select();

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

}