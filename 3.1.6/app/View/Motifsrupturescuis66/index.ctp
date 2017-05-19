<?php
	echo $this->Xhtml->tag(
		'h1',
		$this->pageTitle = __d( 'motifrupturecui66', "Motifsrupturescuis66::{$this->action}" )
	);

	echo $this->Default2->index(
		$motifsrupturescuis66,
		array(
			'Motifrupturecui66.name',
			'Motifrupturecui66.actif' => array( 'type' => 'boolean' ),
		),
		array(
			'cohorte' => false,
			'actions' => array(
				'Motifsrupturescuis66::edit',
				'Motifsrupturescuis66::delete' => array( 'disabled' => '\'#Motifrupturecui66.occurences#\'!= "0"' )
			),
			'add' => 'Motifsrupturescuis66::add'
		)
	);

	echo $this->Default->button(
		'back',
		array(
			'controller' => 'cuis',
			'action'     => 'indexparams'
		),
		array(
			'id' => 'Back'
		)
	);
?>