<?php
/*
Plugin Name: Simple Random Charset
Plugin URI: http://yourls.org/
Description: Random url keywords, (sho.rt/hJudjK), but uses a limited charset and specify link length with admin page!
Version: 1.0
Author: Peter Ryan Berbec
Author URI: https://github.com/peterberbec
ripped from ozh's random keywork generator, but with a charset limitation.
*/


// Generate a random keyword
yourls_add_filter( 'random_keyword', 'prb_random_keyword' );
function prb_random_keyword() {
        return yourls_rnd_string( yourls_get_option( 'link_length' ), 0, yourls_get_option( 'charset_liste' ) );
}
// Don't increment sequential keyword tracker
yourls_add_filter( 'get_next_decimal', 'prb_random_keyword_next_decimal' );
function prb_random_keyword_next_decimal( $next ) {
        return ( $next - 1 );
}

// Hook the admin page into the 'plugins_loaded' event
yourls_add_action( 'plugins_loaded', 'prb_simple_random_charset_add_page' );

function prb_simple_random_charset_add_page () {
    yourls_register_plugin_page( 'prb_simple_random_charset', 'Random Charset', 'prb_simple_random_charset_do_page' );
}
// Display admin page
function prb_simple_random_charset_do_page () {
    if( isset( $_POST['charset_form'] ) ) {
        prb_charset_process ();
    } else {
        prb_charset_form ();
    }
}
// Display form to administrate charset list
function prb_charset_form () {
    $nonce = yourls_create_nonce( 'form_nonce' ) ;
    $liste_charset_display = yourls_get_option ( 'charset_liste' );
    if ( yourls_get_option( 'link_length' ) == false )
    {	
		yourls_add_option ( 'link_length', 5 );
    }
    $link_length_display = yourls_get_option ( 'link_length' );
    echo <<<HTML
        <h2>Random Charset</h2>
        <form method="post">
        <input type="hidden" name="nonce" value="$nonce" />
	<p><label for="charset_form">Random charset contains the following:</label>
	<input type="text" size="50" id="charset_form" name="charset_form" value="$liste_charset_display"></p>
	<p><label for="link_length_form">Length of random created links: </label>
	<input type="text" size="5" id="link_length_form" name="link_length_form" value="$link_length_display"></p>	      
        <p><input type="submit"></p>

		<p>I suggest you add a charset that does not contain easy to misinterperate characters. WARNING : erroneous entries may create unexpected behaviours, please double-check before validation.</p>
		<p>Example, these look similar:         I & l & 1, o & 0, S & 5, Z & 2</p>
		<p>Recommended charset: abcdefghjkmnpqrtuvwxy346789<br></p>
		<p>current charset: $liste_charset_display</p>
		<p>current length: $link_length_display</p>
        </form>
HTML;
}
// Update charset list
function prb_charset_process () {
    // Check nonce
    yourls_verify_nonce( 'form_nonce' ) ;
	
	$charset_long_Form = $_POST['charset_form'] ;
	$link_length_long_Form = $_POST['link_length_form'] ;

	
	// Update list 

	if ( yourls_get_option( 'charset_liste' ) !== false )
	{	
		yourls_update_option ( 'charset_liste', $charset_long_Form );
	}
	else
	{
		yourls_add_option ( 'charset_liste', $charset_long_Form );
	}
	if ( yourls_get_option( 'link_length' ) !== false )
	{	
		yourls_update_option ( 'link_length', $link_length_long_Form );
	}
	else
	{
		yourls_add_option ( 'link_length', $link_length_long_Form );
	}
	echo "Charset updated. New charset is " ;
	echo $charset_long_Form."<BR />";
	echo "Link Length updated. New link length is " ;
	echo $link_length_long_Form."<BR />";

}
