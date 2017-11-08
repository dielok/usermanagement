<?php
namespace Helper;

class Token
{
    private $token;

    public function __construct(){

        $this->token = openssl_random_pseudo_bytes(16);
        $this->token = bin2hex($this->token);
    }
    
    public function newToken(){
        
        return $this->token;
    }
}

