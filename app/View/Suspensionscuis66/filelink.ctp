<?php
	$this->pageTitle =  __d( 'suspensionscuis66', "Suspensionscuis66::{$this->action}" );

	echo $this->Xhtml->tag( 'h1', $this->pageTitle );
	echo $this->Fileuploader->element( 'Suspensioncui66', $fichiers, $record, $options['Suspensioncui66']['haspiecejointe'] );

	echo $this->Observer->disableFormOnSubmit( 'suspensioncui66form' );
?>