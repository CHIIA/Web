<?php
/**
 * Created by PhpStorm.
 * User: mateng
 * Date: 20/03/2018
 * Time: 4:09 PM
 */
namespace app\chiia\controller;

use think\Controller;
use think\Db;
use app\chiia\model\User;


class Index extends Controller
{
    public function login(){
        return $this->fetch();
    }

    public function check()
    {
        $data = input('post.');
        $user = New User();
        $result = Db::table('NLP_USER')->where('username',$data['username'])->find();

        if ($result){
            if($result['password'] === ($data['password'])){
                session('username',$data['username']);
                $this->success('Login success','Main/statistic');
            }else{
                $this->error('PASSWORD, USERNAME NOT MATCH');
            }
        }else{
            $this->error('PASSWORD, USERNAME NOT MATCH');
            exit;
        }

    }
}
