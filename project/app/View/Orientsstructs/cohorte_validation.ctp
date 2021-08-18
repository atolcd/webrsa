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

	// id du formulaire de cohorte
	$cohorteFormId = Inflector::camelize( "{$this->request->params['controller']}_{$this->request->params['action']}_cohorte" );

	$this->start( 'custom_search_filters' );
	/**
	 * FILTRES CUSTOM
	 */

	echo $this->Html->tag(
		'fieldset',
		$this->Html->tag( 'legend', __m('Orientation.propvalidation' ) )
		. $this->Form->input( 'Search.Orientstruct.origine', array( 'label' => __m( 'Search.Orientstruct.origine' ), 'type' => 'select', 'options' => $options['Orientstruct']['origine'], 'empty' => true ) )
		. $this->Form->input( 'Search.Orientstruct.structureorientante_id', array( 'label' => __m( 'Search.Orientstruct.structureorientante_id' ), 'type' => 'select', 'options' => $options['Orientstruct']['structureorientante_id'], 'empty' => true ) )
		. $this->SearchForm->dateRange( 'Search.Orientstruct.date_propo', array(
			'domain' => 'orientstruct',
			'minYear_from' => date( 'Y' ) - 3,
			'minYear_to' => date( 'Y' ) - 3,
			'maxYear_from' => date( 'Y' ) + 1,
			'maxYear_to' => date( 'Y' ) + 1
		))
	);

	$this->end();

	$this->start( 'custom_after_results' );
	echo $this->Form->button( __d('default', 'Toutcocher'), array( 'type' => 'button', 'onclick' => "return toutCocher( 'input.input[type=checkbox]' );" ) ) . ' ';
	echo $this->Form->button( __d('default', 'Toutdecocher'), array( 'type' => 'button', 'onclick' => "return toutDecocher( 'input.input[type=checkbox]' );" ) );
	echo '<br><br>';
	echo $this->Form->button( __d('default', 'Toutvalider'), array( 'type' => 'button', 'onclick' => "return toutChoisir( $( '{$cohorteFormId}' ).getInputs( 'radio' ), 'Orienté', true );" ) ) . ' ';
	echo $this->Form->button( __d('default', 'Toutrefuser'), array( 'type' => 'button', 'onclick' => "return toutChoisir( $( '{$cohorteFormId}' ).getInputs( 'radio' ), 'Refusé', true );" ) );
	$this->end();

	echo $this->element(
		'ConfigurableQuery/cohorte',
		array(
			'customSearch' => $this->fetch( 'custom_search_filters' ),
			'afterResults' => $this->fetch( 'custom_after_results' ),
			'exportcsv' => false,
			'modelName' => 'Personne'
		)
	);
	$results = isset( $results ) ? $results : array();
