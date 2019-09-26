<?php
	require_once  dirname( __FILE__ ).DS.'search.ctp' ;

	if( isset( $results ) ) {
		if((int)$annee <= 2019) {
			if ( (int) $annee == 2019) {
				echo '<h2> ' . __d( $domain, 'Tableau1b6.actionAvantProd2019' ) . $anneeProdMoinsUnJour . '</h2>';
			}
			$thead = $this->Xhtml->tag(
				'thead',
				$this->Xhtml->tableHeaders(
					array(
						__d( $domain, 'Tableau1b6.name' ),
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
		}


		if( (int) $annee >= 2019) {
			if ( (int) $annee == 2019) {
				echo '<h2> ' . __d( $domain, 'Tableau1b6.actionApresProd2019_pre' ) . $anneeProd . __d( $domain, 'Tableau1b6.actionApresProd2019_post' ) . '</h2>';
			}

			// En-tête du tableau
			$thead = $this->Xhtml->tag(
				'thead',
				$this->Xhtml->tableHeaders(
					array(
						__d( $domain, 'Tableau1b6.name' ),
						__d( $domain, 'Tableau1b6.count_personnes_prevues' ),
						__d( $domain, 'Tableau1b6.count_invitations.apres2019' ),
						__d( $domain, 'Tableau1b6.count_seances' ),
						__d( $domain, 'Tableau1b6.count_personnes' ),
						__d( $domain, 'Tableau1b6.count_participations' )
					)
				)
			);

			// Contenu du tableau
			$cells = array();
			$acomptabiliser = 1;

			for($i = 0; $i < 2; $i++) {
				$sousTotalPersonnePrevues = 0;
				$sousTotalInvitations = 0;
				$sousTotalSeances = 0;
				$sousTotalPersonnes = 0;
				$sousTotalParticipations = 0;

				foreach( $results['apresmep'] as $result ) {
					if ($result['Tableau1b6']['acomptabiliser'] == $acomptabiliser) {
						$cntPersonnePrevues = (int)Hash::get( $result, "Tableau1b6.count_personnes_prevues" );
						$cntInvitations = (int)Hash::get( $result, "Tableau1b6.count_invitations" );
						$cntSeances = (int)Hash::get( $result, "Tableau1b6.count_seances" );
						$cntPersonnes = (int)Hash::get( $result, "Tableau1b6.count_personnes" );
						$cntParticipations = (int)Hash::get( $result, "Tableau1b6.count_participations" );
						$cells[] = array(
							array( h( Hash::get( $result, 'Tableau1b6.name' ) ), array( 'class' => 'name' ) ),
							array( $this->Locale->number( $cntPersonnePrevues ), array( 'class' => 'integer number' ) ),
							array( $this->Locale->number( $cntInvitations ), array( 'class' => 'integer number' ) ),
							array( $this->Locale->number( $cntSeances ), array( 'class' => 'integer number' ) ),
							array( $this->Locale->number( $cntPersonnes ), array( 'class' => 'integer number' ) ),
							array( $this->Locale->number( $cntParticipations ), array( 'class' => 'integer number' ) )
						);
						$sousTotalPersonnePrevues += $cntPersonnePrevues;
						$sousTotalInvitations += $cntInvitations;
						$sousTotalSeances += $cntSeances;
						$sousTotalPersonnes += $cntPersonnes;
						$sousTotalParticipations += $cntParticipations;
					}
				}

				if($acomptabiliser == 1) {
					$sousTotalLabel = '<span style="margin-left: 5em"> SOUS TOTAL (à comptabiliser dans l\'objectif de positionnement) </span>';
				} else {
					$sousTotalLabel = '<span style="margin-left: 5em"> SOUS TOTAL (non comptabilisé dans l\'objectif de positionnement) </span>';
				}
				$cells[] = array(
					$sousTotalLabel,
					array( $this->Locale->number( $sousTotalPersonnePrevues ), array( 'class' => 'integer number' ) ),
					array( $this->Locale->number( $sousTotalInvitations ), array( 'class' => 'integer number' ) ),
					array( $this->Locale->number( $sousTotalSeances ), array( 'class' => 'integer number' ) ),
					array( $this->Locale->number( $sousTotalPersonnes ), array( 'class' => 'integer number' ) ),
					array( $this->Locale->number( $sousTotalParticipations ), array( 'class' => 'integer number' ) )
				);

				$acomptabiliser = 0;
			}
			$tbody = $this->Xhtml->tag( 'tbody', $this->Xhtml->tableCells( $cells ) );

			// Pied du tableau
			$cells = array(
				array(
					'Total',
					array( $this->Locale->number( array_sum( (array)Hash::extract( $results['apresmep'], '{n}.Tableau1b6.count_personnes_prevues' ) ) ), array( 'class' => 'integer number' ) ),
					array( $this->Locale->number( array_sum( (array)Hash::extract( $results['apresmep'], '{n}.Tableau1b6.count_invitations' ) ) ), array( 'class' => 'integer number' ) ),
					array( $this->Locale->number( array_sum( (array)Hash::extract( $results['apresmep'], '{n}.Tableau1b6.count_seances' ) ) ), array( 'class' => 'integer number' ) ),
					array( $this->Locale->number( array_sum( (array)Hash::extract( $results['apresmep'], '{n}.Tableau1b6.count_personnes' ) ) ), array( 'class' => 'integer number' ) ),
					array( $this->Locale->number( array_sum( (array)Hash::extract( $results['apresmep'], '{n}.Tableau1b6.count_participations' ) ) ), array( 'class' => 'integer number' ) )
				)
			);
			$tfoot = $this->Xhtml->tag( 'tfoot', $this->Xhtml->tableCells( $cells ) );

			// Affichage du tableau
			echo $this->Xhtml->tag( 'table', $thead.$tfoot.$tbody ,array( 'class' => 'wide tableau1b6' ) );
		}

		echo $this->Xhtml->tag( 'p', '(1) Indiquer le nom de l\'action. Il y a autant de lignes que d\'actions pilotées ou co-pilotées par et avec le Projet Insertion Emploi.' );
		echo $this->Xhtml->tag( 'p', '(2) Indiquer uniquement les sigles suivants (cest l\'objectif qui définit la thématique de l\'action) :<br>E: Emploi  (TRE, découverte des métiers, .recherche demploi par internet,...)<br>F : Formation   (présentations d\'actions organisées par des organismes, sensibilisation aux outils informatiques, ...)<br>VS : Vie Sociale  (soutien administratif, logement, famille, mobilité, ...)<br>LCV : Loisirs, Culture, et Vacances  (relais cultures du coeur, séjours vacances, ....)<br>IRSA :  Information dispositif RSA  (dispositif RSA uniquement)<br>2AD :  Accès aux Droits (retraite, CMU, transport, ...)<br>S : Santé  (prévention, ...)<br>3R : Resocialisation, redynamisation, revalorisation  (image de soi, ...)' );
		echo $this->Xhtml->tag( 'p', '(3) Additionner le total des participants de toutes les séances' );

		include_once  dirname( __FILE__ ).DS.'footer.ctp' ;
	}
?>