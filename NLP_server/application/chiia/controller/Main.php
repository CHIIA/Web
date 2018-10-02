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
use think\Request;

class Main extends Base{

    public function articleIndex(){
        $result = Db::table('NLP_ARTICLE')->order('articleID desc')->limit(100)->select();
        $this->assign('articleData',$result);
        return $this->fetch();
    }

    public function searchArticle(){
        $data= input('post.');

        $AN = input('post.AN','');
        $articleID = input('post.articleID','');
        $title = input('post.title','');
        $author = input('post.author','');
        $fromDate = input('post.fromDate','');
        $toDate = input('post.toDate','');
        $blog = input('post.blog','');
        $website = input('post.website','');
        $Dowjones = input('post.Dowjones','');
        $publication = input('post.publication','');
        $unlabeled = (input('post.unlabeled','') != '') ? (int)input('post.unlabeled') : 9;
        $labeledR = (input('post.labeledR','') !='') ? (int)input('post.labeledR') : 9;
        $labeledIR = (input('post.labeledIR','') !='') ? (int)input('post.labeledIR') : 9;
        $labeledby = input('post.labeledby','');
        $upperLikelihood = (input('post.upperLikelihood','') !='') ? (float)input('post.upperLikelihood') : '';
        $lowerLikelihood = (input('post.lowerLikelihood','') !='') ? (float)input('post.lowerLikelihood') : '';

        $ANlike = '%'.$AN.'%';
        $articleIDlike = '%'.$articleID.'%';
        $titlelike = '%'.$title.'%';
        $authorlike = '%'.$author.'%';
        $labeledbylike = '%'.$labeledby.'%';


        $result=Db::query("SELECT * FROM NLP_ARTICLE as A
                              WHERE (A.AN LIKE ? OR ?='')
                              AND (A.articleID LIKE ? OR ?='')
                              AND (A.title LIKE ? OR ?='')
                              AND (A.author LIKE ? OR ?='')
                              AND (A.date >=? OR ?='')
                              AND (A.date <=? OR ?='')
                              AND ((A.source=?
                              OR A.source=?
                              OR A.source=?
                              OR A.source=?)
                              OR (?='' AND ?='' AND  ?='' AND ?=''))
                              AND ((A.status=?
                              OR A.status=?
                              OR A.status=?)
                              OR (?=9 AND ?=9 AND ?=9))
                              AND (A.labeledby LIKE ? OR ?='')
                              AND (A.likelyhood >=? OR ?='')
                              AND (A.likelyhood <=? OR ?='')",
            [$ANlike, $AN, $articleIDlike,$articleID,$titlelike,$title,$authorlike,$author,$fromDate,$fromDate,$toDate,$toDate,
                $blog, $website,$Dowjones,$publication,$blog, $website,$Dowjones,$publication,
                $unlabeled,$labeledR,$labeledIR,$unlabeled,$labeledR,$labeledIR,$labeledbylike,$labeledby,
                $upperLikelihood,$upperLikelihood,$lowerLikelihood,$lowerLikelihood]);

        $value = [];
        foreach ($result as $tmp){
            $array = [
                'articleID' => $tmp['articleID'],
                'ID' => $tmp['ID'],
                'title' => $tmp['title'],
                'author' => $tmp['author'],
                'date' => $tmp['date'],
                'source' => $tmp['source'],
                'status' => $tmp['status'],
                'labeledby' => $tmp['labeledby'],
                'labeledtime' => $tmp['labeledtime'],
                'likelyhood' => $tmp['likelyhood'],
            ];
            $value[] = $array;
        }
        ini_set('memory_limit','4096M');


        $this->assign('articleData',$value);
        return $this->fetch();
    }

    public function articleList($result){
        $this->assign('articleData',$result);
        ini_set('memory_limit','4096M');
        return $this->fetch();
    }

    public function unLabeledArticleList(){
        $result = Db::table('NLP_ARTICLE')->where('status',0)->order('articleID desc')->limit(5000)->select();
        $value = [];
        foreach ($result as $tmp){
            $array = [
                'articleID' => $tmp['articleID'],
                'ID' => $tmp['ID'],
                'title' => $tmp['title'],
                'author' => $tmp['author'],
                'date' => $tmp['date'],
                'source' => $tmp['source'],
                'status' => $tmp['status'],
                'labeledby' => $tmp['labeledby'],
                'labeledtime' => $tmp['labeledtime'],
                'likelyhood' => $tmp['likelyhood'],
            ];
            $value[] = $array;
        }
        ini_set('memory_limit','4096M');
        return action('articleList',['result'=>$value]);
    }

    public function allArticleList(){
        $result = Db::table('NLP_ARTICLE')->order('articleID desc')->limit(5000)->select();
        $value = [];
        foreach ($result as $tmp){
            $array = [
                'articleID' => $tmp['articleID'],
                'ID' => $tmp['ID'],
                'title' => $tmp['title'],
                'author' => $tmp['author'],
                'date' => $tmp['date'],
                'status' => $tmp['status'],
                'source' => $tmp['source'],
                'labeledby' => $tmp['labeledby'],
                'labeledtime' => $tmp['labeledtime'],
                'likelyhood' => $tmp['likelyhood'],
            ];
            $value[] = $array;
        }

        ini_set('memory_limit','4096M');
        return action('articleList',['result'=>$value]);
    }

    public function labeledArticleList(){
        $result = Db::table('NLP_ARTICLE')->where('status',1)->whereOr('status',2)->order('articleID desc')->limit(5000)->select();
        $value = [];
        foreach ($result as $tmp){
            $array = [
                'articleID' => $tmp['articleID'],
                'ID' => $tmp['ID'],
                'title' => $tmp['title'],
                'author' => $tmp['author'],
                'date' => $tmp['date'],
                'source' => $tmp['source'],
                'status' => $tmp['status'],
                'labeledby' => $tmp['labeledby'],
                'labeledtime' => $tmp['labeledtime'],
                'likelyhood' => $tmp['likelyhood'],
            ];
            $value[] = $array;
        }
        ini_set('memory_limit','4096M');
        return action('articleList',['result'=>$value]);
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
        ini_set('memory_limit','256M');
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

            if($result){
                $this->assign('article', $result);
                return $this->fetch();
            }
        }else{
            while(true){
                $result = Db::table('NLP_ARTICLE')->where('articleID',$id)->select();

                if($result){
                    $this->assign('article', $result);
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

    public function viewWebsiteContent(){
        ini_set('memory_limit','256M');
        $data= input('post.');
        $id = $data['articleID'];

        $result = Db::table('NLP_ARTICLE')->where('articleID',$id)->select();

        if($result){
            $this->assign('article', $result);
            return $this->fetch();
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


