<?php
namespace Token;

use PDO;
/**
 * Description of Token
 *
 * @author martinleue
 */
class TokenController {
    public $config;
    
    public function __construct() {
        $this->config = $ini_array = parse_ini_file(__DIR__."/../../inc/config.ini", TRUE);
    }
    
    public function newToken(){
        return bin2hex(openssl_random_pseudo_bytes(16));
    }
    
    public function init_DB(PDO $pdo){
        $this->pdo = $pdo;
    }
    
    public function headerToken(){
        return getallheaders()['Token'];
    }
    
    public function findToken($token){
        $tokenModel = new TokenModel($this->pdo);
        $db_token = $tokenModel->findToken($token);
   
        if(!empty($db_token)){
            $this->config['token_exists_ini']['Token'] = $token;
            return $this->config['token_exists_ini'];
        }
        else{
            return $this->config['token_exists_Error_ini'];
        }
    }
    
    public function create($token){
        $tokenModel = new TokenModel($this->pdo);
        $findToken = $tokenModel->findToken($token);
        
        if(empty($findToken)){
            $tokenModel->createToken($token);
            return true;
        }
        else{
            return false;
        }
    }
    
    public function delete($token){
        $tokenModel = new TokenModel($this->pdo);
        $db_token = $tokenModel->findToken($token);
   
        if(!empty($db_token)){
            $tokenModel->deleteToken($token);
            return $this->config['token_delete_ini'];
        }
        else{
            return $this->config['token_delete_Error_ini'];
        }
    }
}
