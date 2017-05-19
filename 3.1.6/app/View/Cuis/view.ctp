<?php
	// Donne le domain du plus haut niveau de précision (prefix, action puis controller)
	$domain = current(WebrsaTranslator::domains());
	$defaultParams = compact('options', 'domain');

	echo $this->Default3->titleForLayout($this->request->data, compact('domain'));

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
		echo $this->Html->script( array( 'prototype.event.simulate.js', 'dependantselect.js' ), array( 'inline' => false ) );
	}
	
	echo '<p class="remarque center"><strong>' . __m('intitule_haut_cui') . '</strong><br>' . __m('intitule_haut_text') . '</p>';
	
	echo '<div class="CuiAddEdit">';
	
/***********************************************************************************
 * Secteur
/***********************************************************************************/	
	
	echo '<fieldset id="CuiSecteur"><legend>' . __m('Cui.secteur') . '</legend>'
		. $this->Default3->subformView(
			array(
				'Cui.secteurmarchand',
				'Cui.numconventionindividuelle',
				'Cui.numconventionobjectif',			
			),
			$defaultParams
		) . '</fieldset>'
	;

/***********************************************************************************
 * L'Employeur
/***********************************************************************************/
	
	echo '<fieldset id="PartenairecuiEmployeur"><legend>' . __m('Partenairecui.employeur') . '</legend>'; 
	echo $this->Default3->subformView(
			array(
				'Partenairecui.raisonsociale',
				'Partenairecui.enseigne',
			),
			$defaultParams
		) 
		. '<div class="twopart"></div><div class="twopart"><p class="remarque">' 
			. __m('Partenairecui.remarque')
			. '</p></div>'
		. '<div class="twopart">'	
			. '<fieldset class="first" id="PartenairecuiAdresseemployeur">'
			.		'<legend>' . __m('Partenairecui.adresseemployeur') . '</legend>'
			
		. $this->Default3->subformView(
			array(
				'Adressecui.numvoie',
				'Adressecui.typevoie',
				'Adressecui.nomvoie',
				'Adressecui.complement',
				'Adressecui.codepostal',
				'Adressecui.commune',
				'Adressecui.numtel',
				'Adressecui.email',
				'Adressecui.numfax',
			),
			$defaultParams
		)
		.		'</fieldset>'
			
		. '</div><div class="twopart">'
			. '<fieldset class="last" id="PartenairecuiAdresseadministrative">'
			.		'<legend>' . __m('Partenairecui.adresseadministrative') . '</legend>'
			
		. $this->Default3->subformView(
			array(
				'Adressecui.numvoie2',
				'Adressecui.typevoie2',
				'Adressecui.nomvoie2',
				'Adressecui.complement2',
				'Adressecui.codepostal2',
				'Adressecui.commune2',
				'Adressecui.numtel2',
				'Adressecui.email2',
				'Adressecui.numfax2',
			),
			$defaultParams
		)
			. '</fieldset></div>'
		.	'<fieldset id="CuiVoletdroit">'
		.		'<legend>' . __m('Cui.voletdroit') . '</legend>'
		. $this->Default3->subformView(
			array(
				'Partenairecui.siret',
				'Partenairecui.naf',
				'Partenairecui.statut',
				'Partenairecui.effectif',
				'Partenairecui.organismerecouvrement',
				'Partenairecui.assurancechomage',
			),
			$defaultParams
		) 
		. '</fieldset></fieldset>'
	;

/***********************************************************************************
 * LE SALARIÉ
/***********************************************************************************/
	// On prépare les informations
	$dtnai = new DateTime( Hash::get($this->request->data, 'Personnecui.datenaissance' ) );
	$dtdemrsa = new DateTime( $personne['Dossier']['dtdemrsa'] );
	$personne['Personne']['dtnai'] = date_format($dtnai, 'd/m/Y');
	$personne['Dossier']['dtdemrsa'] = date_format($dtdemrsa, 'd/m/Y');
	switch ( $dif = floor((time() - strtotime(date_format($dtdemrsa, 'Y-m-d'))) / 60 / 60 / 24 / (365 / 12)) ) {
		case $dif < 6: $diffStr = 'moins de 6 mois'; break;
		case $dif <= 11: $diffStr = 'de 6 à 11 mois'; break;
		case $dif <= 23: $diffStr = 'de 12 à 23 mois'; break;
		default: $diffStr = '24 et plus'; break;
	}
	
	$darkLabelGauche = array(
		array( __m('Personne.nom' ), Hash::get($this->request->data, 'Personnecui.nomusage' ) ),
		array( __m('Personne.dtnai' ), date_format($dtnai, 'd/m/Y') ),
		array( __m('Personne.nomcomnai' ), Hash::get($this->request->data, 'Personnecui.villenaissance' ) ),
	);
	$darkLabelDroit = array(		
		array( __m('Personne.prenom' ), Hash::get($this->request->data, 'Personnecui.prenom1' ) ),
		array( __m('Personne.nir' ), Hash::get($this->request->data, 'Personnecui.nir' ) ),
	);
	
	// On affiche les informations
	echo '<fieldset id="PersonneInfo"><legend>' . __m('Personne.info') . '</legend><div class="twopart">';
	
	foreach($darkLabelGauche as $value){
		echo '<div class="input value"><label class="little dark label">' . $value[0] . '</label><p class="dark label value">' . $value[1] . '</p></div>';
	}
	
	echo '</div><div class="twopart">';
	
	foreach($darkLabelDroit as $value){
		echo '<div class="input value"><label class="little dark label">' . $value[0] . '</label><p class="dark label value">' . $value[1] . '</p></div>';
	}
	
	echo '</div>'
		. $this->Xform->fieldValue('Dossier.matricule', $personne['Dossier']['matricule'])
		. $this->Xform->fieldValue('Dossier.fonorg', $personne['Dossier']['fonorg'])
		. '</fieldset>'
	;

/***********************************************************************************
 * SITUATION DU SALARIE AVANT LA SIGNATURE DE LA CONVENTION
/***********************************************************************************/
	
	echo '<fieldset id="CuiSituationsalarie"><legend>' . __m('Cui.situationsalarie') . '</legend>'
		. $this->Default3->subformView(
			array(
				'Cui.niveauformation',
				'Cui.inscritpoleemploi',
				'Cui.sansemploi',
			),
			$defaultParams
		)
		. '<fieldset><legend>Le salarié est-il bénéficiaire</legend>'
		. $this->Default3->subformView(
			array(
				'Cui.beneficiaire_ass',
				'Cui.beneficiaire_aah',
				'Cui.beneficiaire_ata',
				'Cui.beneficiaire_rsa',
			),
			$defaultParams
		)
		. '</fieldset>'
		. $this->Default3->subformView(
			array(
				'Cui.majorationrsa',
				'Cui.rsadepuis',
				'Cui.travailleurhandicape',
			),
			$defaultParams
		) . '</fieldset>'
	;
	
/***********************************************************************************	
 * CONTRAT DE TRAVAIL
/***********************************************************************************/
	
	echo '<fieldset id="CuiContrattravail"><legend>' . __m('Cui.contrattravail') . '</legend>'
		. $this->Default3->subformView(
			array(
				'Cui.typecontrat',
				'Cui.dateembauche' => array( 'type' => 'date', 'dateFormat' => 'DMY' ),
				'Cui.findecontrat' => array( 'type' => 'date', 'dateFormat' => 'DMY' ),
			),
			$defaultParams
		)
		. $this->Default3->subformView(
			array(
				'Cui.salairebrut',
				'Cui.dureehebdo' => array( 'hidden' => true ),
				'Cui.modulation',
				'Cui.dureecollectivehebdo' => array( 'hidden' => true ),
			),
			$defaultParams
		) . '</fieldset>'
	;
	
/***********************************************************************************	
 * LES ACTIONS D'ACCOMPAGNEMENT ET DE FORMATION PRÉVUES
/***********************************************************************************/
	
	echo '<fieldset id="CuiAccompagnement"><legend>' . __m('Cui.accompagnement') . '</legend>'
		. $this->Default3->subformView(
			array(
				'Cui.nomtuteur',
				'Cui.fonctiontuteur',
				'Cui.organismedesuivi',
				'Cui.nomreferent',
				'Cui.actionaccompagnement',
			),
			$defaultParams
		) 
		. '<br /><h4>' . __m('Cui.small_title_accompagnement') . '</h4>'
		. $this->Default3->subformView(
			array(
				'Cui.remobilisationemploi',
				'Cui.aidepriseposte',
				'Cui.elaborationprojet',
				'Cui.evaluationcompetences',
				'Cui.aiderechercheemploi',
				'Cui.autre',
				'Cui.autrecommentaire',
			),
			$defaultParams
		) 
		. '<br /><h4>' . __m('Cui.small_title_formation') . '</h4>'
		. $this->Default3->subformView(
			array(
				'Cui.adaptationauposte',
				'Cui.remiseaniveau',
				'Cui.prequalification',
				'Cui.acquisitioncompetences',
				'Cui.formationqualifiante',
				'Cui.formation',
				'Cui.periodeprofessionnalisation',
				'Cui.niveauqualif',
				'Cui.validationacquis',
			),
			$defaultParams
		) . '</fieldset>'
	;
	
/***********************************************************************************
 * SI CAE - PERIODE IMMERSION ?
/***********************************************************************************/
	
	echo $this->Default3->subformView(array(
		'Cui.periodeimmersion',
		),
		$defaultParams
	);
		
/***********************************************************************************
 * LA PRISE EN CHARGE (CADRE RÉSERVÉ AU PRESCRIPTEUR)
/***********************************************************************************/
	
	echo '<fieldset id="CuiPrise_en_charge"><legend>' . __m('Cui.prise_en_charge') . '</legend>'
		. $this->Default3->subformView(
			array(
				'Cui.effetpriseencharge' => array( 'type' => 'date', 'dateFormat' => 'DMY' ),
				'Cui.finpriseencharge' => array( 'type' => 'date', 'dateFormat' => 'DMY' ),
				'Cui.decisionpriseencharge' => array( 'type' => 'date', 'dateFormat' => 'DMY' ),
				'Cui.dureehebdoretenu',
				'Cui.operationspeciale',
				'Cui.tauxfixeregion',
				'Cui.priseenchargeeffectif',
				'Cui.exclusifcg',
				'Cui.tauxcg',
				'Cui.organismepayeur',
				'Cui.intituleautreorganisme',
				'Cui.adressautreorganisme',
			),
			$defaultParams
		) . '</fieldset>'
	;

/***********************************************************************************
 * DATE
/***********************************************************************************/
	
	echo '<fieldset id="CuiDate"><legend>' . __m('Cui.date') . '</legend>'
		. $this->Default3->subformView(
			array(
				'Cui.faitle' => array( 'type' => 'date', 'dateFormat' => 'DMY' ),
			),
			$defaultParams
		) 
		. '</fieldset>'
	;
	
/***********************************************************************************
 * FIN DU FORMULAIRE
/***********************************************************************************/

	echo '<br />' . $this->Default->button(
		'back',
		array(
			'controller' => 'cuis',
			'action'     => 'index',
			$personne_id
		),
		array(
			'id' => 'Back',
			'class' => 'aere'
		)
	);
	
	echo '</div>';