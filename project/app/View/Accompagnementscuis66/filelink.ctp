<?php
	$this->pageTitle =  __d( 'accompagnementscuis66', "Accompagnementscuis66::{$this->action}" );

	echo $this->Xhtml->tag( 'h1', $this->pageTitle );
	echo $this->Fileuploader->element( 'Accompagnementcui66', $fichiers, $record, $options['Accompagnementcui66']['haspiecejointe'] );

	echo $this->Observer->disableFormOnSubmit( 'accompagnementcui66form' );
?>