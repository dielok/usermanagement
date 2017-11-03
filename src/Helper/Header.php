<?php
/**
 * Description of Header
 *
 * @author martinleue
 */
namespace Helper;

class Header {
    
    public $dashboard;
    public $register;
    public $signin;
    public $signout;
    public $home;
    
    
    public function __construct() {
        $this->dashboard = "/dashboard";
        $this->register  = "/register";
        $this->signin    = "/signin";
        $this->signout   = "/signout";
        $this->home      = "/";
    }
}
