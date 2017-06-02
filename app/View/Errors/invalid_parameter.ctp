<h1><?php echo $this->pageTitle = __( 'Invalid Parameter' ); ?></h1>

<p class="error">
	<strong><?php echo __('Error'); ?>: </strong>
	<?php echo sprintf(
		__( 'Invalid parameter for %1$s%2$s in %3$s line %4$s. URL was %5$s.' ),
		"<em>". $controller."Controller::</em>",
		"<em>". $action ."()</em>",
		"<em>". $file ."</em>",
		"<em>". $line ."</em>",
		"<em>". $url ."</em>"
	);?>
</p>

<p class="notice">
	<strong><?php echo __('Notice'); ?>: </strong>
	<?php echo sprintf(__( 'If you want to customize this error message, edit %s' ), APP_DIR.DS."views".DS."errors".DS."invalid_parameter.ctp");?>
</p>
<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->element( 'exception_stack_trace' );
	}
?>