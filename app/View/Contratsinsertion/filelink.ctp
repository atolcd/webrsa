<?php
	$this->pageTitle =  __d( 'contratinsertion', "Contratsinsertion::{$this->action}" );

	echo $this->Xhtml->tag( 'h1', $this->pageTitle );
	echo $this->Fileuploader->element( 'Contratinsertion', $fichiers, $contratinsertion, $options['Contratinsertion']['haspiecejointe'] );
?>