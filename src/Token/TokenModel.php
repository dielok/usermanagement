<?php
namespace Token;

use PDO;
/**
 * Description of TokenModel
 *
 * @author martinleue
 */
class TokenModel {
    public $pdo, $table;
    
    public function __construct(PDO $pdo = null) {
        if($pdo === null){return;}
        $this->pdo = $pdo;
        $this->table = $this->table();
    }
    
    private function table(){
        return "Tokens";
    }
    
    public function read($token){
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE token = :token");
        $stmt->bindParam(":token", $token);
        $stmt->execute();
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        return $res;
    }
    
    public function create($token){    
        $stmt = $this->pdo->prepare("INSERT INTO {$this->table} (token) VALUES (:token)");
        $stmt->bindParam(":token", $token);
        $stmt->execute();
    }
    
    public function delete($token){
        $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE token = :token");
        $stmt->bindParam(":token", $token);
        $stmt->execute();
    }
}
