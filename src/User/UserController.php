<?php
/**
 * Description of LoginController
 *
 * @author martinleue
 */
namespace User;

use Helper\Salt;
use Helper\Token;
use Helper\Cleaner;
use User\UserRepository;

class UserController {

    public $pdo, $cleaner, $userRepository, $token, $salt;

    public function __construct(UserRepository $userRepository) {
        $this->userRepository = $userRepository;
        $this->cleaner = new Cleaner();
        $this->salt = new Salt();
        $this->token = new Token();
    }
    
    public function register($post){
        
        foreach($post as $key => $value){
            $data[$key] = $this->cleaner->params($value);
        }
        
        $salt = $this->salt->salt();
        $email = $data['email'];
        $this->token->init($email, $salt);
        $token = $this->token->newToken();
        $password = password_hash($data['password'].$salt, PASSWORD_DEFAULT);
        $lastname = $data['lastname'];
        $firstname = $data['firstname'];
        
        $checkEmail = $this->userRepository->findMail($data['email']);
        
        if(empty($checkEmail) AND filter_var($email, FILTER_VALIDATE_EMAIL) !== false){
            if(strlen($password) >= 8){
                if(strlen($lastname) >= 1 && strlen($firstname) >= 1){
                    $register = $this->userRepository->register($email, $password, $lastname, $firstname, $salt, $token);
                    $data = [
                        "Report"    => "Success",
                        "Msg"       => "User successfully registered",
                        "Status"    => 201,
                        "Token"     => $token
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
    
    public function signin($post){

        foreach($post as $key => $value){
            $data[$key] = $this->cleaner->params($value);
        }
        
        $email = $data['email'];
        $password = $data['password'];
        
        $checkUser = $this->userRepository->signin($email);
        
        if(password_verify(($password.$checkUser->salt), $checkUser->password)){
            $token = $this->userRepository->getToken($checkUser->user_id);
            $data = [
                "Report"    => "Success",
                "Msg"       => "Registration was successful",
                "Status"    => 201,
                "Token"     => $token
            ];
        }
        else{
            $data = [
                "Report"    => "Failure",
                "Msg"       => "Login failed",
                "Status"    => 200
            ];
        }
        
        return $data;
    }
}
