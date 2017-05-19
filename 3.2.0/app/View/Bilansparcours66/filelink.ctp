<?php
	$this->pageTitle =  __d( 'bilanparcours66', "Bilansparcours66::{$this->action}" );

	echo $this->Xhtml->tag( 'h1', $this->pageTitle );
	echo $this->Fileuploader->element( 'Bilanparcours66', $fichiers, $bilanparcours66, $options['Bilanparcours66']['haspiecejointe'] );
?>