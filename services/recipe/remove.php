<?php
require_once("../common/recipes.php");

if( valid_recipe_request( Config::API_TYPE_RECIPE_REMOVE ) ){
    echo delete_favorite_recipe( $_POST['id'] );
} else {
    echo false;
}
?>