<?php
if($structures) {
	// Conditions d'accès aux tags
	$departement = Configure::read( 'Cg.departement' );
	$user_type = $this->Session->read( 'Auth.User.type' );
	$utilisateursAutorises = (array)Configure::read( 'acces.recherche.tag' );
	$viewTag = false;

	foreach ($utilisateursAutorises as $utilisateurAutorise) {
		if ($utilisateurAutorise == $user_type) {
			$viewTag = true;
			break;
		}
	}

	if ($departement != 93) {
		$viewTag = true;
	}
	// Conditions d'accès aux tags

	// Conditions d'accès aux origines d'orientation prestataires
	$utilisateursAutorises = (array)Configure::read( 'acces.origine.orientation.prestataire' );
	$viewOriginePresta = false;

	foreach ($utilisateursAutorises as $utilisateurAutorise) {
		if ($utilisateurAutorise == $user_type) {
			$viewOriginePresta = true;
			break;
		}
	}

	$this->start( 'custom_search_filters' );

	$paramDate = array(
		'minYear_from' => date( 'Y' ) - 3,
		'maxYear_from' => date( 'Y' ) + 1,
		'minYear_to' => date( 'Y' ) - 3,
		'maxYear_to' => date( 'Y' ) + 1
	);

	echo $this->Html->tag(
        'fieldset',
		$this->Html->tag( 'legend', __m('Search.Orientstruct.CustomTitle' ) )
		. $this->SearchForm->dateRange( 'Search.Orientstruct.date_valid', $paramDate + array( 'legend' => __m( 'Search.Orientstruct.date_valid' ) ) )
		. $this->Html->tag(
			'fieldset',
			$this->Html->tag( 'legend', __m('Search.Orientstruct.Impression' ) )
			. $this->Default3->subform(
				array(
					'Search.Orientstruct.impression' => array( 'empty' => true ),
				),
				array( 'options' => array( 'Search' => $options ) )
			)
			. $this->SearchForm->dateRange( 'Search.Orientstruct.date_impression', $paramDate + array( 'legend' => __m( 'Search.Orientstruct.date_impression' ) ) )
		)
		. $this->Default3->subform(
			array(
				'Search.Orientstruct.typeorient_id' => array( 'empty' => true, 'required' => false ),
				'Search.Orientstruct.origine' => array( 'empty' => true ),
			),
			array( 'options' => array( 'Search' => $options ) )
		)
		. $this->Form->input(
			'Search.Orientstruct.structureorientante_id',
			array(
				'label' => __m( 'Search.Orientstruct.structureorientante_id' ),
				'type' => 'select',
				'options' => $options['Orientstruct']['structureorientante_id'],
				'empty' => true
			)
		)
		. $this->SearchForm->dateRange( 'Search.Orientstruct.date_propo', array(
            'domain' => 'orientstruct',
            'minYear_from' => date( 'Y' ) - 3,
            'minYear_to' => date( 'Y' ) - 3,
            'maxYear_from' => date( 'Y' ) + 1,
            'maxYear_to' => date( 'Y' ) + 1
        ))
	);

	$this->end();

	$buttons = '<ul class="actionMenu">
		<li>'.$this->Xhtml->printCohorteLink(
				'Imprimer la cohorte',
				Hash::merge(
					array(
						'controller' => 'orientsstructs',
						'action'     => 'cohorte_orientees_validees_impressions',
						'id' => 'Cohorteoriente'
					),
					Hash::flatten( $this->request->data, '__' )
				)
			).'</li>
		</ul>';

	echo $this->element(
		'ConfigurableQuery/search',
		array(
			'modelName' => 'Personne',
			'customSearch' => $this->fetch( 'custom_search_filters' ),
			'exportcsv' => false,
			'afterResults' => $buttons
		)
	);
	$results = isset( $results ) ? $results : array();
}