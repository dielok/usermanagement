<?php
/**
 * Description of UserModel
 *
 * @author martinleue
 */
namespace User;

class UserModel {
    
    /* DB-Table users */
    public $user_id;
    public $email;
    public $password;
    public $salt;
    public $created_at;
}
