<?php
	$departement = Configure::read( 'Cg.departement' );
	$controller = $this->params->controller;
	$action = $this->action;
	$formId = ucfirst($controller) . ucfirst($action) . 'Form';
	$availableDomains = WebrsaTranslator::domains();
	$domain = isset( $availableDomains[0] ) ? $availableDomains[0] : $controller;
	$paramDate = array(
		'domain' => null,
		'minYear_from' => date( 'Y' ),
		'maxYear_from' => date( 'Y' ) + 1,
		'minYear_to' => date( 'Y' ),
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
	echo '<fieldset id="CohorteReferentPreremplissage" style="display: '.(isset( $results ) ? 'block' : 'none').';"><legend>' . __m( 'Referent.preremplissage_fieldset' ) . '</legend>'
	. $this->Default3->subform(
		array(
			'Cohorte.PersonneReferent.selection' => array( 'type' => 'checkbox' ),
			'Cohorte.PersonneReferent.structurereferente_id' => array( 'type' => 'select', 'options' => $options['PersonneReferent']['structurereferente_id'], 'empty' => true ),
			'Cohorte.PersonneReferent.referent_id' => array( 'type' => 'select', 'value' => $options['PersonneReferent']['referent_id']),
			'Cohorte.PersonneReferent.dddesignation' => array( 'type' => 'date', 'dateFormat'=>'DMY', 'maxYear' => date('Y')+1, 'minYear' => date('Y')),
		),
		array(
			'options' => array( 'Cohorte' => $options )
		)
	)
	. '<div class="center"><input type="button" id="preremplissage_cohorte_button" value="'.__m( 'Referent.preremplir' ).'"/></div>'
	. '</fieldset>'
;
	$this->start( 'custom_after_results' );
	echo $this->Form->button( 'Tout cocher', array( 'type' => 'button', 'onclick' => "return toutCocherAction( 'input.input[type=checkbox]' );" ) ) . ' ';
	echo $this->Form->button( 'Tout décocher', array( 'type' => 'button', 'onclick' => "return toutDecocherAction( 'input.input[type=checkbox]' );" ) );

	$this->end();


	$explAction = substr($action, (strpos($action, '_')+1));
	$exportcsvActionName = isset($explAction) ? 'exportcsv_'.$explAction : 'exportcsv';
	echo $this->element(
		'ConfigurableQuery/cohorte',
		array(
			'afterResults' => $this->fetch( 'custom_after_results' ),
			'exportcsv' => array( 'action' => $exportcsvActionName ),
			'modelName' => 'Personne'
		)
	);
	$results = isset( $results ) ? $results : array();

?>

<script type="text/javascript">

	document.observe("dom:loaded", function() {
		//On désactive le bouton d'enregistrement au chargement de la page
		document.querySelector('input[value="Enregistrer"]').disabled = true;

		// Dépendance des champs
		dependantSelect( 'CohortePersonneReferentReferentId', 'CohortePersonneReferentStructurereferenteId' );
		document.querySelectorAll('table.referents.cohorte_ajout > tbody > tr').forEach( function(el, ind) {
			dependantSelect( 'Cohorte'+ind+'PersonneReferentReferentId', 'CohortePersonneReferentStructurereferenteId' );
			dependantSelect( 'Cohorte'+ind+'PersonneReferentReferentId', 'Cohorte'+ind+'PersonneReferentStructurereferenteId' );
		});

		// Vérification de la possibilité d'enregistrer
		document.querySelectorAll('input[type="checkbox"]').forEach( (checkbox) => {
			checkbox.addEventListener('change', function() {
				checkSaveButton();
			});
		});
	});

	function toutCocherAction( selecteur, simulate ) {
		toutCocher( selecteur, simulate );
		document.querySelector('input[value="Enregistrer"]').disabled = false;
		return false;
	}

	function toutDecocherAction( selecteur, simulate ) {
		toutDecocher( selecteur, simulate );
		document.querySelector('input[value="Enregistrer"]').disabled = true;
		return false;
	}

	$('preremplissage_cohorte_button').observe('click', function(){
		$('CohorteReferentPreremplissage').select('input[type="checkbox"], select, textarea').each(function(editable){
			var matches = editable.name.match(/^data\[Cohorte\]\[([\w]+)\]\[([\w]+)\](?:\[([\w]+)\]){0,1}$/),
				regex;

			// Cas date
			if ( matches.length === 4 && matches[3] !== undefined ) {
				 regex = new RegExp('^data\\[Cohorte\\]\\[[\\d]+\\]\\['+matches[1]+'\\]\\['+matches[2]+'\\]\\['+matches[3]+'\\]$');
			}
			else {
				 regex = new RegExp('^data\\[Cohorte\\]\\[[\\d]+\\]\\['+matches[1]+'\\]\\['+matches[2]+'\\]$');
			}

			$$('form input[type="checkbox"], select, textarea').each(function(editable_cohorte){
				if ( regex.test(editable_cohorte.name) ) {
					editable_cohorte.setValue(editable.getValue());
				}
			});
		});
		checkSaveButton();
	});

	// Test si le bouton Enregistrer doit être activé ou non
	function checkSaveButton() {
		let checkboxes = document.querySelectorAll('input[type="checkbox"]');
		let nbChecked = 0;
		checkboxes.forEach( (el) => {
			if( el.name.indexOf("data[Cohorte]") != -1 && el.checked) {
				nbChecked ++;
			}
			if(nbChecked > 0) {
				document.querySelector('input[value="Enregistrer"]').disabled = false;
			} else {
				document.querySelector('input[value="Enregistrer"]').disabled = true;
			}
		});
	}

</script>