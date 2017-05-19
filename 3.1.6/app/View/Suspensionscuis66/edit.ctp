<?php
	echo $this->FormValidator->generateJavascript();
	echo $this->Default3->titleForLayout();

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
		echo $this->Html->script( array( 'prototype.event.simulate.js', 'dependantselect.js' ), array( 'inline' => false ) );
	}
		
	echo $this->Default3->DefaultForm->create( null, array( 'novalidate' => 'novalidate', 'id' => 'Suspensioncui66AddEditForm', 'class' => 'Cui66AddEdit' ) );

/***********************************************************************************
 * Formulaire Suspension
/***********************************************************************************/
	
	// Ajoute un motif au select si le motif stocké en base n'est plus actif
	$id_motif = !empty( $this->request->data['Suspensioncui66']['motif'] ) ? $this->request->data['Suspensioncui66']['motif'] : null;
	if ( $id_motif !== null && !isset( $options['Suspensioncui66']['motif_actif'][$id_motif] ) ){
		$options['Suspensioncui66']['motif_actif'][$id_motif] = $options['Suspensioncui66']['motif'][$id_motif];
	}
	
	echo '<fieldset><legend>' . __d('suspensionscuis66', 'Suspensioncui66.formulaire') . '</legend>'
		. $this->Default3->subform(
			array(
				'Suspensioncui66.id' => array( 'type' => 'hidden' ),
				'Suspensioncui66.cui66_id' => array( 'type' => 'hidden' ),
				'Suspensioncui66.observation',
				'Suspensioncui66.duree',
				'Suspensioncui66.datedebut' => array( 'dateFormat' => 'DMY', 'minYear' => '2009', 'maxYear' => date('Y')+1 ),
				'Suspensioncui66.datefin' => array( 'dateFormat' => 'DMY', 'minYear' => '2009', 'maxYear' => date('Y')+1 ),
				'Suspensioncui66.motif' => array( 'type' => 'select', 'options' => $options['Suspensioncui66']['motif_actif'] ),
			) ,
			array( 'options' => $options )
		)
		. '</fieldset>'
	;

	echo $this->Default3->DefaultForm->buttons( array( 'Save', 'Cancel' ) );
	echo $this->Default3->DefaultForm->end();
	echo $this->Observer->disableFormOnSubmit( 'Suspensioncui66AddEditForm' );
	