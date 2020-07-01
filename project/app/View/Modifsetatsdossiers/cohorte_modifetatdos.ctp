<?php
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

	// Ajoute les boutons tout cocher / décocher sous le bouton Enregistrer
	$this->start( 'custom_after_results' );
	echo $this->Form->button( __d('default','Toutcocher'), array( 'type' => 'button', 'onclick' => "return toutCocherAction( 'input.input[type=checkbox]' );" ) ) . ' ';
	echo $this->Form->button( __d('default','Toutdecocher'), array( 'type' => 'button', 'onclick' => "return toutDecocherAction( 'input.input[type=checkbox]' );" ) );
	$this->end();

	// Ajoute le menu de préremplissage de la cohorte
	echo '<fieldset id="CohorteDossierPreremplissage" style="display: '.(isset( $results ) ? 'block' : 'none').';"><legend>' . __m( 'Modifsetatsdossiers.preremplissage_fieldset' ) . '</legend>'
	. $this->Default3->subform(
		array(
			'Cohorte.Dossier.selection' => array( 'type' => 'checkbox' ),
			'Cohorte.Dossier.nouveletatdos' => array( 'type' => 'select', 'empty' => true ),
			'Cohorte.Dossier.motif' => array( 'type' => 'select', 'empty' => true ),
		),
		array(
			'options' => array( 'Cohorte' => $options )
		)
	)
	. '<div class="center"><input type="button" id="preremplissage_cohorte_button" value="'.__m( 'Dossier.preremplir' ).'"/></div>'
	. '</fieldset>'
;

	// Permet d'ajouter l'export CSV à côté de l'impression
	$explAction = substr($action, (strpos($action, '_')+1));
	$exportcsvActionName = isset($explAction) ? 'exportcsv_'.$explAction : 'exportcsv';
	echo $this->element(
		'ConfigurableQuery/cohorte',
		array(
			//'customSearch' => $this->fetch( 'custom_search_filters' ),
			'afterResults' => $this->fetch( 'custom_after_results' ),
			'exportcsv' => array( 'action' => $exportcsvActionName),
			'modelName' => 'Dossier'
		)
	);

	$results = isset( $results ) ? $results : array();
?>

<script type="text/javascript">

	// Désactive les listes déroulantes à l'initialisation de la cohorte
<?php
	foreach ($results as $i => $value) {
?>
		observeDisableElementsOnValues(
			[
				'Cohorte<?php echo $i;?>DossierNouveletatdos',
				'Cohorte<?php echo $i;?>DossierMotif',
			],
			{element: 'Cohorte<?php echo $i;?>DossierSelection', value: '1', operator: '!='}
		);
<?php
	}
?>

	// Gestion du préremplissage
	$('preremplissage_cohorte_button').observe('click', function(){
		$('CohorteDossierPreremplissage').select('input[type="checkbox"], select, textarea').each(function(editable){
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

			if(editable.id == 'CohorteDossierSelection') {
				if(editable.checked == true) {
					toutCocherAction( 'input.input[type=checkbox]' );
				} else {
					toutDecocherAction( 'input.input[type=checkbox]' );
				}
			}
		});
	});

	// Désactive le bouton enregistrer par défaut et supprime l'innerText des dossiers vérouillés
	document.addEventListener('DOMContentLoaded', (e) => {
		document.querySelector('input[value="<?php echo __d('default', 'Save') ?>"]').disabled = true;
		document.querySelectorAll('input[type="checkbox"]').forEach( (el) => {
			el.addEventListener('change', checkSaveButton);
		});

		document.querySelectorAll('td.dossier_locked').forEach( (el) => {
			el.innerText = '';
		});

	});

	// Fonction permettant de tout cocher
	function toutCocherAction( selecteur, simulate ) {
		toutCocher( selecteur, simulate );
<?php
	foreach ($results as $i => $value) {
?>
		disableElementsOnValues(
			[
				'Cohorte<?php echo $i;?>DossierNouveletatdos',
				'Cohorte<?php echo $i;?>DossierMotif',
			],
			{element: 'Cohorte<?php echo $i;?>DossierSelection', value: '1', operator: '!='}
		);
<?php
	}
?>
		document.querySelector('input[value="<?php echo __d('default', 'Save') ?>"]').disabled = false;
		return false;
	}

	// Fonction permettant de tout cocher
	function toutDecocherAction( selecteur, simulate ) {
		toutDecocher( selecteur, simulate );
<?php
	foreach ($results as $i => $value) {
?>
		disableElementsOnValues(
			[
				'Cohorte<?php echo $i;?>DossierNouveletatdos',
				'Cohorte<?php echo $i;?>DossierMotif',
			],
			{element: 'Cohorte<?php echo $i;?>DossierSelection', value: '1', operator: '!='}
		);
<?php
	}
?>
		document.querySelector('input[value="<?php echo __d('default', 'Save') ?>"]').disabled = true;
		return false;
	}

	// Test si le bouton Enregistrer doit être activé ou non
	function checkSaveButton() {
		let checkboxes = document.querySelectorAll('input[type="checkbox"]');
		let nbChecked = 0;
		checkboxes.forEach( (el) => {
			if( el.name.indexOf("data[Cohorte]") != -1 && el.checked) {
				nbChecked ++;
			}
			if(nbChecked > 0) {
				document.querySelector('input[value="<?php echo __d('default', 'Save') ?>"]').disabled = false;
			} else {
				document.querySelector('input[value="<?php echo __d('default', 'Save') ?>"]').disabled = true;
			}
		});

}

</script>