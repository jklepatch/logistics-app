<?php
/**
 * @since 1.0
 *
 * Search controller
 *
 * Handles search requests for shipments model
 */

//Exit if app wasn't started the normal way (calling index.php in project root)
if( NORMAL_LAUNCH <> true ) {
 	return;
}

//check that search request if for the shipments model, find results and display them
if( 'shipments' === $GLOBALS['request']['model'] ) {
	$shipments_data = $shipments->search_data( array( 'search_value' => $GLOBALS['request']['param_value'] ) );
	$shipments_schema = $shipments->get_schema();
	render_view( 'list', compact( 'shipments_data', 'shipments_schema' ) );
}
 
render_view( 'footer' );







