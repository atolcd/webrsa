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
				'Fichedeliaison.dateenvoiemail' => array('type' => 'date', 'dateFormat' => 'DMY'),
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

	echo $this->Xhtml->link(
		'Retour',
		array('action' => 'index', $foyer_id)
	);