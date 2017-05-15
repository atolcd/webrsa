<?php
	$this->pageTitle =  __d( 'rupturescuis66', "Rupturescuis66::{$this->action}" );

	echo $this->Xhtml->tag( 'h1', $this->pageTitle );
	echo $this->Fileuploader->element( 'Rupturecui66', $fichiers, $record, $options['Rupturecui66']['haspiecejointe'] );

	echo $this->Observer->disableFormOnSubmit( 'rupturecui6666form' );
?>