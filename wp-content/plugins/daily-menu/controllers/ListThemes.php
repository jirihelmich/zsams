<?php
if (!defined("DM_PLUGIN_DIR")) {
	define( 'DM_PLUGIN_DIR', 		WP_PLUGIN_DIR . '/' . dirname( plugin_basename( __FILE__ ) ) );
}

/**
 * This class can ben used to list and get selected themes
 * @author laurent
 *
 */
abstract class ListThemes {
	
	const CSS_DIR = "css";
	const CSS_OPTION_ID = "dm_shotcode_css";
	
	/**
	 * Gives the name of the option used to store the shortcode style
	 * @return string
	 */
	public static function getOptionId() {
		return self::CSS_OPTION_ID;
	}
	
	/**
	 * List names of the files in the subdirectory "css"
	 * If no autorization to list files, then returns false
	 * @return boolean|multitype:mixed
	 */
	public static function getAvailableThemes() {
		$styles_dir = DM_PLUGIN_DIR . "/". self::CSS_DIR ."/";

		$available = array ();

		$dir_handle = @opendir ( $styles_dir );
		
		if (!$dir_handle) {
			return false;
		}
		
		while ( false !== ($dir_file = readdir ( $dir_handle )) ) {
			if (! is_dir ( $dir_file )) {
				$pathinfo = pathinfo ( $dir_file );
				if (isset ( $pathinfo ['extension'] )) {
					if (('css' == strtolower ( $pathinfo ['extension'] )) && ('_' != $pathinfo ['basename'] [0])) {
						$available [] = $pathinfo ['basename'];
					}
				}
			}
		}
		return $available;
	}
	
	/**
	 * Gets the selected theme
	 * @return mixed
	 */
	public static function getSelectedTheme() {
		return get_option("dm_shotcode_css");
	}
	
	/**
	 * Gets the selected theme URL
	 * @return string
	 */
	public static function getSelectedThemeURL() {
		return self::CSS_DIR."/".ListThemes::getSelectedTheme();
	}
}								