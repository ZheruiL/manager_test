<?php
namespace app\controllers;

use app\models\UserModel;
use core\base\Controller;

class UserController extends Controller
{
    public function get($id = null){
        if($id != null){
            $user = (new UserModel())->getUser($id);
            $this->response($user, 200);
        }
        else{
            $users = (new UserModel())->fetchUsers();
            $this->response($users,200);
        }
    }
    public function post(){
        $result = (new UserModel())->createUser();
        if($result['status']==true){
            $this->response($result["error"], 200);
        }
        else{
            $this->response($result["error"],400);
        }
    }
    public function delete($userId){
        $result = (new UserModel())->deleteUser($userId);
        if($result['status']==true){
            $this->response($result["error"], 200);
        }
        else{
            $this->response($result["error"],400);
        }
    }
}