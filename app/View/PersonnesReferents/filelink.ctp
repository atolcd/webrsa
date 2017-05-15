<?php
	$this->pageTitle =  __d( 'personne_referent', "PersonnesReferents::{$this->action}" );

	echo $this->Xhtml->tag( 'h1', $this->pageTitle );
	echo $this->Fileuploader->element( 'PersonneReferent', $fichiers, $personne_referent, $options['haspiecejointe'] );
?>