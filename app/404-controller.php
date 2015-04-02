<?php
/**
 * @since 1.0
 *
 * 404 controller
 * Handles bad requests
 */

//Exit if app wasn't started the normal way (calling index.php in project root)
if( NORMAL_LAUNCH <> true ) {
 	return;
}

render_view( '404' );

render_view( 'footer' );







