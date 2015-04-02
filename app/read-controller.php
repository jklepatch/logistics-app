<?php
/**
 * @since 1.0
 *
 * Read controller
 *
 * Handles read requests for models shipments, remarks and stock
 */

//Exit if app wasn't started the normal way (calling index.php in project root)
if( NORMAL_LAUNCH <> true ) {
 	return;
}

//Check request model, fetch data and render view accordingly
if( 'shipments' === $GLOBALS['request']['model'] ) {
	$shipments_data = $shipments->get_data( array( 'page' => $GLOBALS['request']['param_value'] ) );
	$shipments_schema = $shipments->get_schema();
	render_view( 'list', compact( 'shipments_data', 'shipments_schema' ) );

} elseif( 'remarks' === $GLOBALS['request']['model'] ) {
	$remarks_data = $remarks->get_data( array( 'page' => $GLOBALS['request']['param_value'] ) );
	$remarks_schema = $remarks->get_schema();
	render_view( 'list', compact( 'remarks_data', 'remarks_schema' ) );

} elseif( 'stock' === $GLOBALS['request']['model'] ) {
	$stock_data = $stock->get_data( array( 'page' => $GLOBALS['request']['param_value'] ) );
	$stock_schema = $stock->get_schema();
	render_view( 'list', compact( 'stock_data', 'stock_schema' ) );
} 

render_view( 'footer' );







