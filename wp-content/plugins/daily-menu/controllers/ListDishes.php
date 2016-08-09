<?php
require_once( DM_PLUGIN_DIR . '/models/Dish.php' );
/**
 * ListDish class includes all the necessary methods to access the dish table
 * This class must not be instancied, since all the methods are statics.
 * @author Laurent Morretton
 *
 */
abstract class ListDishes {
	
	/**
	 * This method reads the database and returns an array of Dish objects
	 * @return multitype:Dish
	 */
	public static function getAllDishes() {
		global $wpdb;
		$dish = new Dish();
		$result	= $wpdb->get_results("SELECT picture,composition,name,sstype,type,id_dish FROM ".$dish->getTableName());
		$dishList = array();
		foreach ($result as $line) {
			$dish = new Dish($line->id_dish);
			$dish->setType($line->type);
			$dish->setSstype($line->sstype);
			$dish->setPicture($line->picture);
			$dish->setComposition($line->composition);
			$dish->setName($line->name);
			$dishList[] = $dish;
		}
		return $dishList;
	}
	
	/**
	 * This method returns a string containing an HTML table
	 * This table can be used in html pages to list the dishes
	 * @return string
	 */
	public static function printAllDishesHTML() {
		$html = "<table>";
		$dishes = ListDishes::getAllDishes();
		foreach ($dishes as $dish) {
			$html .= "<tr>";
			$html .= "<td>" . $dish->getName(). "</td>";
			$html .= "<td>" . $dish->getComposition(). "</td>";
			$html .= "</tr>";
		}
		$html .= "</table>";
		return $html;
	}
	
	/**
	 * This method reads the database and returns an array
	 * @return array
	 */
	public static function getAllDishesTable() {
		global $wpdb;
		$dish = new Dish();
		$result	= $wpdb->get_results("SELECT id_dish,name,picture,composition,sstype,type FROM ".$dish->getTableName());
		return $result;
	}
	
	/**
	 * This method gives a JSON object representing all the dishes
	 * that are present in database
	 * @return string
	 */
	public static function getAllDishesJSON() {
		$rows = ListDishes::getAllDishesTable();
		
		//Returns result to jTable
		$jTableResult = array();
		$jTableResult['Result'] = "OK";
		$jTableResult['Records'] = $rows;
		
		// Outputs the result
		return json_encode($jTableResult);
	}
	
	/**
	 * Gives the total number of dishes in database
	 * @return integer
	 */
	public static function getDishesCount() {
		global $wpdb;
		$dish = new Dish();
		return $wpdb->get_var("SELECT count(*) FROM ".$dish->getTableName());
	}
	
	/**
	 * This method returns a restricted number of dishes, ordered as defined
	 * by the third argument $SQLsort, starting from record  $startIndex to
	 * a total of $pageSize
	 * The result is an array.
	 * @param number $startIndex
	 * @param number $pageSize
	 * @param string $SQLsort
	 * @return array
	 */
	public static function getPagedDishesTable($startIndex,$pageSize,$SQLsort) {
		global $wpdb;
		$dish = new Dish();
		$result	= $wpdb->get_results(
				"SELECT id_dish,name,picture,composition,sstype,type ".
				"FROM ".$dish->getTableName()." ".
				(isset($SQLsort)?"ORDER BY " . $SQLsort . " ":" ").
				"LIMIT " . $startIndex . "," . $pageSize);
		return $result;
	}
	
	/**
	 * This method returns a restricted number of dishes, ordered as defined
	 * by the third argument $SQLsort, starting from record  $startIndex to
	 * a total of $pageSize
	 * The result is a JSON object
	 * @param number $startIndex
	 * @param number $pageSize
	 * @param string $SQLsort
	 * @return string
	 */
	public static function getPagedDishesJSON($startIndex = 0,$pageSize = 20,$SQLsort = "name ASC") {
		$rows = ListDishes::getPagedDishesTable($startIndex,$pageSize,$SQLsort);
		$recordCount = ListDishes::getDishesCount();
		//Returns result to jTable
		$jTableResult = array();
		$jTableResult['Result'] = "OK";
		$jTableResult['TotalRecordCount'] = $recordCount;
		$jTableResult['Records'] = $rows;
	
		// Outputs the result
		return json_encode($jTableResult);
	}
	
	/**
	 * Return all the dishes that are of the given type
	 * The result is an array
	 * @see Type
	 * @param string $type
	 * @return array
	 */
	public static function getDishesByTypeTable($type) {
		global $wpdb;
		$dish = new Dish();
		$result	= $wpdb->get_results(
				"SELECT id_dish,name,picture,composition,sstype,type ".
				"FROM ".$dish->getTableName()." ".
				"where type = '".$type."' ".
				"order by name");
		return $result;
	}
	
	/**
	 * Return all the dishes that are of the given type
	 * The result is a JSON object
	 * @see Type
	 * @param string $type
	 * @return string
	 */
	public static function getDishesByTypeJSON($type) {
		$rows = ListDishes::getDishesByTypeTable($type);
	
		
		$options[] = array(
				"DisplayText" => __("No dish",DM_DOMAIN_NAME),
				"Value" => 0);
		
		// Array is decomposed into a table of options as described by jTable
		foreach ($rows as $row) {
			$options[] = array(
					"DisplayText" => $row->name,
					"Value" => $row->id_dish
			);
		}
		
		//Returns result to jTable
		$jTableResult = array();
		$jTableResult['Result'] = "OK";
		$jTableResult['Options'] = $options;
		
		// Outputs the result
		return json_encode($jTableResult);
	}
	
}