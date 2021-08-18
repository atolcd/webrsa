<?php
	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
		echo $this->Html->script( array( 'prototype.event.simulate.js', 'dependantselect.js' ) );
	}

	$departement = Configure::read( 'Cg.departement' );

	$this->pageTitle = __m('Orientation.validation');

	echo $this->Html->tag( 'h1', $this->pageTitle );

	$defaultParams = compact('options', 'domain');

	echo $this->Default3->DefaultForm->create( 'Orientstruct', array( 'novalidate' => 'novalidate' ) );
	echo $this->Default3->subform(
		array(
			'Orientstruct.id' => array( 'type' => 'hidden' ),
			'Orientstruct.personne_id' => array( 'type' => 'hidden' )
		)
	);

	echo $this->Html->tag(
		'fieldset',
		'<div class="structOrientante">'
		. $this->Default3->subformView(
			array(
				'Structureorientante.lib_struc',
				'Referentorientant.nom_complet',
			),
			$defaultParams
		)
		. '</div>'
		. $this->Default3->subformView(
			array(
				'Structureorientante.lib_struc',
				'Referentorientant.nom_complet',
				'Orientstruct.origine',
				'Typeorient.lib_type_orient',
				'Structurereferente.lib_struc',
				'Referent.nom_complet',
				'Calculdroitrsa.toppersdrodevorsa',
				'Orientstruct.date_propo' => array( 'type' => 'date', 'dateFormat' => 'DMY' ),
			),
			$defaultParams
		)
		.  $this->Html->tag(
			'fieldset',
			$this->Html->tag( 'legend', required( __m('Orientation.propvalidation' ) ) )
			. $this->Default3->subform(
				$this->Translator->normalize(
					array(
						'Orientstruct.decisionvalidation' => array( 'type' => 'radio', 'legend' => false )
					)
				),
				array(
					'options' => $options
				)
			)
		)
		. $this->Default3->subform(
			$this->Translator->normalize(
				array(
					'Orientstruct.dtdecisionvalidation' => array(
						'type' => 'date',
						'legend' => false,
						'dateFormat' => 'DMY',
						'minYear' => date( 'Y' ) - 3,
						'maxYear' => date( 'Y' ) + 1,
						'empty' => true ),
				)
			),
			array(
				'options' => $options
			)
		)
	);

	echo $this->Default3->DefaultForm->buttons( array( 'Save', 'Cancel' ) );
	echo $this->Default3->DefaultForm->end();

	echo $this->Observer->disableFieldsOnRadioValue(
		'OrientstructValiderForm',
		'Orientstruct.decisionvalidation',
		array(
			'Orientstruct.dtdecisionvalidation.year',
			'Orientstruct.dtdecisionvalidation.month',
			'Orientstruct.dtdecisionvalidation.day'
		),
		array(1),
		true
	);

	echo $this->Observer->disableFormOnSubmit( $this->Html->domId( "Orientstruct.{$this->action}.form" ) );