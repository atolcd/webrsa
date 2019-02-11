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
	$dateRule = array(
		'date' => array(
			'rule' => array('date'),
			'message' => null,
			'required' => null,
			'allowEmpty' => true,
			'on' => null
		)
	);
	
	$this->start( 'custom_search_filters' );
	
	$dates = array(
		'Dossier' => array('dtdemrsa' => $dateRule),
		'Personne' => array('dtnai' => $dateRule),
		'Dossierpcg66' => array('dateaffectation' => $dateRule),
		'Traitementpcg66' => array(
			'dateecheance' => $dateRule,
			'daterevision' => $dateRule,
			'created' => $dateRule,
		),
	);
	echo $this->FormValidator->generateJavascript($dates, false);
	
	echo '<fieldset><legend>' . __m( 'Traitementpcg66.search' ) . '</legend>'
		. $this->Xform->multipleCheckbox( 'Search.Dossierpcg66.poledossierpcg66_id', $options )
		. $this->Xform->multipleCheckbox( 'Search.Dossierpcg66.user_id', $options, 'divideInto3Columns' )
		. $this->Allocataires->SearchForm->dateRange( 'Search.Dossierpcg66.dateaffectation', $paramDate )
		. $this->Allocataires->SearchForm->dateRange( 'Search.Traitementpcg66.dateecheance', $paramDate )
		. $this->Allocataires->SearchForm->dateRange( 'Search.Traitementpcg66.daterevision', $paramDate )
		. $this->Allocataires->SearchForm->dateRange( 'Search.Traitementpcg66.created', $paramDate )
		. $this->Xform->multipleCheckboxToutCocher( 'Search.Traitementpcg66.situationpdo_id', $options, 'divideInto2Columns' )
		. $this->Xform->multipleCheckboxToutCocher( 'Search.Traitementpcg66.statutpdo_id', $options, 'divideInto2Columns' )
		. $this->Default3->subform(
			array(
				'Search.Traitementpcg66.descriptionpdo_id' => array( 'empty' => true ),
				'Search.Traitementpcg66.typetraitement' => array( 'empty' => true ),
				'Search.Traitementpcg66.clos' => array( 'empty' => true ),
				'Search.Traitementpcg66.annule' => array( 'empty' => true ),
				'Search.Traitementpcg66.regime' => array( 'empty' => true ),
				'Search.Traitementpcg66.saisonnier' => array( 'empty' => true ),
				'Search.Traitementpcg66.nrmrcs',
				'Search.Fichiermodule.exists' => array( 'empty' => true ),
				'Search.Traitementpcg66.etattraitementpcg' => array( 'empty' => true, 'escape' => false ),
			),
			array( 'options' => array( 'Search' => $options ), 'domain' => $domain )
		)
		. '</fieldset>'
	;
	
	$this->end();

	echo $this->element(
		'ConfigurableQuery/search',
		array(
			'customSearch' => $this->fetch( 'custom_search_filters' ),
			'exportcsv' => array( 'action' => 'exportcsv' )
		)
	);
?>