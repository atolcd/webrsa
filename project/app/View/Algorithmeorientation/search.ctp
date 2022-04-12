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
<fieldset>
	<legend><?php echo __m( 'Search.Rendezvous' ); ?></legend>
	<?php
		echo $this->Form->input( 'Search.Rendezvous.periodeorientation', array( 'label' => __m( 'Search.Rendezvous.periodeorientation' ), 'type' => 'checkbox') );
		echo "<br>";
		echo $this->Form->input( 'Search.Rendezvous.statutrdv_id', array( 'label' => __m( 'Search.Rendezvous.statutrdv_id' ), 'type' => 'select', 'multiple' => 'checkbox', 'options' => $options['Rendezvous']['statutrdv_id'], 'empty' => false ) );
		echo $this->Allocataires->communautesr( 'Rendezvous', array( 'options' => array( 'Search' => $options ), 'hide' => false ) );
		echo $this->Form->input( 'Search.Rendezvous.structurereferente_id', array( 'label' => __m( 'Search.Rendezvous.structurereferente_id' ), 'type' => 'select', 'options' => $options['PersonneReferent']['structurereferente_id'], 'empty' => true ) );
		echo $this->Form->input( 'Search.Rendezvous.referent_id', array( 'label' => __m( 'Search.Rendezvous.referent_id' ), 'type' => 'select', 'options' => $options['PersonneReferent']['referent_id'], 'empty' => true ) );
		echo $this->Form->input( 'Search.Rendezvous.permanence_id', array( 'label' => __m( 'Search.Rendezvous.permanence_id' ), 'type' => 'select', 'options' => $options['Rendezvous']['permanence_id'], 'empty' => true ) );
		echo $this->Form->input( 'Search.Rendezvous.typerdv_id', array( 'label' => __m( 'Search.Rendezvous.typerdv_id' ), 'type' => 'select', 'options' => $options['Rendezvous']['typerdv_id'], 'empty' => true ) );

		// Thématiques du RDV
		if( Configure::read( 'Rendezvous.useThematique' ) ) {
			if( isset( $options['Rendezvous']['thematiquerdv_id'] ) && !empty( $options['Rendezvous']['thematiquerdv_id'] ) ) {
				foreach( $options['Rendezvous']['thematiquerdv_id'] as $typerdv_id => $thematiques ) {
					$input = $this->Xform->input(
						'Search.Rendezvous.thematiquerdv_id',
						array(
							'type' => 'select',
							'multiple' => 'checkbox',
							'options' => $thematiques,
							'label' => __m( 'Search.Rendezvous.thematiquerdv_id' )
						)
					);
					echo $this->Xhtml->tag( 'fieldset', $input, array( 'id' => "SearchRendezvousThematiquerdvId{$typerdv_id}", 'class' => 'invisible' ) );
				}
			}
		}

		echo $this->SearchForm->dateRange( 'Search.Rendezvous.daterdv', array(
			'domain' => 'rendezvous', // FIXME
			'minYear_from' => 2009,
			'minYear_to' => 2009,
			'maxYear_from' => date( 'Y' ) + 1,
			'maxYear_to' => date( 'Y' ) + 1
		) );

		echo $this->SearchForm->timeRange( 'Search.Rendezvous.heurerdv', array(
			'domain' => 'rendezvous',
		) );

		echo $this->Form->input('Search.Rendezvous.arevoirle', array( 'label' => __d( 'rendezvous', 'Rendezvous.arevoirle' ), 'type' => 'date', 'dateFormat' => 'MY', 'empty' => true, 'minYear' => date( 'Y' ) - 1, 'maxYear' => date( 'Y' ) + 3 ) );
	?>
</fieldset>
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