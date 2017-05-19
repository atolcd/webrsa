<?php
	echo $this->Default3->titleForLayout();

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
	}

	echo $this->Default3->form(
		array(
			'Sortieaccompagnementd2pdv93.id' => array( 'type' => 'hidden' ),
			'Sortieaccompagnementd2pdv93.name',
			'Sortieaccompagnementd2pdv93.parent_id' => array(
				'type' => 'select',
				'empty' => true
			),
		),
		array(
			'options' => $options,
			'buttons' => array( 'Save', 'Cancel' )
		)
	);
?>