<?php
namespace User;

class UserModel {
    
    /* DB-Table users */
    public $user_id;
    public $email;
    public $password;
    public $lastname;
    public $firstname;
    public $salt;
    public $session_id;
    public $created_at;
    public $status;
    
    public function __construct(){
        $this->status = 201;
    }
}
