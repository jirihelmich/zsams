<?php
/* wppa-htaccess.php
* Package: wp-photo-album-plus
*
* Various funcions
* Version 6.4.19
*
*/

if ( ! defined( 'ABSPATH' ) ) die( "Can't load this file directly" );

// Create .htaccess in the .../uploads/wppa folder to grant normal http access to photo files
function wppa_create_wppa_htaccess() {

	$file = WPPA_UPLOAD_PATH . '/.htaccess';
	wppa_create_wppa_htaccess_( $file );

	$file = WPPA_UPLOAD_PATH . '/thumbs/.htaccess';
	wppa_create_wppa_htaccess_( $file );
}
function wppa_create_wppa_htaccess_( $filename ) {

	switch ( get_option( 'wppa_cre_uploads_htaccess' ) ) {

		// Grant access
		case 'grant':
			$file = fopen( $filename, 'wb' );
			if ( $file ) {
				fwrite( $file, '<IfModule mod_rewrite.c>' );
				fwrite( $file, "\n" . 'RewriteEngine Off' );
				fwrite( $file, "\n" . '</IfModule>' );
				fwrite( $file, "\n" . 'Order Allow,Deny' );
				fwrite( $file, "\n" . 'Allow from all' );
				fclose( $file );
			}
			break;

		// No hotlink
		case 'nohot':
			$domain = site_url();
			$domain = str_replace( 'https://', '', $domain );
			$domain = str_replace( 'http://', '', $domain );
			$domain = str_replace( 'www.', '', $domain );
			$i = strpos( $domain, '/' );
			if ( $i ) {
				$domain = substr( $domain, 0, $i );
			}
			$i = strpos( $domain, '?' );
			if ( $i ) {
				$domain = substr( $domain, 0, $i );
			}
			$file = fopen( $filename, 'wb' );
			if ( $file ) {
/*
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteCond %{HTTP_REFERER} !^http(s)?://(www\.)?beta.opajaap.nl [NC]
RewriteCond %{HTTP_REFERER} !^http(s)?://(www\.)?beta.opajaap.nl.*$ [NC]
RewriteRule \.(jpg|jpeg|png|gif)$ - [NC,F]
</IfModule>
*/
				fwrite( $file, 		  '<IfModule mod_rewrite.c>' );
				fwrite( $file, "\n" . 'RewriteEngine On' );
				fwrite( $file, "\n" . 'RewriteCond %{HTTP_REFERER} !^http(s)?://(www\.)?' . $domain . ' [NC]' );
				fwrite( $file, "\n" . 'RewriteCond %{HTTP_REFERER} !^http(s)?://(www\.)?' . $domain . '.*$ [NC]' );
				fwrite( $file, "\n" . 'RewriteRule \.(jpg|jpeg|png|gif|mp4|ogv|webm|mp3|wav|ogg)$ - [NC,F]' );
				fwrite( $file, "\n" . '</IfModule>' );
				fclose( $file );
			}
			break;

		// Destroy it
		case 'remove':
			@ unlink( $filename );
			break;

		// Leave unchanged
		case 'custom':
			break;

		// Unimplemented
		default:
			wppa_log( 'Err', 'Unimplemented choice for wppa_cre_uploads_htaccess ' . wppa_opt( 'cre_uploads_htaccess' ) );
			break;
	}
}

// Create .../wp-content/wppa-pl and .../wp-content/wppa-pl/.htaccess to support permalinks for photo source files
function wppa_create_pl_htaccess( $pl_dirname = '' ) {
global $wpdb;

	// Only supported on single sites at the moment
	if ( is_multisite() && ! WPPA_MULTISITE_GLOBAL ) {
		return false;
	}

	// Where are the photo source files?
	$source_root = str_replace( ABSPATH, '', wppa_opt( 'source_dir' ) );

	// Find permalink root name
	if ( ! $pl_dirname ) {
		$pl_dirname = wppa_opt( 'pl_dirname' );
	}

	// If no pl_dirname, feature is disabled
	if ( ! $pl_dirname ) {
		return false;
	}

	// Create pl root directory
	$pl_root = WPPA_CONTENT_PATH . '/' . $pl_dirname;
	if ( ! wppa_mktree( $pl_root ) ) {
		wppa_log( 'Error', 'Can not create '.$pl_root );
		return false;
	}

	// Create .htaccess file
	$file = fopen( $pl_root . '/.htaccess', 'wb' );
	if ( ! $file ) {
		wppa_log( 'Error', 'Can not create '.$pl_root . '/.htaccess' );
		return false;
	}

	fwrite( $file, '<IfModule mod_rewrite.c>' );
	fwrite( $file, "\n" . 'RewriteEngine On' );
	// RewriteBase /wp-content/wppa-pl
	fwrite( $file, "\n" . 'RewriteBase /' . str_replace( ABSPATH, '', $pl_root ) );
	$albs = $wpdb->get_results( "SELECT `id`, `name` FROM `".WPPA_ALBUMS."` ORDER BY `name` DESC", ARRAY_A );
	if ( $albs ) foreach( $albs as $alb ) {
		$fm = wppa_sanitize_file_name( $alb['name'], false );
		$to = $source_root . '/album-'.$alb['id'];
		fwrite( $file, "\n" . 'RewriteRule ^'.$fm.'/(.*) /'.$to.'/$1 [NC]' );
	}
	fwrite( $file, "\n" . '</IfModule>' );
	fclose( $file );
	return true;
}

