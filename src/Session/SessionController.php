<?php
namespace Session;

use PDO;
use Exception;

class SessionController {
    
    public $sessionsRepository;
    
    public function __construct(SessionRepository $sessionRepository){
        $this->sessionsRepository = $sessionRepository;
    }
    
    public function checkSession($user_id){
        $session = $this->sessionsRepository->getUserSession($user_id);
        if($session->token != getallheaders()['Token']){
            throw new Exception();
        }
        return true;
    }
    
    public function checkToken($token){
        $session = $this->sessionsRepository->checkToken($token);
        if($session->token != getallheaders()['Token']){
            throw new Exception();
        }else{
            $data = [
                "Report"    => "Success",
                "Msg"       => "Active Session",
                "UserId"    => $session->user_id,
                "Token"     => $session->token,
                "Created"   => $session->created_at,
                "Update"    => $session->updated_at,
                "Status"    => 200
            ];
        }
        return $data;
    }
 
    public function getUserSession($token){
        $session = $this->sessionsRepository->getUserSession($token);
    }
    
    public function sessionInsert($user_id,$token){
        $session = $this->sessionsRepository->sessionInsert($user_id, $token);
    }
    
    public function deleteUserSession($user_id){
        $session = $this->sessionsRepository->deleteUserSession($user_id);
    }
    
    public function sessionUpdate($user_id,$token){
        $timestamp = time();
        $datum = date("Y-m-d H:i:s", $timestamp);
        $session = $this->sessionsRepository->sessionUpdate($user_id, $token, $datum);
    }
    
    public function deleteSession($token){
        $session = $this->sessionsRepository->deleteSession($token);
        $data = $this->sessionsRepository->checkToken($token);
        if(empty($data->token)){
            $data = [
                "Report"    => "Success",
                "Msg"       => "Token delete",
                "Status"    => 200
            ];
        }
        return $data;
    }
}
