<?php
	echo $this->Default3->titleForLayout();

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
		echo $this->Html->script( array( 'prototype.event.simulate.js', 'dependantselect.js' ), array( 'inline' => false ) );
	}

	echo $this->Default3->form(
		array(
			'Ficheprescription93.date_annulation' => array( 'empty' => true, 'dateFormat' => 'DMY', 'maxYear' => date( 'Y' ) + 1 ),
			'Ficheprescription93.motif_annulation',
		)
	);

	echo $this->Observer->disableFormOnSubmit();
?>