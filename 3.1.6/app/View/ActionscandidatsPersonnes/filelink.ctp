<?php
	$this->pageTitle =  __d( 'actioncandidat_personne', "ActionscandidatsPersonnes::{$this->action}" );

	echo $this->Xhtml->tag( 'h1', $this->pageTitle );
	echo $this->Fileuploader->element( 'ActioncandidatPersonne', $fichiers, $actioncandidat_personne, $options['ActioncandidatPersonne']['haspiecejointe'] );
?>