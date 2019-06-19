<?php
	$this->pageTitle =  __d( 'titressuivisannulationsreductions', "Titressuivisannulationsreductions::{$this->action}" );

	echo $this->Xhtml->tag( 'h1', $this->pageTitle );
	echo $this->Fileuploader->element(
		'Titresuiviannulationreduction',
		$fichiers,
		$titresAnnulationReduction,
		$options['Titresuiviannulationreduction']['haspiecejointe']
	);
