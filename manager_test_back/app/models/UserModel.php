<?php
namespace app\models;

use core\db\Db;

class UserModel
{
    public function fetchUsers(){
        isset($_GET['limit'])?$limit = $_GET['limit']: $limit = 20;
        isset($_GET['offset'])?$offset = $_GET['offset']: $offset = 0;
        $sql = "SELECT * 
                FROM user 
                Where 1=1 ";
        $params = array();
        if(isset($_GET['search_q'])){
            $sql.= " AND (UPPER(user.name) LIKE UPPER(:name) OR UPPER(user.email) LIKE UPPER(:email)) ";
            $params['name'] = "%{$_GET['search_q']}%";
            $params['email'] = "%{$_GET['search_q']}%";
        }
        $sql.= "ORDER BY user.id DESC LIMIT :limit offset :offset ";
        $params['limit'] = $limit;
        $params['offset'] = $offset;
        //$stmt = Db::pdo()->prepare($sql);
        //$stmt->execute(['limit' => $limit, 'offset' => $offset]);
        $stmt = Db::pdo()->prepare($sql);
        $stmt->execute($params);
        $users = $stmt->fetchAll();

        return $users;
    }

    public function getUser($id){
        $stmt = Db::pdo()->prepare("SELECT * FROM user Where 1 and id = :id");
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetchAll();
        if($user == null){
            return array();
        }
        return $user[0];
    }

    public function createUser(){
        $error = "";
        if(!isset($_POST['name'])||trim($_POST['name'])==null){
            $error .= "name is required \n ";
        }
        if(!isset($_POST['email'])||trim($_POST['email'])==null){
            $error .= "email is required \n ";
        }
        if($error != ""){
            return array("error"=>$error, "status"=>false);
        }
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $data = [
            'name' => $name,
            'email' => $email
        ];
        $sql = "INSERT INTO user (name, email) VALUES ( :name , :email)";
        $stmt= Db::pdo()->prepare($sql);

        if($stmt->execute($data)==true){
            return array("error"=>"created successfully", "status" => true);
        }
        else{
            return array("error"=>"'Error: ' . {$sql} \n", "status" => false);
        }
    }

    // delete
    public function deleteUser($user_id=null){
        if($user_id === null){
            return array("error"=>"user id is required", "status" => false);
        }
        // delete user and tasks
        $stmt = DB::pdo()->prepare("DELETE FROM task WHERE user_id = ?");
        if($stmt->execute([$user_id])!==true){
            return array("error"=>"'Error: can not delete \n", "status" => false);
        }

        $stmt = DB::pdo()->prepare("DELETE FROM user WHERE id = ?");
        if($stmt->execute([$user_id])==true){
            return array("error"=>"delete successfully", "status" => true);
        }
        else{
            return array("error"=>"'Error: can not delete \n", "status" => false);
        }
    }
}