<?php
require_once( DM_PLUGIN_DIR . '/models/Menu.php' );
require_once( DM_PLUGIN_DIR . '/models/Dish.php' );
abstract class ListMenus {
	
	/**
	 * Returns all the menu as an array
	 * @return array
	 */
	public static function getAllMenusTable() {
		global $wpdb;

		$result	= $wpdb->get_results(
			"SELECT menu.date as date, ".
			"	max(starter.id_dish) as id_starter, ".
			"	max(maincourse.id_dish) as id_maincourse, ".
			"	max(accompaniment.id_dish) as id_accompaniment, ".
			"	max(dairy.id_dish) as id_dairy, ".
			"	max(dessert.id_dish) as id_dessert ".
			"FROM ".$wpdb->prefix . Menu::getTableSuffix()." menu ".
			"left outer join ".$wpdb->prefix . Dish::getTableSuffix()." starter ".
			"	on menu.id_dish = starter.id_dish and starter.type='".Dish::STARTER_CODE."' ".
			"left outer join ".$wpdb->prefix . Dish::getTableSuffix()." maincourse ".
			"	on menu.id_dish = maincourse.id_dish and maincourse.type='".Dish::MAIN_COURSE_CODE."' ".
			"left outer join ".$wpdb->prefix . Dish::getTableSuffix()." accompaniment ".
			"	on menu.id_dish = accompaniment.id_dish and accompaniment.type='".Dish::ACCOMPANIMENT_CODE."' ".
			"left outer join ".$wpdb->prefix . Dish::getTableSuffix()." dairy ".
			"	on menu.id_dish = dairy.id_dish and dairy.type='".Dish::DAIRY_CODE."' ".
			"left outer join ".$wpdb->prefix . Dish::getTableSuffix()." dessert ".
			"	on menu.id_dish = dessert.id_dish and dessert.type='".Dish::DESSERT_CODE."' ".
			"group by menu.date order by menu.date DESC"
		);
		
		return $result;
	}
	
	/**
	 * Returns all the menus as a JSON object
	 * @return string
	 */
	public static function getAllMenusJSON() {
		$rows = ListMenus::getAllMenusTable();
	
		//Returns result to jTable
		$jTableResult = array();
		$jTableResult['Result'] = "OK";
		$jTableResult['Records'] = $rows;
	
		// Outputs the result
		return json_encode($jTableResult);
	}
	
	/**
	 * This method returns a restricted number of menus, ordered as defined
	 * by the third argument $SQLsort, starting from record  $startIndex to
	 * a total of $pageSize records
	 * The result is an array.
	 * @param number $startIndex
	 * @param number $pageSize
	 * @param string $SQLsort
	 * @return array
	 */
	public static function getPagedMenusTable($startIndex,$pageSize,$SQLsort) {
		global $wpdb;
	
		$result	= $wpdb->get_results(
				"SELECT menu.date as date, ".
				"	max(starter.id_dish) as id_starter, ".
				"	max(maincourse.id_dish) as id_maincourse, ".
				"	max(accompaniment.id_dish) as id_accompaniment, ".
				"	max(dairy.id_dish) as id_dairy, ".
				"	max(dessert.id_dish) as id_dessert ".
				"FROM ".$wpdb->prefix . Menu::getTableSuffix()." menu ".
				"left outer join ".$wpdb->prefix . Dish::getTableSuffix()." starter ".
				"	on menu.id_dish = starter.id_dish and starter.type='".Dish::STARTER_CODE."' ".
				"left outer join ".$wpdb->prefix . Dish::getTableSuffix()." maincourse ".
				"	on menu.id_dish = maincourse.id_dish and maincourse.type='".Dish::MAIN_COURSE_CODE."' ".
				"left outer join ".$wpdb->prefix . Dish::getTableSuffix()." accompaniment ".
				"	on menu.id_dish = accompaniment.id_dish and accompaniment.type='".Dish::ACCOMPANIMENT_CODE."' ".
				"left outer join ".$wpdb->prefix . Dish::getTableSuffix()." dairy ".
				"	on menu.id_dish = dairy.id_dish and dairy.type='".Dish::DAIRY_CODE."' ".
				"left outer join ".$wpdb->prefix . Dish::getTableSuffix()." dessert ".
				"	on menu.id_dish = dessert.id_dish and dessert.type='".Dish::DESSERT_CODE."' ".
				"group by menu.date ".
				(isset($SQLsort)?"ORDER BY " . $SQLsort . " ":" ").
				"LIMIT " . $startIndex . "," . $pageSize
		);
	
		return $result;
	}
	
	/**
	 * This method returns a restricted number of menus, ordered as defined
	 * by the third argument $SQLsort, starting from record  $startIndex to
	 * a total of $pageSize records
	 * The result is a JSON string.
	 * @param number $startIndex
	 * @param number $pageSize
	 * @param string $SQLsort
	 * @return string
	 */
	public static function getPagedMenusJSON($startIndex = 0,$pageSize = 20,$SQLsort = "date DESC") {
		$rows = ListMenus::getPagedMenusTable($startIndex,$pageSize,$SQLsort);
		$recordCount = ListMenus::getMenusCount();
		
		//Returns result to jTable
		$jTableResult = array();
		$jTableResult['Result'] = "OK";
		$jTableResult['TotalRecordCount'] = $recordCount;
		$jTableResult['Records'] = $rows;
	
		// Outputs the result
		return json_encode($jTableResult);
	}
	
	/**
	 * Gives the total number of menu in database
	 * @return number
	 */
	public static function getMenusCount() {
		global $wpdb;
		return $wpdb->get_var("SELECT count(distinct date) FROM ".$wpdb->prefix . Menu::getTableSuffix());
	}
	
	/**
	 * Returns the menus of the current week
	 * @return array
	 */
	public static function getWeekMenusTable() {
		global $wpdb;
	
		$result	= $wpdb->get_results(
			"SELECT menu.date as date, ".
			"	max(starter.id_dish) as id_starter, ".
			"	max(maincourse.id_dish) as id_maincourse, ".
			"	max(accompaniment.id_dish) as id_accompaniment, ".
			"	max(dairy.id_dish) as id_dairy, ".
			"	max(dessert.id_dish) as id_dessert ".
			"FROM ".$wpdb->prefix . Menu::getTableSuffix()." menu ".
			"left outer join ".$wpdb->prefix . Dish::getTableSuffix()." starter ".
			"	on menu.id_dish = starter.id_dish and starter.type='".Dish::STARTER_CODE."' ".
			"left outer join ".$wpdb->prefix . Dish::getTableSuffix()." maincourse ".
			"	on menu.id_dish = maincourse.id_dish and maincourse.type='".Dish::MAIN_COURSE_CODE."' ".
			"left outer join ".$wpdb->prefix . Dish::getTableSuffix()." accompaniment ".
			"	on menu.id_dish = accompaniment.id_dish and accompaniment.type='".Dish::ACCOMPANIMENT_CODE."' ".
			"left outer join ".$wpdb->prefix . Dish::getTableSuffix()." dairy ".
			"	on menu.id_dish = dairy.id_dish and dairy.type='".Dish::DAIRY_CODE."' ".
			"left outer join ".$wpdb->prefix . Dish::getTableSuffix()." dessert ".
			"	on menu.id_dish = dessert.id_dish and dessert.type='".Dish::DESSERT_CODE."' ".
				// selection of the next week with menu
			"where EXTRACT(WEEK FROM menu.date) = (	select min(EXTRACT(WEEK FROM next_menu.date)) ".
			"		FROM ".$wpdb->prefix . Menu::getTableSuffix()." next_menu ".
			"		where next_menu.date > CURRENT_DATE ) ".
			"	AND EXTRACT(YEAR FROM menu.date) = (	select min(EXTRACT(YEAR FROM next_menu.date)) ".
			"		FROM ".$wpdb->prefix . Menu::getTableSuffix()." next_menu ".
			"		where next_menu.date > CURRENT_DATE ) ".
			"group by menu.date order by menu.date DESC"
		);
	
		return $result;
	}
	
	/**
	 * Returns the menus of the next 4 weeks
	 * @return array
	 */
	public static function get4WeekMenusTable() {
		global $wpdb;
	
		$result	= $wpdb->get_results(
				"SELECT menu.date as date, ".
				"	max(starter.id_dish) as id_starter, ".
				"	max(maincourse.id_dish) as id_maincourse, ".
				"	max(accompaniment.id_dish) as id_accompaniment, ".
				"	max(dairy.id_dish) as id_dairy, ".
				"	max(dessert.id_dish) as id_dessert, ".
				"	EXTRACT(WEEK FROM menu.date) as week ".
				"FROM ".$wpdb->prefix . Menu::getTableSuffix()." menu ".
				"left outer join ".$wpdb->prefix . Dish::getTableSuffix()." starter ".
				"	on menu.id_dish = starter.id_dish and starter.type='".Dish::STARTER_CODE."' ".
				"left outer join ".$wpdb->prefix . Dish::getTableSuffix()." maincourse ".
				"	on menu.id_dish = maincourse.id_dish and maincourse.type='".Dish::MAIN_COURSE_CODE."' ".
				"left outer join ".$wpdb->prefix . Dish::getTableSuffix()." accompaniment ".
				"	on menu.id_dish = accompaniment.id_dish and accompaniment.type='".Dish::ACCOMPANIMENT_CODE."' ".
				"left outer join ".$wpdb->prefix . Dish::getTableSuffix()." dairy ".
				"	on menu.id_dish = dairy.id_dish and dairy.type='".Dish::DAIRY_CODE."' ".
				"left outer join ".$wpdb->prefix . Dish::getTableSuffix()." dessert ".
				"	on menu.id_dish = dessert.id_dish and dessert.type='".Dish::DESSERT_CODE."' ".
				// selection of the 4 next weeks with menu
				"where DATEDIFF (menu.date, CURRENT_DATE) >= -DAYOFWEEK(CURRENT_DATE) ".
				"	AND DATEDIFF (menu.date, CURRENT_DATE) <= 28-DAYOFWEEK(CURRENT_DATE) ".
				"group by menu.date order by menu.date DESC"
		);
	
		return $result;
	}
	
	/**
	 * Returns the menus of the current week, as an array of Menu
	 * @see Menu
	 * @return Menu
	 */
	public static function getWeekMenus() {
		$menus = ListMenus::getWeekMenusTable();
		foreach ($menus as $menu) {
			$objectMenus [] = self::loadMenuFromResult($menu);
		}
		return $objectMenus;
	}
	
	/**
	 * Returns the menus of the current week and the next 3 weeks,
	 * as an array of Menu.
	 * @see Menu
	 * @return Menu
	 */
	public static function get4WeekMenus() {
		$menus = ListMenus::get4WeekMenusTable();
		foreach ($menus as $menu) {
			$menusOfWeek[$menu->week] [] = self::loadMenuFromResult($menu);
		}
		return $menusOfWeek;
	}
	
	/**
	 * Returns a string containing a HTML table of menus
	 * The menus are in colomn (that is to say day by day)
	 * Types of dish are in row
	 * @return string
	 */
	public static function getWeekMenusHTML() {
		$menus = ListMenus::getWeekMenus();
		if (!isset($menus)) {
			return (__("No menu set yet !",DM_DOMAIN_NAME));
		}
		$types = ListTypes::getAllTypesTable();
		return self::buildHtmlTableFromMenu($menus,$types);
	}

	private function buildHtmlTableFromMenu($menus,$types) {
		$html = "<table class=\"daily-menu\">";
		
		// header line with the days of the week
		if (!isset($menus[0])) {
			return "";
		}
		$html .= "<tr>";
		
		$html .= "<th class=\"daily-menu\">";
		/* translators:
		 * %1$d is the year
		 * %2$d is the number of the week within the year*/
		$html .= sprintf(__('%1$d <br/> Week %2$d',DM_DOMAIN_NAME),
				$menus[0]->getDateTime()->format('o'),
				$menus[0]->getDateTime()->format('W'));
		$html .= "</th>";
		foreach (array_reverse($menus) as $menu) {
		
			$html .= "<th class=\"daily-menu daily-menu_day\">";
			/* translators:
			 * %1$s is the name of the day,
			 * %2$d is the number of the day within month
			 * %3$s is the name of the month*/
			$html .= sprintf(esc_html__('%1$s, %2$d of %3$s',DM_DOMAIN_NAME),
					date_i18n("l",$menu->getDateTime()->getTimestamp()),
					date_i18n("j",$menu->getDateTime()->getTimestamp()),
					date_i18n("F",$menu->getDateTime()->getTimestamp()));
			$html .= "</th>";
		}
		$html .= "</tr>";
		
		// seeking for rowspan in advance
		foreach (array_reverse($menus) as $menu) {
			if ($menu->getDish(Dish::ACCOMPANIMENT_CODE)->getName() != null) {
				$needMainCourseRowspan[$menu->getDate()] = false;
			} else {
				$needMainCourseRowspan[$menu->getDate()] = true;
			}
			if ($menu->getDish(Dish::DAIRY_CODE)->getName() != null) {
				$needDessertRowspan[$menu->getDate()] = false;
			} else {
				$needDessertRowspan[$menu->getDate()] = true;
			}
		}
		
		// building table, starting with dish lines
		foreach ($types as $type => $typename) {
			$html .= "<tr>";
			$html .= "<th class=\"daily-menu daily-menu_dish\">".esc_html($typename)."</th>";
				
			// menu cols
			foreach (array_reverse($menus) as $menu) {
				$background = "";
				$drawtype = $type;
				$rawspan = "";
		
				if ($needMainCourseRowspan[$menu->getDate()] && $type==Dish::MAIN_COURSE_CODE) {
					$rawspan = "rowspan=\"2\"";
				}
					
				if ($needDessertRowspan[$menu->getDate()] && $type==Dish::DAIRY_CODE) {
					$drawtype = Dish::DESSERT_CODE;
					$rawspan = "rowspan=\"2\"";
				}
		
				if ($needDessertRowspan[$menu->getDate()] && $type==Dish::DESSERT_CODE) {
					continue;
				}
		
				if (!$menu->getDish($drawtype)->getPicture()==null) {
					$background .= 'style="background:url(\'';
					$background .= wp_get_attachment_url( $menu->getDish($drawtype)->getPicture());
					$background .= '\')0px 0px; text-shadow: -2px 0 2px white, 0 2px 2px white, 2px 0 2px white, 0 -2px 2px white; "';
				}
		
				if (!$menu->getDish($drawtype)->getName()==null) {
					$html .= "<td ";
					$html .= $background." ";
					$html .= $rawspan." ";
					$html .= "class=\"daily-menu daily-menu_dish\">";
					$html .= esc_html($menu->getDish($drawtype)->getName());
					$html .= "</td>";
				}
			}
				
			$html .= "</tr>";
		}
		$html .= "</table>";
		return $html;
	}
	
	/**
	 * Returns a string containing a HTML table of menus
	 * The menus are in colomn (that is to say day by day)
	 * Types of dish are in row
	 * @return string
	 */
	public static function get4WeeksMenusHTML() {
		$menusOfWeek = ListMenus::get4WeekMenus();
		if (!isset($menusOfWeek)) {
			return (__("No menu set in the next 4 weeks !",DM_DOMAIN_NAME));
		}
		$types = ListTypes::getAllTypesTable();
		foreach ($menusOfWeek as $menus) {
			$html .= self::buildHtmlTableFromMenu($menus,$types);
			$html .= "<br/>";
		}
		return $html;
	}
	
	
	/**
	 * Returns the coming-next menu, assuming that the cut-off is at 4 PM
	 * @return array
	 */
	public static function getNextMenuRow() {
		global $wpdb;
	
		$result	= $wpdb->get_row(
				"SELECT menu.date as date, ".
				"	max(starter.id_dish) as id_starter, ".
				"	max(maincourse.id_dish) as id_maincourse, ".
				"	max(accompaniment.id_dish) as id_accompaniment, ".
				"	max(dairy.id_dish) as id_dairy, ".
				"	max(dessert.id_dish) as id_dessert ".
				"FROM ".$wpdb->prefix . Menu::getTableSuffix()." menu ".
				"left outer join ".$wpdb->prefix . Dish::getTableSuffix()." starter ".
				"	on menu.id_dish = starter.id_dish and starter.type='".Dish::STARTER_CODE."' ".
				"left outer join ".$wpdb->prefix . Dish::getTableSuffix()." maincourse ".
				"	on menu.id_dish = maincourse.id_dish and maincourse.type='".Dish::MAIN_COURSE_CODE."' ".
				"left outer join ".$wpdb->prefix . Dish::getTableSuffix()." accompaniment ".
				"	on menu.id_dish = accompaniment.id_dish and accompaniment.type='".Dish::ACCOMPANIMENT_CODE."' ".
				"left outer join ".$wpdb->prefix . Dish::getTableSuffix()." dairy ".
				"	on menu.id_dish = dairy.id_dish and dairy.type='".Dish::DAIRY_CODE."' ".
				"left outer join ".$wpdb->prefix . Dish::getTableSuffix()." dessert ".
				"	on menu.id_dish = dessert.id_dish and dessert.type='".Dish::DESSERT_CODE."' ".
				"where ((DATEDIFF(menu.date,CURRENT_DATE) = 0) ".
				"   		and (TIME_TO_SEC(TIMEDIFF(160000,CURRENT_TIME)) >= 0)) ".
				"   or (DATEDIFF(menu.date,CURRENT_DATE) > 0) ".
				"group by menu.date order by menu.date ASC ".
				"limit 1");
	
		return $result;
	}
	
	/**
	 * Returns the comming next Menu, assuming that the cut-off is at 16 PM, or false if the is no menu
	 * @return Menu
	 */
	public static function getNextMenu() {
		$menu = ListMenus::getNextMenuRow();
		if (isset($menu)) {
			return self::loadMenuFromResult($menu);
		} else {
			return false;
		}
	}	
	
	/**
	 * 1. Sets the ID of each dish of the table result
	 * 2. Load each dish from DB to gets the name, composition, etc.
	 * @param array $menu
	 * @return Menu
	 */
	private static function loadMenuFromResult($menu) {
		$objectMenu = new Menu();
		$objectMenu->setDate($menu->date);
		$objectMenu->setStarter(new Dish($menu->id_starter));
		$objectMenu->setMainCourse(new Dish($menu->id_maincourse));
		$objectMenu->setAccompaniment(new Dish($menu->id_accompaniment));
		$objectMenu->setDairy(new Dish($menu->id_dairy));
		$objectMenu->setDessert(new Dish($menu->id_dessert));
		$objectMenu->load();
		return $objectMenu;
	}
	
}