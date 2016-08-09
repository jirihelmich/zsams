<?php

if (!defined("DM_PLUGIN_DIR")) {
	define( 'DM_PLUGIN_DIR', 		WP_PLUGIN_DIR . '/' . dirname( plugin_basename( __FILE__ ) ) );
}

require_once( DM_PLUGIN_DIR . '/controllers/ListMenus.php');


wp_enqueue_script( 'jtable' ); // loads jquery and jtable as well as it has dependency on it
wp_enqueue_script( 'jtable.localization' ); // loads languages for Jtable
wp_enqueue_script( 'draw.menu.table' );
wp_enqueue_style( 'jtable' );	
wp_enqueue_style( 'jtable.jquery-ui' );	

echo '<p>';
echo '<div id="MenusTableContainer" style="width:90%"></div>';
echo '</p>';