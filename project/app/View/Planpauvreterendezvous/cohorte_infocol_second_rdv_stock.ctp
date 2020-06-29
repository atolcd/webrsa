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
	$this->start( 'custom_search_filters' );
	/**
	 * FILTRES CUSTOM
	 */

	echo "<fieldset><legend>" . __m( 'Search.Historiquedroit' ) . "</legend>";
	echo $this->SearchForm->dateRange( 'Search.Historiquedroit.created', array(
		'domain' => 'planpauvreterendezvous',
		'minYear_from' => 2009,
		'minYear_to' => 2009,
		'maxYear_from' => date( 'Y' ) + 1,
		'maxYear_to' => date( 'Y' ) + 1,
	) );
	echo '</fieldset>';
	echo "<fieldset><legend>" . __m( 'Search.Rendezvous' ) . "</legend>";
	echo $this->Form->input( 'Search.Rendezvous.structurereferente_id', array( 'label' => __m( 'Search.Rendezvous.structurereferente_id' ), 'type' => 'select', 'options' => $options['PersonneReferent']['structurereferente_id'], 'empty' => true ) );
	echo $this->Form->input( 'Search.Rendezvous.permanence_id', array( 'label' => __m( 'Search.Rendezvous.permanence_id' ), 'type' => 'select', 'options' => $options['Rendezvous']['permanence_id'], 'empty' => true ) );
	echo $this->SearchForm->dateRange( 'Search.Rendezvous.daterdv', array(
		'domain' => 'rendezvous', // FIXME
		'minYear_from' => 2009,
		'minYear_to' => 2009,
		'maxYear_from' => date( 'Y' ) + 1,
		'maxYear_to' => date( 'Y' ) + 1,
	) );

	echo $this->SearchForm->timeRange( 'Search.Rendezvous.heurerdv', array(
		'domain' => 'rendezvous',
	) );
	echo '</fieldset>';
	$this->end();

	$this->start( 'custom_after_results' );
	echo $this->Form->button( 'Tout cocher', array( 'type' => 'button', 'onclick' => "return toutCocherAction( 'input.input[type=checkbox]' );" ) ) . ' ';
	echo $this->Form->button( 'Tout décocher', array( 'type' => 'button', 'onclick' => "return toutDecocherAction( 'input.input[type=checkbox]' );" ) );

	$this->end();

	echo '<fieldset id="CohorteRendezvousPreremplissage" style="display: '.(isset( $results ) ? 'block' : 'none').';"><legend>' . __m( 'Planpauvreterendezvous.preremplissage_fieldset' ) . '</legend>'
	. $this->Default3->subform(
		array(
			'Cohorte.Rendezvous.selection' => array( 'type' => 'checkbox' ),
			'Cohorte.Rendezvous.structurereferente_id' => array( 'type' => 'select', 'options' => $options['Rendezvous']['structurereferente_id'], 'empty' => true ),
			'Cohorte.Rendezvous.referent_id' => array( 'type' => 'select', 'options' => $options['Rendezvous']['referent_id'], 'empty' => true ),
			'Cohorte.Rendezvous.permanence_id' => array( 'type' => 'select', 'options' => $options['Rendezvous']['permanence_id'], 'empty' => true ),
			'Cohorte.Rendezvous.typerdv_id' => array( 'type' => 'text', 'value' => $options['Rendezvous']['typerdv_id']['Typerdv']['libelle'], 'readonly' => 'readonly'),
			'Cohorte.Rendezvous.statutrdv_id' => array( 'type' => 'text', 'value' => $options['Rendezvous']['statutrdv_id']['Statutrdv']['libelle'], 'readonly' => 'readonly' ),
			'Cohorte.Rendezvous.daterdv' => array( 'type' => 'date', 'dateFormat'=>'DMY', 'maxYear' => date('Y')+1, 'minYear' => date('Y')),
			'Cohorte.Rendezvous.heurerdv' => array( 'type' => 'time', 'timeFormat' => '24','minuteInterval'=> 5,  'empty' => true, 'min' => '08', 'max' => '19', 'hourRange' => array( 8, 19 ), 'style' => 'margin-bottom: 0.5em;'),
			'Cohorte.Rendezvous.objetrdv' => array( 'type' => 'textarea', 'style' => 'margin-bottom: 1.5em;' ),
			'Cohorte.Rendezvous.commentairerdv' => array( 'type' => 'textarea' )
		),
		array(
			'options' => array( 'Cohorte' => $options )
		)
	)
	. '<div class="center"><input type="button" id="preremplissage_cohorte_button" value="'.__m( 'Planpauvreterendezvous.preremplir' ).'"/></div>'
	. '</fieldset>'
;


	$explAction = substr($action, (strpos($action, '_')+1));
	$exportcsvActionName = isset($explAction) ? 'exportcsv_'.$explAction : 'exportcsv';
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
		document.observe("dom:loaded", function() {
			dependantSelect( 'CohorteRendezvousPermanenceId', 'CohorteRendezvousStructurereferenteId' );
			dependantSelect( 'CohorteRendezvousReferentId', 'CohorteRendezvousStructurereferenteId' );
			document.querySelectorAll('table.planpauvreterendezvous > tbody > tr').forEach( function(el, ind) {
				dependantSelect( 'Cohorte'+ind+'RendezvousPermanenceId', 'CohorteRendezvousStructurereferenteId' );
				dependantSelect( 'Cohorte'+ind+'RendezvousPermanenceId', 'Cohorte'+ind+'RendezvousStructurereferenteId' );
				dependantSelect( 'Cohorte'+ind+'RendezvousReferentId', 'CohorteRendezvousStructurereferenteId' );
				dependantSelect( 'Cohorte'+ind+'RendezvousReferentId', 'Cohorte'+ind+'RendezvousStructurereferenteId' );
			});

			// Désactive le bouton enregistrer par défaut
			document.querySelector('input[value="Enregistrer"]').disabled = true;
			document.querySelectorAll('input[type="checkbox"]').forEach( (el) => {
				el.addEventListener('change', checkSaveButton);
			});
		});
<?php
	foreach ($results as $i => $value) {
?>
		observeDisableElementsOnValues(
			[
/* 				'Cohorte<?php //echo $i;?>RendezvousDaterdvDay',
				'Cohorte<?php //echo $i;?>RendezvousDaterdvMonth',
				'Cohorte<?php //echo $i;?>RendezvousDaterdvYear' */
			],
			{element: 'Cohorte<?php echo $i;?>RendezvousSelection', value: '1', operator: '!='}
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
/* 				'Cohorte<?php //echo $i;?>RendezvousDaterdvDay',
				'Cohorte<?php //echo $i;?>RendezvousDaterdvMonth',
				'Cohorte<?php //echo $i;?>RendezvousDaterdvYear' */
			],
			{element: 'Cohorte<?php echo $i;?>RendezvousSelection', value: '1', operator: '!='}
		);
		<?php
		}
		?>
		document.querySelector('input[value="Enregistrer"]').disabled = false;
		return false;
	}

	function toutDecocherAction( selecteur, simulate ) {
		toutDecocher( selecteur, simulate );
		<?php
			foreach ($results as $i => $value) {
		?>
		disableElementsOnValues(
			[
/* 				'Cohorte<?php //echo $i;?>RendezvousDaterdvDay',
				'Cohorte<?php //echo $i;?>RendezvousDaterdvMonth',
				'Cohorte<?php //echo $i;?>RendezvousDaterdvYear' */
			],
			{element: 'Cohorte<?php echo $i;?>RendezvousSelection', value: '1', operator: '!='}
		);
		<?php
			}
		?>
		document.querySelector('input[value="Enregistrer"]').disabled = true;
		return false;
	}

	$('preremplissage_cohorte_button').observe('click', function(){
		$('CohorteRendezvousPreremplissage').select('input[type="checkbox"], select, textarea').each(function(editable){
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
	});

	document.addEventListener('DOMContentLoaded', (e) => {
		let numtelCD = document.querySelectorAll('.numtelCD');
		let numtelCAF = document.querySelectorAll('.numtelCAF');
		for(let i=1; i<numtelCD.length; i++)
		{
			if ( ( numtelCAF[i].innerText === "" && numtelCD[i].innerText === "") || 
				( numtelCAF[i].innerText != numtelCD[i].innerText && numtelCAF[i].innerText !== "" && numtelCD[i].innerText !== "" )
			 ) {
				numtelCAF[i].style.backgroundColor = "red";
			}
		}
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