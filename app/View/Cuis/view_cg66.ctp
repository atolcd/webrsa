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
	
	echo '<div class="Cui66AddEdit">';

/***********************************************************************************
 * Choix du formulaire
/***********************************************************************************/
	
	echo '<fieldset><legend id="Cui66Choixformulaire">' . __m('Cui66.choixformulaire') . '</legend>'
		. $this->Default3->subformView(
			array(
				'Cui66.typeformulaire',
				'Cui66.renouvellement',
			),
			$defaultParams
		) . '</fieldset>'
	;
	
/***********************************************************************************
 * Secteur
/***********************************************************************************/	
	
	echo '<fieldset id="CuiSecteur"><legend>' . __m('Cui.secteur') . '</legend>'
		. $this->Default3->subformView(
			array(
				'Cui.secteurmarchand',
				'Cui66.typecontrat',
				'Cui66.codecdiae',
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
				'Adressecui.canton',
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
				'Adressecui.canton2',
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
		. '</fieldset>'
		. '<fieldset id="Partenairecui66Informationssup">'
		.	 '<legend>' . __m('Partenairecui66.informationssup') . '</legend>'
		. $this->Default3->subformView(
			array(
				'Partenairecui66.codepartenaire',
				'Partenairecui66.objet',
				'Partenairecui66.nomtitulairerib',
				'Partenairecui66.codebanque',
				'Partenairecui66.codeguichet',
				'Partenairecui66.numerocompte',
				'Partenairecui66.etablissementbancaire',
				'Partenairecui66.clerib',
				'Partenairecui66.nblits',
				'Partenairecui66.nbcontratsaideshorscg',
				'Partenairecui66.nbcontratsaidescg',
			),
			$defaultParams
		)
		. '</fieldset>'
		. $this->Default3->subformView(
			array(
				'Partenairecui.ajourversement',
			),
			$defaultParams
		)
		. '</fieldset>'
	;
	
/***********************************************************************************
 * DOSSIER RECU/ELIGIBLE/COMPLET
/***********************************************************************************/
	
	echo '<fieldset id="Cui66Dossier"><legend>' . __m('Cui66.dossier') . '</legend>'
		. $this->Default3->subformView(
			array(
				'Cui66.dossierrecu',
				'Cui66.datereception' => array( 'type' => 'date', 'dateFormat' => 'DMY' ),
				'Cui66.dossiereligible',
				'Cui66.dateeligibilite' => array( 'type' => 'date', 'dateFormat' => 'DMY' ),
				'Cui66.dossiercomplet',
				'Cui66.datecomplet' => array( 'type' => 'date', 'dateFormat' => 'DMY' ),
				'Cui66.notedossier' => array( 'type' => 'textarea' ),
			),
			$defaultParams
		) . '</fieldset>'
	;

	/**
	 * Condition d'affichage : le dossier doit être complet et l'e-mail envoyé pour avoir la suite
	 */
if ( !in_array($this->request->data['Cui66']['etatdossiercui66'], array( 'attentepiece', 'dossierrecu', 'dossiereligible' )) ){
	
/***********************************************************************************
 * LE SALARIÉ
/***********************************************************************************/
	// On prépare les informations
	$dtnai = new DateTime( $personne['Personne']['dtnai'] );
	$dtdemrsa = new DateTime( $personne['Dossier']['dtdemrsa'] );
	$personne['Personne']['dtnai'] = date_format($dtnai, 'd/m/Y');
	$personne['Dossier']['dtdemrsa'] = date_format($dtdemrsa, 'd/m/Y');
	$personne['Adresse']['complete'] = $personne['Adresse']['numvoie'] . ' ' . $personne['Adresse']['libtypevoie'] . ' ' . $personne['Adresse']['nomvoie'] . '<br />';
	$personne['Adresse']['complete'] .= $personne['Adresse']['complideadr'] !== null ? $personne['Adresse']['complideadr'] . '<br>' : '';
	$personne['Adresse']['complete'] .= $personne['Adresse']['compladr'] !== null ? $personne['Adresse']['compladr'] . '<br />' : '';
	$personne['Adresse']['complete'] .= $personne['Adresse']['lieudist'] !== null ? $personne['Adresse']['lieudist'] . '<br />' : '';
	$personne['Adresse']['complete'] .= $personne['Adresse']['codepos'] . ' ' . $personne['Adresse']['nomcom'];
	$diffMonth = floor((time() - strtotime(date_format($dtdemrsa, 'Y-m-d'))) / 60 / 60 / 24 / (365 / 12));
	$diffMonth < 6 && $diffStr = 'moins de 6 mois';
	$diffMonth >= 6 && $diffMonth < 11 && $diffStr = 'de 6 à 11 mois';
	$diffMonth >= 11 && $diffMonth < 23 && $diffStr = 'de 12 à 23 mois';
	$diffMonth >= 24 && $diffStr = '24 et plus';
	
	$darkLabelGauche = array(
		array( __m('Personne.nom' ), $personne['Personne']['nom'] ),
		array( __m('Personne.dtnai' ), $personne['Personne']['dtnai'] ),
		array( __m('Personne.nomcomnai' ), $personne['Personne']['nomcomnai'] ),
		array( __m('Adresse.complete' ), $personne['Adresse']['complete'] ),
	);
	$darkLabelDroit = array(		
		array( __m('Personne.prenom' ), $personne['Personne']['prenom'] ),
		array( __m('Personne.nir' ), $personne['Personne']['nir'] ),
		array( __m('Departement.name' ), $personne['Departement']['name'] ),
		array( __m('Adresse.canton' ), $personne['Adresse']['canton'] ),	
		array( __m('Personne.nati' ), $personne['Personne']['nati'] ),
		array( __m('Referentparcours.nom_complet' ), $personne['Referentparcours']['nom_complet'] ),	
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
	
	echo '</div>' . $this->Default3->subformView(
			array(
				'Cui66.zonecouverte',
				'Cui66.datefinsejour' => array( 'type' => 'date', 'dateFormat' => 'DMY'  ),
			),
			$defaultParams
		)
		. $this->Xform->fieldValue('Dossier.matricule', $personne['Dossier']['matricule'])
		. $this->Xform->fieldValue('Dossier.fonorg', $personne['Dossier']['fonorg'])
		. '<div class="input text"><span class="label">' . __m('Cui66.coupleenfant') . '</span>'
		. '<span class="input">' . __m('Couple.enfants_' . $this->request->data['Cui66']['encouple'] . '_' . $this->request->data['Cui66']['avecenfant']) . '</span></div></fieldset>'
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
				'Cui.beneficiairede',
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
		. $this->Romev3->fieldsetView( 'Entreeromev3', $defaultParams )
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

}	

/***********************************************************************************
 * DATE
/***********************************************************************************/
	
	echo '<fieldset id="CuiDate"><legend>' . __m('Cui.date') . '</legend>'
		. $this->Default3->subformView(
			array(
				'Cui.faitle' => array( 'type' => 'date', 'dateFormat' => 'DMY' ),
				'Cui66.datebutoir' => array( 'type' => 'date', 'dateFormat' => 'DMY' ),
				'Cui66.demandeenregistree' => array( 'type' => 'date', 'dateFormat' => 'DMY' )
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