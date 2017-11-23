<?php
namespace Token;

use PDO;
use Exception;
/**
 * Description of Token
 *
 * @author martinleue
 */
class TokenController {
    public $pdo;
    
    public function __construct(PDO $pdo = null){
        if($pdo === null){return;}
        $this->pdo = $pdo;
    }
    
    public function newToken(){
        return bin2hex(openssl_random_pseudo_bytes(16));
    }
    
    public function headerToken(){
        return getallheaders()['Token'];
    }
    
    public function create($token,$token_id){
        $tokenModel = new TokenModel($this->pdo);
        $token_db   = $tokenModel->read($token_id);
        
        if($token_db['token_id'] == $token_id){
            $this->delete($token_id);
        }
        
        $tokenModel->create($token_id, $token);
    }
    
    public function read($token_id){
        $tokenModel = new TokenModel($this->pdo);
        $token      = $tokenModel->read($token_id);
        return $token;
    }
    
    public function delete($token_id){
        $tokenModel = new TokenModel($this->pdo);
        $token_db   = $tokenModel->read($token_id);
        $tokenModel->delete($token_id);
    }
}
