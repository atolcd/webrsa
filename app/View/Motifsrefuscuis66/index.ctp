<?php
	echo $this->Xhtml->tag(
		'h1',
		$this->pageTitle = __d( 'motifrefuscui66', "Motifsrefuscuis66::{$this->action}" )
	);

	echo $this->Default2->index(
		$motifsrefuscuis66,
		array(
			'Motifrefuscui66.name',
			'Motifrefuscui66.actif' => array( 'type' => 'boolean' )
		),
		array(
			'cohorte' => false,
			'actions' => array(
				'Motifsrefuscuis66::edit',
				'Motifsrefuscuis66::delete' => array( 'disabled' => '\'#Motifrefuscui66.occurences#\'!= "0"' )
			),
			'add' => 'Motifsrefuscuis66::add'
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