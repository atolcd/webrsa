<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
		echo $this->Html->script( array( 'prototype.event.simulate.js', 'dependantselect.js' ) );
	}

	$departement = Configure::read( 'Cg.departement' );

	// Début TODO: factoriser
	$personne_id = Hash::get( $dossierMenu, 'personne_id' );
	$personne = Hash::get( (array)Hash::extract( $dossierMenu, "Foyer.Personne.{n}[id={$personne_id}]" ), 0 );

	if( $this->action === 'edit' ) {
		$this->pageTitle = "Modification d'une orientation de {$personne['qual']} {$personne['nom']} {$personne['prenom']}";
	}
	else {
		$this->pageTitle = "Ajout d'une orientation pour {$personne['qual']} {$personne['nom']} {$personne['prenom']}";
	}
	echo $this->Html->tag( 'h1', $this->pageTitle );
	// Fin TODO

	echo $this->Default3->DefaultForm->create( 'Orientstruct', array( 'novalidate' => 'novalidate' ) );

	echo $this->Default3->subform(
		array(
			'Orientstruct.id',
			'Orientstruct.personne_id' => array( 'type' => 'hidden' ),
			'Calculdroitrsa.id' => array( 'type' => 'hidden' ),
			'Calculdroitrsa.personne_id' => array( 'type' => 'hidden' ),
			'Orientstruct.user_id' => array( 'type' => 'hidden' ),
			'Orientstruct.origine' => array( 'type' => 'hidden' ),
		)
	);

	if( $departement == 66 || Configure::read('Orientation.validation.enabled') ) {
		$isMandatory = false;
		if(Configure::read('Orientation.validation.enabled') == true) {
			$isMandatory = true;
		}
		echo $this->Html->tag(
			'fieldset',
			$this->Html->tag( 'legend', 'Orienté par' )
			.$this->Default3->subform(
				array(
					'Orientstruct.structureorientante_id' => array( 'empty' => true, 'required' => $isMandatory ),
					'Orientstruct.referentorientant_id' => array( 'empty' => true, 'required' => $isMandatory ),
				),
				array(
					'options' => $options
				)
			)
		);
	}

	$fields = array(
		'Orientstruct.origine' => ( $departement == 93 ? array( 'empty' => false, 'label' => __d ('orientstruct', 'Orientstruct.origine.externe') ) : array(  'label' =>  false, 'type' => 'hidden', 'value' => '' ) ),
		'Orientstruct.typeorient_id' => array( 'empty' => true ),
		'Orientstruct.structurereferente_id' => array( 'empty' => true, 'label' => ( $departement == 93 ? 'Structure référente' : 'Type de structure' ) ),
		'Orientstruct.referent_id' => array( 'empty' => true, 'required' => false, 'label' => 'Nom du référent' ),
		'Calculdroitrsa.toppersdrodevorsa' => array( 'empty' => 'Non défini', 'required' => true, 'label' => 'Personne soumise à droits et devoirs ?' ),
		'Orientstruct.statut_orient' => ( $departement == 976 ? array( 'empty' => true ) : array(  'label' =>  false, 'type' => 'hidden', 'value' => 'Orienté' ) ),
		'Orientstruct.date_propo' => array( 'dateFormat' => 'DMY', 'minYear' => 2009, 'maxYear' => date( 'Y' ) + 1, 'empty' => true ),
		'Orientstruct.date_valid' => array( 'dateFormat' => 'DMY', 'minYear' => 2009, 'maxYear' => date( 'Y' ) + 1, 'empty' => true, 'label' => 'Date de l\'orientation' ),
	);

	if( $departement == 66 ) {
		$fields['Orientstruct.typenotification'] = array( 'empty' => false, 'type' => 'select' );
	}

	echo $this->Default3->subform( $fields, array( 'options' => $options ) );

	echo $this->Default3->DefaultForm->buttons( array( 'Save', 'Cancel' ) );
	echo $this->Default3->DefaultForm->end();

	if( $departement == 66 || Configure::read('Orientation.validation.enabled') ) {
		echo $this->Observer->dependantSelect(
			array(
				'Orientstruct.structureorientante_id' => 'Orientstruct.referentorientant_id',
			)
		);
	}

	echo $this->Observer->dependantSelect(
		array(
			'Orientstruct.typeorient_id' => 'Orientstruct.structurereferente_id',
			'Orientstruct.structurereferente_id' => 'Orientstruct.referent_id',
		)
	);

	if( $departement == 976 ) {
		echo $this->Observer->disableFieldsOnValue(
			'Orientstruct.statut_orient',
			array(
				'Orientstruct.date_valid.year',
				'Orientstruct.date_valid.month',
				'Orientstruct.date_valid.day'
			),
			array( 'Orienté' ),
			false
		);
	}

	if ( $processValidation ) {
		echo $this->Observer->disableFieldsOnValue(
			'Orientstruct.structureorientante_id',
			array(
				'Orientstruct.date_valid.year',
				'Orientstruct.date_valid.month',
				'Orientstruct.date_valid.day'
			),
			$options['StructOrientanteWorkflow'],
			true
		);
	}

	echo $this->Observer->disableFormOnSubmit( $this->Html->domId( "Orientstruct.{$this->action}.form" ) );
?>