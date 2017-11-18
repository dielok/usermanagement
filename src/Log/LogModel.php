<?php
namespace Log;

use PDO;
/**
 * Description of LogModel
 *
 * @author martinleue
 */
class LogModel {
    private $pdo, $table;
    
    public function __construct(PDO $pdo = null) {
        if($pdo === null){return;}
        $this->pdo = $pdo;
        $this->table = $this->table();
    }
    
    private function table(){
        return "Logs";
    }
    
    public function createLog($ip,$log){
        $stmt = $this->pdo->prepare("INSERT INTO {$this->table} (ip,log) VALUES (:ip, :log)");
        $stmt->bindParam(":ip", $ip);
        $stmt->bindParam(":log", $log);
        $stmt->execute();
    }
}
