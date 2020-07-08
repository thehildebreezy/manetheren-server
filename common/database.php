<?php
require_once('config.php');
class Database {
    private $host = Config::DBHOST;
    private $user = Config::DBUSER;
    private $pass = Config::DBPASS;

    private $table;
    private $database;

    public $statement;
    public $connection;

    function __construct( $database ){
        $this->database = $database;
        try {
            $this->connection = new PDO("mysql:host=$this->host;dbname=$database", $this->user, $this->pass);
            // set the PDO error mode to exception
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
    }

    function __destruct(){
        $this->close();
    }

    function close(){
        $this->connection = null;
    }

    function statement( $statement ){
        $this->statement = $this->connection->prepare($statement);
    }

    function result( $values=[] ){
        $this->execute($values);
        return $this->statement->fetchAll();
    }

    function next(){
        return $this->statement->fetch();
    }

    function execute( $values=[] ){
        $this->statement->execute($values);
    }

    function set_table( $table ){
        $this->table = $table;
    }
}
?>