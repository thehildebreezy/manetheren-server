<?php
require_once("../../common/recipes.php");

if( valid_recipe_request( Config::API_TYPE_RECIPE_SINGLE ) ){
    echo get_single_recipe( $_GET['id'] );
} else {
    echo false;
}
?>