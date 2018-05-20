<?php
/**
 * Created by PhpStorm.
 * User: mateng
 * Date: 14/03/2018
 * Time: 8:31 PM
 */

namespace app\chiia\controller;

use app\chiia\validate\User;
use think\console\command\make\Model;
use think\Controller;
use think\Db;
use app\chiia\model\User as UserModel;
use app\chiia\validate\User as UserValidate;
use app\chiia\model\Article as ArticleMode;


class Admin extends Controller{

    public function add(){
        return $this->fetch();

    }

    public function insert(){

        $data = input('post.');
        $val = new UserValidate();
        if (!$val -> check($data)){
            $this->error($val->getError());
            exit;
        }

        $user = new UserModel($data);
        dump($user);
        die;
//        dump($user);
//        die;
        $result = $user->allowField(true)->save();
        if($result){
            $this->success('Success','admin/userList');
        }else{
            $this->error('Failed');
        }

    }

    public function update(){

        $data = input('post.');
        $id = input("post.userID");

        $val = new UserValidate();
        if (!$val -> check($data)){
            $this->error($val->getError());
            exit;
        }

        $user = new UserModel();
        $result = $user->allowField(true)->save($data,['id' => $id]);

        if($result){
            $this->success('Update Successfully', 'admin/userList');
        } else {
            $this->error('Update Failed');
        }
    }


    public function userManagement(){
        return $this->fetch();
    }

    public function userList(){
        $result = Db::table('NLP_USER')->select();

        foreach($result as &$i){
            $count = Db::table('NLP_JOBLIST')->where('userID',$i['userID'])->count('userID');
            $i['count'] = $count;
        }

        $this->assign('userData',$result);
        return $this->fetch();
    }

    public function editUser(){
        $id = input('get.userID');
        $data = Db::table('NLP_USER')->where('userID',$id)->select();
        $this->assign('data',$data);


        return $this->fetch();
    }

    public function deleteUser(){
        $id = input('get.userID');

        $result = UserModel::destroy($id,true);
        if($result){
            $this->success('Delete Successfully', 'admin/userList');
        } else {
            $this->error('Delete Failed');
        }
    }


    public function unassignedJobList(){
        $result = Db::table('NLP_ARTICLE')->where('assign',0)->where('status=0 OR status=2')->select();
        $this->assign('data',$result);
        return $this->fetch();
    }

    public function selectWorker(){
        $data = input('post.');
        $articleID = $data['chosenArticle'];

        $sql1 = "SELECT u.userID , u.username, COUNT(u.userID) AS undoJOB FROM NLP_USER AS u RIGHT OUTER JOIN (SELECT articleID,userID FROM NLP_ARTICLE AS a NATURAL JOIN NLP_JOBLIST WHERE a.status=0) AS r ON u.userID = r.userID GROUP BY u.userID ORDER BY undoJOB DESC";
        $result1 = Db::query($sql1);

        $sql2 = "SELECT userID, username FROM NLP_USER";
        $result2 = Db::query($sql2);

        $tmp = [];
        foreach ($result1 as $i){
            array_push($tmp, $i['userID']);
        }

        foreach($result2 as $k){
            if(!in_array($k['userID'],$tmp))
                array_push($result1,['userID'=>$k['userID'], 'username'=> $k['username'],'undoJOB'=>0]);
        }

        $this->assign('userUndoJobNo',$result1);
        $this->assign('chosenArticle' ,$articleID);

        return $this->fetch();
    }

    public function updateJoblist(){
        $data = input('post.');

        $articles = $data['chosenArticle'];
        $userID =$data['chosenWorker'];

        foreach ($articles as $articleID){
            $result1 = Db::table('NLP_ARTICLE')->where('articleID', $articleID)->update(['assign'=>'1']);
            $result2 = Db::table('NLP_JOBLIST')->insert(['articleID'=>$articleID, 'userID'=>$userID, 'assignedDate'=> date("Y-m-d")]);
        }

        if( $result1 && $result2){
            $this->success('Assigned Successfully', 'admin/viewJobList');
        } else {
            $this->error('Assigned Failed');
        }
        return $this->fetch();
    }

    public function viewJobList(){
        $sql = "SELECT articleID, title, userID, username, assignedDate FROM (NLP_JOBLIST NATURAL JOIN NLP_ARTICLE)NATURAL JOIN NLP_USER";
        $result = Db::query($sql);

        $this->assign('data', $result);

        return $this->fetch();
    }

    public function spiderSetting(){
        return $this->fetch();
    }

    public function mlSetting(){
        return $this->fetch();
    }

    public function startSpider(){

        $result = shell_exec("");

        if($result){
            $this->success('execute successfully! Please wait for a while!');
        } else {
            $this->success('Processing');
        }

    }

    public function startML(){

        $result = shell_exec("source activate python3 & python /var/www/chiia-nlp/public/model/main.py");

        if($result){
            $this->success('execute successfully! Please wait for a while!');
        } else {
            $this->success('Processing');
        }

    }




}