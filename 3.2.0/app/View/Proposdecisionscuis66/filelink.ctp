<?php
	$this->pageTitle =  __d( 'propodecisioncui66', "Proposdecisionscuis66::{$this->action}" );

	echo $this->Xhtml->tag( 'h1', $this->pageTitle );
	echo $this->Fileuploader->element( 'Propodecisioncui66', $fichiers, $propodecisioncui66, $options['Propodecisioncui66']['haspiecejointe'] );
?>