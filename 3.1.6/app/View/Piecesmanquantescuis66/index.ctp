<?php
	echo $this->Xhtml->tag(
		'h1',
		$this->pageTitle = __d( 'piecesmanquantescuis66', "Piecesmanquantescuis66::{$this->action}" )
	);

	echo $this->Default2->index(
		$piecesmanquantescuis66,
		array(
			'Piecemanquantecui66.name' => array( 'label' => __d( 'piecesmanquantescuis66', 'Piecemanquantecui66.name' )),
			'Piecemanquantecui66.actif' => array( 'type' => 'boolean' ),
		),
		array(
			'cohorte' => false,
			'actions' => array(
				'Piecesmanquantescuis66::edit',
				'Piecesmanquantescuis66::delete' => array( 'disabled' => '\'#Piecemanquantecui66.occurences#\'!= "0"' )
			),
			'add' => 'Piecesmanquantescuis66::add'
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