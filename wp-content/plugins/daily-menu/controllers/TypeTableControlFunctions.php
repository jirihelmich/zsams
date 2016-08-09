<?php
/**
 * AJAX Functions to handle each actions on the table of dish
 */

require_once( DM_PLUGIN_DIR . '/models/JSONError.php' );

/**
 * This function is called by Javascript in order to list all the stored types
 * It returns (more exactly print) a JSON object with a list of types
 * @see Type
 */
function listTypesCallback() {
	print ListTypes::getAllTypesJSON();
	die();
}

/**
 * This function is called by Javascript in order to save a type of dish (e.g starters, accompaniment, etc.)
 * It returns (more exactly print) a JSON object with the just saved type
 * Not used right
 * @see Type
 * @ignore
 */
function createTypeCallback() {
	$type = new Type();
	$type->setFromPOST($_POST);
	if (!$type->save()) {
		$error = new JSONError(__("Unable to save the type",DM_DOMAIN_NAME));
		print $error->getJSON();
		die();
	}
	print $type->getJSON();
	die();
}

/**
 * This function is called by Javascript in order to update a type of dish (e.g. starters, accompaniments, etc.)
 * It returns (more exactly print) a JSON object with the just saved type 
 * The parameter "id_type" is expected in the HTTP request and must correspond to the ID of an existing type
 * Not used right
 * @see Type
 * @ignore
 */
function updateTypeCallback() {
	$type = new Type($_POST["id_type"]);
	$type->setFromPOST($_POST);
	if (!$type->save()) {
		$error = new JSONError(__("Unable to save the type",DM_DOMAIN_NAME));
		print $error->getJSON();
		die();
	}
	print $type->getJSON();
	die();
}

/**
 * This function is called by Javascript in order to delete a type of dish
 * It returns (more exactly print) a JSON object with the just deleted type
 * The parameter "id_type" is expected in the HTTP request and must correspond to the ID of an existing type
 * Not used right
 * @see Type
 * @ignore
 */
function deleteTypeCallback() {
	$type = new Type($_POST["id_type"]);
	if (!$type->delete()) {
		$error = new JSONError(__("Unable to delete the type",DM_DOMAIN_NAME));
		print $error->getJSON();
		die();
	}
	print $type->getJSON();
	die();
}


/**
 * This function is called by Javascript in order to list dish types
 * It returns (more exactly print) a JSON object with a table of dish sub-types
 * @see Type
 */
function listSsTypesCallback() {
	$type = new Type($_GET["type"]);
	print $type->getSsTypesJSON();
	die();
}


/**
 * This function is called by Javascript in order to save a sub-type of dish (e.g fruits, vegetables, etc.)
 * It returns (more exactly print) a JSON object with the just saved sub-type
 * @see Type
 */
function createSsTypeCallback() {
	$type = new Type($_POST["id_type"]);
	if (!$type->addSsType($_POST["sstext"],$_POST["id_sstype"])) {
		$error = new JSONError(__("Unable to save the sub-type",DM_DOMAIN_NAME));
		print $error->getJSON();
		die();
	}
	$jTableResult = array();
	$jTableResult['Result'] = "OK";
	$jTableResult['Record'] = array("id_sstype" => $_POST["id_sstype"],
			"sstext" => $_POST["sstext"],
			"id_type" => $type->getId()
	);
	print json_encode($jTableResult);
	
	die();
}

/**
 * This function is called by Javascript in order to update a sub-type of dish (e.g fruits, vegetables, etc.)
 * It returns (more exactly print) a JSON object with the just saved sub-type
 * The parameter "id_type" is expected in the HTTP request and must correspond to the ID of an existing type
 * The parameter "id_sstype" is expected in the HTTP request and must correspond to the ID of an existing sub-type
 * @see Type
 */
function updateSsTypeCallback() {
	$type = new Type($_POST["id_type"]);
	if (!$type->updateSsType($_POST["sstext"],$_POST["id_sstype"])) {
		$error = new JSONError(__("Unable to save the sub-type",DM_DOMAIN_NAME));
		print $error->getJSON();
		die();
	}
	$jTableResult = array();
	$jTableResult['Result'] = "OK";
	$jTableResult['Record'] = array("id_sstype" => $_POST["id_sstype"],
			"sstext" => $_POST["sstext"],
			"id_type" => $type->getId()
	);
	print json_encode($jTableResult);
	
	die();
}

/**
 * This function is called by Javascript in order to delete a sub-type of dish (e.g fruits, vegetables, etc.)
 * It returns (more exactly print) a JSON object with the just deleted sub-type
 * The parameter "id_type" is expected in the HTTP request and must correspond to the ID of an existing type
 * The parameter "id_sstype" is expected in the HTTP request and must correspond to the ID of an existing sub-type
 * @see Type
 */
function deleteSsTypeCallback() {
	$type = new Type($_GET["type"]);
	if (!$type->deleteSsType($_POST["id_sstype"])) {
		$error = new JSONError(__("Unable to delete the sub-type",DM_DOMAIN_NAME));
		print $error->getJSON();
		die();
	}
	$jTableResult = array();
	$jTableResult['Result'] = "OK";
	print json_encode($jTableResult);
	
	die();
}
