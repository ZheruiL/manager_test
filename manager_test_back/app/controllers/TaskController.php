<?php
namespace app\controllers;

use app\models\TackModel;
use core\base\Controller;

class TaskController extends Controller
{
    public function get($id = null){
        if($id != null){
            $task = (new TackModel())->getTask($id);
            $this->response($task, 200);
        }
        else{
            $tasks = (new TackModel())->fetchTasks();
            $this->response($tasks,200);
        }
    }
    public function post(){
        $result = (new TackModel())->createTask();
        if($result['status']==true){
            $this->response($result["error"], 200);
        }
        else{
            $this->response($result["error"],400);
        }
    }
    public function delete($taskId){
        $result = (new TackModel())->deleteTask($taskId);
        if($result['status']==true){
            $this->response($result["error"], 200);
        }
        else{
            $this->response($result["error"],400);
        }
    }
}