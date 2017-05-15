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
	
	echo '<p class="remarque center"><strong>' . __m('intitule_haut_cui') . '</strong><br>' . __m('intitule_haut_text') . '</p>';
	
	echo $this->Default3->DefaultForm->create( null, array( 'novalidate' => 'novalidate', 'class' => 'CuiAddEdit', 'id' => 'CuiAddEditForm' ) );

/***********************************************************************************
 * Hidden
/***********************************************************************************/
	
	echo $this->Default3->subform(
			array(
				'Cui.id' => array( 'type' => 'hidden' ),
				'Cui.personne_id' => array( 'type' => 'hidden' ),
				'Cui.decision_cui' => array( 'type' => 'hidden' ),
				'Cui.partenairecui_id' => array( 'type' => 'hidden' ),
				'Cui.entreeromev3_id' => array( 'type' => 'hidden' ),
				'Adresse.id' => array( 'type' => 'hidden' ),
				'Partenairecui.id' => array( 'type' => 'hidden' ),
				'Partenairecui.adressecui_id' => array( 'type' => 'hidden' ),
				'Adresse.id' => array( 'type' => 'hidden' ),
				'Entreeromev3.id' => array( 'type' => 'hidden' ),
				'Personnecui.id' => array( 'type' => 'hidden' ),
				'Personnecui.civilite' => array( 'type' => 'hidden' ),
				'Personnecui.nomusage' => array( 'type' => 'hidden' ),
				'Personnecui.prenom1' => array( 'type' => 'hidden' ),
				'Personnecui.nomfamille' => array( 'type' => 'hidden' ),
				'Personnecui.prenom2' => array( 'type' => 'hidden' ),
				'Personnecui.prenom3' => array( 'type' => 'hidden' ),
				'Personnecui.villenaissance' => array( 'type' => 'hidden' ),
				'Personnecui.datenaissance' => array( 'type' => 'hidden' ),
				'Personnecui.nir' => array( 'type' => 'hidden' ),
				'Personnecui.numallocataire' => array( 'type' => 'hidden' ),
				'Personnecui.nationalite' => array( 'type' => 'hidden' ),
				'Personnecui.organismepayeur' => array( 'type' => 'hidden' ),
			),
			$defaultParams
		)
	;
	
/***********************************************************************************
 * Secteur
/***********************************************************************************/
	
	echo '<fieldset id="CuiSecteur"><legend>' . __m('Cui.secteur') . '</legend>'
		. $this->Default3->subform(
			array(
				'Cui.secteurmarchand' => array( 'empty' => true, 'type' => 'select' ),
				'Cui.numconventionindividuelle',
				'Cui.numconventionobjectif'				
			),
			$defaultParams
		) . '</fieldset>'
	;

/***********************************************************************************
 * L'Employeur
/***********************************************************************************/
	
	echo '<fieldset id="PartenairecuiEmployeur"><legend>' . __m('Partenairecui.employeur') . '</legend>'; 
	echo $this->Default3->subform(
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
			
		. $this->Default3->subform(
			array(
				'Adressecui.numvoie',
				'Adressecui.typevoie',
				'Adressecui.nomvoie',
				'Adressecui.complement',
				'Adressecui.codepostal',
				'Adressecui.commune',
				'Adressecui.numtel' => array( 'maxLength' => 14 ),
				'Adressecui.email',
				'Adressecui.numfax' => array( 'maxLength' => 14 ),
			),
			$defaultParams
		)
		.		'</fieldset>'
			
		. '</div><div class="twopart">'
			. '<fieldset class="last" id="PartenairecuiAdresseadministrative">'
			.		'<legend>' . __m('Partenairecui.adresseadministrative') . '</legend>'
			
		. $this->Default3->subform(
			array(
				'Adressecui.numvoie2',
				'Adressecui.typevoie2',
				'Adressecui.nomvoie2',
				'Adressecui.complement2',
				'Adressecui.codepostal2',
				'Adressecui.commune2',
				'Adressecui.numtel2' => array( 'maxLength' => 14 ),
				'Adressecui.email2',
				'Adressecui.numfax2' => array( 'maxLength' => 14 ),
			),
			$defaultParams
		)
			. '</fieldset></div>'
		.	'<fieldset id="CuiVoletdroit">'
		.		'<legend>' . __m('Cui.voletdroit') . '</legend>'
		. $this->Default3->subform(
			array(
				'Partenairecui.siret',
				'Partenairecui.naf' => array( 'empty' => true ),
				'Partenairecui.statut' => array( 'empty' => true ),
				'Partenairecui.effectif',
				'Partenairecui.organismerecouvrement' => array( 'empty' => true ),
				'Partenairecui.assurancechomage' => array( 'type' => 'radio', 'class' => 'uncheckable' ),
			),
			$defaultParams
		) 
		. '</fieldset>'
		. $this->Default3->subform(
			array(
				'Partenairecui.ajourversement' => array( 'type' => 'radio', 'class' => 'uncheckable add-parent-id' ),
			),
			$defaultParams
		)
		. '</fieldset>'
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
		. $this->Xform->fieldValue(__m('Personnecui.numallocataire'), Hash::get($this->request->data, 'Personnecui.numallocataire' ), false)
		. $this->Xform->fieldValue(__m('Personnecui.organismepayeur'), Hash::get($this->request->data, 'Personnecui.organismepayeur' ), false)
	;
	
	echo '<div class="input text"><span class="label">' . __m('Dossier.dtdemrsa') . '</span><span class="input">' . $personne['Dossier']['dtdemrsa'] . ' (' . $dtdemrsa->diff(new DateTime())->format('%y an(s) %m mois %d jours') . ')</span></div>'
		. '<div class="input text"><span class="label">' . __m('Dossier.date_entree_dispositif') . '</span><span class="input">' . $diffStr . '</span></div>'
		. '</fieldset>'
	;

/***********************************************************************************
 * SITUATION DU SALARIE AVANT LA SIGNATURE DE LA CONVENTION
/***********************************************************************************/
	
	echo '<fieldset id="CuiSituationsalarie"><legend>' . __m('Cui.situationsalarie') . '</legend>'
		. $this->Default3->subform(
			array(
				'Cui.niveauformation' => array( 'empty' => true, 'type' => 'select' ),
				'Cui.inscritpoleemploi' => array( 'type' => 'radio', 'class' => 'uncheckable' ),
				'Cui.sansemploi' => array( 'type' => 'radio', 'class' => 'uncheckable' ),
			),
			$defaultParams
		)
		. '<fieldset><legend>Le salarié est-il bénéficiaire</legend>'
		. $this->Default3->subform(
			array(
				'Cui.beneficiaire_ass' => array( 'empty' => true, 'type' => 'select' ),
				'Cui.beneficiaire_aah' => array( 'empty' => true, 'type' => 'select' ),
				'Cui.beneficiaire_ata' => array( 'empty' => true, 'type' => 'select' ),
				'Cui.beneficiaire_rsa' => array( 'empty' => true, 'type' => 'select' ),
			),
			$defaultParams
		)
		. '</fieldset>'
		. $this->Default3->subform(
			array(
				'Cui.majorationrsa' => array( 'type' => 'radio', 'class' => 'uncheckable add-parent-id' ),
				'Cui.rsadepuis' => array( 'type' => 'radio', 'class' => 'uncheckable add-parent-id' ),
				'Cui.travailleurhandicape' => array( 'type' => 'radio', 'class' => 'uncheckable' ),
			),
			$defaultParams
		) . '</fieldset>'
	;
	
/***********************************************************************************	
 * CONTRAT DE TRAVAIL
/***********************************************************************************/
	
	echo '<fieldset id="CuiContrattravail"><legend>' . __m('Cui.contrattravail') . '</legend>'
		. $this->Default3->subform(
			array(
				'Cui.typecontrat' => array( 'type' => 'radio', 'class' => 'uncheckable' ),
				'Cui.dateembauche' => array( 'dateFormat' => 'DMY', 'minYear' => '2009', 'maxYear' => date('Y')+4 ),
				'Cui.findecontrat' => array( 'dateFormat' => 'DMY', 'minYear' => '2009', 'maxYear' => date('Y')+4 ),
			),
			$defaultParams
		)
		. $this->Default3->subform(
			array(
				'Cui.salairebrut' => array( 'type' => 'text', 'class' => 'euros' ),
				'Cui.dureehebdo' => array( 'class' => 'heures_minutes' ),
				'Cui.modulation' => array( 'type' => 'radio', 'class' => 'uncheckable' ),
				'Cui.dureecollectivehebdo' => array( 'class' => 'heures_minutes' ),
			),
			$defaultParams
		) . '</fieldset>'
	;
	
/***********************************************************************************	
 * LES ACTIONS D'ACCOMPAGNEMENT ET DE FORMATION PRÉVUES
/***********************************************************************************/
	
	echo '<fieldset id="CuiAccompagnement"><legend>' . __m('Cui.accompagnement') . '</legend>'
		. $this->Default3->subform(
			array(
				'Cui.nomtuteur',
				'Cui.fonctiontuteur',
				'Cui.organismedesuivi' => array( 'empty' => true ),
				'Cui.nomreferent',
				'Cui.actionaccompagnement' => array( 'type' => 'radio', 'class' => 'uncheckable' ),
			),
			$defaultParams
		) 
		. '<br /><h4>' . __m('Cui.small_title_accompagnement') . '</h4>'
		. $this->Default3->subform(
			array(
				'Cui.remobilisationemploi' => array( 'empty' => true ),
				'Cui.aidepriseposte' => array( 'empty' => true ),
				'Cui.elaborationprojet' => array( 'empty' => true ),
				'Cui.evaluationcompetences' => array( 'empty' => true ),
				'Cui.aiderechercheemploi' => array( 'empty' => true ),
				'Cui.autre' => array( 'empty' => true ),
				'Cui.autrecommentaire'
			),
			$defaultParams
		) 
		. '<br /><h4>' . __m('Cui.small_title_formation') . '</h4>'
		. $this->Default3->subform(
			array(
				'Cui.adaptationauposte' => array( 'empty' => true ),
				'Cui.remiseaniveau' => array( 'empty' => true ),
				'Cui.prequalification' => array( 'empty' => true ),
				'Cui.acquisitioncompetences' => array( 'empty' => true ),
				'Cui.formationqualifiante' => array( 'empty' => true ),
				'Cui.formation' => array( 'type' => 'radio', 'class' => 'uncheckable' ),
				'Cui.periodeprofessionnalisation' => array( 'type' => 'radio', 'class' => 'uncheckable' ),
				'Cui.niveauqualif' => array( 'empty' => true, 'type' => 'select' ),
				'Cui.validationacquis' => array( 'type' => 'radio', 'class' => 'uncheckable' ),
			),
			$defaultParams
		) . '</fieldset>'
	;
	
/***********************************************************************************
 * SI CAE - PERIODE IMMERSION ?
/***********************************************************************************/
	
	echo $this->Default3->subform(array(
		'Cui.periodeimmersion' => array( 'type' => 'radio', 'class' => 'uncheckable add-parent-id' ),
		),
		$defaultParams
	);
		
/***********************************************************************************
 * LA PRISE EN CHARGE (CADRE RÉSERVÉ AU PRESCRIPTEUR)
/***********************************************************************************/
	
	echo '<fieldset id="CuiPrise_en_charge"><legend>' . __m('Cui.prise_en_charge') . '</legend>'
		. $this->Default3->subform(
			array(
				'Cui.effetpriseencharge' => array( 'empty' => true, 'dateFormat' => 'DMY', 'minYear' => '2009', 'maxYear' => date('Y')+4 ),
				'Cui.finpriseencharge' => array( 'empty' => true, 'dateFormat' => 'DMY', 'minYear' => '2009', 'maxYear' => date('Y')+4 ),
				'Cui.decisionpriseencharge' => array( 'empty' => true, 'dateFormat' => 'DMY', 'minYear' => '2009', 'maxYear' => date('Y')+4 ),
				'Cui.dureehebdoretenu' => array( 'class' => 'heures_minutes'),
				'Cui.operationspeciale',
				'Cui.tauxfixeregion' => array( 'type' => 'text', 'class' => 'percent'),
				'Cui.priseenchargeeffectif' => array( 'type' => 'text', 'class' => 'percent'),
				'Cui.exclusifcg' => array( 'type' => 'radio', 'class' => 'uncheckable' ),
				'Cui.tauxcg' => array( 'type' => 'text', 'class' => 'percent'),
				'Cui.organismepayeur' => array( 'type' => 'radio', 'class' => 'uncheckable' ),
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
		. $this->Default3->subform(
			array(
				'Cui.faitle' => array( 'dateFormat' => 'DMY', 'minYear' => '2009', 'maxYear' => date('Y')+4 ),
			),
			$defaultParams
		) 
		. '</fieldset>'
	;
	
/***********************************************************************************
 * FIN DU FORMULAIRE
/***********************************************************************************/
	
	echo $this->Default3->DefaultForm->buttons( array( 'Save', 'Cancel' ) );
	echo $this->Default3->DefaultForm->end();
	echo $this->Observer->disableFormOnSubmit( 'CuiAddEditForm' );
?>
<script type="text/javascript">
	/*global $, $$, $F, $break, Ajax, console, Event, Math, fieldId*/
	
	/**
	 * Converti le contenu de "heures H minutes" en minutes dans le champ caché
	 * @param {HTML} input
	 * @returns {void}
	 */
	function setDureeHebdo( input ){
		var dureeEnHeure = isNaN( parseInt( $F('heures' +  input.id ), 10 ) ) ? 0 : parseInt( $F('heures' +  input.id, 10 ) );
		var minutesRestante = isNaN( parseInt( $F('minutes' +  input.id ), 10 ) ) ? 0 : parseInt( $F('minutes' +  input.id, 10 ) );
		$( input.id ).value = Math.floor( dureeEnHeure * 60 + minutesRestante );
		
		var minutes = isNaN( parseInt( $F( input.id ), 10 ) ) ? 0 : parseInt( $F( input.id ), 10 );
		dureeEnHeure = Math.floor( minutes / 60 );
		minutesRestante = Math.floor( minutes - (dureeEnHeure * 60) );
		$('heures' +  input.id ).value = dureeEnHeure === 0 ? '' : dureeEnHeure;
		$('minutes' +  input.id ).value = minutesRestante === 0 ? '' : minutesRestante;
		
		if ( $F( input.id ) === '0' ) {
			$( input.id ).value = '';
		}
	}
	
	/**
	 * Cache les inputs de class heures_minutes et ajoute 2 inputs séparé par un H
	 * Rempli les inputs avec la valeur initiale en minutes vers "heures H minutes"
	 * Ajoute des evenements onchange sur ces derniers, qui lancent setDureeHebdo()
	 */
	$$('input.heures_minutes').each(function( input ){
		input.insert({after: '<input type="text" id="heures' + input.id + '" class="miniInput" /> H <input type="text" id="minutes' + input.id + '" class="miniInput" />'});
		var minutes = isNaN(parseInt($F(input.id)), 10) ? 0 : parseInt($F(input.id), 10);
		var dureeEnHeure = Math.floor( minutes / 60 );
		var minutesRestante = Math.floor( minutes - (dureeEnHeure * 60) );
		$('heures' + input.id).value = dureeEnHeure === 0 ? '' : dureeEnHeure;
		$('minutes' + input.id).value = minutesRestante === 0 ? '' : minutesRestante;
		
		Event.observe( $('heures' + input.id ), 'change', function(){ setDureeHebdo(input); } );
		Event.observe( $('minutes' + input.id ), 'change', function(){ setDureeHebdo(input); } );
		input.type = 'hidden';
	});
</script>