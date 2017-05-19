<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	echo $this->Xhtml->tag(
		'h1',
		$this->pageTitle = __d( 'motifsortie', "Motifssortie::{$this->action}" )
	);

	echo $this->Default->form(
		array(
			'Motifsortie.name' => array( 'type' => 'text')
		),
		array(
			'actions' => array(
				'Motifsortie.save',
				'Motifsortie.cancel'
			)
		)
	);
	echo $this->Default->button(
		'back',
		array(
			'controller' => 'motifssortie',
			'action'     => 'index'
		),
		array(
			'id' => 'Back'
		)
	);
?>