<?php
/**
 * Database.php
 * manatheren-server at tannerjhildebrand.com
 * Creates a wrapper for connecting to a database for consistency across versions
 * and ease of managing code base
 * @author Tanner Hildebrand
 * @version 1.0
 */
require_once('config.php');

/**
 * Database class wraps our database connection in to a useful helper
 */
class Database {
    // load our connection information from the Config file
    private $host = Config::DBHOST;
    private $user = Config::DBUSER;
    private $pass = Config::DBPASS;

    // private use
    private $database;

    // public use
    public $statement;
    public $connection;

    /**
     * Constructs a new Database object and initializes a connection for the chosen database
     * @param database string name of the database to connect to
     * @return Database a new Database object
     */
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

    /**
     * Closes the database connection on destruction
     */
    function __destruct(){
        $this->close();
    }

    /**
     * Closes the database connection gracefully
     */
    function close(){
        $this->connection = null;
    }

    /**
     * Prepare a statement for safer input handling and for inputing variable values
     * in this wrapper function
     * @param statement string the SQL statement to be evaluated
     */
    function statement( $statement ){
        $this->statement = $this->connection->prepare($statement);
    }

    /**
     * Execute the statement with the given parameters and return the entire array of results
     * @param values array of values to match in the prepared SQL string
     * @return array an associative array of all database rows
     */
    function result( $values=[] ){
        $this->execute($values);
        return $this->statement->fetchAll();
    }

    /**
     * Gets the next record in the database response for an already evaluated statement
     * @return array an associative array of a single database row
     */
    function next(){
        if( !$this->statement ) return null;
        return $this->statement->fetch();
    }

    /** 
     * Executes the update/insert statements as specified in an already prepared statement
     * @param values array of values to match in the prepared SQL string
     */
    function execute( $values=[] ){
        $this->statement->execute($values);
    }

}
?>