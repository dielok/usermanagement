<?php
namespace User;

use Helper\Salt;
use Helper\Cleaner;
use User\UserRepository;

class UserController {

    public $pdo, $cleaner, $userRepository, $salt;

    public function __construct(UserRepository $userRepository) {
        $this->userRepository = $userRepository;
        $this->cleaner = new Cleaner();
        $this->salt = new Salt();
    }
    
    public function register($post, $session_id){
        
        foreach($post as $key => $value){
            $data[$key] = $this->cleaner->params($value);
        }
        
        $salt       = $this->salt->salt();
        $email      = $data['email'];
        $password   = password_hash($data['password'].$salt, PASSWORD_DEFAULT);
        $lastname   = $data['lastname'];
        $firstname  = $data['firstname'];
        
        $checkEmail = $this->userRepository->findMail($data['email']);
        
        if(empty($checkEmail) AND filter_var($email, FILTER_VALIDATE_EMAIL) !== false){
            if(strlen($password) >= 8){
                if((strlen($lastname) && strlen($firstname)) >= 1){
                    
                    $register = $this->userRepository->register($email, $password, $lastname, $firstname, $salt, $session_id);
                    
                    $data = [
                        "Report"    => "Success",
                        "Msg"       => "User successfully registered",
                        "Status"    => 201,
                        "Token"     => $session_id
                    ];                    
                }else{
                    $data = [
                        "Report"    => "Failure",
                        "Msg"       => "First name or last name too short",
                        "Status"    => 200
                    ];              
                }
            }else{
                $data = [
                    "Report"    => "Failure",
                    "Msg"       => "Password is too short",
                    "Status"    => 200
                ];  
            }
        }else{
            $data = [
                "Report"    => "Failure",
                "Msg"       => "User not registered, user already exists",
                "Status"    => 200
            ];
        }
        return $data;
    }
    
    public function signin($post,$session_id){
        
        foreach($post as $key => $value){
            $data[$key] = $this->cleaner->params($value);
        }
        
        $email      = $data['email'];
        $password   = $data['password'];
        
        $newSessionId   = $this->userRepository->newSessionId($email, $session_id);
        $checkUser      = $this->userRepository->signin($email);   
        
        if(password_verify(($password.$checkUser->salt), $checkUser->password)){
            $data = [
                "Report"    => "Success",
                "Msg"       => "Registration was successful",
                "Status"    => 201,
                "Token"     => $checkUser->session_id
            ];
        }else{
            $data = [
                "Report"    => "Failure",
                "Msg"       => "Login failed",
                "Status"    => 200
            ];
        }
        return $data;
    }
}
