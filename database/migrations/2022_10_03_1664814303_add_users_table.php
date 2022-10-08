<?php

require "vendor/autoload.php";

use Go\Database\Migration;

class add_users_table extends Migration {
    
    function onUp(){
        $this->db->raw("CREATE TABLE employee (id int NOT NULL AUTO_INCREMENT, first_name varchar(255) NOT NULL, last_name varchar(255), email varchar(255), PRIMARY KEY (id))");

        $this->db->raw("CREATE TABLE users (id int NOT NULL AUTO_INCREMENT, first_name varchar(255) NOT NULL, last_name varchar(255), email varchar(255), PRIMARY KEY (id))");
    }

    function onDown(){
        $this->db->raw("DROP TABLE IF EXISTS users");
        $this->db->raw("DROP TABLE IF EXISTS employee");
    }

}