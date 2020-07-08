<?php
require_once('database.php');
require_once('config.php');


function request_update($name, $response, $conn, $insert){
    
    $update_statement = 'UPDATE cache SET description=:desc, data=:data, updated=now(), linkid=:lid WHERE name=:name';
    $insert_statement = 'INSERT INTO cache (name, description, data, updated, linkid) VALUES (:name, :desc, :data, now(), :lid)';

    $stmt = $insert ? $insert_statement : $update_statement;

    $conn->statement($stmt);
    $conn->execute(['name'=>$name,'desc'=>'Weather cache','data'=>$response,'lid'=>0]);
}

function request_settings($name){
    $conn = new Database('settings');
    $conn->statement('SELECT *, IF(HOUR(TIMEDIFF(NOW(), updated)) >= 1, 1, 0) AS StaleData FROM cache WHERE name=?');
    $conn->execute([$name]);

    return $conn;
}

function request_api($name, $type){
    $conn = request_settings($name);

    $data = $conn->next();

    $insert = true;

    $response = null;

    if( $data ){
        $response = $data['data'];
        // and here we would check if we need to update our cache
        
        if( !$data['StaleData'] ){
            return $response;
        }

        $insert = false;

    }

    $old = $response;

    $response = api_request($conn,$name,$type);

    if(!$response){
        return $old;
    }

    request_update($name,$response,$conn,$insert);

    return $response;
}

function request_weather(){
    return request_api('weather',Config::API_TYPE_WEATHER);
}

function request_forecast(){
    return request_api('forecast',Config::API_TYPE_WEATHER);
}

function request_photos(){
    return request_api('photos',Config::API_TYPE_PHOTOS);
}

function api_path( $type ){

}

function api_request($conn, $name, $type){
    $conn->statement('SELECT * FROM weather WHERE name=?');
    $conn->execute(['zip']);
    $data = $conn->next();

    $zip = 52233;
    if( $data ){
        $zip = $data['intval'];
    }


    if( $type == Config::API_TYPE_WEATHER ){
        $path = sprintf( Config::API_STRING_WEATHER, $name, $zip, Config::API_KEY_WEATHER );
    } elseif( $type == Config::API_TYPE_PHOTOS ) {
        $path = sprintf( Config::API_STRING_PHOTOS );
    } else {
        return null;
    }
    
    set_error_handler(function(){});
    $api_data = file_get_contents($path);
    restore_error_handler();

    return $api_data;
}

?>
