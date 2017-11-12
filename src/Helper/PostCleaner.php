<?php
namespace Helper;

/**
 * Description of PostCleaner
 *
 * @author martinleue
 */
class PostCleaner {
    public function params($str){
        return htmlentities($str, ENT_QUOTES, 'UTF-8');
    }
}
