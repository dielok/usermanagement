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
    
    public function create($token_id, $token){    
        $stmt = $this->pdo->prepare("INSERT INTO {$this->table} (token_id,token) VALUES (:token_id, :token)");
        $stmt->bindParam(":token_id", $token_id);
        $stmt->bindParam(":token", $token);
        $stmt->execute();
    }
    
    public function read($token_id){
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE token_id = :token_id");
        $stmt->bindParam(":token_id", $token_id);
        $stmt->execute();
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        return $res;
    }
    
//    public function update($token,$token_id){    
//        $stmt = $this->pdo->prepare("UPDATE {$this->table} SET token = :token WHERE token_id = :token_id");
//        $stmt->bindParam(":token", $token);
//        $stmt->bindParam(":token_id", $token_id);
//        $stmt->execute();
//    }
    
    public function delete($token_id){
        $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE token_id = :token_id");
        $stmt->bindParam(":token_id", $token_id);
        $stmt->execute();
    }
}
