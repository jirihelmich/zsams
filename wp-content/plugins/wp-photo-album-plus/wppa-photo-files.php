<?php
/* wppa-photo-files.php
*
* Functions used to create/manipulate photofiles
* Version 6.4.18
*
*/

if ( ! defined( 'ABSPATH' ) ) die( "Can't load this file directly" );

// Unfortunately there is no php function to rotate or resize an image file while the exif data is preserved.
// The origianal sourcefile is normally saved, to be available for download or hires uses e.g. in lightbox.
// The orientation of photos made by mobile devices is often non-standard ( 1 ), so we need a higres file,
// rotated and/or mirrored to the correct position.
// When the sourefile name is e.g.: .../wp-content/uploads/wppa-source/album-1/MyImage.jpg,
// We create the correct oriented file: .../wp-content/uploads/wppa-source/album-1/MyImage-o1.jpg. ( o1 stands for orientation=1 ).
// Note: wppa_get_source_path() should return the un-oriented file always, while wppa_get_hires_url() must return the -o1 file, if available.
function wppa_make_o1_source( $id ) {

	// Init
	$src_path = wppa_get_source_path( $id );

	// Source available?
	if ( ! is_file( $src_path ) ) return false;

	// Only needed for non-standard orientations
	$orient = wppa_get_exif_orientation( $src_path );
	if ( ! in_array( $orient, array( '2', '3', '4', '5', '6', '7', '8' ) ) ) return false;

	// Only on jpg file type
	$ext = wppa_get_ext( $src_path );
	if ( ! in_array( $ext, array( 'jpg', 'JPG', 'jpeg', 'JPEG' ) ) ) return false;

	// Make destination path
	$dst_path = wppa_get_o1_source_path( $id );

	// Copy source to destination
	copy( $src_path, $dst_path );

	// Correct orientation
	if ( ! wppa_orientate_image_file( $dst_path, $orient ) ) {
		unlink( $dst_path );
		return false;
	}

	// Done
	return true;
}

// Convert source file path to proper oriented source file path
function wppa_get_o1_source_path( $id ) {

	$src_path = wppa_get_source_path( $id );
	if ( $src_path ) {
		$src_path = wppa_strip_ext( $src_path ) . '-o1.' . wppa_get_ext( $src_path );
	}

	return $src_path;
}

// Rotate/mirror a photo display image by id
function wppa_orientate_image( $id, $ori ) {

	// If orientation right, do nothing
	if ( ! $ori || $ori == '1' ) {
		return;
	}

	wppa_orientate_image_file( wppa_fix_poster_ext( wppa_get_photo_path( $id ), $id ), $ori );
	wppa_bump_photo_rev();
}

// Rotate/mirror an image file by pathname
function wppa_orientate_image_file( $file, $ori ) {

	// Validate args
	if ( ! is_file( $file ) ) {
		wppa_log( 'Err', 'File not found (wppa_orientate_image_file())' );
		return false;
	}
	if ( ! wppa_is_int( $ori ) || $ori < '2' || $ori > '8' ) {
		wppa_log( 'Err', 'Bad arg $ori:'.$ori.' (wppa_orientate_image_file())' );
		return false;
	}

	// Load image
	$source = wppa_imagecreatefromjpeg( $file );
	if ( ! $source ) {
		wppa_log( 'Err', 'Could not create memoryimage from jpg file ' . $file );
		return false;
	}

	// Perform operation
	switch ( $ori ) {
		case '2':
			$orientate = $source;
			imageflip( $orientate, IMG_FLIP_HORIZONTAL );
			break;
		case '3':
			$orientate = imagerotate( $source, 180, 0 );
			break;
		case '4':
			$orientate = $source;
			imageflip( $orientate, IMG_FLIP_VERTICAL );
			break;
		case '5':
			$orientate = imagerotate( $source, 270, 0 );
			imageflip( $orientate, IMG_FLIP_HORIZONTAL );
			break;
		case '6':
			$orientate = imagerotate( $source, 270, 0 );
			break;
		case '7':
			$orientate = imagerotate( $source, 90, 0 );
			imageflip( $orientate, IMG_FLIP_HORIZONTAL );
			break;
		case '8':
			$orientate = imagerotate( $source, 90, 0 );
			break;
	}

	// Output
	imagejpeg( $orientate, $file, wppa_opt( 'jpeg_quality' ) );

	// Free the memory
	imagedestroy( $source );
	@ imagedestroy( $orientate );

	// Done
	return true;
}

// Make the display and thumbnails from a given pathname or upload temp image file.
// The id and extension must be supplied.
function wppa_make_the_photo_files( $file, $id, $ext ) {
global $wpdb;

	$thumb = wppa_cache_thumb( $id );

	$src_size = @getimagesize( $file, $info );

	// If the given file is not an image file, log error and exit
	if ( ! $src_size ) {
		if ( is_admin() ) wppa_error_message( sprintf( __( 'ERROR: File %s is not a valid picture file.' , 'wp-photo-album-plus'), $file ) );
		else wppa_alert( sprintf( __( 'ERROR: File %s is not a valid picture file.', 'wp-photo-album-plus'), $file ) );
		return false;
	}

	// Find output path photo file
	$newimage = wppa_get_photo_path( $id );
	if ( $ext ) {
		$newimage = wppa_strip_ext( $newimage ) . '.' . strtolower( $ext );
	}

	// If Resize on upload is checked
	if ( wppa_switch( 'resize_on_upload' ) ) {

		// Picture sizes
		$src_width 	= $src_size[0];

		// Temp convert to logical width if stereo
		if ( $thumb['stereo'] ) {
			$src_width /= 2;
		}
		$src_height = $src_size[1];

		// Max sizes
		if ( wppa_opt( 'resize_to' ) == '0' ) {	// from fullsize
			$max_width 	= wppa_opt( 'fullsize' );
			$max_height = wppa_opt( 'maxheight' );
		}
		else {										// from selection
			$screen = explode( 'x', wppa_opt( 'resize_to' ) );
			$max_width 	= $screen[0];
			$max_height = $screen[1];
		}

		// If orientation needs +/- 90 deg rotation, swap max x and max y
		$ori = wppa_get_exif_orientation( $file );
		if ( $ori >= 5 && $ori <= 8 ) {
			$t = $max_width;
			$max_width = $max_height;
			$max_height = $t;
		}

		// Is source more landscape or more portrait than max window
		if ( $src_width/$src_height > $max_width/$max_height ) {	// focus on width
			$focus = 'W';
			$need_downsize = ( $src_width > $max_width );
		}
		else {														// focus on height
			$focus = 'H';
			$need_downsize = ( $src_height > $max_height );
		}

		// Convert back to physical size
		if ( $thumb['stereo'] ) {
			$src_width *= 2;
		}

		// Downsize required ?
		if ( $need_downsize ) {

			// Find mime type
			$mime = $src_size[2];

			// Create the source image
			switch ( $mime ) {	// mime type
				case 1: // gif
					$temp = @ imagecreatefromgif( $file );
					if ( $temp ) {
						$src = imagecreatetruecolor( $src_width, $src_height );
						imagecopy( $src, $temp, 0, 0, 0, 0, $src_width, $src_height );
						imagedestroy( $temp );
					}
					else $src = false;
					break;
				case 2:	// jpeg
					if ( ! function_exists( 'wppa_imagecreatefromjpeg' ) ) {
						wppa_log( 'Error', 'Function wppa_imagecreatefromjpeg does not exist.' );
					}
					$src = @ wppa_imagecreatefromjpeg( $file );
					break;
				case 3:	// png
					$src = @ imagecreatefrompng( $file );
					break;
			}

			if ( ! $src ) {
				wppa_log( 'Error', 'Image file '.$file.' is corrupt while downsizing photo' );
				return false;
			}

			// Create the ( empty ) destination image
			if ( $focus == 'W') {
				if ( $thumb['stereo'] ) $max_width *= 2;
				$dst_width 	= $max_width;
				$dst_height = round( $max_width * $src_height / $src_width );
			}
			else {
				$dst_height = $max_height;
				$dst_width = round( $max_height * $src_width / $src_height );
			}
			$dst = imagecreatetruecolor( $dst_width, $dst_height );

			// If Png, save transparancy
			if ( $mime == 3 ) {
				imagealphablending( $dst, false );
				imagesavealpha( $dst, true );
			}

			// Do the copy
			imagecopyresampled( $dst, $src, 0, 0, 0, 0, $dst_width, $dst_height, $src_width, $src_height );

			// Remove source image
			imagedestroy( $src );

			// Save the photo
			switch ( $mime ) {	// mime type
				case 1:
					imagegif( $dst, $newimage );
					break;
				case 2:
					imagejpeg( $dst, $newimage, wppa_opt( 'jpeg_quality' ) );
					break;
				case 3:
					imagepng( $dst, $newimage, 6 );
					break;
			}

			// Remove destination image
			imagedestroy( $dst );
		}
		else {	// No downsize needed, picture is small enough
			copy( $file, $newimage );
		}
	}	// No resize on upload checked
	else {
		copy( $file, $newimage );
	}

	// File successfully created ?
	if ( is_file ( $newimage ) ) {

		// Optimize file
		wppa_optimize_image_file( $newimage );
	}
	else {
		if ( is_admin() ) wppa_error_message( __( 'ERROR: Resized or copied image could not be created.' , 'wp-photo-album-plus') );
		else wppa_alert( __( 'ERROR: Resized or copied image could not be created.', 'wp-photo-album-plus') );
		return false;
	}

	// Process the iptc data
	wppa_import_iptc( $id, $info );

	// Process the exif data
	wppa_import_exif( $id, $file );

	// GPS
	wppa_get_coordinates( $file, $id );

	// Set ( update ) exif date-time if available
	$exdt = wppa_get_exif_datetime( $file );
	if ( $exdt ) {
		wppa_update_photo( array( 'id' => $id, 'exifdtm' => $exdt ) );
	}

	// Check orientation
	wppa_orientate_image( $id, wppa_get_exif_orientation( $file ) );

	// Compute and save sizes
	wppa_get_photox( $id, 'force' );

	// Show progression
	if ( is_admin() && ! wppa( 'ajax' ) ) echo( '.' );

	// Update CDN
	$cdn = wppa_cdn( 'admin' );
	if ( $cdn ) {
		switch ( $cdn ) {
			case 'cloudinary':
				wppa_upload_to_cloudinary( $id );
				break;
			default:
				wppa_dbg_msg( 'Missing upload instructions for '.$cdn, 'red', 'force' );
		}
	}

	// Create stereo images
	wppa_create_stereo_images( $id );

	// Create thumbnail...
	wppa_create_thumbnail( $id );

	// Clear (super)cache
	wppa_clear_cache();
	return true;

}

// Create thubnail
function wppa_create_thumbnail( $id, $use_source = true ) {

	// Find file to make thumbnail from
	$source_path = wppa_fix_poster_ext( wppa_get_source_path( $id ), $id );

	// Use source if requested and available
	if ( $use_source ) {

		if ( ! wppa_switch( 'watermark_thumbs' ) && is_file( $source_path ) ) {
			$file = $source_path;										// Use sourcefile
		}
		else {
			$file = wppa_fix_poster_ext( wppa_get_photo_path( $id ), $id );	// Use photofile
		}

		// Non standard orientation files: never use source
		$orient = wppa_get_exif_orientation( $file );
		if ( $orient > '1' ) {
			$file = wppa_fix_poster_ext( wppa_get_photo_path( $id ), $id );	// Use photofile
		}
	}
	else {
		$file = wppa_fix_poster_ext( wppa_get_photo_path( $id ), $id );	// Use photofile
	}

	// Max side
	$max_side = wppa_get_minisize();

	// Check file
	if ( ! file_exists( $file ) ) return false;		// No file, fail
	$img_attr = getimagesize( $file );
	if ( ! $img_attr ) return false;				// Not an image, fail

	// Retrieve aspect
	$asp_attr = explode( ':', wppa_opt( 'thumb_aspect' ) );

	// Get output path
	$thumbpath = wppa_get_thumb_path( $id );
	if ( wppa_get_ext( $thumbpath ) == 'xxx' ) { // Video poster
		$thumbpath = wppa_strip_ext( $thumbpath ) . '.jpg';
	}

	// Source size
	$src_size_w = $img_attr[0];
	$src_size_h = $img_attr[1];

	// Temp convert width if stereo
	if ( wppa_get_photo_item( $id, 'stereo' ) ) {
		$src_size_w /= 2;
	}

	// Mime type and thumb type
	$mime = $img_attr[2];
	$type = $asp_attr[2];

	// Source native aspect
	$src_asp = $src_size_h / $src_size_w;

	// Required aspect
	if ( $type == 'none' ) {
		$dst_asp = $src_asp;
	}
	else {
		$dst_asp = $asp_attr[0] / $asp_attr[1];
	}

	// Convert back width if stereo
	if ( wppa_get_photo_item( $id, 'stereo' ) ) {
		$src_size_w *= 2;
	}

	// Create the source image
	switch ( $mime ) {	// mime type
		case 1: // gif
			$temp = @ imagecreatefromgif( $file );
			if ( $temp ) {
				$src = imagecreatetruecolor( $src_size_w, $src_size_h );
				imagecopy( $src, $temp, 0, 0, 0, 0, $src_size_w, $src_size_h );
				imagedestroy( $temp );
			}
			else $src = false;
			break;
		case 2:	// jpeg
			if ( ! function_exists( 'wppa_imagecreatefromjpeg' ) ) wppa_log( 'Error', 'Function wppa_imagecreatefromjpeg does not exist.' );
			$src = @ wppa_imagecreatefromjpeg( $file );
			break;
		case 3:	// png
			$src = @ imagecreatefrompng( $file );
			break;
	}
	if ( ! $src ) {
		wppa_log( 'Error', 'Image file '.$file.' is corrupt while creating thmbnail' );
		return true;
	}

	// Compute the destination image size
	if ( $dst_asp < 1.0 ) {	// Landscape
		$dst_size_w = $max_side;
		$dst_size_h = round( $max_side * $dst_asp );
	}
	else {					// Portrait
		$dst_size_w = round( $max_side / $dst_asp );
		$dst_size_h = $max_side;
	}

	// Create the ( empty ) destination image
	$dst = imagecreatetruecolor( $dst_size_w, $dst_size_h );
	if ( $mime == 3 ) {	// Png, save transparancy
		imagealphablending( $dst, false );
		imagesavealpha( $dst, true );
	}

	// Fill with the required color
	$c = trim( strtolower( wppa_opt( 'bgcolor_thumbnail' ) ) );
	if ( $c != '#000000' ) {
		$r = hexdec( substr( $c, 1, 2 ) );
		$g = hexdec( substr( $c, 3, 2 ) );
		$b = hexdec( substr( $c, 5, 2 ) );
		$color = imagecolorallocate( $dst, $r, $g, $b );
		if ( $color === false ) {
			wppa_log( 'Err', 'Unable to set background color to: '.$r.', '.$g.', '.$b.' in wppa_create_thumbnail' );
		}
		else {
			imagefilledrectangle( $dst, 0, 0, $dst_size_w, $dst_size_h, $color );
		}
	}

	// Switch on what we have to do
	switch ( $type ) {
		case 'none':	// Use aspect from fullsize image
			$src_x = 0;
			$src_y = 0;
			$src_w = $src_size_w;
			$src_h = $src_size_h;
			$dst_x = 0;
			$dst_y = 0;
			$dst_w = $dst_size_w;
			$dst_h = $dst_size_h;
			break;
		case 'clip':	// Clip image to given aspect ratio
			if ( $src_asp < $dst_asp ) {	// Source image more landscape than destination
				$dst_x = 0;
				$dst_y = 0;
				$dst_w = $dst_size_w;
				$dst_h = $dst_size_h;
				$src_x = round( ( $src_size_w - $src_size_h / $dst_asp ) / 2 );
				$src_y = 0;
				$src_w = round( $src_size_h / $dst_asp );
				$src_h = $src_size_h;
			}
			else {
				$dst_x = 0;
				$dst_y = 0;
				$dst_w = $dst_size_w;
				$dst_h = $dst_size_h;
				$src_x = 0;
				$src_y = round( ( $src_size_h - $src_size_w * $dst_asp ) / 2 );
				$src_w = $src_size_w;
				$src_h = round( $src_size_w * $dst_asp );
			}
			break;
		case 'padd':	// Padd image to given aspect ratio
			if ( $src_asp < $dst_asp ) {	// Source image more landscape than destination
				$dst_x = 0;
				$dst_y = round( ( $dst_size_h - $dst_size_w * $src_asp ) / 2 );
				$dst_w = $dst_size_w;
				$dst_h = round( $dst_size_w * $src_asp );
				$src_x = 0;
				$src_y = 0;
				$src_w = $src_size_w;
				$src_h = $src_size_h;
			}
			else {
				$dst_x = round( ( $dst_size_w - $dst_size_h / $src_asp ) / 2 );
				$dst_y = 0;
				$dst_w = round( $dst_size_h / $src_asp );
				$dst_h = $dst_size_h;
				$src_x = 0;
				$src_y = 0;
				$src_w = $src_size_w;
				$src_h = $src_size_h;
			}
			break;
		default:		// Not implemented
			return false;
	}

	// Copy left half if stereo
	if ( wppa_get_photo_item( $id, 'stereo' ) ) {
		$src_w /= 2;
	}

	// Do the copy
	imagecopyresampled( $dst, $src, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h );

	// Save the thumb
	$thumbpath = wppa_strip_ext( $thumbpath );
	switch ( $mime ) {	// mime type
		case 1:
			imagegif( $dst, $thumbpath . '.gif' );
			break;
		case 2:
			imagejpeg( $dst, $thumbpath . '.jpg', wppa_opt( 'jpeg_quality' ) );
			break;
		case 3:
			imagepng( $dst, $thumbpath . '.png', 6 );
			break;
	}

	// Cleanup
	imagedestroy( $src );
	imagedestroy( $dst );

	// Optimize
	wppa_optimize_image_file( $thumbpath );

	// Compute and save sizes
	wppa_get_thumbx( $id, 'force' );	// forces recalc x and y

	return true;
}

// To fix a bug in PHP as that photos made with the selfie camera of an android smartphone
// irroneously cause the PHP warning 'is not a valid JPEG file' and cause imagecreatefromjpag crash.
function wppa_imagecreatefromjpeg( $file ) {

	ini_set( 'gd.jpeg_ignore_warning', true );
	$img = imagecreatefromjpeg( $file );
	return $img;
}