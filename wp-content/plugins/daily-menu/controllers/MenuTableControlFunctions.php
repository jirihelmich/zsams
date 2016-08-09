<?php
/**
 * AJAX Functions to handle each actions on the table of menus
 */

/**
 * This function is called by Javascript in order to list all the stored menus
 * It returns (more exactly print) a JSON object with a list of menus
 * @see Menu
 */
function listMenusCallback() {
//	print ListMenus::getPagedMenusJSON($_GET["jtStartIndex"],$_GET["jtPageSize"],$_GET["jtSorting"]);
	print ListMenus::getPagedMenusJSON($_GET["jtStartIndex"],$_GET["jtPageSize"]);
//	print ListMenus::getAllMenusJSON();
	die();
}

/**
 * This function is called by Javascript in order to create a menu
 * The method returns a JSON object representing the newly created menu
 * or an error message if the creation in database failed
 */
function createMenuCallback() {
	$menu = new Menu();
	$menu->setFromPOST($_POST);
	if (!$menu->save()) {
		$error = new JSONError(__("Unable to save the menu",DM_DOMAIN_NAME));
		print $error->getJSON();
		die();
	}
	print $menu->getJSON();
	die();
}

/**
 * This function is called by Javascript in order to update a menu
 * In case of a change of date, this function first try to delete the old menu
 * before creating a new menu.
 * The method returns a JSON object representing the menu
 * or an error message if the creation in database failed
 */
function updateMenuCallback() {
	$menu = new Menu();
	$menu->setFromPOST($_POST);
	if ($_POST["jtRecordKey"]<>$menu->getDate()) {
		// we have changed the key (date) value, so we need to delete the old menu before
		$oldmenu = clone $menu;
		$oldmenu->setDate($_POST["jtRecordKey"]);
		if (!$oldmenu->delete()) {
			$error = new JSONError(__("Unable to replace the date",DM_DOMAIN_NAME));
			print $error->getJSON();
			die();
		}
	}
	if (!$menu->save()) {
		$error = new JSONError(__("Unable to save the menu",DM_DOMAIN_NAME));
		print $error->getJSON();
		die();
	}
	print $menu->getJSON();
	die();
}

/**
 * This function is called by Javascript in case of menu deletion
 * The method returns a JSON object woth an OK code
 * or an error message if the creation in database failed
 */
function deleteMenuCallback() {
	$menu = new Menu();
	$menu->setDate($_POST["date"]);
	if (!$menu->delete()) {
		$error = new JSONError(__("Unable to delete the menu",DM_DOMAIN_NAME));
		print $error->getJSON();
		die();
	}
	print $menu->getJSON();
	die();
}

/**
 * This function lists dishes from a type retreived from the HTTP request.
 * It is used for listing options value in the createupdate popup
 */
function listDishesFromTypeCallback() {
	print ListDishes::getDishesByTypeJSON($_GET["type"]);
	die();
}
