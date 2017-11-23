<?php
namespace User;

use PDO;
use Exception;
use User\UserModel;
use Token\TokenController;
use Helper\Salt;
use Helper\PostCleaner;
/**
 * Description of UserController
 *
 * @author martinleue
 */
class UserController {
    private $pdo, $postCleaner;
    
    public function __construct(PDO $pdo = null){
        if($pdo === null){return;}
        $this->pdo         = $pdo;
        $this->postCleaner = new PostCleaner();
    }
    
    /*
     * Login Methods
     */
    public function signin($post,$token){  
        $userModel  = new UserModel($this->pdo);
        $email      = $this->postCleaner->params($post['email']);
        $password   = $this->postCleaner->params($post['password']);
        $user       = $userModel->read($email);
        
        if(password_verify($password.$user['salt'], $user['password'])){
            $user['token'] = $token;
            return $user;
        }
        else{
            return null;
        }
    }
    
    /*
     * CRUD Methods
     */
    public function create($post, $token){
        $userModel          = new UserModel($this->pdo);
        $user['email']      = $this->postCleaner->params($post['email']);
        $user['password']   = $this->postCleaner->params($post['password']);
        $user['lastname']   = $this->postCleaner->params($post['lastname']);
        $user['firstname']  = $this->postCleaner->params($post['firstname']);
        $user['pw_salt']    = Salt::back($user['email'],$user['password'])."!";
        $user['password']   = password_hash($user['password'].$user['pw_salt'], PASSWORD_DEFAULT);
                
        $userModel->create($user);
        
        $user = $this->read($user['email']);
        $user['token'] = $token; 
        return $user;
    }
    
    public function read($email){
        $userModel = new UserModel($this->pdo);
        $user      = $userModel->read($email);
        return $user;
    }
    
    public function update($post){
        $userModel = new UserModel($this->pdo);
        $email     = $this->postCleaner->params($post['email']);
        $time      = date("Y-m-d H:i:s");
        foreach($post as $value => $key){
            if($value == "password"){
                $salt = Salt::back($value, $key)."!";
                $key  = password_hash($post['password'].$salt, PASSWORD_DEFAULT);
                $userModel->update($email, "salt", $salt, $time);
            }
            $userModel->update($email, (string)$value, $key, $time);
        }
        $user = $this->read($email);
        return $user;
    }
    
    public function delete($post,$token){
        $userModel = new UserModel($this->pdo);
        $email     = $this->postCleaner->params($post['email']);
        $password  = $this->postCleaner->params($post['password']);
        $user      = $userModel->read($email);
        
        if(password_verify($password.$user['salt'], $user['password'])){
            $userModel->delete($email);
            $user = [
                "msg" => "User was deleted",
                "token" => $token
            ];
            return $user;
        }throw new Exception("The Password is not valid");
    }
}
