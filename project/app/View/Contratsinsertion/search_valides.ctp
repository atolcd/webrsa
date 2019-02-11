<?php
	$departement = Configure::read( 'Cg.departement' );
	$controller = $this->params->controller;
	$action = $this->action;
	$formId = ucfirst($controller) . ucfirst($action) . 'Form';
	$availableDomains = WebrsaTranslator::domains();
	$domain = isset( $availableDomains[0] ) ? $availableDomains[0] : $controller;
	$paramDate = array(
		'domain' => null,
		'minYear_from' => '2009',
		'maxYear_from' => date( 'Y' ) + 1,
		'minYear_to' => '2009',
		'maxYear_to' => date( 'Y' ) + 4
	);
	$notEmptyRule[NOT_BLANK_RULE_NAME] = array(
		'rule' => NOT_BLANK_RULE_NAME,
		'message' => 'Champ obligatoire'
	);
	$dateRule['date'] = array(
		'rule' => array('date'),
		'message' => null,
		'required' => null,
		'allowEmpty' => true,
		'on' => null
	);
	$validationCohorte = array(
		'Contratinsertion' => array(
			'created' => $dateRule,
		)
	);
	echo $this->FormValidator->generateJavascript($validationCohorte, false);

	$this->start( 'custom_search_filters' );

	echo '<fieldset><legend>' . __m( 'Contratsinsertion.'.$action ) . '</legend>'
		. $this->Allocataires->SearchForm->dateRange( 'Search.Contratinsertion.created', $paramDate )
		. $this->Default3->subform(
			array(
				'Search.Contratinsertion.structurereferente_id' => array( 'empty' => true ),
				'Search.Contratinsertion.referent_id' => array( 'empty' => true ),
				'Search.Contratinsertion.decision_ci' => array( 'empty' => true ),
				'Search.Contratinsertion.datevalidation_ci' => $paramDate + array( 'empty' => true ),
				'Search.Contratinsertion.forme_ci' => array( 'type' => 'radio', 'legend' => __m( 'Search.Contratinsertion.forme_ci' ), 'class' => 'uncheckable' )
			),
			array( 'options' => array( 'Search' => $options ) )
		)
		. '</fieldset>'
	;

	$this->end();

	$explAction = explode('_', $action);
	$exportcsvActionName = isset($explAction[1]) ? 'exportcsv_search_'.$explAction[1] : 'exportcsv';
	
	echo $this->element(
		'ConfigurableQuery/search',
		array(
			'customSearch' => $this->fetch( 'custom_search_filters' ),
			'exportcsv' => array( 'action' => $exportcsvActionName ),
			'modelName' => 'Contratinsertion'
		)
	);
	
	$results = isset($results) ? $results : array();
	?>
	<script type="text/javascript">
		var positioncer, positioncer, decision_ci, tmp_positioncer, tmp_decision_ci;
	<?php
	
	foreach ($results as $result) {
		$id = Hash::get($result, 'Contratinsertion.id');
	?>
		positioncer = $('ficheliaisoncer_<?php echo $id;?>').readAttribute('positioncer');
		decision_ci = $('ficheliaisoncer_<?php echo $id;?>').readAttribute('decision_ci');
		tmp_positioncer = new Element('input', {type: 'hidden', id: 'positioncer_<?php echo $id;?>', value: positioncer});
		tmp_decision_ci = new Element('input', {type: 'hidden', id: 'decision_ci_<?php echo $id;?>', value: decision_ci});

		disableElementsOnValues(
			'ficheliaisoncer_<?php echo $id;?>',
			[
				{element: tmp_positioncer, value: 'annule'},
				{element: tmp_positioncer, value: 'fincontrat'},
				{element: tmp_decision_ci, value: 'N', operator: '!='}
			]
		);

		disableElementsOnValues(
			'notifbenef_<?php echo $id;?>',
			[
				{element: tmp_positioncer, value: 'annule'},
				{element: tmp_positioncer, value: 'fincontrat'},
				{element: tmp_decision_ci, value: 'E'}
			]
		);

		disableElementsOnValues(
			'notificationsop_<?php echo $id;?>',
			[
				{element: tmp_positioncer, value: 'annule'},
				{element: tmp_positioncer, value: 'fincontrat'},
				{element: tmp_decision_ci, value: 'V', operator: '!='}
			]
		);

		disableElementsOnValues(
			'impression_<?php echo $id;?>',
			[
				{element: tmp_positioncer, value: 'annule'},
				{element: tmp_positioncer, value: 'fincontrat'}
			]
		);
	<?php
	}
	?>
	dependantSelect(
		'SearchContratinsertionReferentId',
		'SearchContratinsertionStructurereferenteId'
	);
	
	observeDisableFieldsOnValue(
		'SearchContratinsertionDecisionCi',
		[
			'SearchContratinsertionDatevalidationCiDay',
			'SearchContratinsertionDatevalidationCiMonth',
			'SearchContratinsertionDatevalidationCiYear'
		],
		'V',
		false
	);
</script>