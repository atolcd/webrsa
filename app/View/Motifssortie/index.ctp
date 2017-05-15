<?php
	echo $this->Xhtml->tag(
		'h1',
		$this->pageTitle = __d( 'motifsortie', "Motifssortie::{$this->action}" )
	);

	echo $this->Default2->index(
		$motifssortie,
		array(
			'Motifsortie.name',
		),
		array(
			'cohorte' => false,
			'actions' => array(
				'Motifssortie::edit',
				'Motifssortie::delete' => array( 'disabled' => '\'#Motifsortie.occurences#\' != "0"' )
			),
			'add' => 'Motifssortie::add'
		)
	);

	echo $this->Default->button(
		'back',
		array(
			'controller' => 'actionscandidats_personnes',
			'action'     => 'indexparams'
		),
		array(
			'id' => 'Back'
		)
	);
?>