<?php
	$this->pageTitle =  __d( 'recoursgracieux', "Recoursgracieux::{$this->action}" );

	echo $this->Xhtml->tag( 'h1', $this->pageTitle );
	echo $this->Fileuploader->element( 'Recourgracieux', $fichiers, $recoursgracieux, $options['Recourgracieux']['haspiecejointe'] );
?>