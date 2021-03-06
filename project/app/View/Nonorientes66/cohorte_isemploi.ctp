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
	$validationCohorte = array();
	echo $this->FormValidator->generateJavascript($validationCohorte, false);

	$this->start( 'custom_search_filters' );

	/**
	 * FILTRES CUSTOM
	 */
	
	$this->end();

	$this->start( 'custom_after_results' );
	echo $this->Form->button( 'Tout cocher', array( 'type' => 'button', 'onclick' => "return toutCocherAction( 'input.input[type=checkbox]' );" ) );
	echo $this->Form->button( 'Tout décocher', array( 'type' => 'button', 'onclick' => "return toutDecocherAction( 'input.input[type=checkbox]' );" ) );
	$this->end();

	$explAction = explode('_', $action);
	$exportcsvActionName = isset($explAction[1]) ? 'exportcsv_'.$explAction[1] : 'exportcsv';

	echo $this->element(
		'ConfigurableQuery/cohorte',
		array(
			'customSearch' => $this->fetch( 'custom_search_filters' ),
			'afterResults' => $this->fetch( 'custom_after_results' ),
			'exportcsv' => array( 'action' => $exportcsvActionName ),
			'modelName' => 'Personne'
		)
	);

	$results = isset( $results ) ? $results : array();
?>

<script type="text/javascript">
<?php
	foreach ($results as $i => $value) {
?>
		observeDisableElementsOnValues(
			[
				'Cohorte<?php echo $i;?>OrientstructDateValidDay',
				'Cohorte<?php echo $i;?>OrientstructDateValidMonth',
				'Cohorte<?php echo $i;?>OrientstructDateValidYear'
			],
			{element: 'Cohorte<?php echo $i;?>Nonoriente66Selection', value: '1', operator: '!='}
		);
<?php
	}
?>

	function toutCocherAction( selecteur, simulate ) {
		toutCocher( selecteur, simulate );
<?php
	foreach ($results as $i => $value) {
?>
		disableElementsOnValues(
			[
				'Cohorte<?php echo $i;?>OrientstructDateValidDay',
				'Cohorte<?php echo $i;?>OrientstructDateValidMonth',
				'Cohorte<?php echo $i;?>OrientstructDateValidYear'
			],
			{element: 'Cohorte<?php echo $i;?>Nonoriente66Selection', value: '1', operator: '!='}
		);
<?php
	}
?>
		return false;
	}

	function toutDecocherAction( selecteur, simulate ) {
		toutDecocher( selecteur, simulate );
<?php
	foreach ($results as $i => $value) {
?>
		disableElementsOnValues(
			[
				'Cohorte<?php echo $i;?>OrientstructDateValidDay',
				'Cohorte<?php echo $i;?>OrientstructDateValidMonth',
				'Cohorte<?php echo $i;?>OrientstructDateValidYear'
			],
			{element: 'Cohorte<?php echo $i;?>Nonoriente66Selection', value: '1', operator: '!='}
		);
<?php
	}
?>
		return false;
	}

	$$('form#Nonorientes66CohorteIsemploiCohorte>table>tbody>tr>td').each(function(td){
		if (td.innerHTML == "ALERTE") {
			for (var i = 0; i < td.parentNode.childNodes.length; i++) {
				if (typeof td.parentNode.childNodes[i].innerHTML !== 'undefined') {
					td.parentNode.childNodes[i].setStyle ({
						'background-color': '<?php echo (Configure::read( 'Resultats.ligne.erreur' )); ?>'
					});
				}
			}
		}
	});
</script>
