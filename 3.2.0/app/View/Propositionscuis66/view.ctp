<?php
	echo $this->Default3->titleForLayout();

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
		echo $this->Html->script( array( 'prototype.event.simulate.js', 'dependantselect.js' ), array( 'inline' => false ) );
	}
	
	echo '<div class="Cui66AddEdit">';

/***********************************************************************************
 * Voir Proposition
/***********************************************************************************/
	
	echo '<fieldset><legend>' . __d('propositionscuis66', 'Propositioncui66.formulaire') . '</legend>'
		. $this->Default3->subformView(
			array(
				'Propositioncui66.donneuravis',
				'Propositioncui66.dateproposition' => array( 'type' => 'date', 'dateFormat' => 'DMY' ),
				'Propositioncui66.observation',
				'Propositioncui66.avis',
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