<?php 
if( isset($_GET['adjust']) && $_GET['adjust'] == 'timezone'):
    require_once("../common/database.php");

    $conn = new Database('settings');
    $conn->statement("SELECT * FROM general WHERE name='timezone'");
    $conn->execute();
    $data = $conn->next();
    
    $timezone = 'EST';
    
    if( $data ){
        $timezeone =  $data['textval'];
    }

    $off = timezone_offset_get( timezone_open( $timezone ), new DateTime() );
    echo time()+$off;
else:
    echo time();
endif;
?>