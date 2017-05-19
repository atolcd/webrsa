<?php
	echo $this->FormValidator->generateJavascript();
	echo $this->Default3->titleForLayout();

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
		echo $this->Html->script( array( 'prototype.event.simulate.js', 'dependantselect.js' ), array( 'inline' => false ) );
	}
		
	echo $this->Default3->DefaultForm->create( null, array( 'novalidate' => 'novalidate', 'id' => 'Rupturecui66AddEditForm', 'class' => 'Cui66AddEdit' ) );

/***********************************************************************************
 * Formulaire Rupture
/***********************************************************************************/
	
	// Ajoute un motif au select si le motif stocké en base n'est plus actif
	$id_motif = !empty( $this->request->data['Rupturecui66']['motif'] ) ? $this->request->data['Rupturecui66']['motif'] : null;
	if ( $id_motif !== null && !isset( $options['Rupturecui66']['motif_actif'][$id_motif] ) ){
		$options['Rupturecui66']['motif_actif'][$id_motif] = $options['Rupturecui66']['motif'][$id_motif];
	}
	
	echo '<fieldset><legend>' . __d('rupturescuis66', 'Rupturecui66.formulaire') . '</legend>'
		. $this->Default3->subform(
			array(
				'Rupturecui66.id' => array( 'type' => 'hidden' ),
				'Rupturecui66.cui66_id' => array( 'type' => 'hidden' ),
				'Rupturecui66.observation',
				'Rupturecui66.daterupture' => array( 'dateFormat' => 'DMY', 'minYear' => '2009', 'maxYear' => date('Y')+1 ),
				'Rupturecui66.motif' => array( 'type' => 'select', 'options' => $options['Rupturecui66']['motif_actif'] ),
				'Rupturecui66.dateenregistrement' => array( 'view' => true, 'hidden' => true, 'type' => 'date' )
			) ,
			array( 'options' => $options )
		)
		. '</fieldset>'
	;

	echo $this->Default3->DefaultForm->buttons( array( 'Save', 'Cancel' ) );
	echo $this->Default3->DefaultForm->end();
	echo $this->Observer->disableFormOnSubmit( 'Rupturecui66AddEditForm' );
	