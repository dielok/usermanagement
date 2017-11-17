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
    
    public function findUser($email){
        $stmt = $this->pdo->prepare("SELECT user_id FROM {$this->table} WHERE email = :email");
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        return $stmt->fetch();
    }
    
    /*
     * CRUD Methods
     */
    
    public function readUser($id){
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE user_id = :id");
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        return $res;
    }
    
    public function updateUser($id, $name, $value, $time){
        $stmt = $this->pdo->prepare("UPDATE {$this->table} SET $name = :$name, updated_at = :update WHERE user_id = :id");
        $stmt->bindParam(":$name", $value);
        $stmt->bindParam(":update", $time);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
    }
    
    public function deleteUser($id){
        $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE user_id = :id");
        $stmt->bindParam(":id", $id);
        $stmt->execute();
    }
}
