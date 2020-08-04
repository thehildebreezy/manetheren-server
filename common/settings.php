<?php
/**
 * Helper functions to access settings
 */
require_once('database.php');

class Settings {

    private $conn = false;

    private function exec($table, $name){
        $this->conn->statement("SELECT * FROM $table WHERE name=?");
        $this->conn->execute([$name]);
    }

    function __construct(){
        $this->conn = new Database('settings');
    }

    function textValue($table, $name){
        $this->exec( $table, $name );
        return $this->conn->next()['textval'];
    }

    function intValue($table, $name){
        $this->exec( $table, $name );
        return $this->conn->next()['intval'];
    }
}
?>