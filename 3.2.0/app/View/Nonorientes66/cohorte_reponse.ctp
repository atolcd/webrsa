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
		'Nonoriente66' => array(
			'reponseallocataire' => $notEmptyRule
		),
		'Orientstruct' => array(
			'typeorient_id' => $notEmptyRule,
			'structurereferente_id' => $notEmptyRule,
			'date_valid' => $dateRule
		)
	);
	echo $this->FormValidator->generateJavascript($validationCohorte, false);

	$this->start( 'custom_search_filters' );

	echo '<fieldset><legend>' . __m( 'Nonoriente66.search' ) . '</legend>'
		. $this->Allocataires->SearchForm->dateRange( 'Search.Nonoriente66.dateimpression', $paramDate )
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
			'modelName' => 'Personne'
		)
	);
	
	$results = isset( $results ) ? $results : array();
	?><script type="text/javascript">
		var structureAuto = <?php echo $structureAuto;?>;
	<?php
	foreach ($results as $i => $value) {
	?>
		dependantSelect( 'Cohorte<?php echo $i;?>OrientstructStructurereferenteId', 'Cohorte<?php echo $i;?>OrientstructTypeorientId' );
		
		observeDisableElementsOnValues(
			[
				'Cohorte<?php echo $i;?>Nonoriente66ReponseallocataireN',
				'Cohorte<?php echo $i;?>Nonoriente66ReponseallocataireO',
				'Cohorte<?php echo $i;?>OrientstructTypeorientId',
				'Cohorte<?php echo $i;?>OrientstructStructurereferenteId',
				'Cohorte<?php echo $i;?>OrientstructDateValidDay',
				'Cohorte<?php echo $i;?>OrientstructDateValidMonth',
				'Cohorte<?php echo $i;?>OrientstructDateValidYear'
			],
			{element: 'Cohorte<?php echo $i;?>Nonoriente66Selection', value: '1', operator: '!='}
		);
	<?php 
	// Selection automatique de la structure référente en fonction de typeorient et du canton
	$canton = Hash::get($value, 'Canton.canton');
	if ($canton) {?>
		
		$('Cohorte<?php echo $i;?>OrientstructTypeorientId').observe('change', function(){
			var thisVal = $('Cohorte<?php echo $i;?>OrientstructTypeorientId').getValue();
			if (structureAuto['<?php echo $canton;?>'][thisVal] !== undefined) {
				$('Cohorte<?php echo $i;?>OrientstructStructurereferenteId').setValue(structureAuto['<?php echo $canton;?>'][thisVal]);
			}
		});
	<?php }
	} ?>
	</script>