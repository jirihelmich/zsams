<?php
require_once( DM_PLUGIN_DIR . '/models/Menu.php' );
/**
 * The "Dish" class represents a dish of a menu. It can be a main course, a starter, dessert, dairy and a accompaniment.
 * The ID of the dish are handled by the MySQL DB.
 * @author Laurent Morretton
 *
 */
class Dish {
	
	const TABLE_SUFFIX = "dm_dish";
	const STARTER_CODE = "STA";
	const MAIN_COURSE_CODE = "MAI";
	const ACCOMPANIMENT_CODE = "ACC";
	const DAIRY_CODE = "DAI";
	const DESSERT_CODE = "DES";
	
	
	private $table_name;
	private $type;
	private $sstype;
	private $name;
	private $composition;
	private $picture;
	private $id_dish;
	
	/**
	 * The constructor of the "Dish" class. It can accept the ID of an existing dish.
	 * @param number $id The ID of an existing dish
	 */
	function __construct($id = 0){
		global $wpdb;
		$this->table_name = $wpdb->prefix . self::TABLE_SUFFIX;
		$this->id_dish = sanitize_text_field($id);
	}
	
	/**
	 * Loads all attributes of a dish from the database that correspond to the
	 * provided ID, or that set by constructor or setID method
	 * @param number $id The ID of an existing dish
	 */
	function loadFromID($id = 0) {
		global $wpdb;
		if ($id==0) {
			$id = $this->id_dish;
		}
		if (!isset($id)) return;
		if (!$id) return;
		$result	= $wpdb->get_row("SELECT picture,composition,name,sstype,type,id_dish FROM ".$this->table_name." WHERE ID_DISH=". $id);
		$this->type = $result->type;
		$this->sstype = $result->sstype;
		$this->id_dish = $result->id_dish;
		$this->picture = $result->picture;
		$this->composition = $result->composition;
		$this->name = $result->name;
	}
	
	/**
	 * Saves the dish to the database. If no ID had been set, then record a new dish.
	 * @return boolean|Ambigous <number, number> The number of rows added/uptated to DB, or false if it fails
	 */
	function save() {
		global $wpdb;
		$newData = array ("PICTURE" => $this->picture,
				"COMPOSITION" => $this->composition,
				"NAME" => $this->name,
				"SSTYPE" => $this->sstype,
				"TYPE" => $this->type);
		if ($this->id_dish==0) {
			if (!$wpdb->insert($this->table_name, $newData)) return false;
			$this->id_dish = $wpdb->insert_id;
		} else {
			if (!$wpdb->update($this->table_name, $newData, array("ID_DISH"=> $this->id_dish))) return false;
		}
		return $this->id_dish;
	}

	/**
	 * Deletes the dish specified by its ID, in database
	 * @return Ambigous <number, false>|boolean The number of rows deleted from DB, or false if it fails
	 */
	function delete() {
		global $wpdb;
		if (isset($this->id_dish)) {
			return $wpdb->delete($this->table_name, "where id = ". $this->id_dish);
		} else {
			return false;
		}
	}
	
	/**
	 * Adds the dish to a new or existing menu in database. A menu is determined by its date
	 * @see Menu
	 * @param date $date The date object of the menu
	 * @return boolean|Ambigous <number, false> The number of rows added/uptated to DB, or false if it fails
	 */
	function saveInMenu($date) {
		global $wpdb;
		if (!isset($this->id_dish)) return false;
		if ($this->id_dish==0) return true;
		$newData = array ("DATE" => $date->format("Y-m-d"),
				"ID_DISH" => $this->id_dish);
		return $wpdb->replace($wpdb->prefix . Menu::getTableSuffix(), $newData);
	}
	
	/**
	 * Removes the dish to an existing menu in database. A menu is determined by its date
	 * @see Menu
	 * @param date $date The date object of the menu
	 * @return boolean|Ambigous <number, false> The number of rows deleted in DB, or false if it fails
	 */
	function removeFromMenu($date) {
		global $wpdb;
		if (!isset($date)) return false;
		if (!isset($this->id_dish)) return false;
		return $wpdb->delete($wpdb->prefix . Menu::getTableSuffix(), array( 'DATE' => $date->format("Y-m-d") , 'ID_DISH' => $this->id_dish));	
	}
	
	/**
	 * Retrieves the ID of the dish
	 * @return number
	 */
	function getID(){ return $this->id_dish; }
	
	/**
	 * Provides the code of the type of the dish
	 * @see Type
	 * @return string
	 */
	function getType(){ return $this->type; }
	
	/**
	 * Provides the code of the subtype of the dish
	 * @return string
	 */
	function getSstype(){ return $this->sstype; }
	
	/**
	 * Gets the name of the dish
	 * @return string the name of the dish
	 */
	function getName(){ return $this->name; }
	
	/**
	 * Gets the composition (i.e. what the dish is made of) of the dish
	 * @return string the composition of the dish
	 */
	function getComposition(){ return $this->composition; }
	
	/**
	 * Gets the picture ID to show when printing a menu
	 * @return Ambigous <unknown, array>
	 */
	function getPicture(){ return $this->picture; }
	
	/**
	 * Gets the name of the database table in which the dishes are stored 
	 * @return string
	 */
	function getTableName(){ return $this->table_name; }
	static function getTableSuffix() { return self::TABLE_SUFFIX; }
	
	/**
	 * Encodes the dish into a JSON string
	 * @return string A JSON representing the dish
	 */
	function getJSON() {
		$jTableResult = array();
		$jTableResult['Result'] = "OK";
		$jTableResult['Record'] = array("id_dish" => $this->id_dish,
				"picture" => $this->picture,
				"composition" => $this->composition,
				"name" => $this->name,
				"sstype" => $this->sstype,
				"type" => $this->type);
		return json_encode($jTableResult);
	}
	
	/**
	 * Sets the type of the dish
	 * @see Type
	 * @param string $arg
	 */
	function setType($arg){ $this->type = sanitize_text_field($arg); }
	
	/**
	 * Sets the subtype of the dish
	 * @param string $arg
	 */
	function setSstype($arg){ $this->sstype = sanitize_text_field($arg); }
	
	/**
	 * Sets the name of the dish
	 * @param string $arg
	 */
	function setName($arg){ $this->name = sanitize_text_field($arg); }
	
	/**
	 * Sets the composition of the dish
	 * @param string $arg
	 */
	function setComposition($arg){ $this->composition = sanitize_text_field($arg); }
	
	/**
	 * Sets the ID of a picture representing the dish
	 * @param number $arg
	 */
	function setPicture($arg) {
		$this->picture = intval($arg);
		if ( ! $this->picture) {
			$this->picture = '';
		}
	}
	
	/**
	 * Fill all members of the dish from a HTTP request
	 * @param array $args The post argument
	 */
	function setFromPOST($args) {
		$this->setType($args["type"]);
		$this->setSstype($args["sstype"]);
		$this->setName($args["name"]);
		$this->setComposition($args["composition"]);
		$this->setPicture($args["picture"]);
	}
	
}