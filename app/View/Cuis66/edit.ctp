<?php
	echo $this->FormValidator->generateJavascript();
	echo $this->Default3->titleForLayout();

	if( Configure::read( 'debug' ) > 0 ) {
		echo $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) );
		echo $this->Html->script( array( 'prototype.event.simulate.js', 'dependantselect.js' ), array( 'inline' => false ) );
	}
	
	echo '<p class="remarque center"><strong>' . __d('cuis66', 'intitule_haut_cui') . '</strong><br>' . __d('cuis66', 'intitule_haut_text') . '</p>';
	
	echo $this->Default3->DefaultForm->create( null, array( 'novalidate' => 'novalidate', 'class' => 'Cui66AddEdit', 'id' => 'CuiAddEditForm' ) );

/***********************************************************************************
 * Choix du formulaire
/***********************************************************************************/
	
	echo '<fieldset><legend id="Cui66Choixformulaire">' . __d('cuis66', 'Cui66.choixformulaire') . '</legend>'
		. $this->Default3->subform(
			array(
				'Cui.id' => array( 'type' => 'hidden' ),
				'Cui.personne_id' => array( 'type' => 'hidden' ),
				'Cui.partenairecui_id' => array( 'type' => 'hidden' ),
				'Cui.entreeromev3_id' => array( 'type' => 'hidden' ),
				'Cui66.id' => array( 'type' => 'hidden' ),
				'Cui66.cui_id' => array( 'type' => 'hidden' ),
				'Cui66.notifie' => array( 'type' => 'hidden' ),
				'Adresse.id' => array( 'type' => 'hidden' ),
				'Partenairecui.id' => array( 'type' => 'hidden' ),
				'Partenairecui.adressecui_id' => array( 'type' => 'hidden' ),
				'Partenairecui66.id' => array( 'type' => 'hidden' ),
				'Partenairecui66.partenairecui_id' => array( 'type' => 'hidden' ),
				'Adresse.id' => array( 'type' => 'hidden' ),
				'Entreeromev3.id' => array( 'type' => 'hidden' ),
				'Cui66.encouple' => array( 'type' => 'hidden' ),
				'Cui66.avecenfant' => array( 'type' => 'hidden' ),
				'Cui66.etatdossiercui66' => array( 'type' => 'hidden' ),
				'Cui66.typeformulaire' => array( 'empty' => true ),
				'Cui66.renouvellement',
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
				'Personnecui66.id' => array( 'type' => 'hidden' ),
				'Personnecui66.adressecomplete' => array( 'type' => 'hidden' ),
				'Personnecui66.canton' => array( 'type' => 'hidden' ),
				'Personnecui66.departement' => array( 'type' => 'hidden' ),
				'Personnecui66.referent' => array( 'type' => 'hidden' ),
				'Personnecui66.nbpersacharge' => array( 'type' => 'hidden' ),
				'Personnecui66.dtdemrsa' => array( 'type' => 'hidden' ),
				'Personnecui66.montantrsa' => array( 'type' => 'hidden' ),
			),
			array( 'options' => $options )
		) . '</fieldset>'
	;
	
/***********************************************************************************
 * Secteur
/***********************************************************************************/
	
	// Ajoute un typecontrat au select si le typecontrat stocké en base n'est plus actif
	$id_typecontrat = !empty( $this->request->data['Cui66']['typecontrat'] ) ? $this->request->data['Cui66']['typecontrat'] : null;
	if ( $id_typecontrat !== null && !isset( $options['Cui66']['typecontrat_actif'][$id_typecontrat] ) ){
		$options['Cui66']['typecontrat_actif'][$id_typecontrat] = $options['Cui66']['typecontrat'][$id_typecontrat];
	}
	
	echo '<fieldset id="CuiSecteur"><legend>' . __d('cuis66', 'Cui.secteur') . '</legend>'
		. $this->Default3->subform(
			array(
				'Cui.secteurmarchand' => array( 'empty' => true, 'type' => 'select' ),
				'Cui66.typecontrat' => array( 'empty' => true, 'options' => $options['Cui66']['typecontrat_actif'] ),
				'Cui66.codecdiae',
				'Cui.numconventionindividuelle',
				'Cui.numconventionobjectif'				
			),
			array( 'options' => $options )
		) . '</fieldset>'
	;

/***********************************************************************************
 * L'Employeur
/***********************************************************************************/
	
	echo '<fieldset id="PartenairecuiEmployeur"><legend>' . __d('cuis66', 'Partenairecui.employeur') . '</legend>'; 
	echo '<fieldset><legend>' . __d('cuis66', 'Partenaire.charger') . '</legend>'
		. $this->Default3->subform(
			array(
				'Cui.partenaire_id' => array( 'empty' => true, 'type' => 'select' ),
			),
			array( 'options' => $options )
		) 
		. '<div class="submit"><input type="button" id="PartenaireCharger" value="Charger" /></div></fieldset>'
		. $this->Default3->subform(
			array(
				'Partenairecui.raisonsociale',
				'Partenairecui.enseigne',
				'Partenairecui66.responsable',
				'Partenairecui66.telresponsable',
			),
			array( 'options' => $options )
		) 
		. '<div class="twopart"></div><div class="twopart"><p class="remarque">' 
			. __d( 'cuis66', 'Partenairecui.remarque')
			. '</p><input type="checkbox" id="PartenairecuiUtiliseradradministrative" /><label for="PartenairecuiUtiliseradradministrative">'
			. __d( 'cuis66', 'Partenairecui.utiliseradradministrative') . '</label></div>'
		. '<div class="twopart">'	
			. '<fieldset class="first" id="PartenairecuiAdresseemployeur">'
			.		'<legend>' . __d('cuis66', 'Partenairecui.adresseemployeur') . '</legend>'
			
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
				'Adressecui.canton' => array( 'empty' => true ),
				'Partenairecui66.conseillerdep'
			),
			array( 'options' => $options )
		)
		.		'</fieldset>'
			
		. '</div><div class="twopart">'
			. '<fieldset class="last" id="PartenairecuiAdresseadministrative">'
			.		'<legend>' . __d('cuis66', 'Partenairecui.adresseadministrative') . '</legend>'
			
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
				'Adressecui.canton2' => array( 'empty' => true ),
			),
			array( 'options' => $options )
		)
			. '</fieldset></div>'
		.	'<fieldset id="CuiVoletdroit">'
		.		'<legend>' . __d('cuis66', 'Cui.voletdroit') . '</legend>'
		. $this->Default3->subform(
			array(
				'Partenairecui.siret',
				'Partenairecui.naf' => array( 'empty' => true ),
				'Partenairecui.statut' => array( 'empty' => true ),
				'Partenairecui.effectif',
				'Partenairecui.organismerecouvrement' => array( 'empty' => true ),
				'Partenairecui.assurancechomage' => array( 'type' => 'radio', 'class' => 'uncheckable', 'legend' => __d( 'cuis66', 'Partenairecui.assurancechomage' ) ),
			),
			array( 'options' => $options )
		) 
		. '</fieldset>'
		. '<fieldset id="Partenairecui66Informationssup">'
		.	 '<legend>' . __d('cuis66', 'Partenairecui66.informationssup') . '</legend>'
		. $this->Default3->subform(
			array(
				'Partenairecui66.activiteprincipale',
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
				'Partenairecui66.subventioncg' => array( 'type' => 'radio', 'class' => 'uncheckable', 'legend' => __d( 'cuis66', 'Partenairecui66.subventioncg' ) ),
				'Partenairecui66.commentaire' => array( 'type' => 'textarea' ),
			),
			array( 'options' => $options )
		)
		. '</fieldset>'
		. $this->Default3->subform(
			array(
				'Partenairecui.ajourversement' => array( 'type' => 'radio', 'class' => 'uncheckable add-parent-id', 'legend' => __d( 'cuis66', 'Partenairecui.ajourversement' ) ),
			),
			array( 'options' => $options )
		)
		. '</fieldset>'
	;

/***********************************************************************************
 * DOSSIER RECU/ELIGIBLE/COMPLET
/***********************************************************************************/
	
	echo '<fieldset id="Cui66Dossier"><legend>' . __d('cuis66', 'Cui66.dossier') . '</legend>'
		. $this->Default3->subform(
			array(
				'Cui66.dossiereligible' => array( 'empty' => true, 'type' => 'select' ),
				'Cui66.dateeligibilite' => array( 'dateFormat' => 'DMY', 'minYear' => '2009', 'maxYear' => date('Y')+4 ),
				'Cui66.dossierrecu' => array( 'empty' => true, 'type' => 'select' ),
				'Cui66.datereception' => array( 'dateFormat' => 'DMY', 'minYear' => '2009', 'maxYear' => date('Y')+4 ),
				'Cui66.dossiercomplet' => array( 'empty' => true, 'type' => 'select' ),
				'Cui66.datecomplet' => array( 'dateFormat' => 'DMY', 'minYear' => '2009', 'maxYear' => date('Y')+4 ),
				'Cui66.notedossier'
			),
			array( 'options' => $options )
		) . '</fieldset>'
	;

	/**
	 * Condition d'affichage : le dossier doit être reçu pour avoir la suite
	 */
	echo '<div id="CuiHiddenForm">';

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
		array( __d( 'cuis66', 'Personne.nom' ), Hash::get($this->request->data, 'Personnecui.nomusage' ) ),
		array( __d( 'cuis66', 'Personne.dtnai' ), date_format($dtnai, 'd/m/Y') ),
		array( __d( 'cuis66', 'Personne.nomcomnai' ), Hash::get($this->request->data, 'Personnecui.villenaissance' ) ),
		array( __d( 'cuis66', 'Adresse.complete' ), Hash::get($this->request->data, 'Personnecui66.adressecomplete' ) ),
	);
	$darkLabelDroit = array(		
		array( __d( 'cuis66', 'Personne.prenom' ), Hash::get($this->request->data, 'Personnecui.prenom1' ) ),
		array( __d( 'cuis66', 'Personne.nir' ), Hash::get($this->request->data, 'Personnecui.nir' ) ),
		array( __d( 'cuis66', 'Departement.name' ), Hash::get($this->request->data, 'Personnecui66.departement' ) ),
		array( __d( 'cuis66', 'Adresse.canton' ), Hash::get($this->request->data, 'Personnecui66.canton' ) ),
		array( __d( 'cuis66', 'Personne.nati' ), Hash::get($this->request->data, 'Personnecui.nationalite' ) ),
		array( __d( 'cuis66', 'Referentparcours.nom_complet' ), Hash::get($this->request->data, 'Personnecui66.referent' ) ),
	);
	
	// On affiche les informations
	echo '<fieldset id="PersonneInfo"><legend>' . __d('cuis66', 'Personne.info') . '</legend><div class="twopart">';
	
	foreach($darkLabelGauche as $value){
		echo '<div class="input value"><label class="little dark label">' . $value[0] . '</label><p class="dark label value">' . $value[1] . '</p></div>';
	}
	
	echo '</div><div class="twopart">';
	
	foreach($darkLabelDroit as $value){
		echo '<div class="input value"><label class="little dark label">' . $value[0] . '</label><p class="dark label value">' . $value[1] . '</p></div>';
	}
	
	echo '</div>' . $this->Default3->subform(
			array(
				'Cui66.zonecouverte' => array( 'empty' => true, 'type' => 'select' ),
				'Cui66.datefinsejour' => array( 'empty' => true, 'dateFormat' => 'DMY', 'minYear' => '2009', 'maxYear' => date('Y')+15 ),
			),
			array( 'options' => $options )
		)
		. $this->Xform->fieldValue(__m('Personnecui.numallocataire'), Hash::get($this->request->data, 'Personnecui.numallocataire' ), false)
		. $this->Xform->fieldValue(__m('Personnecui.organismepayeur'), Hash::get($this->request->data, 'Personnecui.organismepayeur' ), false)
		. '<div class="input radio"><fieldset id="Cui66Coupleenfant"><legend>' . __d('cuis66', 'Cui66.coupleenfant') . '</legend>'
	;
	
	echo '<input type="radio" name="data[Couple][enfants]" id="CoupleEnfants_1_0" value="1_0" /><label for="CoupleEnfants_1_0">'
		. __d('cuis66', 'Couple.enfants_1_0') . '</label>'
		. '<input type="radio" name="data[Couple][enfants]" id="CoupleEnfants_1_1" value="1_1" /><label for="CoupleEnfants_1_1">'
		. __d('cuis66', 'Couple.enfants_1_1') . '</label>'
		. '<input type="radio" name="data[Couple][enfants]" id="CoupleEnfants_0_0" value="0_0" /><label for="CoupleEnfants_0_0">'
		. __d('cuis66', 'Couple.enfants_0_0') . '</label>'
		. '<input type="radio" name="data[Couple][enfants]" id="CoupleEnfants_0_1" value="0_1" /><label for="CoupleEnfants_0_1">'
		. __d('cuis66', 'Couple.enfants_0_1') . '</label>'
		. '</fieldset></div>'
		. $this->Default3->DefaultForm->input('Personnecui66.nbpersacharge', array( 'label' => __d('cuis66', 'Cuis66.nbenfants') ))
		. '<div class="input text"><span class="label">' . __d('cuis66', 'Dossier.dtdemrsa') . '</span><span class="input">' . $personne['Dossier']['dtdemrsa'] . ' (' . $dtdemrsa->diff(new DateTime())->format('%y an(s) %m mois %d jours') . ')</span></div>'
		. '<div class="input text"><span class="label">' . __d('cuis66', 'Dossier.date_entree_dispositif') . '</span><span class="input">' . $diffStr . '</span></div>'
		. $this->Default3->subform( array( 'Personnecui66.montantrsa' => array( 'view' => true ) ) )
		. '</fieldset>'
	;

/***********************************************************************************
 * SITUATION DU SALARIE AVANT LA SIGNATURE DE LA CONVENTION
/***********************************************************************************/
	
	echo '<fieldset id="CuiSituationsalarie"><legend>' . __d('cuis66', 'Cui.situationsalarie') . '</legend>'
		. $this->Default3->subform(
			array(
				'Cui.niveauformation' => array( 'empty' => true, 'type' => 'select' ),
				'Cui.inscritpoleemploi' => array( 'type' => 'radio', 'class' => 'uncheckable', 'legend' => __d( 'cuis66', 'Cui.inscritpoleemploi' ) ),
				'Cui.sansemploi' => array( 'type' => 'radio', 'class' => 'uncheckable', 'legend' => __d( 'cuis66', 'Cui.sansemploi' ) ),
				'Cui.beneficiairede' => array( 'type' => 'select', 'multiple' => 'checkbox', 'legend' => __d( 'cuis66', 'Cui.beneficiairede' ) ),
				'Cui.majorationrsa' => array( 'type' => 'radio', 'class' => 'uncheckable add-parent-id', 'legend' => __d( 'cuis66', 'Cui.majorationrsa' ) ),
				'Cui.rsadepuis' => array( 'type' => 'radio', 'class' => 'uncheckable add-parent-id', 'legend' => __d( 'cuis66', 'Cui.rsadepuis' ) ),
				'Cui.travailleurhandicape' => array( 'type' => 'radio', 'class' => 'uncheckable', 'legend' => __d( 'cuis66', 'Cui.travailleurhandicape' ) ),
			),
			array( 'options' => $options )
		) . '</fieldset>'
	;
	
/***********************************************************************************	
 * CONTRAT DE TRAVAIL
/***********************************************************************************/
	
	echo '<fieldset id="CuiContrattravail"><legend>' . __d('cuis66', 'Cui.contrattravail') . '</legend>'
		. $this->Default3->subform(
			array(
				'Cui.typecontrat' => array( 'type' => 'radio', 'class' => 'uncheckable', 'legend' => __d( 'cuis66', 'Cui.typecontrat' ) ),
				'Cui.dateembauche' => array( 'dateFormat' => 'DMY', 'minYear' => '2009', 'maxYear' => date('Y')+4 ),
				'Cui.findecontrat' => array( 'dateFormat' => 'DMY', 'minYear' => '2009', 'maxYear' => date('Y')+4 ),
				'Cui66.perennisation' => array( 'type' => 'radio', 'class' => 'uncheckable add-parent-id', 'legend' => __m('Cui66.perennisation')  ),
			),
			array( 'options' => $options )
		) 
		. $this->Romev3->fieldset( 'Entreeromev3', array( 'options' => $options ) )
		. $this->Default3->subform(
			array(
				'Cui66.fonction',
				'Cui.salairebrut' => array( 'type' => 'text', 'class' => 'euros' ),
				'Cui.dureehebdo' => array( 'class' => 'heures_minutes' ),
				'Cui.modulation' => array( 'type' => 'radio', 'class' => 'uncheckable', 'legend' => __d( 'cuis66', 'Cui.modulation' ) ),
				'Cui.dureecollectivehebdo' => array( 'class' => 'heures_minutes' ),
			),
			array( 'options' => $options )
		) . '</fieldset>'
	;
	
/***********************************************************************************	
 * LES ACTIONS D'ACCOMPAGNEMENT ET DE FORMATION PRÉVUES
/***********************************************************************************/
	
	echo '<fieldset id="CuiAccompagnement"><legend>' . __d('cuis66', 'Cui.accompagnement') . '</legend>'
		. $this->Default3->subform(
			array(
				'Cui.nomtuteur',
				'Cui.fonctiontuteur',
				'Cui.organismedesuivi' => array( 'empty' => true ),
				'Cui.nomreferent',
				'Cui.actionaccompagnement' => array( 'type' => 'radio', 'class' => 'uncheckable', 'legend' => __d( 'cuis66', 'Cui.actionaccompagnement' ) ),
			),
			array( 'options' => $options )
		) 
		. '<br /><h4>' . __d('cuis66', 'Cui.small_title_accompagnement') . '</h4>'
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
			array( 'options' => $options )
		) 
		. '<br /><h4>' . __d('cuis66', 'Cui.small_title_formation') . '</h4>'
		. $this->Default3->subform(
			array(
				'Cui.adaptationauposte' => array( 'empty' => true ),
				'Cui.remiseaniveau' => array( 'empty' => true ),
				'Cui.prequalification' => array( 'empty' => true ),
				'Cui.acquisitioncompetences' => array( 'empty' => true ),
				'Cui.formationqualifiante' => array( 'empty' => true ),
				'Cui.formation' => array( 'type' => 'radio', 'class' => 'uncheckable', 'legend' => __d( 'cuis66', 'Cui.formation' ) ),
				'Cui66.commentaireformation',
				'Cui.periodeprofessionnalisation' => array( 'type' => 'radio', 'class' => 'uncheckable', 'legend' => __d( 'cuis66', 'Cui.periodeprofessionnalisation' ) ),
				'Cui.niveauqualif' => array( 'empty' => true, 'type' => 'select' ),
				'Cui.validationacquis' => array( 'type' => 'radio', 'class' => 'uncheckable', 'legend' => __d( 'cuis66', 'Cui.validationacquis' ) ),
			),
			array( 'options' => $options )
		) . '</fieldset>'
	;
	
/***********************************************************************************
 * SI CAE - PERIODE IMMERSION ?
/***********************************************************************************/
	
	echo $this->Default3->subform(array(
		'Cui.periodeimmersion' => array( 'type' => 'radio', 'class' => 'uncheckable add-parent-id', 'legend' => __d( 'cuis66', 'Cui.periodeimmersion' ) ),
		),
		array( 'options' => $options )
	);
		
/***********************************************************************************
 * LA PRISE EN CHARGE (CADRE RÉSERVÉ AU PRESCRIPTEUR)
/***********************************************************************************/
	
	echo '<fieldset id="CuiPrise_en_charge"><legend>' . __d('cuis66', 'Cui.prise_en_charge') . '</legend>'
		. $this->Default3->subform(
			array(
				'Cui.effetpriseencharge' => array( 'empty' => true, 'dateFormat' => 'DMY', 'minYear' => '2009', 'maxYear' => date('Y')+4 ),
				'Cui.finpriseencharge' => array( 'empty' => true, 'dateFormat' => 'DMY', 'minYear' => '2009', 'maxYear' => date('Y')+4 ),
				'Cui.decisionpriseencharge' => array( 'empty' => true, 'dateFormat' => 'DMY', 'minYear' => '2009', 'maxYear' => date('Y')+4 ),
				'Cui.dureehebdoretenu' => array( 'class' => 'heures_minutes'),
				'Cui.operationspeciale',
				'Cui.tauxfixeregion' => array( 'type' => 'text', 'class' => 'percent'),
				'Cui.priseenchargeeffectif' => array( 'type' => 'text', 'class' => 'percent'),
				'Cui.exclusifcg' => array( 'type' => 'radio', 'class' => 'uncheckable', 'legend' => __d( 'cuis66', 'Cui.exclusifcg' ) ),
				'Cui.tauxcg' => array( 'type' => 'text', 'class' => 'percent'),
				'Cui.organismepayeur' => array( 'type' => 'radio', 'class' => 'uncheckable', 'legend' => __d( 'cuis66', 'Cui.organismepayeur' ) ),
				'Cui.intituleautreorganisme',
				'Cui.adressautreorganisme',
			),
			array( 'options' => $options )
		) . '</fieldset>'
	;

	echo '</div>';

/***********************************************************************************
 * DATE
/***********************************************************************************/
	
	echo '<fieldset id="CuiDate"><legend>' . __d('cuis66', 'Cui.date') . '</legend>'
		. $this->Default3->subform(
			array(
				'Cui.faitle' => array( 'dateFormat' => 'DMY', 'minYear' => '2009', 'maxYear' => date('Y')+4 ),
				'Cui66.datebutoir_select' => array( 'empty' => true, 'type' => 'select' ),
				'Cui66.datebutoir' => array( 'empty' => true, 'dateFormat' => 'DMY', 'minYear' => '2009', 'maxYear' => date('Y')+4 ),
				'Cui66.demandeenregistree' => array( 'view' => true, 'hidden' => true, 'type' => 'date' )
			),
			array( 'options' => $options )
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
	 * Spécial couple/célibataire avec/sans enfants. 4 radio remplissent 2 hidden
	 * @param {HTML} input
	 * @returns {void}
	 */ 
	$$('input[name="data[Couple][enfants]"]').each(function( radio ){
		if ( $F('Cui66Encouple') === '1' ){
			if ( $F('Cui66Avecenfant') === '1' ){ $('CoupleEnfants_1_1').checked = true; }
			else{ $('CoupleEnfants_1_0').checked = true; }
		}
		else{
			if ( $F('Cui66Avecenfant') === '1' ){ $('CoupleEnfants_0_1').checked = true; }
			else{ $('CoupleEnfants_0_0').checked = true; }
		}
		radio.onclick = function(){
			$('Cui66Encouple').value = radio.value.substr(0,1); // Si 1_0 retourne 1
			$('Cui66Avecenfant').value = radio.value.substr(2,3); // Si 1_0 retourne 0
		};
	});
	
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
		
	/**
	 * Rempli les champs en fonction de Partenaire.id
	 * @param {Object} json
	 * @returns {void}
	 */
	function remplirChamps( json ){
		var key, champ, correspondancesChamps = <?php echo $correspondancesChamps;?>;
		for (key in correspondancesChamps){
			if (correspondancesChamps.hasOwnProperty(key)){
				champ = $( fieldId(correspondancesChamps[key]) );console.log( champ );
				if (champ !== null){
					champ.value = json[key];
					champ.simulate('change');
				}
			}
		}
	}
	
	/**
	 * Bouton Charger de Partenaire.id
	 * @returns {void}
	 */
	$('PartenaireCharger').onclick = function(){
		new Ajax.Request('<?php echo Router::url( array( 'controller' => 'partenaires', 'action' => 'ajax_coordonnees' ) ); ?>/'+$F('CuiPartenaireId'), {
			asynchronous:true, 
			evalScripts:true, 
			onComplete:function(request, json) {
				remplirChamps( json.Partenaire );
			}
		});
	};
	
	/**
	 * Coche "Utiliser une adresse administrative différente" si une valeur est présente dans adresse administrative
	 */
	var haveValue = false;
	$('PartenairecuiAdresseadministrative').select('input').each(function( input ){
		if ( $F(input) ){
			haveValue = true;
			throw $break;
		}
	});
	if ( haveValue ){
		$('PartenairecuiUtiliseradradministrative').checked = true;
	}
	
	function hiddenForm(){
		if ( $F('Cui66Dossierrecu') === '1' ){
			$('CuiHiddenForm').show();
		}
		else{
			$('CuiHiddenForm').hide();
		}
	}
	$('Cui66Dossierrecu').observe( 'change', hiddenForm);
	hiddenForm();
	
	/**
	 * Si Dénomination, raison sociale est modifié, on regarde dans Nom de l'employeur si la valeur est présente.
	 * Si elle est présente, on la selectionne, sinon, on met à vide.
	 */
	Event.observe( $('PartenairecuiRaisonsociale'), 'change', function(){
		var denomination = this;
		$('CuiPartenaireId').select( 'option' ).each(function( option ){
			option.selected = false;
			if( option.innerHTML.toUpperCase() === denomination.value.toUpperCase() ){
				option.selected = true;
				denomination.value = option.innerHTML;
			}
		});
	});
	
	/**
	 * Gestion de la date de cloture automatique en fonction du délai avant cloture automatique
	 */
	function setDateCloture(){
		'use strict';
		var duree = parseInt( $F('Cui66DatebutoirSelect'), 10 ),
			jour = parseInt( $F('CuiFaitleDay'), 10 ),
			mois = parseInt( $F('CuiFaitleMonth'), 10 ),
			annee = parseInt( $F('CuiFaitleYear'), 10 ),
			dateButoir,
			dateTest;
	
		if ( isNaN(duree*2) ){
			return false;
		}
		
		dateButoir = new Date(annee, mois + duree - 1, jour -1);
		
		$('Cui66DatebutoirDay').select('option').each(function(option){
			option.selected = false;
			if ( parseInt(option.value, 10) === dateButoir.getDate() ){
				option.selected = true;
			}
		});
		$('Cui66DatebutoirMonth').select('option').each(function(option){
			option.selected = false;
			if ( parseInt(option.value, 10) === dateButoir.getMonth() + 1 ){
				option.selected = true;
			}
		});
		$('Cui66DatebutoirYear').select('option').each(function(option){
			option.selected = false;
			if ( parseInt(option.value, 10) === dateButoir.getFullYear() ){
				option.selected = true;
			}
		});
	}
	Event.observe( $('Cui66DatebutoirSelect'), 'change', setDateCloture);
	Event.observe( $('CuiFaitleDay'), 'change', setDateCloture);
	Event.observe( $('CuiFaitleMonth'), 'change', setDateCloture);
	Event.observe( $('CuiFaitleYear'), 'change', setDateCloture);
	
	var disableFunction = {'setDatePriseEnCharge': <?php echo Hash::get($this->request->data, 'Cui.effetpriseencharge') === null ? 'false' : 'true';?>};
	console.log(disableFunction);
	/**
	 * Prérempli les dates de prises en charge en fonction des dates d'embauche
	 * Se désactive si une date d'embauche est saisie manuellement
	 */
	function setDatePriseEnCharge() {
		if (disableFunction.setDatePriseEnCharge) {
			return false;
		}
		
		$('CuiEffetpriseenchargeDay').setValue($('CuiDateembaucheDay').getValue());
		$('CuiEffetpriseenchargeMonth').setValue($('CuiDateembaucheMonth').getValue());
		$('CuiEffetpriseenchargeYear').setValue($('CuiDateembaucheYear').getValue());
		$('CuiFinpriseenchargeDay').setValue($('CuiFindecontratDay').getValue());
		$('CuiFinpriseenchargeMonth').setValue($('CuiFindecontratMonth').getValue());
		$('CuiFinpriseenchargeYear').setValue($('CuiFindecontratYear').getValue());
	}
	
	/**
	 * Permet d'empecher certaines fonctions de faire leur traitement
	 * 
	 * @param {string} functionName
	 */
	function stopFunction(functionName) {
		disableFunction[functionName] = true;
	}
	
	$('CuiEffetpriseenchargeDay').observe('change', function() {stopFunction('setDatePriseEnCharge');});
	$('CuiEffetpriseenchargeMonth').observe('change', function() {stopFunction('setDatePriseEnCharge');});
	$('CuiEffetpriseenchargeYear').observe('change', function() {stopFunction('setDatePriseEnCharge');});
	$('CuiFinpriseenchargeDay').observe('change', function() {stopFunction('setDatePriseEnCharge');});
	$('CuiFinpriseenchargeMonth').observe('change', function() {stopFunction('setDatePriseEnCharge');});
	$('CuiFinpriseenchargeYear').observe('change', function() {stopFunction('setDatePriseEnCharge');});
	$('CuiDateembaucheDay').observe('change', setDatePriseEnCharge);
	$('CuiDateembaucheMonth').observe('change', setDatePriseEnCharge);
	$('CuiDateembaucheYear').observe('change', setDatePriseEnCharge);
	$('CuiFindecontratDay').observe('change', setDatePriseEnCharge);
	$('CuiFindecontratMonth').observe('change', setDatePriseEnCharge);
	$('CuiFindecontratYear').observe('change', setDatePriseEnCharge);
	
	setDatePriseEnCharge();
</script>
<?php
	// Ici on défini les champs à faire apparaitre que si certains autres portent une certaine valeur
	$dateEligibiliteDossier = array('Cui66.dateeligibilite.day', 'Cui66.dateeligibilite.month', 'Cui66.dateeligibilite.year', 'Cui66.dossierrecu');
	$dateReceptionDossier = array('Cui66.datereception.day', 'Cui66.datereception.month', 'Cui66.datereception.year', 'Cui66.dossiercomplet');
	$dateDossierComplet = array('Cui66.datecomplet.day', 'Cui66.datecomplet.month', 'Cui66.datecomplet.year');
	
	echo $this->Observer->disableFieldsetOnValue(
		'Cui.secteurmarchand',
		'PartenairecuiAjourversement0Parent',
		'1',
		false,
		true
	);
	
	echo $this->Observer->disableFieldsetOnCheckbox(
		'Partenairecui.utiliseradradministrative',
		'PartenairecuiAdresseadministrative',
		false,
		false
	);
	
	echo $this->Observer->disableFieldsOnRadioValue(
		'CuiAddEditForm',
		'Partenairecui66.subventioncg',
		array('Partenairecui66Commentaire'),
		array( '', null, '0' ),
		false,
		true
	);
	
	echo $this->Observer->disableFieldsOnValue(
		'Cui66.dossiereligible',
		$dateEligibiliteDossier,
		'1',
		false,
		true
	);
	
	echo $this->Observer->disableFieldsOnValue(
		'Cui66.dossierrecu',
		$dateReceptionDossier,
		'1',
		false,
		true
	);
	
	echo $this->Observer->disableFieldsOnValue(
		'Cui66.dossiercomplet',
		$dateDossierComplet,
		'1',
		false,
		true
	);
	
	echo $this->Observer->disableFieldsetOnRadioValue(
		'CuiAddEditForm',
		'Cui.beneficiairede',
		'CuiMajorationrsa0Parent',
		'RSA',
		true,
		true
	);
	
	echo $this->Observer->disableFieldsetOnRadioValue(
		'CuiAddEditForm',
		'Cui.majorationrsa',
		'CuiRsadepuis05Parent',
		'1',
		true,
		true
	);
	
	echo $this->Observer->disableFieldsOnRadioValue(
		'CuiAddEditForm',
		'Cui.typecontrat',
		array( 'CuiDateembaucheDay', 'CuiDateembaucheMonth', 'CuiDateembaucheYear' ),
		array( '', null ),
		false,
		true
	);
	
	echo $this->Observer->disableFieldsOnRadioValue(
		'CuiAddEditForm',
		'Cui.typecontrat',
		array( 'CuiFindecontratDay', 'CuiFindecontratMonth', 'CuiFindecontratYear' ),
		'CDD',
		true,
		true
	);
	
	echo $this->Observer->disableFieldsetOnRadioValue(
		'CuiAddEditForm',
		'Cui.typecontrat',
		'Cui66Perennisation0Parent',
		'CDD',
		true,
		true
	);
	
	echo $this->Observer->disableFieldsOnValue(
		'Cui.autre',
		'Cui.autrecommentaire',
		array( '', null ),
		true,
		true
	);
	
	echo $this->Observer->disableFieldsOnRadioValue(
		'CuiAddEditForm',
		'Cui.periodeprofessionnalisation',
		'CuiNiveauqualif',
		'1',
		true,
		true
	);
	
	echo $this->Observer->disableFieldsetOnValue(
		'Cui66.typecontrat',
		'CuiPeriodeimmersion0Parent',
		'ACI',
		false,
		true
	);
	
	echo $this->Observer->disableFieldsOnRadioValue(
		'CuiAddEditForm',
		'Cui.exclusifcg',
		'CuiTauxcg',
		'1',
		true,
		true
	);
	
	echo $this->Observer->disableFieldsOnRadioValue(
		'CuiAddEditForm',
		'Cui.organismepayeur',
		array( 'CuiIntituleautreorganisme', 'CuiAdressautreorganisme' ),
		'AUTRE',
		true,
		true
	);
?>