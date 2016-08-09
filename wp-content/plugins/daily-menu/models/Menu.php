<?php
require_once( DM_PLUGIN_DIR . '/models/Dish.php' );
/**
 * The menu class is intended to represent a menu. Since this plugins aims
 * to provide a canteen managment, only one menu is available per day.
 * Moreover, by menu I mean a unique list of dish, without any choice.
 * So this class does not fit restaurant requierement where customers have
 * choice between tow or more dish for starters, main courses, etc.
 * Thus, the date is the key for a menu
 * @author Laurent Morretton
 *
 */
class Menu {
	
	const TABLE_SUFFIX = "dm_menu";
	private $table_name;

	private $date;

	private $starter;
	private $mainCourse;
	private $accompaniment;
	private $dessert;
	private $dairy;
	
	/**
	 * Initialize a menu.
	 */
	function __construct(){
		global $wpdb;
		$this->table_name = $wpdb->prefix . self::TABLE_SUFFIX;
	}
	
	/**
	 * Give the name of the table used to store menus in database
	 * @return string
	 */
	function getTableName() {
		return $this->table_name;
	}
	
	/**
	 * Loads the dish if they have been created previously.
	 * The date must have been set before
	 */
	function load() {
		$this->starter->loadFromID();
		$this->mainCourse->loadFromID();
		$this->accompaniment->loadFromID();
		$this->dessert->loadFromID();
		$this->dairy->loadFromID();
	}
	
	/**
	 * Saves the menu into database.
	 * The date must have been set before
	 * @return boolean True if it is OK, else false
	 */
	function save() {
		// since alls fields are key values, we need first to delete all the menu for the given date
		$this->delete();
		
		if (isset($this->starter)) {
			if (!$this->starter->saveInMenu($this->date)) return false;
		}
		if (isset($this->mainCourse)) {
			if (!$this->mainCourse->saveInMenu($this->date)) return false;
		}
		if (isset($this->accompaniment)) {
			if (!$this->accompaniment->saveInMenu($this->date)) return false;
		}
		if (isset($this->dessert)) {
 			if (!$this->dessert->saveInMenu($this->date)) return false;
		}
		if (isset($this->dairy)) {
			if (!$this->dairy->saveInMenu($this->date)) return false;
		}
	
		return true;
	}
	
	/**
	 * Delete a menu from database
	 * The date must have been set before
	 * @return boolean|Ambigous <number, false> false if it fails, or the number of lines deleted
	 */
	function delete() {
		global $wpdb;
		if (!isset($this->date)) return false;
		return $wpdb->delete($this->table_name, array( 'DATE' => $this->date->format("Y-m-d") ));
	}
	
	/**
	 * Returns the suffix of the table in with menus are stored
	 * @return string
	 */
	static function getTableSuffix() { return self::TABLE_SUFFIX; }
	
	/**
	 * Returns the date of the menu formated as string
	 */
	function getDate() {
		return $this->date->format(__("Y-m-d"));
	}
	
	/**
	 * Returns the date object of the menu
	 */
	function getDateTime() {
		return $this->date;
	}
	
	/**
	 * Sets the date of the menu
	 * @param string $arg A string representing the date in yyyy-mm-dd format
	 */
	function setDate($arg) {
		$newDate = DateTime::createFromFormat(__("Y-m-d"),$arg);
		$this->date = $newDate;
	}
	
	/**
	 * Return the dish for the starter
	 * @return Dish
	 */
	function getStarter() {
		return $this->starter;
	}
	
	/**
	 * Gets the main course (Dish object)
	 * @return Dish
	 */
	function getMainCourse() {
		return $this->mainCourse;
	}
	
	/**
	 * Gets the accompaniment (Dish object)
	 * @return Dish
	 */
	function getAccompaniment() {
		return $this->accompaniment;
	}
	
	/**
	 * Gets the desset (Dish object)
	 * @return Dish
	 */
	function getDessert() {
		return $this->dessert;
	}
	
	/**
	 * Gets the dairy (Dish object)
	 * @return Dish
	 */
	function getDairy() {
		return $this->dairy;
	}
	
	/**
	 * Returns the Dish object corresponding to the given type
	 * @param string $type
	 * @return Dish
	 */
	function getDish($type) {
		switch ($type) {
			case Dish::STARTER_CODE: return $this->getStarter();
			case Dish::MAIN_COURSE_CODE : return $this->getMainCourse();
			case Dish::ACCOMPANIMENT_CODE : return $this->getAccompaniment();
			case Dish::DAIRY_CODE : return $this->getDairy();
			case Dish::DESSERT_CODE : return $this->getDessert();
		}
	}
	
	/**
	 * Serialize the menu as JSON as specified by jTable 
	 * @return string
	 */
	function getJSON() {
		$jTableResult = array();
		$jTableResult['Result'] = "OK";
		$jTableResult['Record'] = array("date" => $this->getDate(),
				"id_starter" => isset($this->starter)?$this->starter->getID():0,
				"id_maincourse" => isset($this->mainCourse)?$this->mainCourse->getID():0,
				"id_accompaniment" => isset($this->accompaniment)?$this->accompaniment->getID():0,
				"id_dairy" => isset($this->dairy)?$this->dairy->getID():0,
				"id_dessert" => isset($this->dessert)?$this->dessert->getID():0);
		return json_encode($jTableResult);
	}
	
	/**
	 * Sets the starter (Dish object)
	 * @param Dish $arg
	 */
	function setStarter($arg) {
		$this->starter = $arg;
	}
	
	/**
	 * Sets the main course (Dish object)
	 * @param Dish $arg
	 */
	function setMainCourse($arg) {
		$this->mainCourse = $arg;
	}
	
	/**
	 * Sets the accompaniment (Dish object)
	 * @param Dish $arg
	 */
	function setAccompaniment($arg) {
		$this->accompaniment = $arg;
	}
	
	/**
	 * Sets the dessert (Dish object)
	 * @param Dish $arg
	 */
	function setDessert($arg) {
		$this->dessert = $arg;
	}
	
	/**
	 * Sets the dairy (Dish object)
	 * @param Dish $arg
	 */
	function setDairy($arg) {
		$this->dairy = $arg;
	}
	
	/**
	 * Fill all the attribute of the dish from a HTTP request
	 * @param array $args
	 */
	function setFromPOST($args) {
		$this->setDate($args["date"]);
		$this->starter = new Dish(intval($args["id_starter"]));
		$this->mainCourse = new Dish(intval($args["id_maincourse"]));
		$this->accompaniment = new Dish(intval($args["id_accompaniment"]));
		$this->dairy = new Dish(intval($args["id_dairy"]));
		$this->dessert = new Dish(intval($args["id_dessert"]));
	}

}