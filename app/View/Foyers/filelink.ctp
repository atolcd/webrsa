<?php
	$this->pageTitle =  __d( 'foyer', "Foyers::{$this->action}", true );

	echo $this->Xhtml->tag( 'h1', $this->pageTitle );
	echo $this->Fileuploader->element( 'Foyer', $fichiers, $foyer, $options['Foyer']['haspiecejointe'] );
?>