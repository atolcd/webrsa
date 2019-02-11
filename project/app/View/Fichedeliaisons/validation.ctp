<?php
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
		array('options' => $options, 'th' => true, 'caption' => 'Fiche de liaison', 'class' => 'table-view')
	);

	echo $this->Default3->view(
		$this->request->data,
		array(
			'Avistechniquefiche.date' => array('type' => 'date', 'dateFormat' => 'DMY'),
			'Avistechniquefiche.choix' => array('type' => 'radio', 'class'=>"uncheckable"),
			'Avistechniquefiche.commentaire' => array('type' => 'textarea'),
		),
		array('options' => $options, 'th' => true, 'caption' => 'Avis technique', 'class' => 'table-view')
	);
	
/***********************************************************************************
 * FORMULAIRE
/***********************************************************************************/
	
	echo '<fieldset>'
		. $this->Default3->subform(
			array(
				'Validationfiche.id' => array('type' => 'hidden'),
				'Fichedeliaison.envoiemail' => array('type' => 'hidden'),	// Pour décider ou pas l'envoi de l'email
				'Fichedeliaison.dateenvoiemail' => array('type' => 'hidden'),
				'Validationfiche.date' => array('type' => 'date', 'dateFormat' => 'DMY'),
				'Validationfiche.choix' => array('type' => 'radio'),
				'Validationfiche.commentaire' => array('type' => 'textarea'),
			),
			array('options' => $options)
		) . '</fieldset>'
	;
	
/***********************************************************************************
 * FIN DU FORMULAIRE
/***********************************************************************************/
	
	echo $this->Default3->DefaultForm->buttons(array('Save', 'Cancel'));
	echo $this->Default3->DefaultForm->end();
	echo $this->Observer->disableFormOnSubmit('FichedeliaisonAddForm');
