<?php
	echo $this->FormValidator->generateJavascript();
	echo $this->Default3->titleForLayout();

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
		echo $this->Html->script( array( 'prototype.event.simulate.js', 'dependantselect.js' ), array( 'inline' => false ) );
	}
		
	echo $this->Default3->DefaultForm->create( null, array( 'novalidate' => 'novalidate', 'id' => 'Accompagnementcui66AddEditForm', 'class' => 'Cui66AddEdit' ) );

/***********************************************************************************
 * Formulaire Accompagnement
/***********************************************************************************/
	
	echo '<fieldset><legend>' . __d('accompagnementscuis66', 'Accompagnementcui66.formulaire') . '</legend>'
		. $this->Default3->subform(
			array(
				'Accompagnementcui66.id' => array( 'type' => 'hidden' ),
				'Accompagnementcui66.cui66_id' => array( 'type' => 'hidden' ),
				'Accompagnementcui66.immersioncui66_id' => array( 'type' => 'hidden' ),
				'Accompagnementcui66.genre' => array( 'empty' => true ),			
				'Accompagnementcui66.organismesuivi',
				'Accompagnementcui66.nomredacteur',
				'Accompagnementcui66.observation',
				'Accompagnementcui66.datededebut' => array( 'dateFormat' => 'DMY', 'minYear' => '2009', 'maxYear' => date('Y')+1 ),
				'Accompagnementcui66.datedefin' => array( 'dateFormat' => 'DMY', 'minYear' => '2009', 'maxYear' => date('Y')+1 ),
				'Accompagnementcui66.datedesignature' => array( 'dateFormat' => 'DMY', 'minYear' => '2009', 'maxYear' => date('Y')+1 ),
			) ,
			array( 'options' => $options )
		)
		. '</fieldset>'
	;
	
	echo '<fieldset id="Immersioncui66Fieldset"><legend>' . __d('accompagnementscuis66', 'Accompagnementcui66.entreprise') . '</legend>'
		. $this->Default3->subform(
			array(
				'Immersioncui66.nomentreprise',
				'Immersioncui66.numvoie',
				'Immersioncui66.typevoie',
				'Immersioncui66.nomvoie',
				'Immersioncui66.complementadresse',
				'Immersioncui66.codepostal',
				'Immersioncui66.commune',
				'Immersioncui66.activiteprincipale',
				'Immersioncui66.objectifprincipal',
			) ,
			array( 'options' => $options )
		)
		. $this->Romev3->fieldset( 'Immersionromev3', array( 'options' => $options ) )
		. '</fieldset>'
	;

	echo $this->Default3->DefaultForm->buttons( array( 'Save', 'Cancel' ) );
	echo $this->Default3->DefaultForm->end();
	echo $this->Observer->disableFormOnSubmit( 'Accompagnementcui66AddEditForm' );
	
	echo $this->Observer->disableFieldsetOnValue(
		'Accompagnementcui66.genre',
		'Immersioncui66Fieldset',
		'immersion',
		false,
		true
	);