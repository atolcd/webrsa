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
		'Aideapre66' => array(
			'montantaccorde' => array('numeric' => array('rule' => array('numeric'), 'allowEmpty' => false)),
			'datemontantaccorde' => $dateRule
		),
	);
	echo $this->FormValidator->generateJavascript($validationCohorte, false);

	$this->start( 'custom_search_filters' );

	echo '<fieldset><legend>' . __m( 'Apres66.'.$action ) . '</legend>'
		. $this->Default3->subform(
			array(
				'Search.Aideapre66.themeapre66_id' => array( 'empty' => true ),
				'Search.Aideapre66.typeaideapre66_id' => array( 'empty' => true ),
				'Search.Apre66.numeroapre',
				'Search.Dernierreferent.recherche' => array('name' => false, 'before' => '<hr>'),
				'Search.Dernierreferent.dernierreferent_id' => array('empty' => true, 'after' => '<hr>'),
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
			'modelName' => 'Apre66'
		)
	);

	$results = isset($results) ? $results : array();
	
	foreach ($results as $i => $result) {
	?>
		<script type="text/javascript">
			observeDisableElementsOnValues(
				[
					'Cohorte<?php echo $i;?>Aideapre66Decisionapre',
					'Cohorte<?php echo $i;?>Aideapre66DatemontantaccordeDay',
					'Cohorte<?php echo $i;?>Aideapre66DatemontantaccordeMonth',
					'Cohorte<?php echo $i;?>Aideapre66DatemontantaccordeYear'
				],
				[
					{element: 'Cohorte<?php echo $i;?>Apre66Selection', value: '1', operateur: '!='}
				]
			);
	
			observeDisableElementsOnValues(
				'Cohorte<?php echo $i;?>Aideapre66Montantaccorde', 
				[
					{element: 'Cohorte<?php echo $i;?>Apre66Selection', value: '1', operateur: '!='},
					{element: 'Cohorte<?php echo $i;?>Aideapre66Decisionapre', value: 'REF', operateur: '=='}
				],
				false, // Hide
				true   // Une condition rempli suffit à désactiver l'element
			);
	
			observeDisableElementsOnValues(
				'Cohorte<?php echo $i;?>Aideapre66Motifrejetequipe', 
				[
					{element: 'Cohorte<?php echo $i;?>Apre66Selection', value: '1', operateur: '!='},
					{element: 'Cohorte<?php echo $i;?>Aideapre66Decisionapre', value: 'ACC', operateur: '=='}
				],
				false, // Hide
				true   // Une condition rempli suffit à désactiver l'element
			);
		</script>
	<?php
	}
	?>
	<script type="text/javascript">
		 dependantSelect(
			'SearchAideapre66Typeaideapre66Id',
			'SearchAideapre66Themeapre66Id'
		);
	
		/**
		 * Remplissage auto Dernierreferent
		 *
		 * @see View/Referents/add_edit.ctp
		 */
		var index = [];

		function format_approchant(text) {
			return text.toLowerCase().replace(/[àâä]/g, 'a').replace(/[éèêë]/g, 'e')
					.replace(/[ïî]/g, 'i').replace(/[ôö]/g, 'o').replace(/[ùüû]/g, 'u').replace('-', ' ');
		}

		$$('#SearchDernierreferentDernierreferentId option').each(function(option){
			index.push({
				value: option.getAttribute('value'),
				textlo: format_approchant(option.innerHTML),
				text: option.innerHTML
			});
		});

		$('SearchDernierreferentRecherche').observe('keypress', function(event){
			'use strict';
			var value = $('SearchDernierreferentRecherche').getValue(),
				regex = /^[a-zA-Z éèï\-ç]$/,
				i,
				newValue = ''
			;

			// Ajoute à la valeur du champ, la "lettre" utilisé
			if (regex.test(event.key)) {
				value += event.key;
			} else if (event.key === 'Backspace') {
				value = value.substr(0, value.length -1);
			}

			// Recherche la valeur à selectionner
			for (i=0; i<index.length; i++) {
				if (index[i].text.indexOf(value) >= 0) {
					newValue = index[i].value;
					break;
				} else if (index[i].textlo.toLowerCase().indexOf(format_approchant(value)) >= 0) {
					newValue = index[i].value;
				}
			}

			// Set de la valeur
			$('SearchDernierreferentDernierreferentId').setValue(newValue);
		});
	</script>