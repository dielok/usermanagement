<?php
namespace User;

use PDO;
/**
 * Description of UserModel
 *
 * @author martinleue
 */
class UserModel {
    private $pdo, $table;
    
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
        $this->table = $this->table();
    }
    
    private function table(){
        return "Users";
    }
    
    /*
     * CRUD Methods
     */
    public function create($user){    
        // User insert
        $stmt = $this->pdo->prepare("INSERT INTO {$this->table} (email,password,lastname,firstname,salt) VALUES (:e, :p, :l, :f, :s)");
        $stmt->bindParam(":e", $user['email']);
        $stmt->bindParam(":p", $user['password']);
        $stmt->bindParam(":l", $user['lastname']);
        $stmt->bindParam(":f", $user['firstname']);
        $stmt->bindParam(":s", $user['pw_salt']);
        $stmt->execute();
    }
    
    public function read($email){
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE email = :email");
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        return $res;
    }
    
    public function update($email, $name, $value, $time){
        $stmt = $this->pdo->prepare("UPDATE {$this->table} SET $name = :$name, updated_at = :update WHERE email = :email");
        $stmt->bindParam(":$name", $value);
        $stmt->bindParam(":update", $time);
        $stmt->bindParam(":email", $email);
        $stmt->execute();
    }
    
    public function delete($email){
        $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE email = :email");
        $stmt->bindParam(":email", $email);
        $stmt->execute();
    }
}
