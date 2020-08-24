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

	 // Permet d'ajouter les blocs Zone Géographique, Rôle personne et Composition du foyer
	/* if( Configure::read( 'CG.cantons' ) ) {
		echo $this->Xform->multipleCheckbox( 'Search.Zonegeographique.id', $options, 'divideInto2Columns' );
	}

	echo $this->Xform->multipleCheckbox( 'Search.Prestation.rolepers', $options, 'divideInto2Columns' );
	echo $this->Xform->multipleCheckbox( 'Search.Foyer.composition', $options, 'divideInto2Columns' );
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

	document.observe("dom:loaded", function() {
		dependantSelect( 'SearchRendezvousPermanenceId', 'SearchRendezvousStructurereferenteId' );
		// Ajoute la couleur rouge si il n'y a pas de numéro de téléphone
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
		// Désactive le bouton enregistrer par défaut
		document.querySelector('input[value="Enregistrer"]').disabled = true;
		document.querySelectorAll('input[type="checkbox"]').forEach( (el) => {
			el.addEventListener('change', checkSaveButton);
		});
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