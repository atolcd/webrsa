<?php
	$this->pageTitle =  __d( 'entretien', "Entretiens::{$this->action}" );

	echo $this->Xhtml->tag( 'h1', $this->pageTitle );
	echo $this->Fileuploader->element( 'Entretien', $fichiers, $entretien, $options['Entretien']['haspiecejointe'] );
?>