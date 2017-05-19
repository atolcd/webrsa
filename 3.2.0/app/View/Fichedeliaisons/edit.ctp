<?php
	echo $this->Default3->titleForLayout($this->request->data, compact('domain'));
	echo $this->FormValidator->generateJavascript();

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
		echo $this->Html->script( array( 'prototype.event.simulate.js', 'dependantselect.js' ), array( 'inline' => false ) );
	}
	
	echo $this->Default3->DefaultForm->create( null, array( 'novalidate' => 'novalidate', 'id' => 'FichedeliaisonAddForm' ) );

/***********************************************************************************
 * FORMULAIRE
/***********************************************************************************/
	
	echo '<fieldset>'
		. $this->Default3->subform(
			$this->Translator->normalize(
				array(
					'Fichedeliaison.id' => array('type' => 'hidden'),
					'Fichedeliaison.direction' => array('type' => 'radio'),
					'Fichedeliaison.expediteurexterne_id' => array(
						'empty' => true,
						'label' => __m('Fichedeliaison.expediteur_id').' (Hors DASAD)',
						'options' => $servicesExterne,
						'required' => true,
					),
					'Fichedeliaison.expediteurinterne_id' => array(
						'empty' => true,
						'label' => __m('Fichedeliaison.expediteur_id').' (DASAD)',
						'options' => $servicesInterne,
						'required' => true,
					),
					'Fichedeliaison.destinataireexterne_id' => array(
						'empty' => true,
						'label' => __m('Fichedeliaison.destinataire_id').' (Hors DASAD)',
						'options' => $servicesExterne,
						'required' => true,
					),
					'Fichedeliaison.destinataireinterne_id' => array(
						'empty' => true,
						'label' => __m('Fichedeliaison.destinataire_id').' (DASAD)',
						'options' => $servicesInterne,
						'required' => true,
					),
					'FichedeliaisonPersonne.personne_id' => array(
						'type' => 'select', 'multiple' => 'checkbox', 'options' => $concerne, 'fieldset' => true, 'required' => false
					),
					'Fichedeliaison.datefiche' => array('type' => 'date', 'dateFormat' => 'DMY'),
					'Fichedeliaison.motiffichedeliaison_id' => array('empty' => true),
					'Fichedeliaison.envoiemail' => array('type' => 'radio'),
					
				)
			),
			array('options' => $options)
		)
		. $this->Xform->multipleCheckbox('Destinataireemail.a',
			array('Destinataireemail' => array('a' => $emailsServices))
		)
		. $this->Xform->multipleCheckbox('Destinataireemail.cc', array(
			'Destinataireemail' => array('cc' => $emailsServices)
		))
		. $this->Default3->subform(
			$this->Translator->normalize(
				array(
					'Fichedeliaison.traitementafaire',
					'Fichedeliaison.commentaire',
				)
			),
			array('options' => $options)
		)
		. '</fieldset>'
	;
	
/***********************************************************************************
 * FIN DU FORMULAIRE
/***********************************************************************************/
	
	echo $this->Default3->DefaultForm->buttons(array('Save', 'Cancel'));
	echo $this->Default3->DefaultForm->end();
	echo $this->Observer->disableFormOnSubmit('FichedeliaisonAddForm');
?>

<script type="text/javascript">
	// Choix de l'expediteur et destinataire en fonction de la direction de la fiche
	observeDisableElementsOnValues(
		[
			'FichedeliaisonExpediteurinterneId',
			'FichedeliaisonDestinataireexterneId'
		],
		[
			{
				element: 'FichedeliaisonDirectionInterneVersExterne',
				value: 'interne_vers_externe',
				operator: '!='
			},
			{
				element: 'FichedeliaisonDirectionExterneVersInterne',
				value: 'interne_vers_externe'
			}
		],
		true
	);
	
	// Choix de l'expediteur et destinataire en fonction de la direction de la fiche
	observeDisableElementsOnValues(
		[
			'FichedeliaisonExpediteurexterneId',
			'FichedeliaisonDestinataireinterneId'
		],
		[
			{
				element: 'FichedeliaisonDirectionExterneVersInterne',
				value: 'externe_vers_interne',
				operator: '!='
			},
			{
				element: 'FichedeliaisonDirectionInterneVersExterne',
				value: 'externe_vers_interne'
			}
		],
		true
	);
	
	// "Envoyer un email" se dégrise lors de la selection d'un destinataire
	observeDisableElementsOnValues(
		[
			'FichedeliaisonEnvoiemail0',
			'FichedeliaisonEnvoiemail1'
		],
		[
			{
				element: 'FichedeliaisonDestinataireexterneId',
				value: ''
			},
			{
				element: 'FichedeliaisonDestinataireinterneId',
				value: ''
			}
		],
		false,
		false
	);
	
	// Affichage de la liste A et CC si Envoyer un e-mail (notification) = Oui
	observeDisableElementsOnValues(
		[
			$('DestinataireemailA').up('fieldset'),
			$('DestinataireemailCc').up('fieldset')
		],
		[
			{
				element: 'FichedeliaisonEnvoiemail1',
				value: '1',
				operator: '!='
			},
			{
				element: 'FichedeliaisonEnvoiemail0',
				value: '0'
			}
		],
		true
	);
	
	function dependantSelectMultipleCheckboxElements(destinataire) {
		var value = destinataire.getValue();
		
		$('DestinataireemailA').up('fieldset').select('input[type="checkbox"]').each(function(input) {
			if (input.getAttribute('value').indexOf(value+'_') === 0) {
				input.up().show();
			} else {
				input.up().hide();
			}
		});
		$('DestinataireemailCc').up('fieldset').select('input[type="checkbox"]').each(function(input) {
			if (input.getAttribute('value').indexOf(value+'_') === 0) {
				input.up().show();
			} else {
				input.up().hide();
			}
		});
	}
	
	$('FichedeliaisonDestinataireexterneId').observe('change', function(event) {
		dependantSelectMultipleCheckboxElements(event.target);
	});
	$('FichedeliaisonDestinataireinterneId').observe('change', function(event) {
		dependantSelectMultipleCheckboxElements(event.target);
	});
</script>
