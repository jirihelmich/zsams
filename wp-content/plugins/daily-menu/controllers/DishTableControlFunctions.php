<?php
/**
 * AJAX Functions to handle each actions on the table of dish
 */

require_once( DM_PLUGIN_DIR . '/models/JSONError.php' );

/**
 * This function is called by Javascript in order to list all the stored dishes
 * It returns (more exactly print) a JSON object with a list of dishes
 * The parameters "jtStartIndex", "jtPageSize" and "jtSorting" are expected in the HTTP request
 * Theres parameters are used by jTable to handle paging and sorting
 * @see Dish
 */
function listDishesCallback() {
	print ListDishes::getPagedDishesJSON($_GET["jtStartIndex"],$_GET["jtPageSize"],$_GET["jtSorting"]);
	die();
}

/**
 * This function is called by Javascript in order to save a dish (name, composition, etc.)
 * It returns (more exactly print) a JSON object with the just saved dish
 * @see Dish
 */
function createDishCallback() {
	$dish = new Dish();
	$dish->setFromPOST($_POST);
	if (!$dish->save()) {
		$error = new JSONError(__("Unable to save the dish",DM_DOMAIN_NAME));
		print $error->getJSON();
		die();
	}
	print $dish->getJSON();
	die();
}

/**
 * This function is called by Javascript in order to update a dish (name, composition, etc.)
 * It returns (more exactly print) a JSON object with the just saved dish 
 * The parameter "id_dish" is expected in the HTTP request and must correspond to the ID of an existing dish
 * @see Dish
 */
function updateDishCallback() {
	$dish = new Dish($_POST["id_dish"]);
	$dish->setFromPOST($_POST);
	if (!$dish->save()) {
		$error = new JSONError(__("Unable to save the dish",DM_DOMAIN_NAME));
		print $error->getJSON();
		die();
	}
	print $dish->getJSON();
	die();
}

/**
 * This function is called by Javascript in order to delete a dish
 * It returns (more exactly print) a JSON object with the just deleted dish
* The parameter "id_dish" is expected in the HTTP request and must correspond to the ID of an existing dish
 * @see Dish
 */
function deleteDishCallback() {
	$dish = new Dish($_POST["id_dish"]);
	if (!$dish->delete()) {
		$error = new JSONError(__("Unable to delete the dish",DM_DOMAIN_NAME));
		print $error->getJSON();
		die();
	}
	print $dish->getJSON();
	die();
}

/**
 * This function is called by Javascript in order to list dish types
 * It returns (more exactly print) a JSON object with a table of dish types
 * @see Type
 */
function listTypesOptionsCallback() {
	print ListTypes::getAllTypesOptionsJSON();
	die();
}

/**
 * This function is called by Javascript in order to list dish types
 * It returns (more exactly print) a JSON object with a table of dish sub-types
 * @see Type
 */
function listSsTypesOptionsCallback() {
	$type = new Type($_GET["type"]);
	print $type->getSsTypesOptionsJSON();
	die();
}