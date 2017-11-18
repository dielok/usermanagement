<?php
namespace Log;

use PDO;
use Log\LogModel;
/**
 * Description of LogController
 *
 * @author martinleue
 */
class LogController {
    private $pdo;
    
    public function __construct(PDO $pdo = null){
        if($pdo === null){return;}
        $this->pdo = $pdo;
    }
    
    public function createLog(){
        $logModel       = new LogModel($this->pdo);
        $ip             = $_SERVER["REMOTE_ADDR"];
        $site           = $_SERVER['REQUEST_URI'];
        $browser        = $_SERVER["HTTP_USER_AGENT"];
        $http_method    = $_SERVER['REDIRECT_STATUS'];
        $port           = $_SERVER['SERVER_PORT'];
        $content_length = $_SERVER['CONTENT_LENGTH'];
        $log            = "Http-Status=$http_method|Content-Length=$content_length|Site=$site|IP=$ip|Port=$port|Browser=$browser";  
        $logModel->createLog($ip,$log);
    }
}
