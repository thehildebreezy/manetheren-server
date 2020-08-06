<?php 
if( !isset($_GET['level']) ){
    die;
}
$level = intval($_GET['level']);
echo file_get_contents('http://tworivers/actions/brightness/set.php?level='.$level); 
?>