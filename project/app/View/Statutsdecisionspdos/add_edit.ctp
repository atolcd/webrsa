<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	echo $this->Xhtml->tag(
		'h1',
		$this->pageTitle = __d( 'statutdecisionpdo', "Statutsdecisionspdos::{$this->action}" )
	)
?>

<?php
	echo $this->Default->form(
		array(
			'Statutdecisionpdo.libelle' => array( 'type' => 'text' )
		),
		array(
			'actions' => array(
				'Statutdecisionpdo.save',
				'Statutdecisionpdo.cancel'
			)
		)
	);

	echo $this->Default->button(
		'back',
		array(
			'controller' => 'statutsdecisionspdos',
			'action'     => 'index'
		),
		array(
			'id' => 'Back'
		)
	);
?>