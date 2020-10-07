<?php
require_once("../common/recipes.php");

if( valid_recipe_request( Config::API_TYPE_RECIPE_ADD ) ){
    echo add_favorite_recipe( $_POST['id'], $_POST['title'], $_POST['data'], $_POST['image']);
} else {
    echo false;
}
?>