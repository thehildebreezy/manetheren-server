<?php
/**
 * Recipes.php
 * manetheren-server from tannerjhildebrand.com
 * @author  Tanner Hildebrand
 * @version 1.0
 */

require_once('database.php');
require_once('settings.php');
require_once('config.php');
require_once('helpers.php');



/**
 * Checks if the requested action is valid
 * @param request_type int The Config:: api type either add or remove
 * @return boolean true if arguments are good, false if not
 */
function valid_recipe_request( $request_type ){

    $request_check = $request_type - Config::API_TYPE_RECIPE_ADD;
    $argument_check_remove = isset($_POST['id']);
    $argument_check_add = $argument_check_remove && isset($_POST['data']) && isset($_POST['title'] && isset($_POST['image']));

    return ($request_check == 0 && $argument_check_add) || ($request_check == 1 && $argument_check_remove);
}

/**
 * Returns the queries search from the
 */
function search_recipes( $query = "chicken" ){
    $path = sprintf(Config::API_STRING_RECIPE_SEARCH, $query);
    $result = https_request_helper($path);
    return $result;
}

/**
 * Gets a recipe item from the database based on id
 * @param id int the integer value of the recipe to get fromdatabase
 * @param db Database object if one already exists
 * @return Database returns the database connection object
 */
function get_by_id( $id, $db = null ){
    // open connection
    $conn = $db ?: new Database('settings');
    // process and execute statement
    $conn->statement("SELECT * FROM recipes WHERE recipeid=?");
    $conn->execute([$id]);
    return $conn;
}

/**
 * Save a recipe to the database
 * @param id int recipe ID to save
 * @param title string Title of the recipe to save
 * @param data JSON recipe instruction data to cache
 * @param iamge_path string path to the image to display as a cover
 * @return boolean false if the item is already in favorites, true otherwise
 */
function add_favorite_recipe( $id, $title, $data, $image_path ){

    $conn = new Database('settings');
    $in_fav = in_favorites( $id, $conn );

    if( $in_fav ) {
        return false;
    }

    $stmt = 'INSERT INTO recipes (recipe_id, title, cache, image_path) VALUES (:id, :title, :cache, :image )';

    // prepare and execute the statement
    $conn->statement($stmt);
    $conn->execute(['id'=>$id,'title'=>$title,'cache'=>$data,'image'=>$image_path]);

    $conn->close();
    return true;
}

/**
 * Removes a favorite recipe from the database
 * @param id int the recipe ID to remove from favorites
 */
function delete_favorite_recipe( $id ){
    
    $conn = new Database('settings');
    $in_fav = in_favorites( $id, $conn );

    if( !$in_fav ) {
        return false;
    }

    $stmt = 'DELETE FROM recipes WHERE recipe_id=?';

    // prepare and execute the statement
    $conn->statement($stmt);
    $conn->execute([$id]);

    $conn->close();
    return true;
}

/**
 * Detirmines if the given item is in the favorites or not
 * @param id int the recipe id to look for in the database
 * @param db Database object if one already exists
 * @return boolean true if already in the database, false otherwise
 */
function in_favorites( $id, $db = null ){
    $conn = get_by_id( $id, $db );
    $data = $conn->next();
    return !(!($data));
}

?>