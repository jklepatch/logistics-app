<?php
/**
 * @since 1.0
 *
 * Update controller
 *
 * Handles all updates (shipments, remarks & stock models)
 */

//Exit if app wasn't started the normal way (calling index.php in project root)
if( NORMAL_LAUNCH <> true ) {
 	return;
}

// Update all three models (shipments, stock and remarks)
if( 'all' === $GLOBALS['request']['model']  && 'update' === $GLOBALS['request']['param_name'] ) {
	$need_update = $shipments->need_update(); //need_update() not yet implemented - as of now always  returns true
	if( $need_update ) {
		$update_result_shipments = $shipments->update_data();
	}

	$need_update = $remarks->need_update(); //need_update() not yet implemented - as of now always  returns true
	if( $need_update ) {
		$update_result_remarks = $remarks->update_data() );
	}

	$need_update = $stock->need_update(); //need_update() not yet implemented - as of now always returns true
	if( $need_update ) {
		$update_result_stock  = $stock->update_data();
	}
}

render_view( 'footer' );







