<?php

if (!defined("DM_PLUGIN_DIR")) {
	define( 'DM_PLUGIN_DIR', 		WP_PLUGIN_DIR . '/' . dirname( plugin_basename( __FILE__ ) ) );
}

require_once( DM_PLUGIN_DIR . '/controllers/ListTypes.php');
require_once( DM_PLUGIN_DIR . '/controllers/ListThemes.php');
require_once( DM_PLUGIN_DIR . '/controllers/ListAdminThemes.php');

wp_enqueue_script( 'jtable' ); // loads jquery and jtable as well as it has dependency on it
wp_enqueue_script( 'jtable.localization' ); // loads languages for Jtable
wp_enqueue_script( 'draw.type.table' );
wp_enqueue_style( 'jtable' );
wp_enqueue_style( 'jtable.jquery-ui' );


echo "<h1>".__("Settings",DM_DOMAIN_NAME)."</h1>";

echo "<h2>".__("General options",DM_DOMAIN_NAME)."</h2>";

echo "<h3>".__("Manage sub-types of dish",DM_DOMAIN_NAME)."</h3>";

echo '<p>';
echo '<div id="TypesTableContainer" style="width:400px"></div>';
echo '</p>';

echo "<h2>".__("Styles",DM_DOMAIN_NAME)."</h2>";

echo "<form method=\"post\" action=\"options.php\">";
settings_fields("dm_settings_group");
do_settings_sections("dm_settings_group");

echo "<h3>".__("Shortcode styles",DM_DOMAIN_NAME)."</h3>";

echo "<p>".sprintf(__("Select the css stylesheet that will be applied when printing daily-menus (through the %s shortcode).",DM_DOMAIN_NAME),"[dm_menu period=week]")."</p>";

$dirs = ListThemes::getAvailableThemes();
if ($dirs) {
	echo "<select name=\"".ListThemes::getOptionId()."\">";
	foreach ($dirs as $dir) {
		$selected = "";
		if ($dir == ListThemes::getSelectedTheme()) {
			$selected = "selected";
		}
		echo "<option value=\"".$dir."\" ".$selected.">".$dir."</option>";
	}
	echo "</select>";
} else {
	echo "<input name=\"".ListThemes::getOptionId()."\" type=\"text\" value=\"".ListThemes::getSelectedTheme()."\" />";
}

echo "<h3>".__("Admin menu styles",DM_DOMAIN_NAME)."</h3>";

echo "<p>".__("Select the css stylesheets for the tables in the administration menus (popups and table).",DM_DOMAIN_NAME)."</p>";

echo "<p>".__("Popups (jQueryUI) : ");

$dirs = ListAdminThemes::getAvailableJQueryUiThemes();

echo "<select name=\"".ListAdminThemes::getJQueryUiOptionId()."\">";
foreach ($dirs as $dir) {
	$selected = "";
	if ($dir == ListAdminThemes::getSelectedJQueryUiTheme()) {
		$selected = "selected";
	}
	echo "<option value=\"".$dir."\" ".$selected.">".$dir."</option>";
}
echo "</select>";

echo "</p>";

echo "<p>".__("Table (jTable) : ");

$dirs = ListAdminThemes::getAvailableJTableThemes();

echo "<select name=\"".ListAdminThemes::getJTableOptionId()."\">";
foreach ($dirs as $dir) {
	$selected = "";
	if ($dir == ListAdminThemes::getSelectedJTableTheme()) {
		$selected = "selected";
	}
	echo "<option value=\"".$dir."\" ".$selected.">".$dir."</option>";
}
echo "</select>";

echo "</p>";

echo submit_button();

echo "</form>";

echo "<h2>".__("Instructions for use",DM_DOMAIN_NAME)."</h2>";

echo "<p>".__("Daily menu provides facilities for canteen management.",DM_DOMAIN_NAME)."</p>";

echo "<p>".sprintf(
		__("In the \"%s\" menu, create several dishes that you will use later
				to compose your daily menu. Once this step is done, create
				day-to-day menus via \"%s\"",DM_DOMAIN_NAME),
		__("Dishes",DM_DOMAIN_NAME),
		__("Menus",DM_DOMAIN_NAME))."</p>";

echo "<p>".sprintf(__("Insert the following shortcode
				to dynamically show the menus of the week : %s.",DM_DOMAIN_NAME),
		"[dm_menu period=week]")."</p>";

echo "<p>".__("You can add a widget on your slide bar with the menu of the day,
		or the coming next menu, if it is 4 PM or after.",DM_DOMAIN_NAME)."</p>";

echo "<p>".__("This plugin uses jTable (http://www.jtable.org/).",DM_DOMAIN_NAME)."</p>";