<?php
/**
 * Helpers.php
 * manetheren-server from tannerjhildebrand.com
 * @author  Tanner Hildebrand
 * @version 1.0
 */

 /**
  * Makes an HTTPS request and returns the data
  * @param path string the path to request a response for
  * @return string the response data from the server, typically JSON
  */
function https_request_helper( $path ){

    // mask warnings we receive if there is no connection
    //set_error_handler(function(){});
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $path);
    // Set so curl_exec returns the result instead of outputting it.
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // Get the response and close the channel.
    $api_data = curl_exec($ch);
    curl_close($ch);
    //$api_data = file_get_contents($path);
    // restore old error handler
    restore_error_handler();

    return $api_data;
}
?>