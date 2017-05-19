<?php
	$this->Csv->preserveLeadingZerosInExcel = true;

	$domain_search_plugin = ( Configure::read( 'Cg.departement' ) == 93 ) ? 'search_plugin_93' : 'search_plugin';

	// En-têtes
	$cells = array(
		'N° Dossier',
		'Qualité',
		'Nom',
		'Prénom',
		'NIR',
		'Date de naissance',
		__d( 'dossier', 'Dossier.matricule' ),
		'Identifiant Pôle Emploi',
		'N° Téléphone',
		'Numéro de voie',
		'Type de voie',
		'Nom de voie',
		'Complément adresse 1',
		'Complément adresse 2',
		'Code postal',
		'Commune',
        'Canton de l\'allocataire',
		'Date d\'ouverture de droit',
		'Etat du droit',
		__d( $domain_search_plugin, 'Structurereferenteparcours.lib_struc' ),
		__d( $domain_search_plugin, 'Referentparcours.nom_complet' ),
	);

	if( Configure::read( 'Cg.departement' ) == 93 ) {
		array_push( $cells, __d( 'orientstruct', 'Orientstruct.origine' ) );
	}

	array_push(
		$cells,
		'Date de l\'orientation',
		'Structure référente',
		'Statut de l\'orientation',
		'Soumis à droits et devoirs'/*,
		'Nature de la prestation'*/
	);

	if( $reorientationEp ) {
		array_push(
			$cells,
			'Date de passage en EP',
			'Décision EP'
		);
	}

	if( Configure::read( 'Cg.departement' ) == 58 ) {
		$cells[] = 'Code activité';
	}

	$this->Csv->addRow( $cells );

	// Résultats
	foreach( $orients as $orient ) {
		$toppersdrodevorsa = Hash::get( $orient, 'Calculdroitrsa.toppersdrodevorsa' );
		switch( $toppersdrodevorsa ) {
			case '0':
				$toppersdrodevorsa = 'Non';
				break;
			case '1':
				$toppersdrodevorsa = 'Oui';
				break;
			default:
				$toppersdrodevorsa = 'Non défini';
				break;
		}

		$row = array(
			Hash::get( $orient, 'Dossier.numdemrsa' ),
			value( $qual, Hash::get( $orient, 'Personne.qual' ) ),
			Hash::get( $orient, 'Personne.nom' ),
			Hash::get( $orient, 'Personne.prenom'),
			Hash::get( $orient, 'Personne.nir' ),
			date_short( Hash::get( $orient, 'Personne.dtnai' ) ),
			Hash::get( $orient, 'Dossier.matricule' ),
			Hash::get( $orient, 'Historiqueetatpe.identifiantpe' ),
			Hash::get( $orient, 'Modecontact.numtel' ),
			Hash::get( $orient, 'Adresse.numvoie' ),
			Hash::get( $orient, 'Adresse.libtypevoie' ),
			Hash::get( $orient, 'Adresse.nomvoie' ),
			Hash::get( $orient, 'Adresse.complideadr' ),
			Hash::get( $orient, 'Adresse.compladr' ),
			Hash::get( $orient, 'Adresse.codepos' ),
			Hash::get( $orient, 'Adresse.nomcom' ),
            Hash::get( $orient, 'Canton.canton' ),
			date_short( Hash::get( $orient, 'Dossier.dtdemrsa' ) ),
			value( $etatdosrsa, Hash::get( $orient, 'Situationdossierrsa.etatdosrsa' ) ),
			Hash::get( $orient, 'Structurereferenteparcours.lib_struc' ),
			Hash::get( $orient, 'Referentparcours.nom_complet' ),
		);

		if( Configure::read( 'Cg.departement' ) == 93 ) {
			array_push(
				$row,
				value( $options['Orientstruct']['origine'], Set::extract( $orient, 'Orientstruct.origine' ) )
			);
		}

		array_push(
			$row,
			date_short( Hash::get( $orient, 'Orientstruct.date_valid' ) ),
			Hash::get( $orient, 'Structurereferente.lib_struc' ),
			Hash::get( $orient, 'Orientstruct.statut_orient' ),
			$toppersdrodevorsa/*,
			Set::enum( Hash::get( $orient, 'Detailcalculdroitrsa.natpf' ), $natpf )*/
		);

		if( $reorientationEp ) {
			if( !empty( $orient['Dossierep']['themeep'] ) ) {
				$modeleDecision = 'Decision'.Inflector::underscore( Inflector::classify( $orient['Dossierep']['themeep'] ) );
				$decision = value( $enums[$modeleDecision]['decision'], Hash::get( $orient, "{$modeleDecision}.decision" ) );
			}
			else {
				$decision = null;
			}

			array_push(
				$row,
				date_short( $orient['Commissionep']['dateseance'] ),
				$decision
			);
		}


		if( Configure::read( 'Cg.departement' ) == 58 ) {
			$row[] = value( $act, Hash::get( $orient, 'Activite.act' ) );
		}

		$this->Csv->addRow($row);
	}

	Configure::write( 'debug', 0 );
	echo $this->Csv->render( 'orientstructs-'.date( 'Ymd-His' ).'.csv' );
?>