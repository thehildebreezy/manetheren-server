<?php
/**
 * Dim the display of all connected components in the manetheren network.
 * @author Tanner Hildebrand
 */
require_once('../common/serial.php');
$conn = new SerialConnection();
$conn->sendOther('dim.php');
?>