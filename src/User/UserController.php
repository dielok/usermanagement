<?php
namespace User;

use Exception;
use Helper\Salt;
use Helper\Cleaner;
use User\UserModel;
use User\UserRepository;

class UserController {

    public $pdo, $cleaner, $userRepository, $salt;

    public function __construct(UserRepository $userRepository) {
        $this->userRepository = $userRepository;
        $this->cleaner = new Cleaner();
        $this->salt = new Salt();
    }
    
    public function register($post, $token){
        // Clean all -> $_POST
        foreach($post as $key => $value){
            $data[$key] = $this->cleaner->params($value);
        }
        // Params
        $salt       = $this->salt->salt();
        $email      = $data['email'];
        $password   = password_hash($data['password'].$salt, PASSWORD_DEFAULT);
        $lastname   = $data['lastname'];
        $firstname  = $data['firstname'];
        // User Exists
        $checkEmail = $this->userRepository->findMail($email);
        // Email validate
        if(empty($checkEmail) AND filter_var($email, FILTER_VALIDATE_EMAIL) !== false){
            if(strlen($password) >= 8){
                if((strlen($lastname) && strlen($firstname)) >= 1){
                    // UserData insert DB
                    $register = $this->userRepository->register($email, $password, $lastname, $firstname, $salt);
                    // Info
                    $data = [
                        "Report"    => "Success",
                        "Msg"       => "User successfully registered",
                        "Status"    => 201,
                        "Token"     => $token,
                        "UserId"    => $register
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
    
    public function signin($post,$token){
        
        foreach($post as $key => $value){
            $data[$key] = $this->cleaner->params($value);
        }
        
        $email      = $data['email'];
        $password   = $data['password'];

        $checkUser      = $this->userRepository->signin($email);   
        
        if(password_verify(($password.$checkUser->salt), $checkUser->password)){
            $data = [
                "Report"    => "Success",
                "Msg"       => "Registration was successful",
                "Status"    => 201,
                "Token"     => $token,
                "UserId"    => $checkUser->user_id
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
    
    public function getUserData($user_id){
        $user = $this->userRepository->getUserData($user_id);      
        if(empty($user)){
            throw new Exception();
        }
        return $user;
    }
    
    public function userUpdate($user_id,$post){
        // Clean all -> $_POST
        foreach($post as $key => $value){
            $data[$key] = $this->cleaner->params($value);
        } 
        // GetAllUserData
        $user = $this->userRepository->getUserData($user_id);
        // Update Params
        $u['user_id'] = $user_id;
        if(isset($data['email'])){
            $d .= "email = :email,";
            $u['email']=$data['email'];
        }
        if(isset($data['password'])){
            $d .= "password = :password,";
            $u['password']= password_hash($data['password'].$user->salt, PASSWORD_DEFAULT);
        }
        if(isset($data['lastname'])){
            $d .= "lastname = :lastname,";
            $u['lastname']=$data['lastname'];
        }
        if(isset($data['firstname'])){
            $d .= "firstname = :firstname,";
            $u['firstname']=$data['firstname'];
        }   
        $d = substr($d, 0, (strlen($d)-1));
        // Check Token AND UserId
        if(empty($user)){
            // Error
            throw new Exception();
        }
        $timestamp = time();
        $datum = date("Y-m-d H:i:s", $timestamp);
        $d .=",updated_at = :update";
        $u['update']=$datum;
        // Update
        $this->userRepository->userUpdate($user_id, $d, $u);   
        return $user;
    }
    
    public function deleteUser($user_id, $post){
        // getUserData
        $user = $this->userRepository->getUserData($user_id);  
        // Check UserId
        if(empty($user)){
            // Error
            throw new Exception();
        }else{
            // Clean password -> $_POST
            $password = $this->cleaner->params($post['password']);
            // Check Password
            if(password_verify($password.$user->salt, $user->password)){
                // Delete User
                $this->userRepository->deleteUser($user_id);
                $data = [
                    "Report"    => "Success",
                    "Msg"       => "User and Session was deleted",
                    "Status"    => 200
                ];
            }else{
                // Error
                throw new Exception();
            }  
        }
        return $data;
    }
}
