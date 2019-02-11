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
		'ActioncandidatPersonne' => array(
			'motifsortie_id' => $notEmptyRule,
			'sortiele' => $dateRule
		)
	);
	echo $this->FormValidator->generateJavascript($validationCohorte, false);

	$this->start( 'custom_search_filters' );

	echo '<fieldset><legend>' . __m( 'ActionscandidatsPersonnes.'.$action ) . '</legend>'
		. $this->Default3->subform(
			array(
				'Search.Partenaire.codepartenaire',
				'Search.Contactpartenaire.partenaire_id' => array( 'empty' => true ),
				'Search.ActioncandidatPersonne.actioncandidat_id' => array( 'empty' => true ),
				'Search.ActioncandidatPersonne.referent_id' => array( 'empty' => true ),
			),
			array( 'options' => array( 'Search' => $options ), 'domain' => $domain )
		) 
		. $this->Allocataires->SearchForm->dateRange( 'Search.ActioncandidatPersonne.datesignature', $paramDate )
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
			'modelName' => 'ActioncandidatPersonne'
		)
	);

	$results = isset($results) ? $results : array();
	
	foreach ($results as $i => $result) {
	?>
		<script type="text/javascript">	
			observeDisableElementsOnValues(
				[
					'Cohorte<?php echo $i;?>ActioncandidatPersonneMotifsortieId',
					'Cohorte<?php echo $i;?>ActioncandidatPersonneSortieleDay',
					'Cohorte<?php echo $i;?>ActioncandidatPersonneSortieleMonth',
					'Cohorte<?php echo $i;?>ActioncandidatPersonneSortieleYear'
				],
				{element: 'Cohorte<?php echo $i;?>ActioncandidatPersonneIssortie', value: '1', operator: '!='}
			);
	
			var motifsortie = '_'+$('Cohorte<?php echo $i;?>ActioncandidatPersonneMotifsortie').getValue()+'_';console.log(motifsortie);
			$('Cohorte<?php echo $i;?>ActioncandidatPersonneMotifsortieId').select('option').each(function(option) {
				if (motifsortie.indexOf('_'+option.value+'_') === -1 && option.value !== '') {
					option.remove();
				}
			});
		</script>
	<?php
	}
	?>
		<script type="text/javascript">
			dependantSelect( 'SearchActioncandidatPersonneActioncandidatId', 'SearchContactpartenairePartenaireId' );
		</script>