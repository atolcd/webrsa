<?php
	echo $this->FormValidator->generateJavascript();
	echo $this->Default3->titleForLayout();

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
		echo $this->Html->script( array( 'prototype.event.simulate.js', 'dependantselect.js' ), array( 'inline' => false ) );
	}
		
	echo $this->Default3->DefaultForm->create( null, array( 'novalidate' => 'novalidate', 'id' => 'Propositioncui66AddEditForm', 'class' => 'Cui66AddEdit' ) );

/***********************************************************************************
 * Formulaire Proposition
/***********************************************************************************/
	
	echo '<fieldset><legend>' . __d('propositionscuis66', 'Propositioncui66.formulaire') . '</legend>'
		. $this->Default3->subform(
			array(
				'Propositioncui66.id' => array( 'type' => 'hidden' ),
				'Propositioncui66.cui66_id' => array( 'type' => 'hidden' ),
				'Propositioncui66.donneuravis',
				'Propositioncui66.dateproposition' => array( 'dateFormat' => 'DMY', 'minYear' => '2009', 'maxYear' => date('Y')+1 ),
				'Propositioncui66.observation',
				'Propositioncui66.avis',
				'Propositioncui66.motif',
			) ,
			array( 'options' => $options )
		)
		. '</fieldset>'
	;

	echo $this->Default3->DefaultForm->buttons( array( 'Save', 'Cancel' ) );
	echo $this->Default3->DefaultForm->end();
	echo $this->Observer->disableFormOnSubmit( 'Propositioncui66AddEditForm' );
	
	echo $this->Observer->disableFieldsOnValue(
		'Propositioncui66.avis',
		array( 'Propositioncui66.motif' ),
		array( 'attentedecision', 'accord' ),
		true,
		true
	);