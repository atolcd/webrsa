<?php
	$this->pageTitle =  __d( 'decisionscuis66', "Decisionscuis66::{$this->action}" );

	echo $this->Xhtml->tag( 'h1', $this->pageTitle );
	echo $this->Fileuploader->element( 'Decisioncui66', $fichiers, $record, $options['Decisioncui66']['haspiecejointe'] );

	echo $this->Observer->disableFormOnSubmit( 'decisioncui66form' );
?>