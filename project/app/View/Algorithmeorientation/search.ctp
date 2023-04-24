<?php $this->start( 'custom_search_filters' );?>
<?php
	$user_type = $this->Session->read( 'Auth.User.type' );

	// Conditions d'accès aux origines d'orientation prestataires
	$utilisateursAutorises = (array)Configure::read( 'acces.origine.orientation.prestataire' );
	$viewOriginePresta = false;

	foreach ($utilisateursAutorises as $utilisateurAutorise) {
		if ($utilisateurAutorise == $user_type) {
			$viewOriginePresta = true;
			break;
		}
	}

	if ($viewOriginePresta == false) {
	    foreach ($options['Orientstruct']['origine'] as $key => $value) {
	        if (preg_match('|^presta|', $key)) {
	            unset ($options['Orientstruct']['origine'][$key]);
	        }
	    }
	}
	// Conditions d'accès aux origines d'orientation prestataires

	$controller = $this->params->controller;
	$action = $this->action;
	$formId = ucfirst($controller) . ucfirst($action) . 'Form';
	$availableDomains = WebrsaTranslator::domains();
	$domain = isset( $availableDomains[0] ) ? $availableDomains[0] : $controller;
	$paramDate = array(
		'domain' => $domain,
		'minYear_from' => '2009',
		'maxYear_from' => date( 'Y' ) + 1,
		'minYear_to' => '2009',
		'maxYear_to' => date( 'Y' ) + 4,
		'default_from' => strtotime('01-'.date('m-Y')),
		'default_to' => strtotime(date('t-m-Y'))
	);
	$paramAllocataire = array(
		'options' => $options,
		'prefix' => 'Search',
	);
	$dateRule = array(
		'date' => array(
			'rule' => array('date'),
			'message' => null,
			'required' => null,
			'allowEmpty' => true,
			'on' => null
		)
	);

	echo '<fieldset><legend>' . __m( 'Orientstruct.search' ) . '</legend>'
		. $this->Default3->subform(
			array(
				'Search.Orientstruct.derniere' => array( 'type' => 'checkbox' )
			),
			array( 'options' => array( 'Search' => $options ) )
		)
		. $this->Default3->subform(
			array(
				'Search.Orientstruct.dernierevalid' => array( 'type' => 'checkbox' )
			),
			array( 'options' => array( 'Search' => $options ) )
		)
		. $this->SearchForm->dateRange( 'Search.Orientstruct.date_valid', $paramDate )
	;

	echo $this->Default3->subform(
		array(
			'Search.Orientstruct.origine' => array('empty' => true),
		),
		array( 'options' => array( 'Search' => $options ) )
	);

	echo $this->Default3->subform(
			array(
				'Search.Orientstruct.typeorient_id' => array('empty' => true, 'required' => false),
			),
			array( 'options' => array( 'Search' => $options ) )
		);

	echo $this->Allocataires->communautesrSelect( 'Orientstruct', array( 'options' => array( 'Search' => $options ) ) );

	echo $this->Default3->subform(
			array(
				'Search.Orientstruct.structurereferente_id' => array('empty' => true, 'required' => false),
				'Search.Orientstruct.statut_orient' => array('empty' => true, 'required' => false)
			),
			array( 'options' => array( 'Search' => $options ) )
		)
		. '</fieldset>'
	;
?>

<?php $this->end();?>

<?php
	echo $this->element(
		'ConfigurableQuery/search',
		array(
			'customSearch' => $this->fetch( 'custom_search_filters' ),
			'exportcsv' => array( 'action' => 'exportcsv_recherche' ),
			'modelName' => 'Orientstruct'
		)
	);
?>
<?php
	// Si l'utilisateur connecté a accès aux projets insertion emploi communautaires
	if( true === Hash::check($options, 'Orientstruct.communautesr_id') ) {
		echo $this->Observer->disableFieldsOnValue(
			'Search.Orientstruct.typeorient_id',
			'Search.Orientstruct.communautesr_id',
			array( '', null ),
			false
		);

		echo $this->Observer->disableFieldsOnValue(
			'Search.Orientstruct.communautesr_id',
			'Search.Orientstruct.typeorient_id',
			array( '', null ),
			false
		);
	}


	echo $this->Allocataires->communautesrScript(
		'Orientstruct',
		array(
			'options' => array( 'Search' => $options )
		)
	);


	echo $this->Observer->dependantSelect(
		array(
			'Search.Orientstruct.typeorient_id' => 'Search.Orientstruct.structurereferente_id'
		)
	);
?>
<script type="text/javascript">
    observeDisableFieldsOnCheckbox(
        'SearchOrientstructDerniere',
        [
            'SearchOrientstructDernierevalid',
        ],
        true
    );
    observeDisableFieldsOnCheckbox(
        'SearchOrientstructDernierevalid',
        [
            'SearchOrientstructDerniere',
        ],
        true
    );
</script>