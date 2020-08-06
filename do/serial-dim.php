<?php
/**
 * Dim the display of all connected components in the manetheren network.
 * @author Tanner Hildebrand
 */
require_once('../common/serial.php');
$conn = new SerialConnection();
// this will prompt the far end to access 
// 'services/dim.php' 
$dimmed = $conn->sendOther('dim.php');
if( $dimmed ){
    echo "Successfully dimmed";
}
?>