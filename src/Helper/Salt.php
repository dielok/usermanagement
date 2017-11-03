<?php
/**
 * Description of Salt
 *
 * @author martinleue
 */
namespace Helper;

class Salt {
    
    private $salt;
    
    public function __construct() {
        $this->salt = openssl_random_pseudo_bytes(16);
        $this->salt = bin2hex($this->salt );
    }
    
    public function salt(){
        return $this->salt;
    }
}
