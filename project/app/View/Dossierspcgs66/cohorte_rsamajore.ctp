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
		'Traitementpcg66' => array('dateecheance' => $dateRule),
		'Statutpdo' => array('Statutpdo' => $notEmptyRule),
		'Tag' => array(
			'etat' => $notEmptyRule,
			'calcullimite' => $notEmptyRule,
			'limite' => $dateRule
		),
	);
	echo $this->FormValidator->generateJavascript($validationCohorte, false);

	$this->start( 'custom_search_filters' );

	if( Configure::read( 'CG.cantons' ) ) {
		echo $this->Xform->multipleCheckbox( 'Search.Zonegeographique.id', $options, 'divideInto2Columns' );
	}

	echo $this->Xform->multipleCheckbox( 'Search.Prestation.rolepers', $options, 'divideInto2Columns' );
	echo $this->Xform->multipleCheckbox( 'Search.Foyer.composition', $options, 'divideInto2Columns' );

	echo '<fieldset><legend>' . __m( 'Tag.cohorte_fieldset' ) . '</legend>'
		. $this->Default3->subform(
			array(
				'Search.Foyer.nb_enfants' => array( 'empty' => true ),
				'Search.Detailcalculdroitrsa.mtrsavers' => array( 'empty' => true ),
				'Search.Requestmanager.name' => array( 'empty' => true ),
			),
			array(
				'options' => array( 'Search' => $options )
			)
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
			'modelName' => 'Dossier'
		)
	);

	$results = isset($results) ? $results : array();

	foreach ($results as $i => $result) {
		echo $this->Observer->disableFieldsOnValue(
			"Cohorte.{$i}.Dossierpcg66.create",
			array(
				"Cohorte.{$i}.Dossierpcg66.orgpayeur",
				"Cohorte.{$i}.Statutpdo.Statutpdo",
				"Cohorte.{$i}.Traitementpcg66.typetraitement",
				"Cohorte.{$i}.Traitementpcg66.affiche_couple",
				"Cohorte.{$i}.Modeletraitementpcg66.commentaire",
				"Cohorte.{$i}.Traitementpcg66.dureeecheance",
				"Cohorte.{$i}.Traitementpcg66.imprimer",
			),
			'1',
			false,
			false
		);
	
		echo $this->Observer->disableFieldsOnValue(
			"Cohorte.{$i}.Dossierpcg66.selection",
			array(
				"Cohorte.{$i}.Dossierpcg66.create",
				"Cohorte.{$i}.Dossierpcg66.orgpayeur",
				"Cohorte.{$i}.Statutpdo.Statutpdo",
				"Cohorte.{$i}.Traitementpcg66.typetraitement",
				"Cohorte.{$i}.Traitementpcg66.affiche_couple",
				"Cohorte.{$i}.Modeletraitementpcg66.commentaire",
				"Cohorte.{$i}.Traitementpcg66.dureeecheance",
				"Cohorte.{$i}.Traitementpcg66.imprimer",
				"Cohorte.{$i}.Tag.etat",
				"Cohorte.{$i}.Tag.calcullimite",
				"Cohorte.{$i}.Tag.limite.day",
				"Cohorte.{$i}.Tag.limite.month",
				"Cohorte.{$i}.Tag.limite.year",
				"Cohorte.{$i}.Tag.commentaire",
			),
			'1',
			false,
			false
		);

		echo $this->Observer->disableFieldsOnValue(
			"Cohorte.{$i}.Traitementpcg66.dureeecheance",
			array(
				"Cohorte.{$i}.Traitementpcg66.dateecheance.day",
				"Cohorte.{$i}.Traitementpcg66.dateecheance.month",
				"Cohorte.{$i}.Traitementpcg66.dateecheance.year"
			),
			'0',
			true,
			false
		);
		?>
		<script type="text/javascript">
			Event.observe( $('Cohorte<?php echo $i?>TagCalcullimite'), 'change', function() {
				setDateCloture('Cohorte<?php echo $i?>TagCalcullimite', 'Cohorte.<?php echo $i?>.Tag.limite');
			});

			Event.observe( $('Cohorte<?php echo $i?>Traitementpcg66Dureeecheance'), 'change', function() {
				setDateCloture('Cohorte<?php echo $i?>Traitementpcg66Dureeecheance', 'Cohorte.<?php echo $i?>.Traitementpcg66.dateecheance');
			});
			
			Event.observe( $('Cohorte<?php echo $i?>Dossierpcg66Selection'), 'change', function() {
				var value = $('Cohorte<?php echo $i?>Dossierpcg66Selection').checked ? 1 : 0;
				
				$('Cohorte<?php echo $i?>Dossierpcg66Create').setValue(value);
				$('Cohorte<?php echo $i?>TagEtat').setValue(value ? 'traite' : 'encours');
			});
			
			Event.observe( $('Cohorte<?php echo $i?>Dossierpcg66Create'), 'change', function() {
				$('Cohorte<?php echo $i?>TagEtat').setValue($('Cohorte<?php echo $i?>Dossierpcg66Create').getValue() === '1' ? 'traite' : 'encours');
			});
		</script>
	<?php
	}