<?php 
if( isset($_GET['adjust']) && $_GET['adjust'] == 'timezone'):
    require_once("../common/settings.php");

    $settings = new Settings();
    
    $timezone = $settings->textValue('general','timezone');
    
    if( !$timezone ){
        $timezeone =  'EST';
    }

    $off = timezone_offset_get( timezone_open( $timezone ), new DateTime() );
    echo time()+$off;
else:
    echo time();
endif;
?>