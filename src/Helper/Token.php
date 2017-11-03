<?php
namespace Helper;

class Token
{
    protected $token;

    public function init($mail, $salt){
        $timestamp = date("Y-m-d H:i:s");
        $this->token = base_convert(base_convert(bin2hex($mail), 16, 10) * $timestamp * base_convert(bin2hex($salt), 16, 10) * pow(13,143), 10, 26);
    }
    
    public function newToken(){
        return $this->token;
    }
}

