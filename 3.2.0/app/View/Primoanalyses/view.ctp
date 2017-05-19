<?php
	$defaultParams = array(
		'options' => $options,
		'th' => true,
		'class' => 'table-view'
	);

	echo $this->Default3->titleForLayout($this->request->data);
	echo $this->FormValidator->generateJavascript();

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
		echo $this->Html->script( array( 'prototype.event.simulate.js', 'dependantselect.js' ), array( 'inline' => false ) );
	}

/***********************************************************************************
 * INFORMATIONS
/***********************************************************************************/

	echo '<h3>Résumé de la fiche de liaison</h3>';
	
	echo $this->Default3->view(
		$this->request->data,
		$this->Translator->normalize(
			array(
				'Fichedeliaison.etat' => array('empty' => true),
				'Fichedeliaison.expediteur_id' => array('empty' => true),
				'Fichedeliaison.destinataire_id' => array('empty' => true),
				'FichedeliaisonPersonne.personne_id' => array(
					'type' => 'select', 'multiple' => 'checkbox', 'options' => $concerne, 'fieldset' => true
				),
				'Fichedeliaison.datefiche' => array('type' => 'date', 'dateFormat' => 'DMY'),
				'Fichedeliaison.motiffichedeliaison_id' => array('empty' => true),
				'Fichedeliaison.envoiemail' => array('type' => 'radio'),
				'Destinataireemail.a' => array(
					'type' => 'select', 'multiple' => 'checkbox', 'options' => $emailsServices, 'fieldset' => true
				),
				'Destinataireemail.cc' => array(
					'type' => 'select', 'multiple' => 'checkbox', 'options' => $emailsServices, 'fieldset' => true
				),
				'Fichedeliaison.commentaire' => array('type' => 'textarea'),
			)
		),	
		$defaultParams + array('caption' => 'Fiche de liaison')
	);

	echo $this->Default3->view(
		$this->request->data,
		$this->Translator->normalize(
			array(
				'Avistechniquefiche.date' => array('type' => 'date', 'dateFormat' => 'DMY'),
				'Avistechniquefiche.choix' => array('type' => 'radio'),
				'Avistechniquefiche.commentaire' => array('type' => 'textarea'),
			)
		),
		$defaultParams + array('caption' => 'Avis technique')
	);

	echo $this->Default3->view(
		$this->request->data,
		$this->Translator->normalize(
			array(
				'Validationfiche.date' => array('type' => 'date', 'dateFormat' => 'DMY'),
				'Validationfiche.choix' => array('type' => 'radio'),
				'Validationfiche.commentaire' => array('type' => 'textarea'),
			)
		),
		$defaultParams + array('caption' => 'Validation')
	);
	
	echo '<h3>Proposition</h3>';
	
	/**
	 * Primoanalyse
	 */
	echo $this->Default3->view(
		$this->request->data,
		$this->Translator->normalize(
			array(
				'Primoanalyse.etat' => array('empty' => true),
				'Primoanalyse.created' => array('type' => 'date', 'dateFormat' => 'DMY'),
				'Primoanalyse.modified' => array('type' => 'date', 'dateFormat' => 'DMY'),
			)
		),
		$defaultParams + array('caption' => 'Primoanalyse')
	);
	
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
		$this->Translator->normalize(
			array(
				'Primoanalyse.createdossierpcg' => array('type' => 'checkbox'),
				'Primoanalyse.propositionprimo_id' => array('empty' => true),
				'Primoanalyse.dateprimo' => array('type' => 'date', 'dateFormat' => 'DMY'),
				'Primoanalyse.commentaire' => array('type' => 'textarea'),
			)
		),
		$defaultParams + array('caption' => 'Proposition')
	);
	
	/**
	 * Avis technique
	 */
	echo $this->Default3->view(
		$this->request->data,
		$this->Translator->normalize(
			array(
				'Avistechniqueprimo.date' => array('type' => 'date', 'dateFormat' => 'DMY'),
				'Avistechniqueprimo.choix' => array('type' => 'radio'),
				'Avistechniqueprimo.commentaire' => array('type' => 'textarea'),
			)
		),
		$defaultParams + array('caption' => 'Avis technique')
	);
	
	/**
	 * Validation
	 */
	echo $this->Default3->view(
		$this->request->data,
		$this->Translator->normalize(
			array(
				'Validationprimo.date' => array('type' => 'date', 'dateFormat' => 'DMY'),
				'Validationprimo.choix' => array('type' => 'radio'),
				'Validationprimo.commentaire' => array('type' => 'textarea'),
			)
		),
		$defaultParams + array('caption' => 'Validation')
	);
	
	/**
	 * Vu
	 */
	echo $this->Default3->view(
		$this->request->data,
		$this->Translator->normalize(
			array(
				'Primoanalyse.actionvu' => array('type' => 'radio'),
				'Primoanalyse.datevu' => array('type' => 'date', 'dateFormat' => 'DMY'),
				'Primoanalyse.commentairevu' => array('type' => 'textarea'),
			)
		),
		$defaultParams + array('caption' => 'Vu')
	);
	
	/**
	 * A faire
	 */
	echo $this->Default3->view(
		$this->request->data,
		$this->Translator->normalize(
			array(
				'Primoanalyse.actionafaire' => array('type' => 'radio'),
				'Primoanalyse.dateafaire' => array('type' => 'date', 'dateFormat' => 'DMY'),
				'Primoanalyse.commentaireafaire' => array('type' => 'textarea'),
			)
		),
		$defaultParams + array('caption' => 'A faire')
	);
	
	echo $this->Xhtml->link(
		'Retour',
		array('controller' => 'fichedeliaisons', 'action' => 'index', $foyer_id)
	);