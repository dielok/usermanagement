<?php
namespace Token;

use PDO;
/**
 * Description of TokenModel
 *
 * @author martinleue
 */
class TokenModel {
    private $pdo, $table;
    
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
        $this->table = $this->table();
    }
    
    private function table(){
        return "Tokens";
    }
    
    public function findToken($token){
        $stmt = $this->pdo->prepare("SELECT token FROM {$this->table} WHERE token = :token");
        $stmt->bindParam(":token", $token);
        $stmt->execute();
        return $stmt->fetch();
    }
    
    public function createToken($token){    
        $stmt = $this->pdo->prepare("INSERT INTO {$this->table} (token) VALUES (:t)");
        $stmt->bindParam(":t", $token);
        $stmt->execute();
    }
    
    public function deleteToken($token){
        $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE token = :token");
        $stmt->bindParam(":token", $token);
        $stmt->execute();
    }
}
