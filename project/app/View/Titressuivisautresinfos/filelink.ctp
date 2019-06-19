<?php
	$this->pageTitle =  __d( 'titressuivisautresinfos', "Titressuivisautresinfos::{$this->action}" );

	echo $this->Xhtml->tag( 'h1', $this->pageTitle );
	echo $this->Fileuploader->element(
		'Titresuiviautreinfo',
		$fichiers,
		$titresAutreInfo,
		$options['Titresuiviautreinfo']['haspiecejointe']
	);