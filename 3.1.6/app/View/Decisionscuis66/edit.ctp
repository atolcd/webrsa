<?php
	echo $this->FormValidator->generateJavascript();
	echo $this->Default3->titleForLayout();

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
		echo $this->Html->script( array( 'prototype.event.simulate.js', 'dependantselect.js' ), array( 'inline' => false ) );
	}
	
	echo $this->Default3->DefaultForm->create( null, array( 'novalidate' => 'novalidate', 'id' => 'Decisioncui66AddEditForm', 'class' => 'Cui66AddEdit' ) );

/***********************************************************************************
 * Formulaire Décision
/***********************************************************************************/
	
	// Ajoute un motif au select si le motif stocké en base n'est plus actif
	$id_motif = !empty( $this->request->data['Decisioncui66']['motif'] ) ? $this->request->data['Decisioncui66']['motif'] : null;
	if ( $id_motif !== null && !isset( $options['Decisioncui66']['motif_actif'][$id_motif] ) ){
		$options['Decisioncui66']['motif_actif'][$id_motif] = $options['Decisioncui66']['motif'][$id_motif];
	}
	
	echo '<fieldset><legend>' . __d('propositionscuis66', 'Propositioncui66.formulaire') . '</legend>'
		. $this->Default3->index(
			$results,
			array(
				'Propositioncui66.donneuravis',
				'Propositioncui66.dateproposition',
				'Propositioncui66.avis',
				'Propositioncui66.motif' => array( 'type' => 'select' ),
				'Propositioncui66.observation',
			),
			array(
				'options' => $options,
				'paginate' => false,
				'domain' => 'propositionscuis66'
			)
		) . '</fieldset>'
	;
	
	echo '<fieldset><legend>' . __d('decisionscuis66', 'Decisioncui66.formulaire') . '</legend>'
		. $this->Default3->subform(
			array(
				'Decisioncui66.id' => array( 'type' => 'hidden' ),
				'Decisioncui66.cui66_id' => array( 'type' => 'hidden' ),
				'Decisioncui66.decision',
				'Decisioncui66.motif' => array( 'empty' => true, 'options' => $options['Decisioncui66']['motif_actif'] ),
				'Decisioncui66.datedecision' => array( 'dateFormat' => 'DMY', 'timeFormat' => 24, 'minYear' => 2009, 'maxYear' => date('Y')+1 ),
				'Decisioncui66.observation',
			) ,
			array( 'options' => $options )
		)
		. '</fieldset>'
	;

	echo $this->Default3->DefaultForm->buttons( array( 'Save', 'Cancel' ) );
	echo $this->Default3->DefaultForm->end();
	echo $this->Observer->disableFormOnSubmit( 'Decisioncui66AddEditForm' );
	
	echo $this->Observer->disableFieldsOnValue(
		'Decisioncui66.decision',
		array( 'Decisioncui66.motif' ),
		array( 'accord' ),
		true,
		true
	);