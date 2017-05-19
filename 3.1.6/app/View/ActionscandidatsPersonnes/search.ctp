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
		'ActioncandidatPersonne' => array('datesignature' => $dateRule)
	);
	echo $this->FormValidator->generateJavascript($dates, false);

	echo '<fieldset><legend>' . __m( 'ActioncandidatPersonne.search' ) . '</legend>'
		. $this->Default3->subform(
			array(
				'Search.Contactpartenaire.partenaire_id' => array( 'empty' => true ),
				'Search.ActioncandidatPersonne.actioncandidat_id' => array( 'empty' => true ),
				'Search.ActioncandidatPersonne.referent_id' => array( 'empty' => true ),
				'Search.ActioncandidatPersonne.positionfiche' => array( 'empty' => true ),
			),
			array( 'options' => array( 'Search' => $options ), 'domain' => $domain )
		) 
		. $this->Allocataires->SearchForm->dateRange( 'Search.ActioncandidatPersonne.datesignature', $paramDate )
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
<script type="text/javascript">
	document.observe("dom:loaded", function() {
		dependantSelect( 'SearchActioncandidatPersonneActioncandidatId', 'SearchContactpartenairePartenaireId' );
	});
</script>