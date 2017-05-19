<?php
	require_once( dirname( __FILE__ ).DS.'search.ctp' );

	if( isset( $results ) ) {
		$thead = $this->Xhtml->tag(
			'thead',
			$this->Xhtml->tableHeaders(
				array(
					__d( $domain, 'Tableau1b6.name' ),
//					__d( $domain, 'Tableau1b6.theme' ),
					__d( $domain, 'Tableau1b6.count_personnes_prevues' ),
					__d( $domain, 'Tableau1b6.count_invitations' ),
					__d( $domain, 'Tableau1b6.count_seances' ),
					__d( $domain, 'Tableau1b6.count_personnes' ),
					__d( $domain, 'Tableau1b6.count_participations' )
				)
			)
		);

		$cells = array();
		foreach( $results as $result ) {
			$cells[] = array(
				array( h( Hash::get( $result, 'Tableau1b6.name' ) ), array( 'class' => 'name' ) ),
//				array( h( Hash::get( $result, 'Tableau1b6.theme' ) ), array( 'class' => 'theme' ) ),
				array( $this->Locale->number( (int)Hash::get( $result, "Tableau1b6.count_personnes_prevues" ) ), array( 'class' => 'integer number' ) ),
				array( $this->Locale->number( (int)Hash::get( $result, "Tableau1b6.count_invitations" ) ), array( 'class' => 'integer number' ) ),
				array( $this->Locale->number( (int)Hash::get( $result, "Tableau1b6.count_seances" ) ), array( 'class' => 'integer number' ) ),
				array( $this->Locale->number( (int)Hash::get( $result, "Tableau1b6.count_personnes" ) ), array( 'class' => 'integer number' ) ),
				array( $this->Locale->number( (int)Hash::get( $result, "Tableau1b6.count_participations" ) ), array( 'class' => 'integer number' ) )
			);
		}
		$tbody = $this->Xhtml->tag( 'tbody', $this->Xhtml->tableCells( $cells ) );

		// Pied du tableau
		$cells = array(
			array(
				'Total',
				array( $this->Locale->number( array_sum( (array)Hash::extract( $results, '{n}.Tableau1b6.count_personnes_prevues' ) ) ), array( 'class' => 'integer number' ) ),
				array( $this->Locale->number( array_sum( (array)Hash::extract( $results, '{n}.Tableau1b6.count_invitations' ) ) ), array( 'class' => 'integer number' ) ),
				array( $this->Locale->number( array_sum( (array)Hash::extract( $results, '{n}.Tableau1b6.count_seances' ) ) ), array( 'class' => 'integer number' ) ),
				array( $this->Locale->number( array_sum( (array)Hash::extract( $results, '{n}.Tableau1b6.count_personnes' ) ) ), array( 'class' => 'integer number' ) ),
				array( $this->Locale->number( array_sum( (array)Hash::extract( $results, '{n}.Tableau1b6.count_participations' ) ) ), array( 'class' => 'integer number' ) )
			)
		);
		$tfoot = $this->Xhtml->tag( 'tfoot', $this->Xhtml->tableCells( $cells ) );

		echo $this->Xhtml->tag( 'table', $thead.$tfoot.$tbody ,array( 'class' => 'wide tableau1b6' ) );

		echo $this->Xhtml->tag( 'p', '(1) Indiquer le nom de l\'action. Il y a autant de lignes que d\'actions pilotées ou co-pilotées par et avec le Projet de Ville.' );
		echo $this->Xhtml->tag( 'p', '(2) Indiquer uniquement les sigles suivants (cest l\'objectif qui définit la thématique de l\'action) :<br/>E: Emploi  (TRE, découverte des métiers, .recherche demploi par internet,...)<br/>F : Formation   (présentations d\'actions organisées par des organismes, sensibilisation aux outils informatiques, ...)<br/>VS : Vie Sociale  (soutien administratif, logement, famille, mobilité, ...)<br/>LCV : Loisirs, Culture, et Vacances  (relais cultures du coeur, séjours vacances, ....)<br/>IRSA :  Information dispositif RSA  (dispositif RSA uniquement)<br/>2AD :  Autres Accès aux Droits (retraite, CMU, transport, ...)<br/>S : Santé  (prévention, ...)<br/>3R : Resocialisation, redynamisation, revalorisation  (image de soi, ...)<br/>FM : Forum et manifestation : Piloté ou copiloté par le Projet de ville' );
		echo $this->Xhtml->tag( 'p', '(3) Additionner le total des participants de toutes les séances' );

		require_once( dirname( __FILE__ ).DS.'footer.ctp' );
	}
?>