<?php
/**
 * Parsers.php
 * manetheren-server from tannerjhildebrand.com
 * Provides the parsers for providing consistent data to end points
 * @author tanner hildebrand
 * @version 1.0
 */

/**
 * APIParser class that is a base class for future parsers
 */
class APIParser {

    private $raw = null;
    private $parsed = null;
    private $version = 1;

    /**
     * Construct the APIParser class and load the raw data into memory for processing
     * @param raw string the raw JSON string we want to make our own
     */
    function __construct( $raw ){
        $this->raw = json_decode( $raw );
        $this->version = isset($_GET['v']) ? intval($_GET['v']) : 1;
    }

    /**
     * Process the result and return the encoded JSON
     * @param name string name of the service to process
     * @return string JSON string object
     */
    function result($name){
        $this->parsed = $this->raw;
        return json_encode($this->parsed);
    }
}

/**
 * PhotoParser extends APIParser and overloads the result method
 * to provide clean data for photo services
 */
class PhotoParser extends APIParser {
    // we don't have to do anything yet
    // I just want the parser to quietly pass the data along without modifying
}

/**
 * PhotoParser extends APIParser and overloads the result method
 * to provide clean data for weather and forecast data
 */
class OpenWeatherMapParser extends APIParser {

    // we don't have to do anything yet
    // I just want the parser to quietly pass the data along without modifying

}
?>