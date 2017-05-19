<?php
	// Donne le domain du plus haut niveau de précision (prefix, action puis controller)
	$domain = current(WebrsaTranslator::domains());
	$defaultParams = compact('options', 'domain');

	echo $this->Default3->titleForLayout($this->request->data, compact('domain'));
	echo $this->FormValidator->generateJavascript();

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
		echo $this->Html->script( array( 'prototype.event.simulate.js', 'dependantselect.js' ), array( 'inline' => false ) );
	}
	
	echo $this->Default3->DefaultForm->create( null, array( 'novalidate' => 'novalidate', 'id' => 'FichedeliaisonAddForm' ) );

/***********************************************************************************
 * INFORMATIONS
/***********************************************************************************/

	echo '<h3>Résumé de la fiche de liaison</h3>';
	
	echo $this->Default3->view(
		$this->request->data,
		array(
			'Fichedeliaison.expediteur_id' => array('empty' => true, 'before' => '<tr><th colspan="2">Test</th><tr>'),
			'Fichedeliaison.destinataire_id' => array('empty' => true),
			'FichedeliaisonPersonne.personne_id' => array(
				'type' => 'select', 'multiple' => 'checkbox', 'options' => $concerne, 'fieldset' => true
			),
			'Fichedeliaison.datefiche' => array('type' => 'date', 'dateFormat' => 'DMY'),
			'Fichedeliaison.motiffichedeliaison_id' => array('empty' => true),
			'Fichedeliaison.commentaire' => array('type' => 'textarea'),
		),
		$defaultParams + array('th' => true, 'caption' => 'Fiche de liaison', 'class' => 'table-view')
	);

	echo $this->Default3->view(
		$this->request->data,
		array(
			'Avistechniquefiche.date' => array('type' => 'date', 'dateFormat' => 'DMY'),
			'Avistechniquefiche.choix' => array('type' => 'radio'),
			'Avistechniquefiche.commentaire' => array('type' => 'textarea'),
		),
		$defaultParams + array('th' => true, 'caption' => 'Avis technique', 'class' => 'table-view')
	);

	echo $this->Default3->view(
		$this->request->data,
		array(
			'Validationfiche.date' => array('type' => 'date', 'dateFormat' => 'DMY'),
			'Validationfiche.choix' => array('type' => 'radio'),
			'Validationfiche.commentaire' => array('type' => 'textarea'),
		),
		$defaultParams + array('th' => true, 'caption' => 'Validation', 'class' => 'table-view')
	);
	
/***********************************************************************************
 * FORMULAIRE
/***********************************************************************************/
	
	echo '<fieldset>'
		. $this->Default3->subform(
			array(
				'Primoanalyse.id' => array('type' => 'hidden'),
				'Primoanalyse.dateaffectation' => array('type' => 'date', 'dateFormat' => 'DMY'),
				'Primoanalyse.user_id' => array('empty' => true),
			),
			$defaultParams
		) . '</fieldset>'
	;
	
/***********************************************************************************
 * FIN DU FORMULAIRE
/***********************************************************************************/
	
	echo $this->Default3->DefaultForm->buttons(array('Save', 'Cancel'));
	echo $this->Default3->DefaultForm->end();
	echo $this->Observer->disableFormOnSubmit('FichedeliaisonAddForm');
