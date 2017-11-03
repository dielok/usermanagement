<?php
/**
 * Description of Cleaner
 *
 * @author martinleue
 */
namespace Helper;

class Cleaner {
    
    public function params($str){
        return htmlentities($str, ENT_QUOTES, 'UTF-8');
    }
}
