<?php
/**
 * Description of UserRepository
 *
 * @author martinleue
 */
namespace User;

use PDO;

class UserRepository {
    
    public $pdo, $table, $table2, $model;
    
    public function __construct(PDO $pdo) {
        $this->pdo      = $pdo;
        $this->table    = $this->table();
        $this->table2   = $this->table2();
        $this->model    = $this->model();
    }
    
    public function table(){
	// DB name
        return 'Users';
    }
    
    public function table2(){
	// DB name
        return 'Token';
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

    public function register($mail, $pass, $lastname, $firstname, $salt, $token){
	$stmt = $this->pdo->prepare("INSERT INTO {$this->table} (email,password,lastname,firstname,salt) VALUES (:mail, :pass, :lastname, :firstname, :salt)");
        $stmt->bindParam(":mail", $mail);
        $stmt->bindParam(":pass", $pass);
        $stmt->bindParam(":lastname", $lastname);
        $stmt->bindParam(":firstname", $firstname);
        $stmt->bindParam(":salt", $salt);
        $stmt->execute();
        
        $db_id = $this->pdo->lastInsertId();
        $stmt = $this->pdo->prepare("INSERT INTO {$this->table2} (id,token) VALUES (:id, :token)");
        $stmt->bindParam(":id", $db_id);
        $stmt->bindParam(":token", $token);
        $stmt->execute();
    }
    
    public function userData($user_id){
	// getUserData
        $stmt = $this->pdo->prepare("SELECT user_id, email, password, salt, UNIX_TIMESTAMP(created_at) as created_at FROM {$this->table} WHERE user_id = :user_id");
        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_CLASS, $this->model);
        
        $data = $stmt->fetch();
        return $data;
    }
        
    public function signin($email){
	// getUserData
        $stmt = $this->pdo->prepare("SELECT user_id,password,salt FROM {$this->table} WHERE email = :email");
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        
        $data = $stmt->fetch(PDO::FETCH_OBJ);
        return $data;
    }
    
    public function getToken($user_id){
	// getUserData
        $stmt = $this->pdo->prepare("SELECT token FROM {$this->table2} WHERE id = :id");
        $stmt->bindParam(":id", $user_id);
        $stmt->execute();
        
        $data = $stmt->fetch(PDO::FETCH_OBJ);
        return $data->token;
    }
    
    public function reHash($hash, $id, $salt){
	// password newHash save
        $stmt = $this->pdo->prepare("UPDATE {$this->table} SET password = :password, salt = :salt WHERE user_id = :id");
        $stmt->bindParam(":password", $hash);
	$stmt->bindParam(":salt", $salt);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
    }
}
