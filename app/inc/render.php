<?php
/**
 * @since 1.0
 *
 * Set of helper functions for displaying views and their elements
 *
 * It can render:
 *   - Views
 *   - Table
 *   - Pagination
 */

/**
 * @since 1.0
 *
 * Render a given view with data
 *
 * @param  (string) $view filename of the view
 * @param  (array)  $args data passed to the view
 * @return void
 */
function render_view( $view, $args = array() ) {
	if( $view === '' ) {
		return;
	}

	$view_path = LOCAL_VIEWS_DIR . '\\' . $view . '.php';
	if( file_exists ( $view_path ) ) {
		extract( $args );
		require_once $view_path;
	}
}

/**
 * @since 1.0
 *
 * Render a table
 *
 * Is normally called within a view
 * 
 * @param  (array)     $schema Description of data
 * @param  (resource)  $rows   Data
 * @return (void)      
 */
function render_table( $schema, $rows ) {
	echo '<table class="table table-striped"><thead><tr>';
	foreach( $schema['headers'] as $header_item ) {
		echo '<th>' . $header_item . '</th>';		
	}
	echo '</tr></thead><tbody>';

	/**
	 * We cache the result of the DB request in an array in order to be able
	 * to re-access these results after if needed (for render_tracking_number_message() 
	 * for example). If we don't do this and directly access the db resource,
	 * once we finish to loop through all the results it is not possible to 
	 * access any particular returned data nor to 'rewind' the db resource. 
	 */
	$rows = $rows->fetchAll(); //
	if( ! empty( $rows ) ) {
		foreach( $rows as $row ) {
			echo '<tr>';
			foreach( $schema['fields']  as $field ) {
				echo '<td>' . $row[$field] . '</td>';
			}
			echo '</tr>';
		}

	} else {
		echo '<tr><td colspan="100">No results</td></tr>';
	}

	echo '</tbody></table>';

	if( 'search' === get_request_type() && ! empty( $rows ) ) {
		render_tracking_number_message( $rows[0]['tracking'] );
	}

}

/**
 * @since 1.0
 *
 * Echo the tracking number and a description message relative to a specific shipment row
 *
 * Is called from a view, after a search has been done on a specific shipment
 *
 * @param  (int) $tracking_number Tracking number of the searched shipment
 * @return void      
 */
function render_tracking_number_message( $tracking_number ) {
	?>

	Hi, 
	<br>
	<br>
	Thanks for your order. 
	<br>
	<br>
	Your parcel is on the way. 
	<br>
	<br>
	You can track your parcel with below information:
	<br> 
	Tracking number: <?php echo $tracking_number; ?>
	<br>
	Link to the post office website: http://www.colissimo.fr/portail_colissimo/suivre.do?language=en_GB	
	<br>
	<br>
	Thanks for your patience. 
	<br>
	<br>
	Regards,
	<br> 
	Julien Klepatch
	<br> 
	Homido.Com / VirtualCardboard.com

	<?php
}

/**
 * @since 1.0
 * 
 * Render pagination for shipments, remarks and stock
 *
 * Is called from within a view. Displayed in table header
 * @return (void)
 */
function render_pagination() {

	//we check if the $request_model received in the url is one of the $request_model of the table - If not defaults to 'shipments'
	if( in_array( $GLOBALS['request']['model'], array( 'shipments', 'remarks', 'stock' ) ) ) {
		$request_model = $GLOBALS['request']['model'];
	} else {
		$request_model = 'shipments';
	}

	$next_page_url = LOCAL_APP_URI . $request_model  . '/page/' . $GLOBALS['pagination_params']['next_page'];
	$previous_page_url = LOCAL_APP_URI . $request_model  . '/page/' . $GLOBALS['pagination_params']['previous_page'];;

	?>

	<!-- The previous button is never disabled because we dont have any way to find out how many there is so far, @need-fix -->
	<nav>
	  	<ul class="pager" style="margin-top: 0; margin-bottom: 0;">
	    	<li class="<?php echo $GLOBALS['pagination_params']['disabled']; ?>"><a href="<?php echo $next_page_url; ?>"><span>&larr;</span>Newer</a></li>
	    	<li><a href="<?php echo $previous_page_url; ?>">Older<span>&rarr;</span></a></li>
	  	</ul>
	</nav>

<?php
}