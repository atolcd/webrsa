<?php
	$this->Csv->preserveLeadingZerosInExcel = true;

	$departement = Configure::read( 'Cg.departement' );
	$domain_search_plugin = ( $departement == 93 ) ? 'search_plugin_93' : 'search_plugin';

	$canton = '';
	if( Configure::read('Cg.departement') == 66 ) {
		$canton = 'Canton';
	}

	// Ligne d'en-tête
	$row = array(
		'N° Dossier',
		__d( 'dossier', 'Dossier.matricule' ),
		'Etat du droit',
		'Qualité',
		'Nom',
		'Prénom',
		__d( 'dossier', 'Dossier.matricule' ),
		'Numéro de voie',
		'Type de voie',
		'Nom de voie',
		'Complément adresse 1',
		'Complément adresse 2',
		'Code postal',
		'Commune',
		'Type d\'orientation',
		( $departement == 93 ) ? 'Personne établissant le CER' : 'Référent',
		( $departement == 93 ) ? 'Structure établissant le CER' : 'Service référent',
		'Type de contrat',
		'Date début contrat',
		'Durée',
		'Date fin contrat',
		'Décision et date validation',
		__d( $domain_search_plugin, 'Structurereferenteparcours.lib_struc' ),
		__d( $domain_search_plugin, 'Referentparcours.nom_complet' ),
	);

	if( $departement == 58 ) {
		$row = array_merge(
			$row,
			array(
				__d( 'personne', 'Personne.etat_dossier_orientation' )
			)
		);
	}
	else if( $departement == 93 ) {
		$row = array_merge(
			$row,
			array(
				// 1. Expériences professionnelles significatives
				// 1.1 Codes INSEE
				__d( 'criteresci', 'Secteuractiexppro.name' ),
				__d( 'criteresci', 'Metierexerceexppro.name' ),
				// 1.2 Codes ROME v.3
				__d( 'criteresci', 'Familleexppro.name' ),
				__d( 'criteresci', 'Domaineexppro.name' ),
				__d( 'criteresci', 'Metierexppro.name' ),
				__d( 'criteresci', 'Appellationexppro.name' ),
				// 2. Emploi trouvé
				// 2.1 Codes INSEE
				__d( 'criteresci', 'Secteuracti.name' ),
				__d( 'criteresci', 'Metierexerce.name' ),
				// 2.2 Codes ROME v.3
				__d( 'criteresci', 'Familleemptrouv.name' ),
				__d( 'criteresci', 'Domaineemptrouv.name' ),
				__d( 'criteresci', 'Metieremptrouv.name' ),
				__d( 'criteresci', 'Appellationemptrouv.name' ),
				// 3. Votre contrat porte sur
				// 3.1 Sujets, ... du CER
				__d( 'sujetcer93', 'Sujetcer93.name' ),
				'Autre, précisez',
				__d( 'soussujetcer93', 'Soussujetcer93.name' ),
				'Autre, précisez',
				__d( 'valeurparsoussujetcer93', 'Valeurparsoussujetcer93.name' ),
				'Autre, précisez',
				// 3.2 Codes ROME v.3
				__d( 'criteresci', 'Famillesujet.name' ),
				__d( 'criteresci', 'Domainesujet.name' ),
				__d( 'criteresci', 'Metiersujet.name' ),
				__d( 'criteresci', 'Appellationsujet.name' )
			)
		);
	}

	$this->Csv->addRow( $row );

	// Lignes de résultats
	foreach( $contrats as $contrat ) {
		$lib_type_orient = Hash::get( $contrat, 'Typeorient.lib_type_orient' );

		$duree = Hash::get( $contrat, 'Cer93.duree' );
		if( empty( $duree ) ) {
			$duree = Hash::get( $contrat, 'Contratinsertion.duree_engag' );
		}
		$duree = "{$duree} mois";

		if( $departement == 93 ) {
			$decision = Hash::get( $options['Cer93']['positioncer'], Hash::get( $contrat, 'Cer93.positioncer' ) )
				.( Hash::get( $contrat, 'Contratinsertion.decision_ci' ) == 'V' ? ' '.$this->Locale->date( 'Date::short', Hash::get( $contrat, 'Contratinsertion.datedecision' ) ) : '' );
		}
		else {
			$decision = value( $decision_ci, Hash::get( $contrat, 'Contratinsertion.decision_ci' ) ).' '.date_short( Hash::get( $contrat, 'Contratinsertion.datevalidation_ci' ) );
		}

		$row = array(
			Hash::get( $contrat, 'Dossier.numdemrsa' ),
			Hash::get( $contrat, 'Dossier.matricule' ),
			value( $etatdosrsa, Hash::get( $contrat, 'Situationdossierrsa.etatdosrsa' ) ),
			value( $qual, Hash::get( $contrat, 'Personne.qual' ) ),
			Hash::get( $contrat, 'Personne.nom' ),
			Hash::get( $contrat, 'Personne.prenom' ),
			Hash::get( $contrat, 'Dossier.matricule' ),
			Hash::get( $contrat, 'Adresse.numvoie' ),
			Hash::get( $contrat, 'Adresse.libtypevoie' ),
			Hash::get( $contrat, 'Adresse.nomvoie' ),
			Hash::get( $contrat, 'Adresse.complideadr' ),
			Hash::get( $contrat, 'Adresse.compladr' ),
			Hash::get( $contrat, 'Adresse.codepos' ),
			Hash::get( $contrat, 'Adresse.nomcom' ),
			( empty( $lib_type_orient ) ? 'Non orienté' : $lib_type_orient ),
			@$contrat['Referent']['nom_complet'],
			Hash::get( $contrat, 'Structurereferente.lib_struc' ),
			(
				( $departement == 93 )
				? value( $forme_ci, Hash::get( $contrat, 'Contratinsertion.forme_ci' ) )
				: Set::enum( Hash::get( $contrat, 'Contratinsertion.num_contrat' ), $numcontrat['num_contrat'] )
			),
			date_short( Hash::get( $contrat, 'Contratinsertion.dd_ci' ) ),
			$duree,
			date_short( Hash::get( $contrat, 'Contratinsertion.df_ci' ) ),
			$decision,
			Hash::get( $contrat, 'Structurereferenteparcours.lib_struc' ),
			Hash::get( $contrat, 'Referentparcours.nom_complet' ),
		);

		if( $departement == 66 ) {
			$row = array_merge(
				$row,
				array( Hash::get( $contrat, 'Canton.canton' ) )
			);
		}

		if( $departement == 58 ) {
			$row = array_merge(
				$row,
				array(
					value( (array)Hash::get( $options, 'Personne.etat_dossier_orientation' ), Hash::get( $contrat, 'Personne.etat_dossier_orientation' ) )
				)
			);
		}
		else if( $departement == 93 ) {
			$row = array_merge(
				$row,
				array(
					// 1. Expériences professionnelles significatives
					// 1.1 Codes INSEE
					Hash::get( $contrat, 'Secteuractiexppro.name' ),
					Hash::get( $contrat, 'Metierexerceexppro.name' ),
					// 1.2 Codes ROME v.3
					Hash::get( $contrat, 'Familleexppro.name' ),
					Hash::get( $contrat, 'Domaineexppro.name' ),
					Hash::get( $contrat, 'Metierexppro.name' ),
					Hash::get( $contrat, 'Appellationexppro.name' ),
					// 2. Emploi trouvé
					// 2.1 Codes INSEE
					Hash::get( $contrat, 'Secteuracti.name' ),
					Hash::get( $contrat, 'Metierexerce.name' ),
					// 2.2 Codes ROME v.3
					Hash::get( $contrat, 'Familleemptrouv.name' ),
					Hash::get( $contrat, 'Domaineemptrouv.name' ),
					Hash::get( $contrat, 'Metieremptrouv.name' ),
					Hash::get( $contrat, 'Appellationemptrouv.name' ),
					// 3. Votre contrat porte sur
					// 3.1 Sujets, ... du CER
					Hash::get( $contrat, 'Sujetcer93.name' ),
					Hash::get( $contrat, 'Cer93Sujetcer93.commentaireautre' ),
					Hash::get( $contrat, 'Soussujetcer93.name' ),
					Hash::get( $contrat, 'Cer93Sujetcer93.autresoussujet' ),
					Hash::get( $contrat, 'Valeurparsoussujetcer93.name' ),
					Hash::get( $contrat, 'Cer93Sujetcer93.autrevaleur' ),
					// 3.2 Codes ROME v.3
					Hash::get( $contrat, 'Famillesujet.name' ),
					Hash::get( $contrat, 'Domainesujet.name' ),
					Hash::get( $contrat, 'Metiersujet.name' ),
					Hash::get( $contrat, 'Appellationsujet.name' )
				)
			);
		}

		$this->Csv->addRow( $row );
	}

	Configure::write( 'debug', 0 );
	echo $this->Csv->render( 'contrats_engagement-'.date( 'Ymd-His' ).'.csv' );
?>