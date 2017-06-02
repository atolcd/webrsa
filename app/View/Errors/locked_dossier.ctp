<?php
	if( Configure::read( 'Cg.departement' ) == 93 ) {
		$title_for_layout = 'Dossier verrouillé';
		$message = sprintf( "Le dossier RSA n°%s est en cours de modification par un autre utilisateur. Veuillez ré-essayer ultérieurement.", '<strong>'.$error->params['dossier']['Dossier']['numdemrsa'].'</strong>' );
	}
	else {
		$title_for_layout = 'Erreur 401:  Accès au dossier refusé';
		$message = sprintf( "Ce dossier a été bloqué en modification par %s jusqu'au %s.", '<strong>'.$error->params['user'].'</strong>', '<strong>'.strftime( '%d/%m/%Y à %H:%M:%S', $error->params['time'] ).'</strong>' );
	}

	$this->set( compact( 'title_for_layout' ) );
?>

<h1><?php echo $title_for_layout;?></h1>
<br/>
<p><?php echo $message;?></p>

<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->element( 'exception_stack_trace' );
	}
?>