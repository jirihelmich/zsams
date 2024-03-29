<?php
/* wppa-tinymce-shortcodes.php
* Pachkage: wp-photo-album-plus
*
*
* Version 6.5.02
*
*/

if ( ! defined( 'ABSPATH' ) )
    die( "Can't load this file directly" );

class wppaGallery
{
    function __construct() {
		add_action( 'init', array( $this, 'action_admin_init' ) ); // 'admin_init'
	}

	function action_admin_init() {
		// only hook up these filters if we're in the admin panel, and the current user has permission
		// to edit posts or pages
		if ( current_user_can( 'edit_posts' ) || current_user_can( 'edit_pages' ) ) {
			add_filter( 'mce_buttons', array( $this, 'filter_mce_button' ) );
			add_filter( 'mce_external_plugins', array( $this, 'filter_mce_plugin' ) );
		}
	}

	function filter_mce_button( $buttons ) {
		// add a separation before our button.
		array_push( $buttons, '|', 'mygallery_button' );
		return $buttons;
	}

	function filter_mce_plugin( $plugins ) {
		// this plugin file will work the magic of our button
		$file = 'js/wppa-tinymce-shortcodes.js';
		$plugins['wppagallery'] = plugin_dir_url( __FILE__ ) . $file;
		return $plugins;
	}

}

$wppagallery = new wppaGallery();

add_action('admin_head', 'wppa_inject_js');

function wppa_inject_js() {
global $wppa_api_version;

	// Things that wppa-tinymce.js AND OTHER MODULES!!! need to know
	echo('<script type="text/javascript">'."\n");
	echo('/* <![CDATA[ */'."\n");
		echo("\t".'wppaImageDirectory = "'.wppa_get_imgdir().'";'."\n");
		echo("\t".'wppaAjaxUrl = "'.admin_url('admin-ajax.php').'";'."\n");
		echo("\t".'wppaPhotoDirectory = "'.WPPA_UPLOAD_URL.'/";'."\n");
		echo("\t".'wppaThumbDirectory = "'.WPPA_UPLOAD_URL.'/thumbs/";'."\n");
		echo("\t".'wppaTempDirectory = "'.WPPA_UPLOAD_URL.'/temp/";'."\n");
		echo("\t".'wppaFontDirectory = "'.WPPA_UPLOAD_URL.'/fonts/";'."\n");
		echo("\t".'wppaNoPreview = "'.__('No Preview available', 'wp-photo-album-plus').'";'."\n");
		echo("\t".'wppaVersion = "'.$wppa_api_version.'";'."\n");
		echo("\t".'wppaSiteUrl = "'.site_url().'";'."\n");
		echo("\t".'wppaWppaUrl = "'.WPPA_URL.'";'."\n");
		echo("\t".'wppaIncludeUrl = "'.trim(includes_url(), '/').'";'."\n");
	echo("/* ]]> */\n");
	echo("</script>\n");
}

function wppa_make_tinymce_dialog() {
global $wpdb;

	// Prepare albuminfo
	$albums = $wpdb->get_results( "SELECT `id`, `name` FROM `".WPPA_ALBUMS."` ORDER BY `timestamp` DESC", ARRAY_A );
	if ( wppa_switch( 'hier_albsel' ) ) {
		$albums = wppa_add_paths( $albums );
		$albums = wppa_array_sort( $albums, 'name' );
	}

	// Prepare photoinfo
	$photos = $wpdb->get_results( "SELECT `id`, `name`, `album`, `ext` FROM `".WPPA_PHOTOS."` ORDER BY `timestamp` DESC LIMIT 100", ARRAY_A );

	// Get Tags/cats
	$tags 	= wppa_get_taglist();
	$cats 	= wppa_get_catlist();

	// Pages suitable for landing
	$query = "SELECT ID, post_title, post_content, post_parent " .
			 "FROM " . $wpdb->posts . " " .
			 "WHERE post_type = 'page' AND post_status = 'publish' " .
			 "ORDER BY post_title ASC";
	$pages = $wpdb->get_results( $query, ARRAY_A );

	if ( $pages ) {

		// Add parents optionally OR translate only
		if ( wppa_switch( 'hier_pagesel' ) ) $pages = wppa_add_parents( $pages );

		// Just translate
		else {
			foreach ( array_keys( $pages ) as $index ) {
				$pages[$index]['post_title'] = __( stripslashes($pages[$index]['post_title']  ) );
			}
		}

		// Sort alpahbetically
		$pages = wppa_array_sort( $pages, 'post_title' );
	}

	$admins = array();

	if ( wppa_user_is( 'administrator' ) ) {
		$users = get_users( array( 'role' => 'administrator' ) );
	}

	// Make the html
	$result =
	'<style>#TB_ajaxContent {box-sizing:border-box; width:100% !important;}</style>'.
	'<div id="wppagallery-form">'.
		'<style type="text/css">'.
			'#wppagallery-table tr, #wppagallery-table th, #wppagallery-table td {'.
				'padding: 2px; 0;'.
			'}'.
		'</style>'.
		'<table id="wppagallery-table" class="form-table">'.

			// Top type selection
			'<tr >'.
				'<th><label for="wppagallery-top-type">'.__('Type of WPPA display:', 'wp-photo-album-plus').'</label></th>'.
				'<td>'.
					'<select id="wppagallery-top-type" name="type" onchange="wppaGalleryEvaluate()">'.
						'<option value="" selected="selected" disabled="disabled" style="color:#700" >-- '.__('Please select a display type', 'wp-photo-album-plus').' --</option>'.
						'<option value="galerytype" style="color:#070" >'.__('A gallery with covers and/or thumbnails', 'wp-photo-album-plus').'</option>'.
						'<option value="slidestype" style="color:#070" >'.__('A slideshow', 'wp-photo-album-plus').'</option>'.
						'<option value="singletype" style="color:#070" >'.__('A single image', 'wp-photo-album-plus').'</option>'.
						'<option value="searchtype" style="color:#070" >'.__('A search/selection box', 'wp-photo-album-plus').'</option>'.
						'<option value="misceltype" style="color:#070" >'.__('An other box type', 'wp-photo-album-plus').'</option>'.
					'</select>'.
				'</td>'.
			'</tr>'.

			// Top type I: gallery sub type
			'<tr id="wppagallery-galery-type-tr" style="display:none;" >'.
				'<th><label for="wppagallery-galery-type">'.__('Type of gallery display:', 'wp-photo-album-plus').'</label></th>'.
				'<td>'.
					'<select id="wppagallery-galery-type" name="type" onchange="wppaGalleryEvaluate()">'.
						'<option value="" selected="selected" disabled="disabled" style="color:#700" >-- '.__('Please select a gallery type', 'wp-photo-album-plus').' --</option>'.
						'<option value="cover" style="color:#070" >'.__('The cover(s) of specific album(s)', 'wp-photo-album-plus').'</option>'.
						'<option value="content" style="color:#070" >'.__('The content of specific album(s)', 'wp-photo-album-plus').'</option>'.
						'<option value="covers" style="color:#070" >'.__('The covers of the subalbums of specific album(s)', 'wp-photo-album-plus').'</option>'.
						'<option value="thumbs" style="color:#070" >'.__('The thumbnails of specific album(s)', 'wp-photo-album-plus').'</option>'.
					'</select>'.
				'</td>'.
			'</tr>'.

			// Top type II: slide sub type
			'<tr id="wppagallery-slides-type-tr" style="display:none;" >'.
				'<th><label for="wppagallery-slides-type">'.__('Type of slideshow:', 'wp-photo-album-plus').'</label></th>'.
				'<td>'.
					'<select id="wppagallery-slides-type" name="type" onchange="wppaGalleryEvaluate()">'.
						'<option value="" selected="selected" disabled="disabled" style="color:#700" >-- '.__('Please select a slideshow type', 'wp-photo-album-plus').' --</option>'.
						'<option value="slide" style="color:#070" >'.__('A fully featured slideshow', 'wp-photo-album-plus').'</option>'.
						'<option value="slideonly" style="color:#070" >'.__('A slideshow without supporting boxes', 'wp-photo-album-plus').'</option>'.
						'<option value="slideonlyf" style="color:#070" >'.__('A slideshow with a filmstrip only', 'wp-photo-album-plus').'</option>'.
						'<option value="filmonly" style="color:#070" >'.__('A filmstrip only', 'wp-photo-album-plus').'</option>'.
					'</select>'.
				'</td>'.
			'</tr>'.

			// Top type III: single sub type
			'<tr id="wppagallery-single-type-tr" style="display:none;" >'.
				'<th><label for="wppagallery-single-type">'.__('Type of single image:', 'wp-photo-album-plus').'</label></th>'.
				'<td>'.
					'<select id="wppagallery-single-type" name="type" onchange="wppaGalleryEvaluate()">'.
						'<option value="" selected="selected" disabled="disabled" style="color:#700" >-- '.__('Please select a single image type', 'wp-photo-album-plus').' --</option>'.
						'<option value="photo" style="color:#070" >'.__('A plain single photo', 'wp-photo-album-plus').'</option>'.
						'<option value="mphoto" style="color:#070" >'.__('A single photo with caption', 'wp-photo-album-plus').'</option>'.
						'<option value="slphoto" style="color:#070" >'.__('A single photo in the style of a slideshow', 'wp-photo-album-plus').'</option>'.
					'</select>'.
				'</td>'.
			'</tr>'.

			// Top type IV: search sub type
			'<tr id="wppagallery-search-type-tr" style="display:none;" >'.
				'<th><label for="wppagallery-search-type">'.__('Type of search:', 'wp-photo-album-plus').'</label></th>'.
				'<td>'.
					'<select id="wppagallery-search-type" name="type" onchange="wppaGalleryEvaluate()">'.
						'<option value="" selected="selected" disabled="disabled" style="color:#700" >-- '.__('Please select a search type', 'wp-photo-album-plus').' --</option>'.
						'<option value="search" style="color:#070" >'.__('A search box', 'wp-photo-album-plus').'</option>'.
						'<option value="supersearch" style="color:#070" >'.__('A supersearch box', 'wp-photo-album-plus').'</option>'.
						'<option value="tagcloud" style="color:#070" >'.__('A tagcloud box', 'wp-photo-album-plus').'</option>'.
						'<option value="multitag" style="color:#070" >'.__('A multitag box', 'wp-photo-album-plus').'</option>'.
						'<option value="superview" style="color:#070" >'.__('A superview box', 'wp-photo-album-plus').'</option>'.
						'<option value="calendar" style="color:#070" >'.__('A calendar box', 'wp-photo-album-plus').'</option>'.
					'</select>'.
				'</td>'.
			'</tr>'.

			// Top type V: other sub type
			'<tr id="wppagallery-miscel-type-tr" style="display:none;" >'.
				'<th><label for="wppagallery-miscel-type">'.__('Type miscellaneous:', 'wp-photo-album-plus').'</label></th>'.
				'<td>'.
					'<select id="wppagallery-miscel-type" name="type" onchange="wppaGalleryEvaluate()">'.
						'<option value="" selected="selected" disabled="disabled" style="color:#700" >-- '.__('Please select a miscellaneous display', 'wp-photo-album-plus').' --</option>'.
						'<option value="generic">'.__('A generic albums display', 'wp-photo-album-plus').'</option>'.
						'<option value="upload">'.__('An upload box', 'wp-photo-album-plus').'</option>'.
						'<option value="landing">'.__('A landing page shortcode', 'wp-photo-album-plus').'</option>'.
						'<option value="stereo">'.__('A 3D stereo settings box', 'wp-photo-album-plus').'</option>'.
						'<option value="choice">'.__('An admins choice box', 'wp-photo-album-plus').'</option>'.
					'</select>'.
				'</td>'.
			'</tr>'.

			// Administrators ( for admins choice, show admin only if current user is an admin or superuser )
			'<tr id="wppagallery-admins-tr" style="display:none;" >'.
				'<th><label for="wppagallery-admins">'.__('Users:', 'wp-photo-album-plus').'</label></th>'.
				'<td>'.
					'<select id="wppagallery-admins" name="admins" style="color:#070;" multiple="multiple" onchange="wppaGalleryEvaluate()">'.
						'<option value="" selected="selected" style="color:#070;" >-- '.__('All', 'wp-photo-album-plus').' --</option>';
							foreach( $users as $user ) {
								$result .=
								'<option value="'.$user->data->user_login.'" class="wppagallery-admin" style="color:#070;" >'.$user->data->user_login.'</option>';
							}
							$users = get_option( 'wppa_super_users', array() );
							foreach( $users as $user ) {
								$result .=
								'<option value="'.$user.'" class="wppagallery-admin" style="color:#070" >'.$user.'</option>';
							}
						$result .=
					'</select>'.
				'</td>'.
			'</tr>'.

			// Real or Virtual albums
			'<tr id="wppagallery-album-type-tr" style="display:none;" >'.
				'<th><label for="wppagallery-album-type">'.__('Kind of selection:', 'wp-photo-album-plus').'</label></th>'.
				'<td>'.
					'<select id="wppagallery-album-type" name="type" onchange="wppaGalleryEvaluate()">'.
						'<option value="" selected="selected" disabled="disabled" style="color:#700" >-- '.__('Please select a type of selection to be used', 'wp-photo-album-plus').' --</option>'.
						'<option value="real">'.__('One or more wppa+ albums', 'wp-photo-album-plus').'</option>'.
						'<option value="virtual">'.__('A special selection', 'wp-photo-album-plus').'</option>'.
					'</select>'.
				'</td>'.
			'</tr>'.

			// Virtual albums
			'<tr id="wppagallery-album-virt-tr" style="display:none;" >'.
				'<th><label for="wppagallery-album-virt">'.__('The selection to be used:', 'wp-photo-album-plus').'</label></th>'.
				'<td>'.
					'<select id="wppagallery-album-virt" name="album" class="wppagallery-album" onchange="wppaGalleryEvaluate()">'.
						'<option value="" disabled="disabled" selected="selected" style="color:#700" >-- '.__('Please select a virtual album', 'wp-photo-album-plus').' --</option>'.
						'<option value="#last" >'.__('The most recently modified album', 'wp-photo-album-plus').'</option>'.
						'<option value="#topten" >'.__('The top rated photos', 'wp-photo-album-plus').'</option>'.
						'<option value="#lasten" >'.__('The most recently uploaded photos', 'wp-photo-album-plus').'</option>'.
						'<option value="#featen" >'.__('A random selection of featured photos', 'wp-photo-album-plus').'</option>'.
						'<option value="#comten" >'.__('The most recently commented photos', 'wp-photo-album-plus').'</option>'.
						'<option value="#tags" >'.__('Photos tagged with certain tags', 'wp-photo-album-plus').'</option>'.
						'<option value="#cat" >'.__('Albums tagged with a certain category', 'wp-photo-album-plus').'</option>'.
						'<option value="#owner" >'.__('Photos in albums owned by a certain user', 'wp-photo-album-plus').'</option>'.
						'<option value="#upldr" >'.__('Photos uploaded by a certain user', 'wp-photo-album-plus').'</option>'.
						'<option value="#all" >'.__('All photos in the system', 'wp-photo-album-plus').'</option>'.
					'</select>'.
				'</td>'.
			'</tr>'.

			// Virtual albums that have covers
			'<tr id="wppagallery-album-virt-cover-tr" style="display:none;" >'.
				'<th><label for="wppagallery-album-virt-cover">'.__('The selection to be used:', 'wp-photo-album-plus').'</label></th>'.
				'<td>'.
					'<select id="wppagallery-album-virt-cover" name="album" class="wppagallery-album" onchange="wppaGalleryEvaluate()">'.
						'<option value="" disabled="disabled" selected="selected" style="color:#700" >-- '.__('Please select a virtual album', 'wp-photo-album-plus').' --</option>'.
						'<option value="#last" >'.__('The most recently modified album', 'wp-photo-album-plus').'</option>'.
						'<option value="#owner" >'.__('Albums owned by a certain user', 'wp-photo-album-plus').'</option>'.
						'<option value="#cat" >'.__('Albums tagged with certain categories', 'wp-photo-album-plus').'</option>'.
						'<option value="#all" >'.__('All albums in the system', 'wp-photo-album-plus').'</option>'.
					'</select>'.
				'</td>'.
			'</tr>'.

			// Real albums
			'<tr id="wppagallery-album-real-tr" style="display:none;" >'.
				'<th><label for="wppagallery-album-real">'.__('The Album(s) to be used:', 'wp-photo-album-plus').'</label></th>'.
				'<td>'.
					'<select id="wppagallery-album-real" style="max-width:400px;" name="album" multiple="multiple" onchange="wppaGalleryEvaluate()">';
						if ( $albums ) {

							// Please select
							$result .= '<option id="wppagallery-album-0" value="0" disabled="disabled" selected="selected" style="color:#700" >-- '.__('Please select one or more albums', 'wp-photo-album-plus').' --</option>';

							// All standard albums
							foreach ( $albums as $album ) {
								$id = $album['id'];
								$result .= '<option class="wppagallery-album-r" value="' . $id . '" >'.stripslashes( __( $album['name'] ) ) . ' (' . $id . ')</option>';
							}
						}
						else {
							$result .= '<option value="0" >' . __('There are no albums yet', 'wp-photo-album-plus') . '</option>';
						}
					$result .= '</select>'.
				'</td>'.
			'</tr>'.

			// Real albums optional
			'<tr id="wppagallery-album-realopt-tr" style="display:none;" >'.
				'<th><label for="wppagallery-album-realopt">'.__('The Album(s) to be used:', 'wp-photo-album-plus').'</label></th>'.
				'<td>'.
					'<select id="wppagallery-album-realopt" style="max-width:400px;" name="album" multiple="multiple" onchange="wppaGalleryEvaluate()">';
						if ( $albums ) {

							// Please select
							$result .= '<option id="wppagallery-album-0" class="wppagallery-album-ropt" value="0" selected="selected" style="color:#070" >-- '.__('All albums', 'wp-photo-album-plus').' --</option>';

							// All standard albums
							foreach ( $albums as $album ) {
								$id = $album['id'];
								$result .= '<option class="wppagallery-album-ropt" style="color:#070" value="' . $id . '" >'.stripslashes( __( $album['name'] ) ) . ' (' . $id . ')</option>';
							}
						}
						else {
							$result .= '<option value="0" >' . __('There are no albums yet', 'wp-photo-album-plus') . '</option>';
						}
					$result .= '</select>'.
				'</td>'.
			'</tr>'.

			// Owner selection
			'<tr id="wppagallery-owner-tr" style="display:none" >'.
				'<th><label for="wppagallery-owner">'.__('The album owner:', 'wp-photo-album-plus').'</label></th>'.
				'<td>'.
					'<select id="wppagallery-owner" name="owner" class="wppagallery-owner" onchange="wppaGalleryEvaluate()">'.
						'<option value="" disabled="disabled" selected="selected" style="color:#700" >-- '.__('Please select a user', 'wp-photo-album-plus').' --</option>'.
						'<option value="#me" >-- '.__('The logged in visitor', 'wp-photo-album-plus').' --</option>';
						$users = wppa_get_users();
						if ( $users ) foreach ( $users as $user ) {
							$result .= '<option value="'.$user['user_login'].'" >'.$user['display_name'].'</option>';
						}
						else {	// Too many
							$result .= '<option value="xxx" >-- '.__('Too many users, edit manually', 'wp-photo-album-plus').' --</option>';
						}
					$result .=
					'</select>'.
				'</td>'.
			'</tr>'.

			// Owner Parent album
			'<tr id="wppagallery-owner-parent-tr" style="display:none;" >'.
				'<th><label for="wppagallery-owner-parent">'.__('Parent album:', 'wp-photo-album-plus').'</label></th>'.
				'<td>'.
					'<select id="wppagallery-owner-parent" style="color:#070;max-width:400px;" name="parentalbum" multiple="multiple" onchange="wppaGalleryEvaluate()">';
						if ( $albums ) {

							// Please select
							$result .= '<option value="" selected="selected" >-- '.__('No parent specification', 'wp-photo-album-plus').' --</option>';

							// Generic
							$result .= '<option value="0" >-- '.__('The generic parent', 'wp-photo-album-plus').' --</option>';

							// All standard albums
							foreach ( $albums as $album ) {
								$id = $album['id'];
								$result .= '<option class="wppagallery-album-p" value="'.$id.'" >'.stripslashes(__($album['name'])).' ('.$id.')</option>';
							}
						}
						else {
							$result .= '<option value="0" >'.__('There are no albums yet', 'wp-photo-album-plus').'</option>';
						}
					$result .= '</select>'.
				'</td>'.
			'</tr>'.

			// Album parent
			'<tr id="wppagallery-album-parent-tr" style="display:none;" >'.
				'<th><label for="wppagallery-album-parent">'.__('Parent album:', 'wp-photo-album-plus').'</label></th>'.
				'<td>'.
					'<select id="wppagallery-album-parent-parent" style="color:#070;max-width:400px;" name="parentalbum" onchange="wppaGalleryEvaluate()">';
						if ($albums) {

							// Please select
							$result .= '<option id="wppagallery-album-0" value="0" selected="selected" style="color:#700" >-- '.__('The generic parent', 'wp-photo-album-plus').' --</option>';

							// All standard albums
							foreach ( $albums as $album ) {
								$id = $album['id'];
								$result .= '<option class="wppagallery-album" value="'.$id.'" >'.stripslashes(__($album['name'])).' ('.$id.')</option>';
							}
						}
						else {
							$result .= '<option value="0" >'.__('There are no albums yet', 'wp-photo-album-plus').'</option>';
						}
					$result .= '</select>'.
				'</td>'.
			'</tr>'.

			// Album count
			'<tr id="wppagallery-album-count-tr" style="display:none;" >'.
				'<th><label for="wppagallery-album-count">'.__('Max Albums:', 'wp-photo-album-plus').'</label></th>'.
				'<td>'.
					'<input id="wppagallery-album-count" type="text" style="color:#070;" value="1" onchange="wppaGalleryEvaluate()" />'.
				'</td>'.
			'</tr>'.

			// Photo count
			'<tr id="wppagallery-photo-count-tr" style="display:none;" >'.
				'<th><label for="wppagallery-photo-count">'.__('Max Photos:', 'wp-photo-album-plus').'</label></th>'.
				'<td>'.
					'<input id="wppagallery-photo-count" type="text" style="color:#070;" value="1" onchange="wppaGalleryEvaluate()" />'.
				'</td>'.
			'</tr>'.

			// Albums with certain cats
			'<tr id="wppagallery-albumcat-tr" style="display:none;" >'.
				'<th><label for="wppagallery-albumcat">'.__('The album cat(s):', 'wp-photo-album-plus').'</label></th>'.
				'<td>'.
					'<select id="wppagallery-albumcat" style="color:#700;" onchange="wppaGalleryEvaluate()" multiple="multiple" >'.
						'<option value="" disabled="disabled" selected="selected" style="color:#700" >'.__('--- please select category ---', 'wp-photo-album-plus').'</option>';
						if ( $cats ) foreach ( array_keys( $cats ) as $cat ) {
							$result .= '<option class="wppagallery-albumcat" value="'.$cat.'" >'.$cat.'</option>';
						}
						$result .=
					'</select>'.
				'</td>'.
			'</tr>'.

			// Photo selection
			'<tr id="wppagallery-photo-tr" style="display:none;" >'.
				'<th><label for="wppagallery-photo" class="wppagallery-photo" >'.__('The Photo to be used:', 'wp-photo-album-plus').'</label></th>'.
				'<td>'.
					'<select id="wppagallery-photo" name="photo" class="wppagallery-photo" onchange="wppaGalleryEvaluate()" >';
						if ( $photos ) {

							// Please select
							$result .= '<option value="" disabled="disabled" selected="selected" style="color:#700" >-- '.__('Please select a photo', 'wp-photo-album-plus').' --</option>';
							$result .= '<option value="#potd" >-- '.__('The photo of the day', 'wp-photo-album-plus').' --</option>';

							// Most recent 100 photos
							foreach ( $photos as $photo ) {
								$name = stripslashes(__($photo['name']));
								if ( strlen($name) > '50') $name = substr($name, '0', '50').'...';
								if ( get_option( 'wppa_file_system' ) == 'flat' ) {
									$result .= '<option value="'.wppa_fix_poster_ext($photo['id'].'.'.$photo['ext'], $photo['id']).'" >'.$name.' ('.wppa_get_album_name($photo['album']).')'.'</option>';
								}
								else {
									$result .= '<option value="'.wppa_fix_poster_ext(wppa_expand_id($photo['id']).'.'.$photo['ext'], $photo['id']).'" >'.$name.' ('.wppa_get_album_name($photo['album']).')'.'</option>';
								}
							}
							$result .=  '<option value="#last" >-- '.__('The most recently uploaded photo', 'wp-photo-album-plus').' --</option>'.
										'<option value="#potd" >-- '.__('The photo of the day', 'wp-photo-album-plus').' --</option>';
						}
						else {
							$result .= '<option value="0" >'.__('There are no photos yet', 'wp-photo-album-plus').'</option>';
						}
						$result .=
					'</select>'.
					'<br />'.
					'<small style="display:none;" class="wppagallery-photo" >'.
						__('Specify the photo to be used', 'wp-photo-album-plus').'<br />'.
						__('You can select from a maximum of 100 most recently added photos', 'wp-photo-album-plus').'<br />'.
					'</small>'.
				'</td>'.
			'</tr>'.

			// Photo preview
			'<tr id="wppagallery-photo-preview-tr" style="display:none;" >'.
				'<th><label for="wppagallery-photo-preview" >'.__('Preview image:', 'wp-photo-album-plus').'</label></th>'.
				'<td id="wppagallery-photo-preview" style="text-align:center;" >'.
				'</td >'.
			'</tr>'.

			// Photos with certain tags
			'<tr id="wppagallery-phototags-tr" style="display:none;" >'.
				'<th><label for="wppagallery-phototags">'.__('The photo tag(s):', 'wp-photo-album-plus').'</label></th>'.
				'<td>'.
					'<select id="wppagallery-phototags" style="color:#700;" multiple="multiple" onchange="wppaGalleryEvaluate()">'.
						'<option value="" disabled="disabled" selected="selected" style="color:#700" >'.__('--- please select tag(s) ---', 'wp-photo-album-plus').'</option>';
						if ( $tags ) foreach ( array_keys($tags) as $tag ) {
							$result .= '<option class="wppagallery-phototags" value="'.$tag.'" >'.$tag.'</option>';
						}
						$result .=
					'</select>'.
				'</td>'.
			'</tr>'.

			// Tags and cats additional settings
			'<tr id="wppagallery-tags-cats-tr" style="display:none;" >'.
				'<th><label>'.__('Or / And:', 'wp-photo-album-plus').'</label></th>'.
				'<td>'.
					'<input id="wppagallery-or" type="radio" name="andor" value="or" onchange="wppaGalleryEvaluate()"/>'.__('Meet any', 'wp-photo-album-plus').'&nbsp;'.
					'<input id="wppagallery-and" type="radio" name="andor" value="and" onchange="wppaGalleryEvaluate()"/>'.__('Meet all', 'wp-photo-album-plus').
				'</td>'.
			'</tr>'.

			// Search additional settings
			'<tr id="wppagallery-search-tr" style="display:none;" >'.
				'<th><label>'.__('Additional features:', 'wp-photo-album-plus').'</label></th>'.
				'<td>'.
					'<input id="wppagallery-sub" type="checkbox" name="sub" onchange="wppaGalleryEvaluate()"/>'.__('Enable Subsearch', 'wp-photo-album-plus').'&nbsp;'.
					'<input id="wppagallery-root" type="checkbox" name="root" onchange="wppaGalleryEvaluate()"/>'.__('Enable Rootsearch', 'wp-photo-album-plus').
				'</td>'.
			'</tr>'.

			// Optiona root album
			'<tr id="wppagallery-rootalbum-tr" style="display:none;" >'.
				'<th><label>'.__('Search root:', 'wp-photo-album-plus').'</label></th>'.
				'<td>'.
					'<select id="wppagallery-rootalbum" style="color:#070" onchange="wppaGalleryEvaluate()" >'.
						'<option value="0" selected="selected" >'.__('--- default ---', 'wp-photo-album-plus').'</option>';
						if ( $albums ) {

							// All standard albums
							foreach ( $albums as $album ) {
								$id = $album['id'];
								$result .= '<option class="wppagallery-rootalbum" value="'.$id.'" >'.stripslashes(__($album['name'])).' ('.$id.')</option>';
							}
						}
						$result .=
					'</select>'.
				'</td>'.
			'</tr>'.

			// Landing page
			'<tr id="wppagallery-landing-tr" style="display:none;" >'.
				'<th><label>'.__('Landing page:', 'wp-photo-album-plus').'</label></th>'.
				'<td>'.
					'<select id="wppagallery-landing" style="color:#070" onchange="wppaGalleryEvaluate()" >'.
						'<option value="0" selected="selected" >'.__('--- default ---', 'wp-photo-album-plus').'</option>';
						if ( $pages ) {
							foreach( $pages as $page ) {
								$dis = '';
								if ( strpos( $page['post_content'], '[wppa' ) === false ) {
									$dis = ' disabled="disabled"';
								}
								$result .= '<option value="'.$page['ID'].'"'.$dis.' >'.__( $page['post_title'] ).'</option>';
							}
						}
						$result .=
					'</select>'.
				'</td>'.
			'</tr>'.

			// Tagcloud/list additional settings
			'<tr id="wppagallery-taglist-tr" style="display:none;" >'.
				'<th><label>'.__('Additional features:', 'wp-photo-album-plus').'</label></th>'.
				'<td>'.
					'<input id="wppagallery-alltags" type="checkbox" checked="checked" name="alltags" onchange="wppaGalleryEvaluate()"/>'.__('Enable all tags', 'wp-photo-album-plus').'&nbsp;'.
					'<select id="wppagallery-seltags" style="color:#070; display:none;" name="seltags" multiple="multiple" onchange="wppaGalleryEvaluate()">';
						if ( $tags ) {
							'<option value="" disabled="disabled" selected="selected" style="color:#700" >-- '.__('Please select the tags to show', 'wp-photo-album-plus').' --</option>';
							foreach( array_keys($tags) as $tag ) {
								$result .= '<option class="wppagallery-taglist-tags" value="'.$tag.'"style="color:#700" >'.$tag.'</option>';
							}
						}
						else {
							'<option value="" disabled="disabled" selected="selected" style="color:#700" >-- '.__('There are no tags', 'wp-photo-album-plus').' --</option>';
						}
						$result .= '</select>'.
				'</td>'.
			'</tr>'.

			// Superview additional settings: optional parent
			'<tr id="wppagallery-album-super-tr" style="display:none;" >'.
				'<th><label for="wppagallery-album-super">'.__('Parent album:', 'wp-photo-album-plus').'</label></th>'.
				'<td>'.
					'<select id="wppagallery-album-super-parent" style="color:#070;max-width:400px;" name="parentalbum" onchange="wppaGalleryEvaluate()">';
						if ( $albums ) {

							// Please select
							$result .= '<option value="" selected="selected" style="color:#700" >-- '.__('The generic parent', 'wp-photo-album-plus').' --</option>';

							// All standard albums
							foreach ( $albums as $album ) {
								$id = $album['id'];
								$result .= '<option class="wppagallery-album" value="'.$id.'" >'.stripslashes(__($album['name'])).' ('.$id.')</option>';
							}
						}
						else {
							$result .= '<option value="0" >'.__('There are no albums yet', 'wp-photo-album-plus').'</option>';
						}
					$result .= '</select>'.
				'</td>'.
			'</tr>'.

			// Calendar
			'<tr id="wppagallery-calendar-tr" style="display:none;" >'.
				'<th><label for="wppagallery-calendar">'.__('Calendar type:', 'wp-photo-album-plus').'</lable></th>'.
				'<td>'.
					'<select id="wppagallery-calendar-type" style="color:#070;max-width:400px;" onchange="wppaGalleryEvaluate()" >'.
						'<option value="exifdtm" >'.__('By EXIF date', 'wp-photo-album-plus').'</option>'.
						'<option value="timestamp" >'.__('By date of upload', 'wp-photo-album-plus').'</option>'.
						'<option value="modified" >'.__('By date last modified', 'wp-photo-album-plus').'</option>'.
					'</select>'.
					'<br />'.
					'<input type="checkbox" id="wppagallery-calendar-reverse" onchange="wppaGalleryEvaluate()" >'.__('Last date first', 'wp-photo-album-plus').'&nbsp;&nbsp;'.
					'<input type="checkbox" id="wppagallery-calendar-allopen" onchange="wppaGalleryEvaluate()" >'.__('Initially display all', 'wppw', 'wp-photo-album-plus').
				'</td>'.
			'</tr>'.

			// Size
			'<tr>'.
				'<th><label for="wppagallery-size">'.__('The size of the display:', 'wp-photo-album-plus').'</label></th>'.
				'<td>'.
					'<input type="text" id="wppagallery-size" value="" style="color:#070;" onchange="wppaGalleryEvaluate();"/>'.
					'<br />'.
					'<small>'.
						__('Specify the horizontal size in pixels or <span style="color:blue" >auto</span>.', 'wp-photo-album-plus').' '.
						__('A value less than <span style="color:blue" >100</span> will automaticly be interpreted as a <span style="color:blue" >percentage</span> of the available space.', 'wp-photo-album-plus').
						__('For responsive with a fixed maximum, add the max to auto e.g. <span style="color:blue" >auto,550</span>', 'wp-photo-album-plus' ).'<br />'.
						__('Leave this blank for default size', 'wp-photo-album-plus').
						'</small>'.
				'</td>'.
			'</tr>'.

			// Align
			'<tr>'.
				'<th><label for="wppagallery-align">'.__('Horizontal alignment:', 'wp-photo-album-plus').'</label></th>'.
				'<td>'.
					'<select id="wppagallery-align" name="align" style="color:#070;" onchange="wppaGalleryEvaluate();">'.
						'<option value="none" >'.__('--- none ---', 'wp-photo-album-plus').'</option>'.
						'<option value="left" >'.__('left', 'wp-photo-album-plus').'</option>'.
						'<option value="center" >'.__('center', 'wp-photo-album-plus').'</option>'.
						'<option value="right" >'.__('right', 'wp-photo-album-plus').'</option>'.
					'</select>'.
					'<br />'.
					'<small>'.__('Specify the alignment to be used or --- none ---', 'wp-photo-album-plus').'</small>'.
				'</td>'.
			'</tr>'.

		'</table>'.
		'<div id="wppagallery-shortcode-preview-container" >'.
			'<input type="text" id="wppagallery-shortcode-preview" style="background-color:#ddd; width:100%; height:26px;" value="[wppa]Any comment[/wppa]" />'.
		'</div>'.
		'<div><small>'.__('This is a preview of the shortcode that is being generated. You may edit the comment', 'wp-photo-album-plus').'</small></div>'.
		'<p class="submit">'.
			'<input type="button" id="wppagallery-submit" class="button-primary" value="'.__('Insert Gallery', 'wp-photo-album-plus').'" name="submit" />&nbsp;'.
			'<input type="button" id="wppagallery-submit-notok" class="button-secundary" value="'.__('insert Gallery', 'wp-photo-album-plus').'" onclick="alert(\''.esc_js(__('Please complete the shortcode specs', 'wp-photo-album-plus')).'\')" />&nbsp;'.
		'</p>'.
	'</div>'.
	'<script type="text/javascript" >wppaGalleryEvaluate();</script>';
	return $result;
}
?>