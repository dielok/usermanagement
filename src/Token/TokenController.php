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
    
    public function create($token){
        $tokenModel = new TokenModel($this->pdo);
        $token_db   = $tokenModel->read($token);
        $tokenModel->create($token);
    }
    
    public function read($t){
        $tokenModel = new TokenModel($this->pdo);
        $token      = $tokenModel->read($t);
        return $token;
    }
    
    public function delete($token){
        $tokenModel = new TokenModel($this->pdo);
        $token_db   = $tokenModel->read($token);
   
        if(isset($token_db['token'])){
            $tokenModel->delete($token);
            $token = [
                "msg" => "Token has been deleted"
            ];
            return $token;
        }
    }
}
