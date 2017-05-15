<?php
	$this->pageTitle =  __d( 'orientstruct', "Orientsstructs::{$this->action}" );

	echo $this->Xhtml->tag( 'h1', $this->pageTitle );
	echo $this->Fileuploader->element( 'Orientstruct', $fichiers, $orientstruct, $options['Orientstruct']['haspiecejointe'] );
?>