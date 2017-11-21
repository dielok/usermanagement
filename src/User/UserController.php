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
    
    public function __construct(PDO $pdo = null) {

        if ($pdo === null) return;
        
        $this->pdo = $pdo;

        $this->postCleaner = new PostCleaner();
        $this->config = $ini_array = parse_ini_file(__DIR__."/../../inc/config.ini", TRUE);
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
    public function create($user) {

        $user['email']      = $this->postCleaner->params($user['email']);
        $user['password']   = $this->postCleaner->params($user['password']);
        $user['lastname']   = $this->postCleaner->params($user['lastname']);
        $user['firstname']  = $this->postCleaner->params($user['firstname']);
        $user['pw_salt']    = Salt::back($user['email'],$user['password'])."!";
        $user['password']   = password_hash($user['password'].$user['pw_salt'], PASSWORD_DEFAULT);

        // User insert
        $stmt = $this->pdo->prepare("INSERT INTO Users (email,password,lastname,firstname,salt) VALUES (:e, :p, :l, :f, :s)");
        
        $stmt->bindParam(":e", $user['email']);
        $stmt->bindParam(":p", $user['password']);
        $stmt->bindParam(":l", $user['lastname']);
        $stmt->bindParam(":f", $user['firstname']);
        $stmt->bindParam(":s", $user['pw_salt']);

        if ( ! $stmt->execute()) {

            throw new Exception("PDO won't!");

        } else {
            
            unset($user['pw_salt']);
            unset($user['password']);

            return $user;

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
