<?php
namespace classes;

class Middleware1
{
    protected $tokenClass;
    protected $getToken;
    
    public function __construct(){
        
        $this->tokenClass    = new Token();
        
        if(@isset($_GET['token'])){             $this->getToken = @$_GET['token'];      }
        elseif(@isset($_COOKIE['token'])){      $this->getToken = @$_COOKIE['token'];   }      
        else{                                   $this->getToken = 0;                    }
    }
    
    public function __invoke($request, $response, $next){
        
        //$response->getBody()->write('<hr><br>BEFORE<br><hr>');
        
        $data[] = $this->tokenClass->checkToken($this->getToken);    
        $verify = @$data[0]['verify'];
        $token  = @$data[0]['token'];

        if($data[0]['verify'] == true) {
            if(@empty($_COOKIE['token'])){
                setcookie("token", "$token", time()+(3600*24));
            }
            $response = $next($request, $response);
            return $response;
        }
        else{
            
            return $response->withStatus(401)->write("Unauthorized. Token missing.");
        }    
        
        //$response->getBody()->write('<hr><br>AFTER<br><hr>');      
    }
}

