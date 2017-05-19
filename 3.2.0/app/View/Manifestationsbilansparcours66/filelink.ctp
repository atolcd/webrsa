<?php
	$this->pageTitle =  __d( 'manifestationbilanparcours66', "Manifestationsbilansparcours66::{$this->action}" );

	echo $this->Xhtml->tag( 'h1', $this->pageTitle );
	echo $this->Fileuploader->element( 'Manifestationbilanparcours66', $fichiers, $manifestationbilanparcours66, $options['Manifestationbilanparcours66']['haspiecejointe'] );
?>