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
				'Search.Contratinsertion.structurereferente_id' => array( 'empty' => true, 'required' => false ),
				'Search.Contratinsertion.referent_id' => array( 'empty' => true, 'required' => false ),
			),
			array( 'options' => array( 'Search' => $options ), 'domain' => $domain )
		)
		. '</fieldset>'
	;

	$this->end();

	$explAction = explode('_', $action);
	$exportcsvActionName = isset($explAction[1]) ? 'exportcsv_'.$explAction[1] : 'exportcsv';
	
	echo $this->element(
		'ConfigurableQuery/cohorte',
		array(
			'customSearch' => $this->fetch( 'custom_search_filters' ),
			'exportcsv' => array( 'action' => $exportcsvActionName ),
			'modelName' => 'Contratinsertion'
		)
	);
	
	$results = isset($results) ? $results : array();
	
	foreach ($results as $i => $result) {
	?>
		<script type="text/javascript">	
			observeDisableElementsOnValues(
				'Cohorte<?php echo $i;?>ContratinsertionDecisionCi',
				{element: 'Cohorte<?php echo $i;?>ContratinsertionSelection', value: null}
			);
	
			observeDisableElementsOnValues(
				[
					'Cohorte<?php echo $i;?>ContratinsertionDatedecisionDay',
					'Cohorte<?php echo $i;?>ContratinsertionDatedecisionMonth',
					'Cohorte<?php echo $i;?>ContratinsertionDatedecisionYear',
					'Cohorte<?php echo $i;?>ContratinsertionObservCi'
				],
				[
					{element: 'Cohorte<?php echo $i;?>ContratinsertionSelection', value: null},
					{element: 'Cohorte<?php echo $i;?>ContratinsertionDecisionCi', value: 'E'}
				],
				false,
				true
			);
		</script>
	<?php
	}
	?>
<script>
	dependantSelect(
		'SearchContratinsertionReferentId',
		'SearchContratinsertionStructurereferenteId'
	);
</script>