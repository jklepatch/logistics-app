<?php
/**
 * @since 1.0
 *
 * 404 Error view
 * 
 * Renders an error page for bad requests, and show a link to home page 
 */
?>

<div class="row">
	<div class="col-sm-12">
			<h1>Error 404 - OOps ... the requested object <strong style="color: red;"><?php echo htmlspecialchars( $GLOBALS['request']['model'] ); ?></strong> does not exist</h1>
			<p class="lead">Would you like to go back to the <a href="<?php echo LOCAL_APP_URI; ?>" title="Home Page">Home Page?</a></p>
	</div><!--/col-sm-12-->
</div><!--/row -->