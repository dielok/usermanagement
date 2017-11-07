<?php
/**
 * Description of Session
 *
 * @author martinleue
 */
namespace Helper;

class Session {

    public function session_id(){
        
        return session_id();
    }
    
    public function start(){
        
        return session_start();
    }
    
    public function isSession($session){
        
        if(isset($session)){
            
            return true;
        }
        return false;
    }
    
    public function regenerate(){
        
        return session_regenerate_id(true);
    }
    
    public function destroy(){
        
        return session_destroy();
    }
}
