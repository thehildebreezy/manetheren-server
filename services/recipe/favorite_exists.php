<?php
require_once("../../common/recipes.php");

if( valid_recipe_request( Config::API_TYPE_RECIPE_REMOVE ) ){
    echo (in_favorites( $_GET['id'] ) ) ? "yes" : "no";
} else {
    echo "no";
}
?>