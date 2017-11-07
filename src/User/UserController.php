<?php
namespace User;

use Helper\Salt;
use Helper\Cleaner;
use Exception;
use User\UserModel;
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
        
        $checkEmail = $this->userRepository->findMail($email);
        
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
    
    public function getUserdata($user_id,$session_id){
        $user = $this->userRepository->getUserData($user_id);      
        
        if(empty($user) || $user->session_id != $session_id){
            throw new Exception();
        }
        return $user;
    }
    
    public function userUpdate($user_id,$post,$session_id){
        foreach($post as $key => $value){
            $data[$key] = $this->cleaner->params($value);
        } 
        
        $pwSalt = $this->userRepository->getUserData($user_id);

        $dataUpdate['user_id'] = $user_id;
        if(isset($data['email'])){
            $d .= "email = :email,";
            $dataUpdate['email']=$data['email'];
        }
        if(isset($data['password'])){
            $d .= "password = :password,";
            $dataUpdate['password']= password_hash($data['password'].$pwSalt->salt, PASSWORD_DEFAULT);
        }
        if(isset($data['lastname'])){
            $d .= "lastname = :lastname,";
            $dataUpdate['lastname']=$data['lastname'];
        }
        if(isset($data['firstname'])){
            $d .= "firstname = :firstname,";
            $dataUpdate['firstname']=$data['firstname'];
        }   
        $d = substr($d, 0, (strlen($d)-1));
        
        $this->userRepository->userUpdate($user_id, $d, $dataUpdate);
        $user = $this->userRepository->getUserData($user_id);
        
        if(empty($user) || $user->session_id != $session_id){
            throw new Exception();
        }
        return $user;
    }
    
    public function deleteUser($user_id, $post){
        $password = $this->cleaner->params($post['password']);
        
        $user = $this->userRepository->getUserData($user_id);  
        
        if(password_verify($password.$user->salt, $user->password)){
            $this->userRepository->deleteUser($user_id);
            $data = [
                "Report"    => "Success",
                "Msg"       => "User was deleted",
                "Status"    => 200
            ];
        }else{
            throw new Exception();
        }
        return $data;
    }
}
