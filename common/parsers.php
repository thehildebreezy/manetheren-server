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

    protected $raw = null;
    protected $parsed = null;
    protected $version = 1;

    /**
     * Construct the APIParser class and load the raw data into memory for processing
     * @param raw string the raw JSON string we want to make our own
     */
    function __construct( $raw ){
        $this->raw = json_decode( $raw, true );
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
    function result($name){
        
        // version 0 allows "other" version
        if( $name == "forecast" &&
            $this->version == 0 && 
            isset($_GET['other']) && 
            $_GET['other'] == 'simple' ){

                // Generate an idea of what day it is from the first listed
                // date time text group from the OWM response

                $d = new DateTime( $this->raw['list'][0]['dt_txt'] );
                $d->setTime(0,0); // reset time to 0 in case

                $newList = array();

                // now find the first item in the forecast that beats this day; i.e, date+1
                $nextDayIndex = 0;
                for( $i=0; $i < count($this->raw['list']); $i++ ){
                    $next = new DateTime( $this->raw['list'][$i]['dt_txt'] );
                    $next->setTime(0,0);   // reset day to 0 hours like we did to the base date
                    if($next > $d){         // found day > today
                        if( isset($this->raw['list'][$i+4]) ){
                            $newList[count($newList)] = $this->raw['list'][$i+4];
                        }
                        $d = $next;
                    }
                }

                $this->parsed = $this->raw;
                $this->parsed['list'] = $newList;
                return json_encode($this->parsed);
            
        }
        
        return parent::result($name);
    }
}
?>