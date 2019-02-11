<?php
	$departement = Configure::read( 'Cg.departement' );
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
		'Dossierpcg66' => array(
			'poledossierpcg66_id' => $notEmptyRule,
			'dateaffectation' => $dateRule
		),
	);
	echo $this->FormValidator->generateJavascript($validationCohorte, false);

	$this->start( 'custom_search_filters' );

	echo '<fieldset><legend>' . __m( 'Dossierpcg66.'.$action ) . '</legend>'
		. $this->Allocataires->SearchForm->dateRange( 'Search.Dossierpcg66.datereceptionpdo', $paramDate )
		. $this->Default3->subform(
			array(
				'Search.Dossierpcg66.originepdo_id' => array( 'empty' => true ),
				'Search.Dossierpcg66.serviceinstructeur_id' => array( 'empty' => true ),
				'Search.Dossierpcg66.typepdo_id' => array( 'empty' => true ),
				'Search.Dossierpcg66.orgpayeur' => array( 'empty' => true ),
				'Search.Dossierpcg66.has_poledossierpcg66_id' => array( 'empty' => true ),
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
			'modelName' => 'Dossierpcg66'
		)
	);

	$results = isset($results) ? $results : array();
?>
<script type="text/javascript">
	document.observe("dom:loaded", function() {
		for (var i=0; i<<?php echo count($results);?>; i++) {
			dependantSelect( 'Cohorte'+i+'Dossierpcg66UserId', 'Cohorte'+i+'Dossierpcg66Poledossierpcg66Id' );

			observeDisableFieldsOnCheckbox(
				'Cohorte'+i+'Dossierpcg66Atraiter',
				[
					'Cohorte'+i+'Dossierpcg66Poledossierpcg66Id',
					'Cohorte'+i+'Dossierpcg66UserId',
					'Cohorte'+i+'Dossierpcg66DateaffectationDay',
					'Cohorte'+i+'Dossierpcg66DateaffectationMonth',
					'Cohorte'+i+'Dossierpcg66DateaffectationYear'
				],
				false
			);
		}
	});
</script>