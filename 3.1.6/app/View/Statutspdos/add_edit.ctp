<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	echo $this->Xhtml->tag(
		'h1',
		$this->pageTitle = __d( 'statutpdo', "Statutspdos::{$this->action}" )
	)
?>
<?php
	echo $this->Default->form(
		array(
			'Statutpdo.libelle' => array( 'type' => 'text' ),
			'Statutpdo.isactif' => array( 'type' => 'radio', 'empty' => false )
		),
		array(
			'actions' => array(
				'Statutpdo.save',
				'Statutpdo.cancel'
			),
            'options' => $options
		)
	);
?>
<?php
	echo $this->Default->button(
		'back',
		array(
			'controller' => 'statutspdos',
			'action'     => 'index'
		),
		array(
			'id' => 'Back'
		)
	);
?>
