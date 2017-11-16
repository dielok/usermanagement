<?php
namespace User;

use PDO;
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
    private $pdo, $postCleaner, $config,$userModel;
    
    public function __construct() {
        $this->postCleaner = new PostCleaner();
        $this->config = $ini_array = parse_ini_file(__DIR__."/../../inc/config.ini", TRUE);
    }
    
    /*
     * init DB
     */
    public function init_DB(PDO $pdo){
        $this->pdo = $pdo;
        $this->userModel = $userModel = new UserModel($this->pdo);
    }
    
    /*
     * Login Methods
     */
    public function signinSession($post, $t){  
        $email      = $this->postCleaner->params($post['email']);
        $password   = $this->postCleaner->params($post['password']);
        $token      = $this->postCleaner->params($t);
        $findUser = $this->userModel->findUser($email);
        $user = $this->userModel->readUser($findUser['user_id']);
        
        if(password_verify($password.$user['salt'], $user['password']) and (!empty($findUser))){        
            $this->config['signinUser_ini']['Token'] = $token;
            return $this->config['signinUser_ini'];
        }else{
            return $this->config['signinUser_Error_ini'];  
        }
    }
    
    /*
     * CRUD Methods
     */
    public function create($post, $token){
        $user['email']      = $this->postCleaner->params($post['email']);
        $user['password']   = $this->postCleaner->params($post['password']);
        $user['lastname']   = $this->postCleaner->params($post['lastname']);
        $user['firstname']  = $this->postCleaner->params($post['firstname']);
        $user['pw_salt']    = Salt::back($user['email'],$user['password'])."!";
        $user['password'] = password_hash($user['password'].$user['pw_salt'], PASSWORD_DEFAULT);
        $findUser  = $this->userModel->findUser($user['email']);
                
        if(empty($findUser)){
            $this->userModel->createUser($user);
            $this->config['createUser_ini']['Token'] = $token;
            return $this->config['createUser_ini'];
        }
        else{
            return $this->config['createUser_Error_ini'];
        }
    }
    
    public function read($id){
        $user = $this->userModel->readUser($id);
        
        if($user === false){
            return $this->config['readUser_Error_ini'];
        }
        else{
            $this->config['readUser_ini']['UserId']     = $user['user_id'];
            $this->config['readUser_ini']['Email']      = $user['email'];
            $this->config['readUser_ini']['Password']   = $user['password'];
            $this->config['readUser_ini']['Lastname']   = $user['lastname'];
            $this->config['readUser_ini']['Firstname']  = $user['firstname'];
            $this->config['readUser_ini']['Salt']       = $user['salt'];
            $this->config['readUser_ini']['Created']    = $user['created_at'];
            $this->config['readUser_ini']['Updated']    = $user['updated_at'];
            return $this->config['readUser_ini'];
        }
    }
    
    public function update($id, $post){
        $time = date("Y-m-d H:i:s");
        foreach($post as $value => $key){
            if($value == "password"){
                $salt = Salt::back($value, $key)."!";
                $key = password_hash($post['password'].$salt, PASSWORD_DEFAULT);
                $this->userModel->updateUser($id, "salt", $salt, $time);
            }
            $this->userModel->updateUser($id, (string)$value, $key, $time);
            $data[$value] = $key; 
        }
        
        if($data === null){
            return $this->config['updateUser_Error_ini'];
        }
        else{
            return $this->config['updateUser_ini'];
        }
    }
    
    public function delete($id,$post){
        $password = $this->postCleaner->params($post['password']);
        $userData = $this->userModel->readUser($id);
        
        if(password_verify($password.$userData['salt'], $userData['password'])){
            $this->userModel->deleteUser($id);
            $this->config['deleteUser_ini']['UserId'] = $id;
            return $this->config['deleteUser_ini'];
        }
        else{
            $this->config['deleteUser_Error_ini']['UserId'] = $id;
            return $this->config['deleteUser_Error_ini'];
        }
    }
}
