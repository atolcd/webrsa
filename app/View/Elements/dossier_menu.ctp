<?php
	$departement = (integer)Configure::read( 'Cg.departement' );

	/*
	* INFO: Parfois la variable a le nom personne_id, parfois personneId
	* 	On met tout le monde d'accord (en camelcase)
	*/

	if( isset( ${Inflector::variable( 'personne_id' )} ) ) {
		$personne_id = ${Inflector::variable( 'personne_id' )};
	}

	if( isset( ${Inflector::variable( 'foyer_id' )} ) ) {
		$foyer_id = ${Inflector::variable( 'foyer_id' )};
	}

	/*
	* Recherche du dossier à afficher
	*/
	if( isset( $dossierMenu ) ) {
		$dossier = $dossierMenu;
		if( isset( $dossierMenu['personne_id'] ) && !empty( $dossierMenu['personne_id'] ) ) {
			$personne_id = $dossierMenu['personne_id'];
		}
	}
	else {
		if( isset( $personne_id ) ) {
			$dossier = $this->requestAction( array( 'controller' => 'dossiers', 'action' => 'menu' ), array( 'personne_id' => $personne_id ) );
		}
		else if( isset( $foyer_id ) ) {
			$dossier = $this->requestAction( array( 'controller' => 'dossiers', 'action' => 'menu' ), array( 'foyer_id' => $foyer_id ) );
		}
		else if( isset( $id ) ) {
			$dossier = $this->requestAction( array( 'controller' => 'dossiers', 'action' => 'menu' ), array( 'id' => $id ) );
		}
	}
?>

<div class="treemenu">
		<h2 >
			<?php if( Configure::read( 'UI.menu.large' ) ):?>
			<?php
				echo $this->Xhtml->link(
					$this->Xhtml->image( 'icons/bullet_toggle_plus2.png', array( 'alt' => '', 'title' => 'Étendre le menu ', 'style' => 'width: 12px;' ) ),
					'#',
					array( 'onclick' => 'treeMenuExpandsAll( \''.Router::url( '/' ).'\' ); return false;', 'id' => 'treemenuToggleLink' ),
					false,
					false
				);
			?>
			<?php endif;?>

			<?php
				echo $this->Xhtml->link( 'Dossier RSA '.$dossier['Dossier']['numdemrsa'], array( 'controller' => 'dossiers', 'action' => 'view', $dossier['Dossier']['id'] ) )
				.$this->Xhtml->lockedDossier( $dossier )
				.$this->Xhtml->lockerIsMe( $dossier )
				.$this->Gestionanomaliebdd->foyerErreursPrestationsAllocataires( $dossier )
				.$this->Gestionanomaliebdd->foyerPersonnesSansPrestation( $dossier );
			?>
		</h2>

<?php
	if( $departement == 66 ) {
        $isOa = false;
        $structureNonOAId = (array) Configure::read( 'Nonorganismeagree.Structurereferente.id' );
        $typestructureByIds = Hash::combine( $dossierMenu, 'Foyer.Personne.{n}.Orientstruct.structurereferente_id', 'Foyer.Personne.{n}.Structurereferente.typestructure' );

        foreach( $typestructureByIds as $srid => $srts ) {
            if( 'oa' === $srts && !in_array( $srid, $structureNonOAId ) ) {
                $isOa = true;
            }
        }

        if( $isOa ) {
			echo $this->Xhtml->tag( 'p', 'Ce dossier est géré par un organisme agréé', array( 'class' => 'etatDossier structurereferenteOa' ) );
		}
	}
?>

<?php $etatdosrsaValue = Set::classicExtract( $dossier, 'Situationdossierrsa.etatdosrsa' );?>

<?php
	if( isset( $personne_id ) ) {
		$personneDossier = null;
		foreach( Set::extract( $dossier, '/Foyer/Personne' ) as $i => $personne ) {
			if( $personne_id == Set::classicExtract( $personne, 'Personne.id' ) ) {
				$personneDossier = Set::classicExtract( $personne, 'Personne.qual' ).' '.Set::classicExtract( $personne, 'Personne.nom' ).' '.Set::classicExtract( $personne, 'Personne.prenom' );
			}
		}

		if( Configure::read( 'UI.menu.lienDemandeur' ) ) {
			echo $this->Xhtml->tag(
				'p',
				$this->Xhtml->link( $personneDossier, sprintf( Configure::read( 'UI.menu.lienDemandeur' ), $dossier['Dossier']['matricule'] ), array(  'class' => 'external' ) ),
				array( 'class' => 'etatDossier' ),
				false,
				false
			);
		}
		else {
			echo $this->Xhtml->tag( 'p', $personneDossier, array( 'class' => 'etatDossier' ) );
		}
	}
?>

<p class="etatFlux">
	<?php
				$dtliqValue = Set::classicExtract( $dossier, 'Evenement.dtliq' );
				$dtliqText = date('d/m/Y',strtotime($dtliqValue));
				$motitransfluxValue = Set::classicExtract( $dossier, 'Evenement.fg' );
				$motitransflux = ClassRegistry::init('Evenement')->enum('fg');
			    if ( isset( $dtliqValue ) ){
					echo "Date MAJ Flux : $dtliqText  <br>" ;
					echo isset( $motitransfluxValue) ? $motitransflux[$motitransfluxValue] : 'Aucun Motif Transmis' ;
			    } else{
					echo "Aucune transmission repertoriée";
			    }
	?>
</p>

<p class="etatDossier">
<?php
    $etatdosrsa = ClassRegistry::init('Dossier')->enum('etatdosrsa');
    echo  isset( $etatdosrsa[$etatdosrsaValue] ) ? $etatdosrsa[$etatdosrsaValue] : 'Non défini' ;?>
</p>

<p class="numCaf">
	<?php
		$numcaf = $dossier['Dossier']['matricule'];
		$fonorg = $dossier['Dossier']['fonorg'];

		if( !empty( $numcaf ) && !empty( $fonorg ) ) {
			echo 'N°'.( isset( $fonorg ) ? $fonorg : '' ).' : '.( isset( $numcaf ) ? $numcaf : '' );
		}
		else {
			echo '';
		}
	?>
</p>

	<?php
		$itemsAllocataires = array();
		foreach( $dossier['Foyer']['Personne'] as $personne ) {
			$subAllocataire = array( 'url' => array( 'controller' => 'personnes', 'action' => 'view', $personne['id'] ) );

			$ancienAllocataire = ( Configure::read( 'AncienAllocataire.enabled' ) && Hash::get( $personne, 'ancienallocataire' ) );

			if( $personne['Prestation']['rolepers'] == 'DEM' || $personne['Prestation']['rolepers'] == 'CJT' || $ancienAllocataire ) {
				if( $departement == '66' ) {
                    $count = $personne['Memo']['nb_memos_lies'];
					$subAllocataire["Mémos ({$count})"] = array( 'url' => array( 'controller' => 'memos', 'action' => 'index', $personne['id'] ) );
				}

				// Droit
				$subAllocataire['Droit'] = array(
					( $departement == 93 ? 'DSP' : 'DSP d\'origine' ) => array( 'url' => array( 'controller' => 'dsps', 'action' => 'view', $personne['id'] ) ),
					( $departement == 66 ? 'DSPs mises à jour' : 'MAJ DSP' ) => array( 'url' => array( 'controller' => 'dsps', 'action' => 'histo', $personne['id'] ) ),
				);

				$nom_form_pdo_cg = Configure::read( 'nom_form_pdo_cg' );
				if (Configure::read( 'nom_form_ci_cg' ) == 'cg58' ) {
					if( null !== $nom_form_pdo_cg ) {
						$subAllocataire['Droit']['Consultation dossier PDO'] = array( 'url' => array( 'controller' => 'propospdos', 'action' => 'index', $personne['id'] ) );
					}
					$subAllocataire['Droit']['Orientation'] = array( 'url' => array( 'controller' => 'orientsstructs', 'action' => 'index', $personne['id'] ) );
				}
				else if (Configure::read( 'nom_form_ci_cg' ) == 'cg66' ) {
					$subAllocataire['Droit']['Orientation'] = array( 'url' => array( 'controller' => 'orientsstructs', 'action' => 'index', $personne['id'] ) );
					if( null !== $nom_form_pdo_cg ) {
						$subAllocataire['Droit']['Traitements PCG'] = array( 'url' => array( 'controller' => 'traitementspcgs66', 'action' => 'index', $personne['id'] ) );
					}
				}
				else {
					$subAllocataire['Droit']['Orientation'] = array( 'url' => array( 'controller' => 'orientsstructs', 'action' => 'index', $personne['id'] ) );
					if( null !== $nom_form_pdo_cg ) {
						$subAllocataire['Droit']['Consultation dossier PDO'] = array( 'url' => array( 'controller' => 'propospdos', 'action' => 'index', $personne['id'] ) );
					}
				}

				// Accompagnement du parcours
				$subAllocataire['Accompagnement du parcours'] = array( 'Chronologie parcours' => array( 'url' => '#' ) );

				if( $departement == '93' ) {
					$subAllocataire['Accompagnement du parcours']['Synthèse du suivi'] = array(
						'class' => 'accompagnementsbeneficiaires index',
						'url' => array( 'controller' => 'accompagnementsbeneficiaires', 'action' => 'index', $personne['id'] )
					);
				}

				$subAllocataire['Accompagnement du parcours'] = array_merge(
					$subAllocataire['Accompagnement du parcours'],
					array(
						( $departement == 93 ? 'Personne chargée du suivi' : 'Référent du parcours' ) => array( 'url' => array( 'controller' => 'personnes_referents', 'action' => 'index', $personne['id'] ) ),
						'Gestion RDV' => array( 'url' => array( 'controller' => 'rendezvous', 'action' => 'index', $personne['id'] ) ),
					)
				);

				if( $departement == 66 ) {
					$subAllocataire['Accompagnement du parcours']['Bilan du parcours'] = array( 'url' => array( 'controller' => 'bilansparcours66', 'action' => 'index', $personne['id'] ) );
				}

				if( $departement == 93 ) {
					$subAllocataire['Accompagnement du parcours']['B7 accès à l\'emploi'] = array(
						'url' => array( 'controller' => 'questionnairesb7pdvs93', 'action' => 'index', $personne['id'] )
					);

					$subAllocataire['Accompagnement du parcours']['Questionnaires D1'] = array(
						'url' => array( 'controller' => 'questionnairesd1pdvs93', 'action' => 'index', $personne['id'] )
					);

					$subAllocataire['Accompagnement du parcours']['Questionnaires D2'] = array(
						'url' => array( 'controller' => 'questionnairesd2pdvs93', 'action' => 'index', $personne['id'] )
					);
				}

				$contratcontroller = 'contratsinsertion';
				if( $departement == 93 ) {
					$contratcontroller = 'cers93';
				}
				$subAllocataire['Accompagnement du parcours']['Contrats'] = array(
					'url' => '#',
					'CER' => array( 'url' => array( 'controller' => $contratcontroller, 'action' => 'index', $personne['id'] ) ),
				);
				if ( Configure::read( 'Module.Cui.enabled' ) === true ){
					$subAllocataire['Accompagnement du parcours']['Contrats']['CUI'] = array( 'url' => array( 'controller' => 'cuis', 'action' => 'index', $personne['id'] ) );
				}
				$subAllocataire['Accompagnement du parcours']['Actualisation suivi'] = array(
					'url' => '#',
					'Entretiens' => array( 'url' => array( 'controller' => 'entretiens', 'action' => 'index', $personne['id'] ) ),
				);

				if( $departement == 93 ) {
					$subAllocataire['Accompagnement du parcours']['Actualisation suivi']['Relances'] = array( 'url' => array( 'controller' => 'relancesnonrespectssanctionseps93', 'action' => 'index', $personne['id'] ) );
				}

				if( $departement == 58 ) {
					$subAllocataire['Accompagnement du parcours']['Historique des COV'] = array(
						'url' => array( 'controller' => 'historiquescovs58', 'action' => 'index', $personne['id'] ),
					);
				}

				$subAllocataire['Accompagnement du parcours']['Historique des EPs'] = array(
					'url' => array( 'controller' => 'historiqueseps', 'action' => 'index', $personne['id'] ),
				);

				$subAllocataire['Accompagnement du parcours']['Offre d\'insertion'] = array(
					'url' => '#',
					'Fiche de candidature' => array(
						'url' => array( 'controller' => 'actionscandidats_personnes', 'action' => 'index', $personne['id'] ),
						'disabled' => ( $departement != 66 )
					),
					'Fiche de positionnement' => array(
						'url' => array( 'controller' => 'fichesprescriptions93', 'action' => 'index', $personne['id'] ),
						'disabled' => ( $departement != 93 )
					)
				);
				$subAllocataire['Accompagnement du parcours']['Aides financières'] = array(
						'url' => '#',
						'Créances Alimentaires' => array(
							'url' => array( 'controller' => 'Creancesalimentaires', 'action' => 'index', $personne['id'] )
						)
					);
				if( true === in_array( $departement, array( 66, 93 ), true ) ) {
						$subAllocataire['Accompagnement du parcours']['Aides financières']['Aides / APRE'] = array(
							'url' => array( 'controller' => 'apres'.Configure::read( 'Apre.suffixe' ), 'action' => 'index', $personne['id'] )
						);
				}
				if( $departement != 66 ) {
					$subAllocataire['Accompagnement du parcours']['Mémos'] = array(
						'url' => array( 'controller' => 'memos', 'action' => 'index', $personne['id'] ),
					);
				}

				if( $departement == 93 ) {
					$subAllocataire['Accompagnement du parcours'][__d( 'historiqueemploi', 'Historiqueemplois::index' )] = array(
						'url' => array( 'controller' => 'historiqueemplois', 'action' => 'index', $personne['id'] ),
					);
				}

				// Situation financière
				$subAllocataire['Situation financière'] = array(
					'url' => '#',
					'Ressources' => array( 'url' => array( 'controller' => 'ressources', 'action' => 'index', $personne['id'] ) ),
				);
			}

			// Informations personne
			$subAllocataire['Informations personne'] = array(
				'url' => '#',
				'Tags allocataire' => array(
					'url' => array(
						'controller' => 'tags',
						'action' => 'index',
						'Personne', $personne['id']
					)
				),
				'Données CAF' => array(
					'disabled' => !Configure::read('Module.Donneescaf.enabled'),
					'url' => array(
						'controller' => 'donneescaf',
						'action' => 'personne',
						$personne['id'],
					)
				)
			);

			// INFO: on ajoute des espaces à la clé pour éviter d'écraser avec les doublons
			$key = implode( ' ', array( '(', $personne['Prestation']['rolepers'], ')', $personne['qual'], $personne['nom'], $personne['prenom'] ) );
			while( isset( $itemsAllocataires[$key] ) ) {
				$key .= ' ';
			}
			$itemsAllocataires[$key] = $subAllocataire;
		}

		$items = array(
			'Composition du foyer' => array(
				'url' => array( 'controller' => 'personnes', 'action' => 'index', $dossier['Foyer']['id'] ),
			)
			+ $itemsAllocataires,
			'Informations foyer' => array(
				'Historique du droit' => array( 'url' => array( 'controller' => 'situationsdossiersrsa', 'action' => 'index', $dossier['Dossier']['id'] ) ),
				'Détails du droit RSA' => array( 'url' => array( 'controller' => 'detailsdroitsrsa', 'action' => 'index', $dossier['Dossier']['id'] ) ),
				'Adresses' => array( 'url' => array( 'controller' => 'adressesfoyers', 'action' => 'index', $dossier['Foyer']['id'] ) ),
				'Evénements' => array( 'url' => array( 'controller' => 'evenements', 'action' => 'index', $dossier['Foyer']['id'] ) ),
				'Modes de contact' => array( 'url' => array( 'controller' => 'modescontact', 'action' => 'index', $dossier['Foyer']['id'] ) ),
				'Informations financières' => array( 'url' => array( 'controller' => 'infosfinancieres', 'action' => 'index', $dossier['Dossier']['id'] ) ),
				'Liste des Indus' => array( 'url' => array( 'controller' => 'indus', 'action' => 'index', $dossier['Dossier']['id'] ) ),
				'Suivi instruction du dossier' => array( 'url' => array( 'controller' => 'suivisinstruction', 'action' => 'index', $dossier['Dossier']['id'] ) ),
			)
		);

		// Tags du foyer
		$items['Informations foyer'] += array(
			'Fiche de liaison' => array( 'url' => array( 'controller' => 'fichedeliaisons', 'action' => 'index', $dossier['Foyer']['id'] ) ),
			'Tags du foyer' => array( 'url' => array( 'controller' => 'tags', 'action' => 'index', 'Foyer', $dossier['Foyer']['id'] ) ),
		);


		$items['Informations foyer'] += array(
			'Données CAF' => array(
				'disabled' => !Configure::read('Module.Donneescaf.enabled'),
				'url' => array(
					'controller' => 'donneescaf',
					'action' => 'foyer',
					$dossier['Foyer']['id'],
				)
			)
		);

		// Dossier PCG (CG 66)
		if( $departement == 66 ) {
			$items['PCGs'] = array(
				'Dossier PCG' => array( 'url' => array( 'controller' => 'dossierspcgs66', 'action' => 'index', $dossier['Foyer']['id'] ) ),
				'Corbeille PCG' => array( 'url' => array( 'controller' => 'foyers', 'action' => 'corbeille', $dossier['Foyer']['id'] ) )
			);
		}

		$items['Informations complémentaires'] = array( 'url' => array( 'controller' => 'infoscomplementaires', 'action' => 'view', $dossier['Dossier']['id'] ) );

		$items['Synthèse du parcours d\'insertion'] = array( 'url' => array( 'controller' => 'suivisinsertion', 'action' => 'index', $dossier['Dossier']['id'] ) );
		$items['Modification Dossier RSA'] = array(
			'disabled' => !$this->Permissions->checkDossier( 'dossiers', 'edit', Hash::get( $this->viewVars, 'dossierMenu' ) ),
			'url' => array( 'controller' => 'dossiers', 'action' => 'edit', $dossier['Dossier']['id'] )
		);

		// Préconisation d'orientation
		if( $departement != 58 ) {
			$itemsPreconisations = array();

			if( !empty( $dossier['Foyer']['Personne'] ) ) {
				foreach( $dossier['Foyer']['Personne'] as $personnes ) {
					if( in_array( $personnes['Prestation']['rolepers'], array( 'DEM', 'CJT' ) ) ) {
						$itemsPreconisations[$personnes['qual'].' '.$personnes['nom'].' '.$personnes['prenom']] = array(
							'disabled' => !$this->Permissions->checkDossier( 'dossierssimplifies', 'edit', Hash::get( $this->viewVars, 'dossierMenu' ) ),
							'url' => array( 'controller' => 'dossierssimplifies', 'action' => 'edit', $personnes['id'] )
						);
					}
				}
			}

			$items['Préconisation d\'orientation'] = $itemsPreconisations;
		}

		echo $this->Menu->make2( $items );
	?>
</div>