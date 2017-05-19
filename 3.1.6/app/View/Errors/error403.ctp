<h1><?php echo $this->pageTitle = 'Erreur 403: accès interdit';?></h1>
<?php $message = ( isset( $message ) ? $message : $this->request->here ); ?>
<p><?php echo sprintf( "Accès interdit à la page %s.", "<strong>'{$message}'</strong>" );?></p>
<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->element( 'exception_stack_trace' );
	}
?>