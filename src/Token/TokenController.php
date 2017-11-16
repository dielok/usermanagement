<?php
namespace Token;

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
    
    public function init_DB($pdo){
        $this->pdo = $pdo;
    }
    
    public function headerToken(){
        return getallheaders()['Token'];
    }
    
    public function findToken($token){
        $tokenModel = new TokenModel($this->pdo);
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
        
        $ip = $_SERVER['REMOTE_ADDR'];
        $findIp = $tokenModel->findIp($ip);
        
        if(empty($findToken['token']) and $findIp['ip'] != $ip){
            $tokenModel->createToken($token, $ip);
        }else{
            $data['token'] = $findIp['token'];
            $data['error'] = true;
            return $data;
        }
    }
    
    public function delete($token){
        $tokenModel = new TokenModel($this->pdo);
        $db_token = $tokenModel->findToken($token);
   
        if(!empty($db_token['token'])){
            $tokenModel->deleteToken($token);
            $this->config['token_delete_ini']['Token'] = $token;
            return $this->config['token_delete_ini'];
        }
        else{
            return $this->config['token_delete_Error_ini'];
        }
    }
}
