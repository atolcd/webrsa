<?php
	$this->pageTitle =  __d( 'personne', "Personnes::{$this->action}" );

	echo $this->Xhtml->tag( 'h1', $this->pageTitle );
	echo $this->Fileuploader->element( 'Personne', $fichiers, $personne, $options['haspiecejointe'] );
?>