<?php
namespace User;

use PDO;

class UserRepository {
    
    public $pdo, $table, $model;
    
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
    
    public function findUser($user_id){
	// getUserData
        $stmt = $this->pdo->prepare("SELECT password,salt FROM {$this->table} WHERE user_id = :user_id");
        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();
        
        $data = $stmt->fetch(PDO::FETCH_OBJ);
        return $data;
    }

    public function register($mail, $pass, $lastname, $firstname, $salt){
	$stmt = $this->pdo->prepare("INSERT INTO {$this->table} (email,password,lastname,firstname,salt) VALUES (:mail, :pass, :lastname, :firstname, :salt)");
        $stmt->bindParam(":mail", $mail);
        $stmt->bindParam(":pass", $pass);
        $stmt->bindParam(":lastname", $lastname);
        $stmt->bindParam(":firstname", $firstname);
        $stmt->bindParam(":salt", $salt);
        $stmt->execute();
        return $this->pdo->lastInsertId();
    }
    
    public function getUserData($user_id){
	// getUserData
        $stmt = $this->pdo->prepare("SELECT user_id, email, password, lastname, firstname, salt, created_at, updated_at FROM {$this->table} WHERE user_id = :user_id");
        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_CLASS, $this->model);
        
        $data = $stmt->fetch();
        return $data;
    }
    
    public function deleteUser($user_id){
        $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE user_id = :user_id");
        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();
        return $this->pdo->lastInsertId();
    }
        
    public function signin($email){
	// getUserData
        $stmt = $this->pdo->prepare("SELECT user_id,password,salt FROM {$this->table} WHERE email = :email");
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        
        $data = $stmt->fetch(PDO::FETCH_OBJ);
        return $data;
    }
    
    public function userUpdate($user_id, $sql, $data){
	$stmt = $this->pdo->prepare("UPDATE {$this->table} SET {$sql} WHERE user_id = :user_id");
        $stmt->execute($data);
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
