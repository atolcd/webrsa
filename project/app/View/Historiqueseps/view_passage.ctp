<h1><?php echo $this->pageTitle = 'Visualisation d\'un passage en commission d\'EP';?></h1>
<?php
	$detailsDossier = array(
		'Commissionep.Ep.identifiant',
		'Commissionep.identifiant',
		'Commissionep.dateseance',
		'Passagecommissionep.etatdossierep',
		'Dossierep.themeep',
		'Dossierep.created',
	);

	switch( $passage['Dossierep']['themeep'] ) {
		case 'contratscomplexeseps93';
			break;
		case 'defautsinsertionseps66';
			$detailsDossier[] = "{$modeleTheme}.origine";
			$detailsDossier[] = "{$modeleTheme}.type";
			break;
		case 'nonorientationsproseps58';
			break;
		case 'nonorientationsproseps93';
			break;
		case 'nonrespectssanctionseps93':
			$detailsDossier[] = "{$modeleTheme}.origine";
			$detailsDossier[] = "{$modeleTheme}.rgpassage";
			break;
		case 'regressionsorientationseps58';
			$detailsDossier[] = "{$modeleTheme}.datedemande";
			$detailsDossier[] = "{$modeleTheme}.commentaire";
			break;
		case 'reorientationseps93';
			$detailsDossier[] = "{$modeleTheme}.datedemande";
			$detailsDossier[] = "Motifreorientep93.name";
			$detailsDossier[] = "{$modeleTheme}.commentaire";
			$detailsDossier[] = "{$modeleTheme}.accordaccueil";
			$detailsDossier[] = "{$modeleTheme}.desaccordaccueil";
			$detailsDossier[] = "{$modeleTheme}.accordallocataire";
			$detailsDossier[] = "{$modeleTheme}.urgent";
			break;
		case 'saisinesbilansparcourseps66';
			$detailsDossier[] = "{$modeleTheme}.choixparcours";
			$detailsDossier[] = "{$modeleTheme}.maintienorientparcours";
			$detailsDossier[] = "{$modeleTheme}.changementrefparcours";
			$detailsDossier[] = "{$modeleTheme}.reorientation";
			break;
		case 'saisinespdoseps66';
			break;
		case 'sanctionseps58';
			$detailsDossier[] = "{$modeleTheme}.origine";

			switch( $passage['Sanctionep58']['origine'] ) {
				case 'noninscritpe':
					$detailsDossier["Orientstruct.Typeorient.lib_type_orient"] = array( 'domain' => 'typeorient' );
					$detailsDossier["Orientstruct.Structurereferente.lib_struc"] = array( 'domain' => 'structurereferente' );
					$detailsDossier["Orientstruct.statut_orient"] = array( 'domain' => 'orientstruct' );
					$detailsDossier["Orientstruct.date_valid"] = array( 'domain' => 'orientstruct' );
					$detailsDossier["Orientstruct.rgorient"] = array( 'domain' => 'orientstruct' );
					break;
				case 'nonrespectcer':
					$detailsDossier["Contratinsertion.Structurereferente.lib_struc"] = array( 'domain' => 'sanctionep58' );
					$detailsDossier["Contratinsertion.dd_ci"] = array( 'domain' => 'contratinsertion' );
					$detailsDossier["Contratinsertion.df_ci"] = array( 'domain' => 'contratinsertion' );
					$detailsDossier["Contratinsertion.rg_ci"] = array( 'domain' => 'contratinsertion' );
					$detailsDossier["Contratinsertion.decision_ci"] = array( 'domain' => 'contratinsertion' );
					$detailsDossier["Contratinsertion.datevalidation_ci"] = array( 'domain' => 'contratinsertion' );
					$detailsDossier["Contratinsertion.date_saisi_ci"] = array( 'domain' => 'contratinsertion' );
					$detailsDossier["Contratinsertion.Typocontrat.lib_typo"] = array( 'domain' => 'sanctionep58' );
					break;
				case 'radiepe':
					$detailsDossier[] = "Historiqueetatpe.identifiantpe";
					$detailsDossier[] = "Historiqueetatpe.date";
					$detailsDossier["Historiqueetatpe.etat"] = array( 'domain' => 'sanctionep58' );
					$detailsDossier["Historiqueetatpe.code"] = array( 'domain' => 'sanctionep58' );
					$detailsDossier["Historiqueetatpe.motif"] = array( 'domain' => 'sanctionep58' );
					break;
			}
			$detailsDossier[] = "{$modeleTheme}.commentaire";
			break;
		case 'sanctionsrendezvouseps58';
			$detailsDossier[] = "{$modeleTheme}.commentaire";
			break;
		case 'signalementseps93';
			$detailsDossier[] = "{$modeleTheme}.motif";
			$detailsDossier[] = "{$modeleTheme}.date";
			$detailsDossier[] = "{$modeleTheme}.rang";
			break;
	}

	echo $this->Default2->view(
		$passage,
		$detailsDossier,
		array(
			'options' => $options,
			'class' => 'aere'
		)
	);

	// Décisions
	$detailsDecision = array( "{$modeleDecision}.decision" );

	switch( $passage['Dossierep']['themeep'] ) {
		case 'contratscomplexeseps93';
			$detailsDecision[] = "{$modeleDecision}.observ_ci";
			$detailsDecision[] = "{$modeleDecision}.datevalidation_ci";
			break;
		case 'defautsinsertionseps66';
			$detailsDecision[] = "{$modeleDecision}.decisionsup";
			$detailsDecision[] = "Typeorient.lib_type_orient";
			$detailsDecision[] = "Structurereferente.lib_struc";
			$detailsDecision["Referent.nom_complet"] = array( 'type' => 'text' );
			break;
		case 'nonorientationsproseps58';
			$detailsDecision[] = "Typeorient.lib_type_orient";
			$detailsDecision[] = "Structurereferente.lib_struc";
			$detailsDecision["Referent.nom_complet"] = array( 'type' => 'text' );
			break;
		case 'nonorientationsproseps93';
			$detailsDecision[] = "Typeorient.lib_type_orient";
			$detailsDecision[] = "Structurereferente.lib_struc";
			break;
		case 'nonrespectssanctionseps93':
			$detailsDecision[] = "{$modeleDecision}.montantreduction";
			$detailsDecision[] = "{$modeleDecision}.dureesursis";
			break;
		case 'regressionsorientationseps58';
			$detailsDecision[] = "Typeorient.lib_type_orient";
			$detailsDecision[] = "Structurereferente.lib_struc";
			$detailsDecision["Referent.nom_complet"] = array( 'type' => 'text' );
			break;
		case 'reorientationseps93';
			$detailsDecision[] = "Typeorient.lib_type_orient";
			$detailsDecision[] = "Structurereferente.lib_struc";
			break;
		case 'saisinesbilansparcourseps66';
			$detailsDecision[] = "Typeorient.lib_type_orient";
			$detailsDecision[] = "Structurereferente.lib_struc";
			$detailsDecision["Referent.nom_complet"] = array( 'type' => 'text' );
			$detailsDecision[] = "{$modeleDecision}.maintienorientparcours";
			$detailsDecision[] = "{$modeleDecision}.changementrefparcours";
			$detailsDecision[] = "{$modeleDecision}.reorientation";
			break;
		case 'saisinespdoseps66';
			$detailsDecision[] = "Decisionpdo.libelle";
			$detailsDecision[] = "{$modeleDecision}.nonadmis";
			$detailsDecision[] = "{$modeleDecision}.motifpdo";
			$detailsDecision[] = "{$modeleDecision}.datedecisionpdo";
			break;
		case 'sanctionseps58';
			$detailsDecision["Listesanctionep58.sanction"] =  array( 'domain' => 'decisionsanctionep58' );
			$detailsDecision["Listesanctionep58.duree"] =  array( 'domain' => 'decisionsanctionep58' );
			$detailsDecision[] = "{$modeleDecision}.decision2";
			$detailsDecision["Autrelistesanctionep58.sanction"] = array( 'domain' => 'decisionsanctionep58' );
			$detailsDecision["Autrelistesanctionep58.duree"] =  array( 'domain' => 'decisionsanctionep58' );
			break;
		case 'sanctionsrendezvouseps58';
			$detailsDecision["Listesanctionep58.sanction"] =  array( 'domain' => 'decisionsanctionrendezvousep58' );
			$detailsDecision["Listesanctionep58.duree"] =  array( 'domain' => 'decisionsanctionep58' );
			$detailsDecision[] = "{$modeleDecision}.decision2";
			$detailsDecision["Autrelistesanctionep58.sanction"] = array( 'domain' => 'decisionsanctionrendezvousep58' );
			$detailsDecision["Autrelistesanctionep58.duree"] =  array( 'domain' => 'decisionsanctionep58' );
			break;
		case 'signalementseps93';
			$detailsDecision[] = "{$modeleDecision}.montantreduction";
			$detailsDecision[] = "{$modeleDecision}.dureesursis";
			break;
	}

	$detailsDecision[] = "{$modeleDecision}.commentaire";
	$detailsDecision[] = "{$modeleDecision}.raisonnonpassage";

	if( Configure::read( 'Cg.departement' ) == 58 ) {
		$maxPassages = 0;
	}
	else {
		$maxPassages = 1;
	}

	if( $passage['Commissionep']['etatcommissionep'] == 'annule' ) {
		echo $this->Xhtml->tag( 'p', "Commission annulée: {$passage['Commissionep']['raisonannulation']}", array( 'class' => 'notice' ) );
	}
	else {
		for( $i = 0 ; $i <= $maxPassages ; $i++ ) {
			if( !empty( $passage['Decision'][$i] ) ) {
				if( Configure::read( 'Cg.departement' ) == 58 ) {
					$label = 'Décision EP';
				}
				else {
					$label = ( ( $i == 0 ) ? 'Avis EP' : 'Décision PCG' );
				}

				if( Configure::read( 'Cg.departement' ) == 66 ) {
					if( empty( $passage['Decision'][0]['Decisiondefautinsertionep66']['decisionsup'] ) ) {
						unset( $passage['Decision'][1] ); // A remplacer par la valeur de la décision prise via le dossier PCG 66
					}
				}

				echo '<h2>'.$label.'</h2>';
				echo $this->Default2->view(
					$passage['Decision'][$i],
					$detailsDecision,
					array(
						'options' => $options,
						'class' => 'aere'
					)
				);

				// Affichage spécifique aux sanctions EPs CG58
				if( Configure::read( 'Cg.departement' ) == 58 && isset( $suivisanction58 ) && !empty( $suivisanction58 ) ) {
					echo '<h2>Suivi des sanctions</h2>';
					echo $this->Default2->index(
						$suivisanction58,
						array(
							"{$modeleDecision}.decision" => array( 'label' => 'Décision', 'type' => 'text' ),
							"{$modeleDecision}.sanction" => array( 'label' => 'Sanction', 'type' => 'text' ),
							"{$modeleDecision}.duree" => array( 'label' => 'Durée (en mois)', 'type' => 'integer' ),
							"{$modeleDecision}.dd" => array( 'label' => 'Date de début', 'type' => 'date' ),
							"{$modeleDecision}.df" => array( 'label' => 'Date de fin', 'type' => 'date' ),
							"{$modeleDecision}.etat" => array( 'label' => 'État', 'type' => 'text' ),
						)
					);

					// Date prévisionnelle de radiation
					$datePrevisionnelleRadiationInterval = Configure::read( 'Decisionsanctionep58.datePrevisionnelleRadiation' );
					$datePrevisionnelleRadiation = date( 'Y-m-d', strtotime( $datePrevisionnelleRadiationInterval, strtotime( Hash::get( $passage, 'Commissionep.dateseance' ) ) ) );

					$fields = array(
						"{$modeleDecision}.date_previsionnelle_radiation" => array(
							'value' => $datePrevisionnelleRadiation,
							'type' => 'date',
							'label' => 'Date prévisionnelle de radiation',
						),
					);

					if( !empty( $passage['Decision'][$i][$modeleDecision]['commentairearretsanction'] ) ){
						$fields[] = "{$modeleDecision}.commentairearretsanction";
					}

					echo $this->Default2->view(
						$passage['Decision'][$i],
						$fields,
						array(
							'options' => $options,
							'class' => 'aere'
						)
					);
				}
			}
		}
	}

	echo '<p>'.$this->Default->button(
		'back',
		array( 'action' => 'index', $passage['Dossierep']['personne_id'] ),
		array( 'id' => 'Back' )
	).'</p>';
?>