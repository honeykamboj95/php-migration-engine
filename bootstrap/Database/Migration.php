<?php

namespace Go\Database;

abstract class Migration {

    var $db;

    function __construct($argv){

        $this->db = new DB();

        if($argv['rollback']){
            $this->onDown();
        }else{
            $this->onUp();
        }

        echo sprintf("\033[33m%s\033[0m", $argv['file']).' : Executed successfully.'.PHP_EOL;
    }

    abstract function onUp();

    abstract function onDown();

}