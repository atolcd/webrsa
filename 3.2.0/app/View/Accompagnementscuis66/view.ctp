<?php
	echo $this->Default3->titleForLayout();

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
		echo $this->Html->script( array( 'prototype.event.simulate.js', 'dependantselect.js' ), array( 'inline' => false ) );
	}
	
	echo '<div class="Cui66AddEdit">';

/***********************************************************************************
 * Voir accompagnement
/***********************************************************************************/
	
	echo '<fieldset><legend>' . __d('accompagnementscuis66', 'Accompagnementcui66.formulaire') . '</legend>'
		. $this->Default3->subformView(
			array(
				'Accompagnementcui66.genre' => array( 'hidden' => true ),
				'Accompagnementcui66.genre',			
				'Accompagnementcui66.organismesuivi',
				'Accompagnementcui66.nomredacteur',
				'Accompagnementcui66.observation' => array ( 'type' => 'textarea' ),
				'Accompagnementcui66.datededebut' => array( 'type' => 'date', 'dateFormat' => 'DMY' ),
				'Accompagnementcui66.datedefin' => array( 'type' => 'date', 'dateFormat' => 'DMY' ),
				'Accompagnementcui66.datedesignature' => array( 'type' => 'date', 'dateFormat' => 'DMY' ),
			) ,
			array( 'options' => $options )
		)
		. '</fieldset>'
	;
	
	echo '<fieldset id="Immersioncui66Fieldset"><legend>' . __d('accompagnementscuis66', 'Accompagnementcui66.entreprise') . '</legend>'
		. $this->Default3->subformView(
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
		. $this->Romev3->fieldsetView( 'Immersionromev3', $this->request->data, array( 'options' => $options ) )
		. '</fieldset>'
	;
	
	echo '</div>';
	
	echo '<br />' . $this->Default->button(
		'back',
		array(
			'controller' => 'accompagnementscuis66',
			'action'     => 'index',
			$cui_id
		),
		array(
			'id' => 'Back',
			'class' => 'aere'
		)
	);
	
	echo $this->Observer->disableFieldsetOnValue(
		'Accompagnementcui66.genre',
		'Immersioncui66Fieldset',
		'immersion',
		false,
		true
	);