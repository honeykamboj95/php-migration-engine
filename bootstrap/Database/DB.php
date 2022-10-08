<?php

namespace Go\Database;

include_once 'config.php';

class DB {

    var $conn;

    function  __construct(){
        $this->conn = new \mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        // Check connection
        if ($this->conn->connect_errno) {
            echo "Failed to connect to MySQL: " . $this->conn->connect_error;
            die;
        }
    }

    function createMigrationTableIfNotExists(){
        $this->raw("CREATE TABLE IF NOT EXISTS migrations (id INT PRIMARY KEY AUTO_INCREMENT, migration varchar(255) NOT NULL, batch int NOT NULL)", true);
    }

    function insertMigrationData($file, $batch_id){

        $file = str_replace('.php', '', $file);

        $this->raw("INSERT INTO migrations (migration, batch) VALUES ('".$file."', ".$batch_id.")");
    }

    function deleteMigrationData($file){
        $file = str_replace('.php', '', $file);
        $this->raw("DELETE FROM migrations WHERE migration = '".$file."'");
    }

    function getBatchId($rollback = false){

        $batch = 0;

        $command = "SELECT * FROM migrations ORDER BY id DESC LIMIT 1";

        $result = $this->conn->query($command);

        if($result->num_rows > 0){
            $batch = $result->fetch_assoc()['batch'];
        }

        if($rollback){
            return $batch;
        }else{
            return $batch + 1;
        }
    }

    function getMigratedFiles($rollback = false){
        $files = [];

        $command = "SELECT batch, migration FROM migrations";

        if($rollback){
            $command .= ' WHERE batch = '.$this->getBatchId(true);
        }

        $result = $this->conn->query($command);

        if($result->num_rows > 0){
            while($row = $result->fetch_assoc()) {
                $files[] = $row['migration'];
            }
        }

        return $files;
    }

    function raw($command, $system = false){
        if($this->conn->query($command) === TRUE) {
            
        } else {
            echo sprintf("\033[31m%s\033[0m", "Error").": " . $this->conn->error.PHP_EOL;
            $this->close();
            die;
        }
    }

    function close(){
        $this->conn->close();
    }

}