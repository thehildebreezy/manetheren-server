<?php
/**
 * Requests.php
 * manetheren-server from tannerjhildebrand.com
 * @author  Tanner Hildebrand
 * @version 1.0
 */
require_once('database.php');
require_once('settings.php');
require_once('config.php');
require_once('parsers.php');

/**
 * Generalized function for processing a supplicants request to the proxy server
 * Checks for cached data and provides that if it is not stale, otherwise parses a server
 * response and returns our clean data.
 * @param name string name of the requested service we're processing - for caching/selecting
 * @param type int type of service being requested as a predefined value from Config class
 * @return string the JSON string value of the service response
 */
function request_api($name, $type, $cache = true){


    // Assume first that we do not have cached data and will need to insert
    // a new response into our cache table
    $insert = true;

    // Assume we have not received any cached data or remote server data
    $response = null;

    // initialize for later reference
    $conn = null;
    $data = null;

    // only cache if we want to cache, otherwise no cache
    if( $cache ){

        // check to see if we have any cached data in the Database
        // 3600 seconds in an hour
        $conn = request_cache($name,['staleName'=>'StaleData','staleTime',3600]);
        $data = $conn->next();


        // if we do have data in the cache table, check if it is useable
        if( $data ){

            // save the cached data as the response even if it is stale in case
            // there is no server response
            $response = $data['data'];
            
            // if the data is not stale, return the cached data to the user and exit function
            if( !$data['StaleData'] ){
                return request_parse( $name, $type, $response );
                //return $response;
            }

            // we found cached data for this $name, we will not be inserting a new row
            $insert = false;

        }

        // save the old cached data in case we do not get a response from the server
        $old = $response;
    
    }

    // make a request of the remote server and see what comes back
    $response = request_remote($conn,$name,$type);

    if( $cache ){
        // if we didn't get a response from the server, serve up the stale cached data
        // and exit the function
        if(!$response){
            return request_parse( $name, $type, $old );
            //return $old;
        }

        // if we got data from the server, we'll parse it now
        // update our cache
        request_update($name,$response,$conn,$insert);

    }

    // parse the response to clean our data up
    $response = request_parse( $name, $type, $response );

    // return the response to the end point
    return $response;
}

/**
 * Queries our settings database cache table for the named service and returns the connection
 * @param name string name of the service we are requesting the cache data for
 * @param args array an associative array of strings containing deviations from our default values
 *              staleName string default 'isStale' name to return stale boolean
 *              staleTime int default 60 minutes before data goes stale
 * @return Database returns the connection's database wrapper for use
 */
function request_cache($name,$args){
    // open connection
    $conn = new Database('settings');
    // load any deviations from defaults
    $staleName = isset($args['staleName']) ? $args['staleName'] : 'isStale';
    $staleTime = isset($args['staleTime']) ? $args['staleTime'] : 600; // 60 sec * 10 min
    // process and execute statement
    $conn->statement("SELECT *, IF(TIMEDIFF(NOW(), updated) >= $staleTime, 1, 0) AS $staleName FROM cache WHERE name=?");
    $conn->execute([$name]);
    return $conn;
}

/**
 * Requests a response from a remote servers API for us to proxy or cache to the end point
 * @param conn Database the database connected object we are referencing to pull settings from
 * @param name string name of the service we are requesting from the remote server
 * @param type int type of service value defined in Config that we are looking to process
 * @return string JSON string value if successful, null if not
 */
function request_remote($conn, $name, $type){
    

    // if we are requesting services from a weather API
    if( $type == Config::API_TYPE_WEATHER ){

        // get the zip value
        $settings = new Settings();
        $zip = $settings->intValue('weather','zip');
        if( !$zip ) $zip = 28310;

        // pull the path to our chosen API from our Config class and specify using name wether it
        // is weather or forcast, the zip code, and provide the API key
        $path = sprintf( Config::API_STRING_WEATHER, $name, $zip, Config::API_KEY_WEATHER );

    // if we are requesting services from a Photos API
    } elseif( $type == Config::API_TYPE_PHOTOS ) {

        // this is a static path in this version of manetheren-server
        $path = sprintf( Config::API_STRING_PHOTOS );
    
    } elseif( $type == Config::API_TYPE_RECIPES_INGREDIENTS ){

        $ingredients = "chicken";
        $number = "100";
        if( isset($_GET['ingredients']) ){
            $ingredients = $_GET['ingredients'];
        }
        if( isset($_GET['number']) ){
            $number = $_GET['number'];
        }
        $path = sprintf( Config::API_STRING_RECIPES_INGREDIENTS, $ingredients, $number, Config::API_KEY_RECIPES );
    
    } elseif( $type == Config::API_TYPE_RECIPE ){
        $id = "100";
        if( isset($_GET['id']) ){
            $id = $_GET['id'];
        }
        $path = sprintf( Config::API_STRING_RECIPE, $id, Config::API_KEY_RECIPES );

    // if no matching types, return null
    } else {
        return null;
    }
    

    // mask warnings we receive if there is no connection
    set_error_handler(function(){});
    // make the request of the foreign server
    $arrContextOptions=array(
        "ssl"=>array(
            "verify_peer"=>false,
            "verify_peer_name"=>false,
        ),
    );
    $api_data = file_get_contents($path, false, stream_context_create($arrContextOptions));
    // restore old error handler
    restore_error_handler();

    // and return the result to the end point
    return $api_data;
}

/**
 * Parses a server response and cleans the data in to a consistent format for end points
 * @param name string name of the service to process
 * @param type int type of service we will be looking at
 * @param data string JSON data string we need to parse through
 * @return string JSON data with a consistent format, or null if bad data
 */
function request_parse( $name, $type, $data ){

    // parse a photo type resposne
    if( $type == Config::API_TYPE_PHOTOS ){

        $classname = Config::API_PARSER_PHOTOS;

    // parse a weather type response
    } elseif( $type == Config::API_TYPE_WEATHER) {

        $classname = Config::API_PARSER_WEATHER;

    // otherwise fail
    } else {
        return null;
    }
    
    // initialize and parse the data
    $parser = new $classname($data);
    return $parser->result($name);
}

/**
 * Updates the cache table or inserts a new row as specified in the arguments
 * @param name string name of the service to cache
 * @param response string JSON string of the data to cache
 * @param conn Database connection object to the settings database for the cache table
 * @param insert boolean value is true if we are inserting, false if updating
 */
function request_update($name, $response, $conn, $insert){
    
    // generate the SQL statements necessary to update our cache table
    $update_statement = 'UPDATE cache SET description=:desc, data=:data, updated=now(), linkid=:lid WHERE name=:name';
    $insert_statement = 'INSERT INTO cache (name, description, data, updated, linkid) VALUES (:name, :desc, :data, now(), :lid)';

    // select the appropriate statement based off provided input
    $stmt = $insert ? $insert_statement : $update_statement;

    // prepare and execute the statement
    $conn->statement($stmt);
    $conn->execute(['name'=>$name,'desc'=>'Weather cache','data'=>$response,'lid'=>0]);
}

/**
 * Calls for a response from the weather API, weather service
 */
function request_weather(){
    return request_api('weather',Config::API_TYPE_WEATHER);
}

/**
 * Calls for a response from the weather API, forecast service
 */
function request_forecast(){
    return request_api('forecast',Config::API_TYPE_WEATHER);
}

/**
 * Calls for a response from the photos API
 */
function request_photos(){
    return request_api('photos',Config::API_TYPE_PHOTOS, false);
}

/**
 * Calls for a response for recipe by ingredients API
 */
function request_recipes_ingredients(){
    return request_api('recipes_ingredients',Config::API_TYPE_RECIPES_INGREDIENTS, false);
}

/**
 * Calls for a response for a recipe API
 */
function request_recipe(){
    return request_api('recipe',Config::API_TYPE_RECIPE, false);
}
?>
