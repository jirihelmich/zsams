<?php
require_once( DM_PLUGIN_DIR . '/models/Type.php' );
class ListTypes {

	/**
	 * Returns all types of dish, that are stored in wordpress options, as an array
	 * @return mixed
	 */
	public static function getAllTypesTable() {
		$result	= get_option(Type::getOptionId());
		
		return $result;
	}

	/**
	 * Returns all types of dish, that are stored in wordpress options, as a JSON object
	 * The JSON object represents a list of options 
	 * @return string a JSON Object
	 */
	public static function getAllTypesOptionsJSON() {
		$rows = ListTypes::getAllTypesTable();
		
		// Array is decomposed into a table of options as described by jTable
		foreach ($rows as $id => $text) {
			$options[] = array(
					"DisplayText" => $text,
					"Value" => $id
			);
		}
		
		//Returns result to jTable
		$jTableResult = array();
		$jTableResult['Result'] = "OK";
		$jTableResult['Options'] = $options;
	
		// Outputs the result
		return json_encode($jTableResult);
	}
	
	/**
	 * Returns all types of dish, that are stored in wordpress options, as a JSON object
	 * The JSON object represents a record set
	 * @return string a JSON Object
	 */
	public static function getAllTypesJSON() {
		$rows = ListTypes::getAllTypesTable();
	
		// Array is decomposed into a table of options as described by jTable
		foreach ($rows as $id => $text) {
			$options[] = array(
					"id_type" => $id,
					"text" => $text
			);
		}
		
		//Returns result to jTable
		$jTableResult = array();
		$jTableResult['Result'] = "OK";
		$jTableResult['Records'] = $options;
	
		// Outputs the result
		return json_encode($jTableResult);
	}
	
	
}