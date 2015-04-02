<?php
/**
 * @since 1.0
 *
 * Map request URI to request type
 * 
 * 1) First Break down request URI into 3 components:
 *    - GLOBALS['request']['model']        => 1st part of request uri following the project URL
 *    - GLOBALS['request']['param_name']   => 2nd part of request uri following the project URL
 *    - GLOBALS['request']['param_value']  => 3rd part of request uri following the project URL
 * Example:
 * If the request URI is myapp.com/shipments/page/1, we will have:
 *    - GLOBALS['request']['model']       ==== 'shipments'
 *    - GLOBALS['request']['param_name']  ==== 'page'
 *    - GLOBALS['request']['param_value'] ==== 1
 *
 * 2) Then also has a function used to determine the type of request made, based on the
 * the 3 superglobals determined before
 */

$request_uri = filter_var( $_SERVER['REQUEST_URI'], FILTER_SANITIZE_URL );
if( false === $request_uri ) {
	return;
}
$request_uri = parse_url( $request_uri );

//Delete trailing slash so that below explode works well
$request_relative_path = rtrim( str_replace( LOCAL_APP_URI, '', $request_uri['path'] ), '/' ); 

//Explode $request_relative_path elements in a an array ( '/' is the separator)
$request_relative_path = explode( '/', $request_relative_path  );

//If there isn't every element in the request url, add empty string ''
$request_relative_path = array_pad( $request_relative_path, 3, '' );

$GLOBALS['request']['model']       = ( $request_relative_path[0] === '' ) ? 'shipments' : $request_relative_path[0];
$GLOBALS['request']['param_name']  = ( $request_relative_path[1] === '' ) ? 'page'      : $request_relative_path[1];
$GLOBALS['request']['param_value'] = ( $request_relative_path[2] === '' ) ? 1           : $request_relative_path[2];
$GLOBALS['pagination_params']      = get_pagination_params( $GLOBALS['request']['param_value'] );


/**
 * @since 1.0
 *
 * Determines the type of request based on url
 *
 * The function is based on the values of two superglobals:
 *    - $GLOBALS['request']['model'] => 1st part of request uri following the project URL
 *    - $GLOBALS['request']['param_name'] => 2nd part of request uri following the project URL
 * It checks both value to determine the type of the request.
 * The returned value is then use by index.php (bootstrap file at project root) to call the 
 * appropriate controller
 * 
 * @param void
 * @return (string) 'read' | 'search' | 'update' | '404' - the type of the request
 */
function get_request_type() {
	$models = array( 'shipments', 'remarks', 'stock' );

	if( in_array( $GLOBALS['request']['model'], $models ) &&  'page' === $GLOBALS['request']['param_name'] ) {
		return 'read';

	} elseif( in_array( $GLOBALS['request']['model'], $models ) && 'search' === $GLOBALS['request']['param_name'] ) {
		//Correct the value of param_value if its a search (i.e search will send search parameter with a GET request, not in URL)
		$GLOBALS['request']['param_value'] = isset( $_GET['search_value'] ) ? strip_tags( $_GET['search_value'] ) : 1;
		return 'search';

	} elseif( 'all' === $GLOBALS['request']['model'] && 'update' === $GLOBALS['request']['param_name'] ) {
		return 'update';

	} else {
		return '404';
	}
}
