<?php

require_once __DIR__.'/vendor/autoload.php';

include_once 'config.php';

use Go\Commands\Migration;
use Go\Commands\Controller;

class Go {

    var $commands_info = [
        'php go help' => 'Show all available commands',

        'php go db make:migration' => 'Create migration in main databases',
        'php go db migrate' => 'Run all migrations on main databases',
        'php go db migrate' => 'Run all migrations on main databases',
        'php go tenant make:migration' => 'Coming Soon!',
        'php go tenant migrate' => 'Coming Soon!',

        // 'php go tenant make:migration' => 'Create migration in main databases',
        // 'php go tenant migrate' => 'Run all migrations on main databases',
    ];

    function __construct($argv){

        if(count($argv) < 2 || $argv[1] == 'help'){
            $this->help();
        }elseif($argv[1] == 'db'){
            if(!$argv[2]){
                echo sprintf("\033[31m%s\033[0m", 'Command Not Found!'.PHP_EOL);
                $this->help();
            }else if($argv[2] == 'make:migration'){
                if($argv[3]){
                    $migration = new Migration();
                    $migration->createMigration($argv[3]);
                }else {
                    echo sprintf("\033[31m%s\033[0m", 'Command Not Found!2'.PHP_EOL);
                    $this->help();
                }
            }else if($argv[2] == 'migrate'){
                $migration = new Migration();
                if(count($argv) < 4){
                    $migration->migrate();
                }else{
                    if($argv[3] == '--rollback'){
                        $migration->migrate(true);
                    }else{
                        echo sprintf("\033[31m%s\033[0m", 'Command Not Found!'.PHP_EOL);
                        $this->help();
                    }
                }
            }else{
                echo sprintf("\033[31m%s\033[0m", 'Command Not Found!'.PHP_EOL);
                $this->help();
            }
        }else{
            
            echo sprintf("\033[31m%s\033[0m", 'This Command is curently under development!'.PHP_EOL);
            $this->help();
            return;
            
            if(!$argv[2]){
                echo sprintf("\033[31m%s\033[0m", 'Command Not Found!'.PHP_EOL);
                $this->help();
            }else if($argv[2] == 'make:migration'){
                if($argv[3]){
                    $migration = new Migration();
                    $migration->createMigration($argv[3], true);
                }else {
                    echo sprintf("\033[31m%s\033[0m", 'Command Not Found!2'.PHP_EOL);
                    $this->help();
                }
            }else if($argv[2] == 'migrate'){
                $migration = new Migration();
                if(count($argv) < 4){
                    $migration->migrate(false, true);
                }else{
                    if($argv[3] == '--rollback'){
                        $migration->migrate(true, true);
                    }else{
                        echo sprintf("\033[31m%s\033[0m", 'Command Not Found!'.PHP_EOL);
                        $this->help();
                    }
                }
            }else{
                echo sprintf("\033[31m%s\033[0m", 'Command Not Found!'.PHP_EOL);
                $this->help();
            }
        }

    }
    
    function help(){
        echo 'Please use comands from below list only:'.PHP_EOL;
        foreach($this->commands_info as $command => $desc){
            echo sprintf("\033[33m%s\033[0m", $command);
            echo ' : '.$desc.PHP_EOL;
        }
    }

}

new Go($argv);