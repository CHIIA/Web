<?php
/**
 * Created by PhpStorm.
 * User: mateng
 * Date: 14/03/2018
 * Time: 8:31 PM
 */

namespace app\chiia\controller;

use think\Controller;
use think\Db;
use app\chiia\model\User as UserModel;
use app\chiia\validate\User as UserValidate;


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
        $result = $user->allowField(true)->save();
        if($result){
            $this->success('Success');
        }else{
            $this->error('Failed');
        }

    }


}