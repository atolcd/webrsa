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
	$paramAllocataire = array(
		'options' => $options,
		'prefix' => 'Search',
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
	
	echo '<fieldset><legend>' . __m( 'Dossierpcg66.search_gestionnaire' ) . '</legend>'
		. $this->Xform->multipleCheckbox( 'Search.Dossierpcg66.poledossierpcg66_id', $options )
		. $this->Xform->multipleCheckbox( 'Search.Dossierpcg66.user_id', $options, 'divideInto3Columns' )
		. $this->Allocataires->SearchForm->dateRange( 'Search.Dossierpcg66.dateaffectation', $paramDate )
		. $this->Xform->multipleCheckbox( 'Search.Dossierpcg66.etatdossierpcg', $options, 'divideInto2Columns' )
		. $this->Xform->multipleCheckbox( 'Search.Decisiondossierpcg66.org_id', $options, 'divideInto2Columns' )
		. $this->Default3->subform(
			array(
				'Search.Dossierpcg66.originepdo_id' => array( 'empty' => true ),
				'Search.Dossierpcg66.typepdo_id' => array( 'empty' => true ),
				'Search.Dossierpcg66.orgpayeur' => array( 'empty' => true ),
			),
			array( 'options' => array( 'Search' => $options ), 'domain' => $domain )
		)
		. $this->Xform->multipleCheckbox( 'Search.Decisiondossierpcg66.decisionpdo_id', $options, 'divideInto2Columns' )
		. $this->Xform->multipleCheckboxToutCocher( 'Search.Traitementpcg66.situationpdo_id', $options, 'divideInto2Columns' )
		. $this->Xform->multipleCheckboxToutCocher( 'Search.Traitementpcg66.statutpdo_id', $options, 'divideInto2Columns' )
		. $this->Default3->subform(
			array(
				'Search.Dossierpcg66.dossierechu' => array( 'type' => 'checkbox' )
			),
			array( 'options' => array( 'Search' => $options ), 'domain' => $domain )
		)	
		. '</fieldset>'
	;

	$this->end();
	
	echo $this->element(
		'ConfigurableQuery/search',
		array(
			'customSearch' => $this->fetch( 'custom_search_filters' )
		)
	);