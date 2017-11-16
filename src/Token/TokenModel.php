<?php
namespace Token;

/**
 * Description of TokenModel
 *
 * @author martinleue
 */
class TokenModel {
    public $pdo, $table;
    
    public function __construct($pdo) {
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
    
    public function findIp($ip){
        $stmt = $this->pdo->prepare("SELECT ip,token FROM {$this->table} WHERE ip = :ip");
        $stmt->bindParam(":ip", $ip);
        $stmt->execute();
        return $stmt->fetch();
    }
    
    public function createToken($token,$ip){    
        $stmt = $this->pdo->prepare("INSERT INTO {$this->table} (token,ip) VALUES (:t, :ip)");
        $stmt->bindParam(":t", $token);
        $stmt->bindParam(":ip", $ip);
        $stmt->execute();
    }
    
    public function deleteToken($token){
        $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE token = :token");
        $stmt->bindParam(":token", $token);
        $stmt->execute();
    }
}
