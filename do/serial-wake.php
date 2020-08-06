<?php
/**
 * Wake the display of all connected components in the manetheren network.
 * @author Tanner Hildebrand
 */

require_once('../common/serial.php');
$conn = new SerialConnection();
// this will prompt the far end to access 
// 'services/wake.php' 
$woke = $conn->sendOther('wake.php');
if( $woke ){
    echo "Successfully woke";
}
?>