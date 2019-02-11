<?php
	$this->pageTitle =  __d( 'creances', "Creances::{$this->action}" );

	echo $this->Xhtml->tag( 'h1', $this->pageTitle );
	echo $this->Fileuploader->element( 'Creances', $fichiers, $creances, $options['haspiecejointe'] );

?>