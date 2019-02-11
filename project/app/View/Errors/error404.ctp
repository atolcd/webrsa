<?php $this->pageTitle = 'Erreur 404: page non trouvée';?>

<h1>Erreur 404: page non trouvée</h1>
<?php $message = ( isset( $message ) ? $message : $this->request->here ); ?>
<p><?php echo sprintf( "La page %s n'existe pas.", "<strong>'{$message}'</strong>" );?></p>
<!--<p>Page possible: <a class="parent" href="/">Blog</a></p>-->
<p>Rendez-vous à l'<?php echo $this->Xhtml->link( 'accueil', '/' );?><!-- ou au <a href="/sitemap.php">plan du site-->.</a>
<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->element( 'exception_stack_trace' );
	}
?>