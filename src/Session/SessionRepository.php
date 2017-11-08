<?php
namespace Session;

use PDO;

class SessionRepository {
    public $pdo, $table, $model;
    
    public function __construct(PDO $pdo) {
        $this->pdo      = $pdo;
        $this->table    = $this->table();
        $this->model    = $this->model();
    }
    
    public function table(){
	// DB name
        return 'Sessions';
    }
    
    public function model(){  
	// class Modelname
        return 'Session\\SessionModel';
    }
    
    public function getUserSession($user_id){
	// getUserSession
        $stmt = $this->pdo->prepare("SELECT user_id,token,created_at,updated_at FROM {$this->table} WHERE user_id = :user_id");
        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_CLASS, $this->model);
        
        $session = $stmt->fetch();
        return $session;
    }
    
    public function checkToken($token){
	// getUserData
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE token = :token");
        $stmt->bindParam(":token", $token);
        $stmt->execute();
        
        $data = $stmt->fetch(PDO::FETCH_OBJ);
        return $data;
    }
    
    public function sessionInsert($user_id,$token){
	$stmt = $this->pdo->prepare("INSERT INTO {$this->table} (user_id,token) VALUES (:user_id, :token)");
        $stmt->bindParam(":user_id", $user_id);
        $stmt->bindParam(":token", $token);
        $stmt->execute();  
    }
    
    public function sessionUpdate($user_id, $token, $update){
	$stmt = $this->pdo->prepare("UPDATE {$this->table} SET token = :token, updated_at = :update WHERE user_id = :user_id");
        $stmt->bindParam(":token", $token);
        $stmt->bindParam(":update", $update);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();
    }
    
    public function newToken($user_id, $token){
	$stmt = $this->pdo->prepare("UPDATE {$this->table} SET token = :token WHERE user_id = :user_id");
        $stmt->bindParam(":token", $token);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();
    }
    
    public function deleteUserSession($user_id){
        $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE user_id = :user_id");
        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();
    }
    
    public function deleteSession($token){
        $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE token = :token");
        $stmt->bindParam(":token", $token);
        $stmt->execute();
    }
}
