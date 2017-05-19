<?php
	echo $this->Xhtml->tag(
		'h1',
		$this->pageTitle = __d( 'courrierpdo', "Courrierspdos::{$this->action}" )
	);

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	echo $this->Default->form(
		array(
			'Courrierpdo.name' => array( 'type' => 'text' ),
			'Courrierpdo.modeleodt' => array( 'type' => 'text' )
		),
		array(
			'actions' => array(
				'courrierspdos::save',
				'courrierspdos::cancel'
			)
		)
	);
?>