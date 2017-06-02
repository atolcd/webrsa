<?php
	$this->pageTitle =  __d( 'propositionscuis66', "Propositionscuis66::{$this->action}" );

	echo $this->Xhtml->tag( 'h1', $this->pageTitle );
	echo $this->Fileuploader->element( 'Propositioncui66', $fichiers, $record, $options['Propositioncui66']['haspiecejointe'] );

	echo $this->Observer->disableFormOnSubmit( 'propositioncui66form' );
?>