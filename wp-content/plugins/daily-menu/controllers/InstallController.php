<?php
require_once( DM_PLUGIN_DIR . '/models/Dish.php' );
require_once( DM_PLUGIN_DIR . '/models/Type.php' );

class InstallController {
	function __construct() {
	}
	
	/**
	 * install
	 */
	public static function install() {
		global $wpdb;
		if (!defined("DM_PLUGIN_DIR")) {
			define( 'DM_PLUGIN_DIR', 		WP_PLUGIN_DIR . '/' . dirname( plugin_basename( __FILE__ ) ) );
		}

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		require_once( DM_PLUGIN_DIR . '/controllers/ListDishes.php');

		
		/*
		 * We'll set the default character set and collation for this table.
		 * If we don't do this, some characters could end up being converted
		 * to just ?'s when saved in our table.
		 */
		$charset_collate = '';
		
		if ( ! empty( $wpdb->charset ) ) {
			$charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset}";
		}
		
		if ( ! empty( $wpdb->collate ) ) {
			$charset_collate .= " COLLATE {$wpdb->collate}";
		}
		
		$dish_table_name = $wpdb->prefix . Dish::getTableSuffix();
		$sql = "CREATE TABLE $dish_table_name (
                      ID_DISH int(11) NOT NULL AUTO_INCREMENT,
                      TYPE char(3) NOT NULL,
                      SSTYPE char(3) NOT NULL,
                      NAME varchar(255) NOT NULL,
                      COMPOSITION varchar(255) NULL,
                      PICTURE int(11) NOT NULL,
                      PRIMARY KEY  (ID_DISH)
                    ) $charset_collate ; ";
		
		dbDelta( $sql );
		
		$menu_table_name = $wpdb->prefix . "dm_menu";
		$sql = "CREATE TABLE $menu_table_name (
                      DATE date NOT NULL,
                      ID_DISH int(11) NOT NULL,
                      PRIMARY KEY  (DATE,ID_DISH),
                      FOREIGN KEY (ID_DISH) REFERENCES $dish_table_name (ID_DISH)
                    ) $charset_collate ;";
		
		dbDelta( $sql );
		
		// Adding master types of dish
		add_option(Type::getOptionId(), array(
			Dish::STARTER_CODE => __("Starter",DM_DOMAIN_NAME),
			Dish::MAIN_COURSE_CODE => __("Main course",DM_DOMAIN_NAME),
			Dish::ACCOMPANIMENT_CODE => __("Accompaniment",DM_DOMAIN_NAME),
			Dish::DAIRY_CODE => __("Dairy",DM_DOMAIN_NAME),
			Dish::DESSERT_CODE => __("Dessert",DM_DOMAIN_NAME)
		));
		
		// Adding sub-type of dish samples
		add_option(Type::getSsTypeOptionId(), array(
			Dish::DESSERT_CODE => array(
				"FRU" => __("Fruit",DM_DOMAIN_NAME),
				"PAT" => __("Cake",DM_DOMAIN_NAME),
				"GLA" => __("Icecream",DM_DOMAIN_NAME)),
			Dish::DAIRY_CODE => array(
				"CHE" => __("Cheese",DM_DOMAIN_NAME),
				"YAO" => __("Yaourt",DM_DOMAIN_NAME)),
			Dish::STARTER_CODE => array(
				"SAL" => __("Salad",DM_DOMAIN_NAME),
				"PIE" => __("Pie",DM_DOMAIN_NAME),
				"TER" => __("Terrine",DM_DOMAIN_NAME)),
			Dish::MAIN_COURSE_CODE => array(
				"MEA" => __("Meat",DM_DOMAIN_NAME),
				"FIS" => __("Fish",DM_DOMAIN_NAME),
				"PAS" => __("Pastry",DM_DOMAIN_NAME)),
			Dish::ACCOMPANIMENT_CODE => array(
				"VEG" => __("Vegetables",DM_DOMAIN_NAME),
				"STA" => __("Starchy",DM_DOMAIN_NAME),
				"GRA" => __("Gratin",DM_DOMAIN_NAME))
		));
		
		// Adding default styles
		add_option(ListAdminThemes::getJTableOptionId(),"lightcolor/blue/jtable.min.css");
		add_option(ListAdminThemes::getJQueryUiOptionId(),"jqueryui-redmond/jquery-ui.css");
		add_option(ListThemes::getOptionId(),"menu_week.css");
		
		
		// Adding version number
		add_option( "dm_db_version", "0.1" );
		
	}
	
	/**
	 * delete
	 */
	public static function delete() {
		global $wpdb;
		$sql [] = "DROP TABLE IF EXISTS " . $wpdb->prefix . "dm_dish";
		$sql [] = "DROP TABLE IF EXISTS " . $wpdb->prefix . "dm_menu";
		foreach ( $sql as $query ) {
			$wpdb->query ( $query );
		}
		delete_option("dm_type_list");
		delete_option("dm_sstype_list");
		delete_option("dm_db_version");
	}
	
	public static function addSampleData() {
		global $wpdb;
		$sql [] = "insert into " . $wpdb->prefix . "dm_dish values (1,'DAI','YAO','Yaourt','Lait',0);";
		$sql [] = "insert into " . $wpdb->prefix . "dm_dish values (2,'DES','FRU','Pomme','Pomme',2);";
		$sql [] = "insert into " . $wpdb->prefix . "dm_dish values (3,'DES','PAT','Tarte aux pommes','Pate feuilletée',3);";
		$sql [] = "insert into " . $wpdb->prefix . "dm_dish values (4,'DAI','CHE','Fromage','Camembert, Bleu, fourme',0);";
		$sql [] = "insert into " . $wpdb->prefix . "dm_dish values (5,'DES','GLA','Glace','Vanille, chocolat',0);";
		$sql [] = "insert into " . $wpdb->prefix . "dm_dish values (6,'DES','FRU','Banane','Banane',0);";
		
		
		foreach ( $sql as $query ) {
			$wpdb->query ( $query );
		}
	}
}//class
//}//if