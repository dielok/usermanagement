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
    public $created_at;
    public $updated_at;
    public $status;
    
    public function __construct(){
        $this->status = 201;
    }
}
