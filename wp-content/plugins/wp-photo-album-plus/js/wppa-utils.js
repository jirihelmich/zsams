// wppa-utils.js
//
// conatins common vars and functions
//
var wppaJsUtilsVersion = '6.5.03';
var wppaDebug;

// Trim
// @1 string to be trimmed
// @2 character, string, or array of characters or strings to trim off,
//    default: trim spaces, tabs and newlines
function wppaTrim( str, arg ) {

	var result;

	result = wppaTrimLeft( str, arg );
	result = wppaTrimRight( result, arg );

	return result;
}

// Trim left
// @1 string to be trimmed
// @2 character, string, or array of characters or strings to trim off,
//    default: trim spaces, tabs and newlines
function wppaTrimLeft( str, arg ) {

	var result;
	var strlen;
	var arglen;
	var argcount;
	var i;
	var done;
	var oldStr, newStr;

	switch ( typeof ( arg ) ) {
		case 'string':
			result = str;
			strlen = str.length;
			arglen = arg.length;
			while ( strlen >= arglen && result.substr( 0, arglen ) == arg ) {
				result = result.substr( arglen );
				strlen = result.length;
			}
			break;
		case 'object':
			done = false;
			newStr = str;
			while ( ! done ) {
				i = 0;
				oldStr = newStr;
				while ( i < arg.length ) {
					newStr = wppaTrimLeft( newStr, arg[i] );
					i++;
				}
				done = ( oldStr == newStr );
			}
			result = newStr;
			break;
		default:
			return str.replace( /^\s\s*/, '' );
	}

	return result;
}

// Trim right
// @1 string to be trimmed
// @2 character, string, or array of characters or strings to trim off,
//    default: trim spaces, tabs and newlines
function wppaTrimRight( str, arg ) {

	var result;
	var strlen;
	var arglen;
	var argcount;
	var i;
	var done;
	var oldStr, newStr;

	switch ( typeof ( arg ) ) {
		case 'string':
			result = str;
			strlen = str.length;
			arglen = arg.length;
			while ( strlen >= arglen && result.substr( strlen - arglen ) == arg ) {
				result = result.substr( 0, strlen - arglen );
				strlen = result.length;
			}
			break;
		case 'object':
			done = false;
			newStr = str;
			while ( ! done ) {
				i = 0;
				oldStr = newStr;
				while ( i < arg.length ) {
					newStr = wppaTrimRight( newStr, arg[i] );
					i++;
				}
				done = ( oldStr == newStr );
			}
			result = newStr;
			break;
		default:
			return str.replace( /\s\s*$/, '' );
	}

	return result;
}

// Cookie handling
function wppa_setCookie(c_name,value,exdays) {
var exdate=new Date();
exdate.setDate(exdate.getDate() + exdays);
var c_value=escape(value) + ((exdays==null) ? "" : "; expires="+exdate.toUTCString());
document.cookie=c_name + "=" + c_value;
}

function wppa_getCookie(c_name) {
var i,x,y,ARRcookies=document.cookie.split(";");
for (i=0;i<ARRcookies.length;i++)
{
  x=ARRcookies[i].substr(0,ARRcookies[i].indexOf("="));
  y=ARRcookies[i].substr(ARRcookies[i].indexOf("=")+1);
  x=x.replace(/^\s+|\s+$/g,"");
  if (x==c_name)
    {
    return unescape(y);
    }
  }
  return "";
}

// Change stereotype cookie
function wppaStereoTypeChange( newval ) {
	wppa_setCookie( 'stereotype', newval, 365 );
}

// Change stereoglass cookie
function wppaStereoGlassChange( newval ) {
	wppa_setCookie( 'stereoglass', newval, 365 );
}

// Console logging
function wppaConsoleLog( arg, force ) {

	if ( typeof( console ) != 'undefined' && ( wppaDebug || force == 'force' ) ) {
		var d = new Date();
		var n = d.getTime();
		var t = n % (24*60*60*1000); 				// msec this day
		var h = Math.floor( t / ( 60*60*1000 ) ); 	// Hours this day
		t -= h * 60*60*1000;						// msec this hour
		var m = Math.floor( t / ( 60*1000 ) );		// Minutes this hour
		t -= m * 60*1000;							// msec this minute
		var s = Math.floor( t / 1000 );				// Sec this minute
		t -= s * 1000;								// msec this sec
		console.log( 'At: ' + h + ':' + m + ':' + s + '.' + t + ' message: ' + arg );
	}
}

// Conversion utility
function wppaConvertScriptToShortcode( scriptId, shortcodeId ) {

	var script;
	var workArr;
	var temp;
	var item;
	var value;
	var type;
	var album;
	var photo;
	var size;
	var align;
	var result;

	script = jQuery( '#'+scriptId ).val();
	if ( typeof( script ) != 'string' || script.length == 0 ) {
		jQuery( '#'+shortcodeId ).val( 'No script found' );
		jQuery( '#'+shortcodeId ).css( 'color', 'red' );
		return;
	}

	workarr = script.split( '%%' );
	if ( workarr[1] != 'wppa' || workarr.length < 3 ) {
		jQuery( '#'+shortcodeId ).val( 'No %%wppa%% found' );
		jQuery( '#'+shortcodeId ).css( 'color', 'red' );
		return;
	}

	for ( i=3;i<workarr.length;i+=2 ) {
		temp = workarr[i].split( '=' );
		item = temp[0];
		value = temp[1];
		if ( item && value ) {
			switch( item ) {
				case 'size':
					size = value;
					break;
				case 'align':
					align = value;
					break;
				case 'photo':
				case 'mphoto':
				case 'slphoto':
					type = item;
					photo = value;
					break;
				case 'album':
				case 'cover':
				case 'slide':
				case 'slideonly':
				case 'slideonlyf':
				case 'slidef':
					type = item;
					album = value;
					break;
				default:
					jQuery( '#'+shortcodeId ).val( 'Token "' + workarr[i] + '" not recognized' );
					jQuery( '#'+shortcodeId ).css( 'color', 'red' );
					return;

			}
		}
	}

	result = '[wppa';

	if ( type && type.length > 0 ) {
		result += ' type="' + type + '"';
	}

	if ( album && album.length > 0 ) {
		result += ' album="' + album + '"';
	}

	if ( photo && photo.length > 0 ) {
		result += ' photo="' + photo + '"';
	}

	if ( size && size.length > 0 ) {
		result += ' size="' + size + '"';
	}

	if ( align && align.length > 0 ) {
		result += ' align="' + align + '"';
	}

	result += '][/wppa]';

	jQuery( '#'+shortcodeId ).val( result );
	jQuery( '#'+shortcodeId ).css( 'color', 'green' );

	document.getElementById( shortcodeId ).focus();
    document.getElementById( shortcodeId ).select();

}

// Say we're in
wppaConsoleLog( 'wppa-utils.js version '+wppaJsUtilsVersion+' loaded.', 'force' );