<?php
/**
 * @since 1.0
 *
 * List view in main section below header
 * 
 * Display the main view of the app, which is tabbed table showing 3 sub-views:
 *   - shipments (list of shipments)
 *   - remarks (problem with shipment)
 *   - stock
 *
 * Additionally, it is possible to search a shipment with the right button (just work for shipments)
 */
?>

<div class="row">
	<div class="col-sm-12">

			<div class="panel panel-default">
			 	<div class="panel-heading">
    				<div class="row">

    					<!--Button to switch between different subviews-->
    					<div class="col-lg-4 visible-lg">
    						<div class="btn-group" role="group">
							  	<a class="btn btn-default <?php echo is_active( 'shipments', $GLOBALS['request']['model'] ); ?>" href="<?php echo LOCAL_APP_URI . 'shipments/' ?>"><span class="glyphicon glyphicon-send"></span>&nbsp;&nbsp;Shipments</a>
							  	<a class="btn btn-default <?php echo is_active( 'remarks', $GLOBALS['request']['model'] ); ?>" href="<?php echo LOCAL_APP_URI . 'remarks/'; ?>"><span class="glyphicon glyphicon-info-sign"></span>&nbsp;&nbsp;Remarks</a>
							  	<a class="btn btn-default <?php echo is_active( 'stock', $GLOBALS['request']['model'] ); ?>" href="<?php echo LOCAL_APP_URI . 'stock/'; ?>"><span class="glyphicon glyphicon-shopping-cart"></span>&nbsp;&nbsp;Stock</a>
							</div>
    					</div>
    					
						<!--same button has before but just visible on smaller screen (attribute btn-block make them take all the horizontal space)-->
    					<div class="col-sm-6 col-md-4 hidden-lg">
    						<div class="btn-group" role="group">
							  	<a class="btn btn-default btn-block <?php echo is_active( 'shipments', $GLOBALS['request']['model'] ); ?>" href="<?php echo LOCAL_APP_URI . 'shipments/' ?>"><span class="glyphicon glyphicon-send"></span>&nbsp;&nbsp;Shipments</a>
							  	<a class="btn btn-default btn-block <?php echo is_active( 'remarks', $GLOBALS['request']['model'] ); ?>" href="<?php echo LOCAL_APP_URI . 'remarks/'; ?>"><span class="glyphicon glyphicon-info-sign"></span>&nbsp;&nbsp;Remarks</a>
							  	<a class="btn btn-default btn-block <?php echo is_active( 'stock', $GLOBALS['request']['model'] ); ?>" href="<?php echo LOCAL_APP_URI . 'stock/'; ?>"><span class="glyphicon glyphicon-shopping-cart"></span>&nbsp;&nbsp;Stock</a>
							</div>
    					</div>
    					<div class="visible-xs" style="margin-bottom: 30px;"></div>

    					<!--Pagination-->
    					<div class="col-sm-6 col-md-3 text-right">
    						<?php render_pagination(); ?>
    						<div class="hidden-md hidden-lg" style="margin-bottom: 30px;"></div>
    					</div>

						<!--Search form for shipments sub-view-->
						<form class="form-inline" role="search" action="<?php echo LOCAL_APP_URI .'shipments/search/'; ?>" method ="get">
							<div class="col-sm-3 col-md-3 col-lg-3" style="text-align:right;">
								<div class="form-group">
									<input type="text" class="form-control" name ="search_value" placeholder="Search Order #">
								</div>
							</div>
							<div class="col-sm-3 col-md-2 col-lg-2" style="text-align:right;">
								<button type="submit" class="btn btn-default btn-block">Submit</button>
							</div>
						</form>

    				</div><!-- /row -->
  				</div><!--/panel heading -->
				
				<!-- Render the tabular data here (shipments, remarks or stock)-->
	  			<div class="panel-body">
	  				<?php 
	  				if( isset( $shipments_schema ) && isset( $shipments_data ) ) {
	  					render_table( $shipments_schema, $shipments_data );

	  				} elseif(  isset( $remarks_schema ) && isset( $remarks_data ) ) {
	  					render_table( $remarks_schema, $remarks_data );

	  				} elseif( isset( $stock_schema ) && isset( $stock_data ) ) {
	  					render_table( $stock_schema, $stock_data );
	  				} 
					?>
				 </div><!--panel body-->
			</div><!--panel-->

	</div><!--/col-sm-12-->
</div><!--/row -->