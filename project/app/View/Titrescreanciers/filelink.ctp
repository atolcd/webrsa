<?php
	$this->pageTitle =  __d( 'titrescreanciers', "Titrescreanciers::{$this->action}" );

	echo $this->Xhtml->tag( 'h1', $this->pageTitle );
	echo $this->Fileuploader->element( 'Titrescreanciers', $fichiers, $titrescreanciers, $options['haspiecejointe'] );
?>