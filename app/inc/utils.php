<?php
/**
 * @since 1.0
 *
 * Utilities functions
 */

function is_active( $current_value, $request_value ) {
	return $is_active = ( $current_value === $request_value ) ? 'active' : '';
}

function is_updated( $table ) {
	$ftp_files = get_ftp_files( $table );
	$local_files = get_local_files( $table );
	$diff = array_diff( $ftp_files, $local_files );
	
	return count( $diff ) === 0 ? ' <span class="glyphicon glyphicon-ok bg-success"></span>&nbsp;&nbsp; Updated' : ' <span class="glyphicon glyphicon-remove bg-danger"></span>&nbsp;&nbsp; Not Updated';
}

function get_pagination_params( $page ) {
	$page = abs( $page ); //make sure we work with a number
	$start_from = max( 0, ( $page - 1 ) * 20 ); 
	$next_page = max( 0, $page - 1 );
	$previous_page = max( 1, $page + 1 ); //If page is negative it could be negative, so need max as well
	$disabled = ( $next_page === 0 ) ? 'disabled' : '' ;

	return array( 
		'row_offset'    => $start_from,
		'row_max'       => 20,
		'next_page'     => $next_page, 
		'previous_page' => $previous_page, 
		'disabled'      => $disabled, 
	);
}

function create_message( $content, $type ) {
	$_SESSION['message']['content'] = htmlspecialchars( $content );
	$_SESSION['message']['type']    = htmlspecialchars( $type );
}

/**
 * Used for printing variable content during debugging
 *
 * named inspired of Laravel dd() 'die and dump' utility
 * function, but actually it does not die()
 * 
 * @param  (mixed)   $variable       Value or reference of the variable to inspect
 * @param  (string)  $variable_name  Name of the variable to inspect
 * @return (void)
 */
function dd( $variable, $variable_name ) {
	echo '<hr>';
	echo "<h2> VARIABLE: {$variable_name}</h1>";
	echo '<pre>';
	var_dump( $variable );
	echo '</pre>';
	echo '<hr>';
}