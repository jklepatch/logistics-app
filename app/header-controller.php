<?php
/**
 * @since 1.0
 *
 * Header controller
 *
 * Is called for every type of requests
 */

//Exit if app wasn't started the normal way (calling index.php in project root)
if( NORMAL_LAUNCH <> true ) {
 	return;
}

//Instantiates models
$shipments = new Shipments();
$remarks = new Remarks();
$stock = new Stock();

//Get model data
$remarks_data = $remarks->get_data( array( 'row_offset' => 0, 'row_max' => 3  ) );
$stock_data_homido = $stock->get_data( array( 'row_offset' => 0, 'row_max' => 1, 'where_field' => 'code', 'where_value' => 'HOMIDOFULLKITB'  ) );
$stock_data_carton = $stock->get_data( array( 'row_offset' => 0, 'row_max' => 1, 'where_field' => 'code', 'where_value' => 'CARTONCARRE'  ) );
$stock_data_lentille = $stock->get_data( array( 'row_offset' => 0, 'row_max' => 1, 'where_field' => 'code', 'where_value' => 'LENTILLE'  ) );

//Pass data to view and display it
render_view( 'header', compact( 'remarks_data', 'stock_data', 'stock_data_homido', 'stock_data_carton', 'stock_data_lentille' ) );