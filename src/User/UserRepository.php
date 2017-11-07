<?php
namespace User;

use PDO;

class UserRepository {
    
    public $pdo, $table, $table2, $model;
    
    public function __construct(PDO $pdo) {
        $this->pdo      = $pdo;
        $this->table    = $this->table();
        $this->model    = $this->model();
    }
    
    public function table(){
	// DB name
        return 'Users';
    }
    
    public function model(){  
	// class Modelname
        return 'User\\UserModel';
    }
    
    public function findMail($email){
	// getUserData
        $stmt = $this->pdo->prepare("SELECT email FROM {$this->table} WHERE email = :email");
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        
        $data = $stmt->fetch();
        return $data;
    }

    public function register($mail, $pass, $lastname, $firstname, $salt, $session_id){
	$stmt = $this->pdo->prepare("INSERT INTO {$this->table} (email,password,lastname,firstname,salt, session_id) VALUES (:mail, :pass, :lastname, :firstname, :salt, :session_id)");
        $stmt->bindParam(":mail", $mail);
        $stmt->bindParam(":pass", $pass);
        $stmt->bindParam(":lastname", $lastname);
        $stmt->bindParam(":firstname", $firstname);
        $stmt->bindParam(":salt", $salt);
        $stmt->bindParam(":session_id", $session_id);
        $stmt->execute();
    }
    
    public function getUserData($user_id){
	// getUserData
        $stmt = $this->pdo->prepare("SELECT user_id, email, password, lastname, firstname, salt, session_id, created_at FROM {$this->table} WHERE user_id = :user_id");
        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_CLASS, $this->model);
        
        $data = $stmt->fetch();
        return $data;
    }
        
    public function signin($email){
	// getUserData
        $stmt = $this->pdo->prepare("SELECT user_id,password,salt,session_id FROM {$this->table} WHERE email = :email");
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        
        $data = $stmt->fetch(PDO::FETCH_OBJ);
        return $data;
    }
    
    public function newSessionId($email, $session_id){
	$stmt = $this->pdo->prepare("UPDATE {$this->table} SET session_id = :session_id WHERE email = :email");
        $stmt->bindParam(":session_id", $session_id);
        $stmt->bindParam(":email", $email);
        $stmt->execute();
    }
    
    public function reHash($hash, $email, $salt){
	// password newHash save
        $stmt = $this->pdo->prepare("UPDATE {$this->table} SET password = :password, salt = :salt WHERE email = :email");
        $stmt->bindParam(":password", $hash);
	$stmt->bindParam(":salt", $salt);
        $stmt->bindParam(":email", $email);
        $stmt->execute();
    }
}
