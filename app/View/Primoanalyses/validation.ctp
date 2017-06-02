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
	
	echo '<h3>Proposition</h3>';
	
	/**
	 *  Logiciels et/ou sites consultés
	 */
	echo '<table class="primoanalyses avis table-view">
		<caption>Logiciels et/ou sites consultés</caption>
		<thead><tr><th>&nbsp;</th><th>Date</th><th>Commentaire</th></thead>
		<tbody>';
		
	foreach ((array)Hash::get($this->request->data, 'Logicielprimo') as $key => $line) {
		$class = $key %2 === 0 ? 'odd' : 'even';
		echo '<tr class="'.$class.'">';
		
		echo '<th>'.$line['name'].'</th>';
		echo '<td>'.date_format(new DateTime($line['LogicielprimoPrimoanalyse']['consultation']), 'd/m/Y').'</td>';
		echo '<td>'.$line['LogicielprimoPrimoanalyse']['commentaire'].'</td>';
		
		echo '</tr>';
	}

	echo '</tbody></table>';
	
	/**
	 * Proposition
	 */
	echo $this->Default3->view(
		$this->request->data,
		array(
			'Primoanalyse.createdossierpcg' => array('type' => 'checkbox'),
			'Primoanalyse.propositionprimo_id' => array('empty' => true),
			'Primoanalyse.dateprimo' => array('type' => 'date', 'dateFormat' => 'DMY'),
			'Primoanalyse.commentaire' => array('type' => 'textarea'),
		),
		$defaultParams + array('th' => true, 'caption' => 'Proposition', 'class' => 'table-view')
	);
	
	/**
	 * Avis technique
	 */
	echo $this->Default3->view(
		$this->request->data,
		array(
			'Avistechniqueprimo.date' => array('type' => 'date', 'dateFormat' => 'DMY'),
			'Avistechniqueprimo.choix' => array('type' => 'radio'),
			'Avistechniqueprimo.commentaire' => array('type' => 'textarea'),
		),
		$defaultParams + array('th' => true, 'caption' => 'Avis technique', 'class' => 'table-view')
	);
	
/***********************************************************************************
 * FORMULAIRE
/***********************************************************************************/
	
	echo '<fieldset>'
		. $this->Default3->subform(
			array(
				'Validationprimo.id' => array('type' => 'hidden'),
				'Validationprimo.date' => array('type' => 'date', 'dateFormat' => 'DMY'),
				'Validationprimo.choix' => array('type' => 'radio'),
				'Validationprimo.commentaire' => array('type' => 'textarea'),
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
