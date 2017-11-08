<?php
namespace Middleware;

class TokenAuth
{
    private $token;
    
    public function __construct(){
        
        $this->token = getallheaders()['Token'];
    }
    
    public function __invoke($request, $response, $next){
        if(!empty($this->token)) {
            $response = $next($request, $response);
            return $response;
        }
        else{
            $data = [
                "Report"    => "Failure",
                "Msg"       => "Unauthorized. Token missing",
                "Status"    => 401
            ];
            return $response->withJson($data, $data['Status']);
        }         
    }
}

