<?php
	$this->pageTitle =  __d( 'decisiondossierpcg66', "Decisionsdossierspcgs66::{$this->action}" );

	echo $this->Xhtml->tag( 'h1', $this->pageTitle );
	echo $this->Fileuploader->element( 'Decisiondossierpcg66', $fichiers, $decisiondossierpcg66, $options['Decisiondossierpcg66']['haspiecejointe'] );
?>