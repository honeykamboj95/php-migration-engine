<?php

namespace Go\Commands;

use Go\Database\DB;

include_once 'config.php';

class Migration {

    var $db;

    var $path = 'database/migrations/';

    var $content = '<?php

require "vendor/autoload.php";

use Go\Database\Migration;

class [FILENAME] extends Migration {
    
    function onUp(){

    }

    function onDown(){

    }

}';

    function __construct(){
        $this->db = new DB();
    }

    function createMigration($name, $tenant = false){

        $filename = $this->clean($name);

        $content = str_replace('[FILENAME]', $filename, $this->content);

        $this->createFile($filename, $content, $tenant);
    }

    function createFile($filename, $content, $tenant = false){

        if($tenant){
            $this->path .= 'tenant/';
        }

        $name = $this->path;

        $name .= date('Y_m_d_').time().'_'.$filename.'.php';

        $files = array_diff(scandir($this->path), array('.', '..', 'tenant'));

        foreach($files as $file){
            $file_name =  $this->cleanNumbers($file);
            $temp_name = $this->cleanNumbers(str_replace($this->path, '', $name));
            if($file_name == $temp_name){
                echo ' Migarion already exists!'.PHP_EOL;
                return;
            }
        }

        if(file_exists($name)){
            echo sprintf("\033[31m%s\033[0m", $filename);
            echo ' Migarion already exists!'.PHP_EOL;
        }else{
            $fp = fopen($name,"wb");
            fwrite($fp, $content);
            fclose($fp);

            echo sprintf("\033[32m%s\033[0m", $filename);
            
            echo ' Migarion created successfully!'.PHP_EOL;
        }
        
    }

    function clean($string) {
        $string = str_replace(' ', '_', $string); // Replaces all spaces with hyphens.
        $string = preg_replace('/[^A-Za-z0-9\_]/', '', $string); // Removes special chars.
     
        return preg_replace('/_+/', '_', $string); // Replaces multiple hyphens with single one.
    }

    function cleanNumbers($string) {
        $string = str_replace('.php', '', $string); // Replaces all spaces with hyphens.
        $string = preg_replace('/[^A-Za-z\_]/', '', $string); // Removes special chars.
     
        return preg_replace('/_+/', '_', $string); // Replaces multiple hyphens with single one.
    }

    function migrate($rollback = false, $tenant = false){

        if($tenant){
            $this->path .= 'tenant/'; 
        }

        //create migration table if not exists
        $this->db->createMigrationTableIfNotExists();

        $batch_id = $this->db->getBatchId();

        //scan directory for all avaialble migration
        $files = array_diff(scandir($this->path), array('.', '..', 'tenant'));

        //get all migrated files
        $migrated_files = $this->db->getMigratedFiles($rollback);

        $migration_arr = [];

        foreach($files as $file){
            if($rollback){
                if(in_array(str_replace('.php', '', $file), $migrated_files)){
                    $migration_arr[] = $file;
                }
            }else{
                if(!in_array(str_replace('.php', '', $file), $migrated_files)){
                    $migration_arr[] = $file;
                }
            }
        }

        if(count($migration_arr)){
            foreach($migration_arr as $file){

                $file_name = $this->cleanNumbers($file);
                $file_name = substr($file_name,1);
    
                if($rollback){
    
                    //including migration as a class
                    include_once $this->path.$file;
    
                    //calling constructor of included class
                    new $file_name([
                        'file' => $file,
                        'rollback' => $rollback
                    ]);
    
                    $this->db->deleteMigrationData($file);
                }else{
    
                    //including migration as a class
                    include_once $this->path.$file;
    
                    //calling constructor of included class
                    new $file_name([
                        'file' => $file,
                        'rollback' => $rollback
                    ]);
    
                    $this->db->insertMigrationData($file, $batch_id);
                }
            }
        }else{
            echo sprintf("\033[31m%s\033[0m", "Nothing to migrate!").PHP_EOL;
        }

        //connection closed
        $this->db->close();
    }

}