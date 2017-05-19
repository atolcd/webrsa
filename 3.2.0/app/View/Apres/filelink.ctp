<?php
	$this->modelClass = Inflector::classify( $this->request->params['controller'] );
	$this->pageTitle =  __d( 'apre', "Apres::{$this->action}" );

	echo $this->Xhtml->tag( 'h1', $this->pageTitle );
	echo $this->Fileuploader->element( $this->modelClass, $fichiers, $apre, $options['haspiecejointe'] );
?>