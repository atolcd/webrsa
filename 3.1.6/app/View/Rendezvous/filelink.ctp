<?php
	$this->pageTitle =  __d( 'rendezvous', "Rendezvous::{$this->action}" );

	echo $this->Xhtml->tag( 'h1', $this->pageTitle );
	echo $this->Fileuploader->element( 'Rendezvous', $fichiers, $rendezvous, $options['haspiecejointe'] );
?>