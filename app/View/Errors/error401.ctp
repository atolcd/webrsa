<h1><?php echo $this->pageTitle = 'Erreur 401:  Accès à la ressource refusé';?></h1>
<p><?php echo sprintf( "Accès interdit à la page %s.", "<strong>'{$message}'</strong>" );?></p>
<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->element( 'exception_stack_trace' );
	}
?>