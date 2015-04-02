<?php
/**
 * @since 1.0
 *
 * Header view
 * 
 * Displays:
 *   - top header with app title
 *   - Remarks, Stock & Data Update - Can be replaced by other important indicators
 */

//We use sessions for displaying feedback message after user actions
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>LOGISTICS APP></title>
	<link rel="stylesheet" type="text/css" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">
</head>
<body>

<div class="page-header text-center">
  	<a href="<?php echo LOCAL_APP_URI; ?>"><h1><span class="glyphicon glyphicon-dashboard">&nbsp;</span>Logistics App</h1></a>
</div>

<div class="container">

	<!-- Flash Message (result of edit / delete action if any -->
	<div class="row">
		<?php if( isset( $_SESSION['message'] ) ) : ?>
				<?php $glyphicon = $_SESSION['message']['type'] === 'success' ? 'glyphicon glyphicon-ok' :'glyphicon glyphicon-remove'; ?>
				<?php echo "<div class='col-sm-12'><div class='alert alert-{$_SESSION['message']['type']}' role='alert'><span class='{$glyphicon}'></span>&nbsp;&nbsp;{$_SESSION['message']['content']}</div></div>"; ?>
				<?php unset( $_SESSION['message'] ); ?>
		<?php endif; ?>
	</div><!--/Flash Message -->

	<!-- Remarks, Stock & Data Update - Can be replaced by other important indicators -->
	<div class="row">
		<!-- Remarks -->
		<div class="col-sm-12 col-md-6 col-lg-5">	
			<div class="panel panel-primary">
				<div class="panel-heading">
			 		<h3 class="panel-title"><span class="glyphicon glyphicon-info-sign"></span>&nbsp;&nbsp;Remarks</h3>
			 	</div>
			 	<!-- <div class="panel-body"> -->
			 	<?php //$rows = get_rows( "SELECT order_id, code_article, remark, date_modified FROM remarks ORDER BY date_modified DESC LIMIT 3;"); ?>
			 	<?php //$rows = $remarks_data; ?>
			 		<ul class="list-group">
			 			<?php foreach( $remarks_data as $row ) : ?>
							<li class="list-group-item">
								<?php printf('<strong>%s</strong> - <small>%s - <em>%s</em></small>', $row['order_id'], $row['code_article'], $row['modified_date'] ); ?>
								<br>
								<?php echo $row['remark']; ?>
						  	</li>
						<?php endforeach; ?>
					</ul>
			 	<!-- </div> -->
			</div>
		</div><!-- /Remarks -->

		<!-- Stock -->
		<div class="col-sm-12 col-md-6 col-lg-5">	
			<div class="panel panel-primary">
				<div class="panel-heading">
			 		<h3 class="panel-title"><span class="glyphicon glyphicon-shopping-cart"></span>&nbsp;&nbsp;Stock</h3>
			 	</div>
			 	<!-- <div class="panel-body"> -->
			 		<ul class="list-group">
						<li class="list-group-item">
						    <span class="badge"><?php echo $stock_data_homido->fetch()['stock']; ?></span>
						    Homidos
						    <br><br>
					  	</li>
					  	<li class="list-group-item">
						    <span class="badge"><?php echo $stock_data_carton->fetch()['stock']; ?></span>
						    Cardboards
						    <br> <br>
					  	</li>
					  	<li class="list-group-item">
						    <span class="badge"><?php echo $stock_data_lentille->fetch()['stock']; ?></span>
						    Lenses
						    <br> <br>
					  	</li>
					</ul>
			 	<!-- </div> -->
			</div>
		</div><!-- /Stock -->

		<!-- Data Update -->
		<div class="col-sm-12 col-md-12 col-lg-2">	
			<div class="panel panel-primary">
				<div class="panel-heading">
			 		<h3 class="panel-title"><span class="glyphicon glyphicon-refresh"></span>&nbsp;&nbsp;Is Up-to-date?</h3>
			 	</div>
			 	<div class="panel-body" style="padding-top: 0; padding-bottom: 0;">
		 			<div class="row">
		 		 		<ul class="list-group" style="margin-bottom: 0;">
		 					<li class="list-group-item">
		 						Shipments <span class= "badge">No</span>
		 				  	</li>
		 			  		<li class="list-group-item">
		 			  			Remarks <span class= "badge">No</span>
		 			  	  	</li>
		 		  	  		<li class="list-group-item">
		 		  	  			Stock <span class= "badge">No</span>
		 		  	  	  	</li>
							<li class="list-group-item">
								<form method="post" action="<?php echo LOCAL_APP_URI . 'all/update'; ?>" class="form-horizontal">
									<!-- <input type="hidden" name="action" value="create"> -->
						      		<button type="submit" name="update" value="Update" class="btn btn-primary btn-md btn-block">Update</button>
	        			 		</form>
							</li>
	        			</ul>
		 			</div><!--/row-->
				</div><!--/panel-body-->
			</div><!--/panel-->
		</div><!-- /Data Update -->
	</div><!-- /.row Remarks, Stock & Data Update -->