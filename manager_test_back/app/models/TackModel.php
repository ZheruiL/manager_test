<?php
namespace app\models;

use core\db\Db;
use app\models\UserModel;

class TackModel
{
    public function fetchTasks(){
        isset($_GET['limit'])?$limit = $_GET['limit']: $limit = 20;
        isset($_GET['offset'])?$offset = $_GET['offset']: $offset = 0;

        $sql="SELECT t.id, t.user_id, t.title, t.description, u.name user_name, t.creation_date, t.status
              FROM task t, user u
              Where 1 and t.user_id = u.id ";
        $params = array();
        if(isset($_GET['user_id'])&&$_GET['user_id']!=null){
            $sql.="and u.id = :user_id ";
            $params['user_id']=$_GET['user_id'];
        }
        if(isset($_GET['search_q'])){
            $sql.= " AND (UPPER(t.title) LIKE UPPER(:title) OR UPPER(t.description) LIKE UPPER(:description)) ";
            $params['title'] = "%{$_GET['search_q']}%";
            $params['description'] = "%{$_GET['search_q']}%";
        }
        $sql.="ORDER BY t.id desc ";
        $sql.="LIMIT :limit offset :offset ";
        $params['limit'] = $limit;
        $params['offset'] = $offset;
        $stmt = Db::pdo()->prepare($sql);
        $stmt->execute($params);
        $tasks = $stmt->fetchAll();

        return $tasks;
    }

    public function getTask($id){
        $sql = "SELECT t.id, t.user_id, t.title, t.description, u.name user_name, t.creation_date, t.status
                FROM task t, user u 
                Where 1 and t.id = :id 
                and t.user_id = u.id";

        $stmt = Db::pdo()->prepare($sql);
        $stmt->execute(['id' => $id]);
        $task = $stmt->fetchAll();
        return $task;
    }

    // create
    public function createTask(){
        $error = "";
        if(!isset($_POST['title'])||trim($_POST['title'])==null){
            $error .= "title is required \n ";
        }
        else{
            $title = trim($_POST['title']);
        }
        isset($_POST['description'])? $description = $_POST['description']: $description = null;
        isset($_POST['user_id'])? $user_id = $_POST['user_id']: $error .= "user id is required \n ";

        // check if the user id is ok
        $find = false;
        $_GET['limit'] = 99999;
        $users = (new UserModel())->fetchUsers();
        foreach ($users as $user){
            if($user["id"]==$_POST['user_id']){
                $find = true;
                break;
            }
        }
        if($find==false){
            $error .= "user id is not valid \n ";
        }

        if($error != ""){
            return array("error"=>$error, "status"=>false);
        }
        $data = [
            'user_id' => $user_id,
            'title' => $title,
            'description' => $description
        ];
        $sql = "INSERT INTO task (user_id, title, description) VALUES ( :user_id, :title , :description)";
        $stmt = Db::pdo()->prepare($sql);

        if($stmt->execute($data)==true){
            return array("error"=>"created successfully", "status" => true);
        }
        else{
            return array("error"=>"'Error: ' . {$sql} \n", "status" => false);
        }
    }

    // delete
    public function deleteTask($task_id=null){
        if($task_id == null){
            return array("error"=>"task id is required", "status" => false);
        }
        $stmt = Db::pdo()->prepare("DELETE FROM task WHERE id = ?");
        if($stmt->execute([$task_id])==true){
            return array("error"=>"delete successfully", "status" => true);
        }
        else{
            return array("error"=>"'Error: can not delete \n", "status" => false);
        }
    }
}