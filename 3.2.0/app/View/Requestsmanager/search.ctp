<?php
	$departement = Configure::read( 'Cg.departement' );
	$controller = $this->params->controller;
	$action = $this->action;
	$formId = ucfirst($controller) . ucfirst($action) . 'Form';
	$availableDomains = WebrsaTranslator::domains();
	$domain = isset( $availableDomains[0] ) ? $availableDomains[0] : $controller;
	echo $this->Default3->titleForLayout( array(), array( 'domain' => $domain ) );

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
		echo $this->Html->script( array( 'prototype.event.simulate.js', 'dependantselect.js' ), array( 'inline' => false ) );
	}

	if( isset( $title ) ) {
		echo $this->Xhtml->tag(	'h3', $title ).'<br>';
	}

	if( isset( $results ) ) {
		// V2
		echo $this->Default3->index(
			$results,
			$fields,
			array(
				'format' => $this->element( 'pagination_format' ),
				'options' => $options
			)
		);
	}

	echo $this->Xhtml->link(
		'Retour Editeur',
		array(
			'controller' => 'requestsmanager',
			'action' => 'index'
		),
		array(
			'title' => "Retour à l'Editeur de requêtes"
		)
	);