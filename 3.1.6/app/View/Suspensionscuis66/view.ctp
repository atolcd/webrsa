<?php
	echo $this->Default3->titleForLayout();

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
		echo $this->Html->script( array( 'prototype.event.simulate.js', 'dependantselect.js' ), array( 'inline' => false ) );
	}
	
	echo '<div class="Cui66AddEdit">';

/***********************************************************************************
 * Voir Suspension
/***********************************************************************************/
	
	echo '<fieldset><legend>' . __d('suspensionscuis66', 'Suspensioncui66.formulaire') . '</legend>'
		. $this->Default3->subformView(
			array(
				'Suspensioncui66.observation' => array ( 'type' => 'textarea' ),
				'Suspensioncui66.duree',
				'Suspensioncui66.datedebut' => array( 'type' => 'date', 'dateFormat' => 'DMY' ),
				'Suspensioncui66.datefin' => array( 'type' => 'date', 'dateFormat' => 'DMY' ),
				'Suspensioncui66.motif',
			) ,
			array( 'options' => $options )
		)
		. '</fieldset>'
	;

	echo '<br />' . $this->Default->button(
		'back',
		array(
			'controller' => 'propositionscuis66',
			'action'     => 'index',
			$cui_id
		),
		array(
			'id' => 'Back',
			'class' => 'aere'
		)
	);
	
	echo '</div>';