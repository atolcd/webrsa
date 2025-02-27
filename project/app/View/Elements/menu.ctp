<?php if( $this->Session->check( 'Auth.User' ) ): ?>
<div id="menu1Wrapper">
	<div class="menu1">
<?php
	$departement = Configure::read( 'Cg.departement' );

	$user_type = $this->Session->read( 'Auth.User.type' );
	$user_cg = 'cg' === $user_type;
	$user_externe = strpos( $user_type, 'externe_' ) === 0;
	$user_cpdv = in_array( $user_type, array( 'externe_cpdv', 'externe_secretaire', 'externe_cpdvcom' ) );

	$monMenu = array('disabled' => true);
	$nomDuMenu = Configure::read('Module.Savesearch.mon_menu.name') ?: 'Mon menu';
	if (Configure::read('Module.Savesearch.enabled') && Configure::read('Module.Savesearch.mon_menu.enabled')) {
		$monMenu = array(
			'disabled' => false,
			'Modifier '.strtolower($nomDuMenu) => array('url' => array('controller' => 'savesearchs', 'action' => 'index'))
		);

		if (isset($main_navigation_menu_data['mon_menu']['Sauvegardes personnelles'])) {
			$monMenu['Sauvegardes personnelles'] = $main_navigation_menu_data['mon_menu']['Sauvegardes personnelles'];
		}
		if (isset($main_navigation_menu_data['mon_menu']['Sauvegardes de groupe'])) {
			$monMenu['Sauvegardes de groupe'] = $main_navigation_menu_data['mon_menu']['Sauvegardes de groupe'];
		}
	}

	$items = array(
		$nomDuMenu => $monMenu,
		'Accueil' => array (
			'url' => array( 'controller' => 'accueils', 'action' => 'index' ),
		),
		( $departement == 66 ? 'Gestion de listes' : 'Cohortes' ) => array(
			'APRE' => array(
				'disabled' => ( $departement != 66 ),
				'À valider' => array( 'class' => 'search', 'url' => array( 'controller' => 'apres66', 'action' => 'cohorte_validation' ) ),
				'À notifier' => array( 'class' => 'search', 'url' => array( 'controller' => 'apres66', 'action' => 'cohorte_imprimer' ) ),
				'Notifiées' => array( 'class' => 'search', 'url' => array( 'controller' => 'apres66', 'action' => 'cohorte_notifiees' ) ),
				'Transfert cellule' => array( 'class' => 'search', 'url' => array( 'controller' => 'apres66', 'action' => 'cohorte_transfert' ) ),
				'Traitement cellule' => array( 'class' => 'search', 'url' =>  array( 'controller' => 'apres66', 'action' => 'cohorte_traitement' ) ),
			),
			'CER' => array(
				'disabled' => ( !in_array( $departement, array( 66, 93 ) ) ),
				'Contrats Simples à valider' => array(
					'class' => 'search',
					'disabled' => ( $departement != 66 ),
					'url' => array( 'controller' => 'contratsinsertion', 'action' => 'cohorte_cersimpleavalider' )
				),
				'Contrats Particuliers à valider' => array(
					'class' => 'search',
					'disabled' => ( $departement != 66 ),
					'url' => array( 'controller' => 'contratsinsertion', 'action' => 'cohorte_cerparticulieravalider' )
				),
				'Décisions prises' => array(
					'class' => 'search',
					'disabled' => ( $departement != 66 ),
					'url' => array( 'controller' => 'contratsinsertion', 'action' => 'search_valides' )
				),
				'Contrats à valider' => array(
					'class' => 'search',
					'disabled' => ( $departement != 93 ),
					'url' => array( 'controller' => 'contratsinsertion', 'action' => 'cohorte_nouveaux' )
				),
				'Contrats validés' => array(
					'class' => 'search',
					'disabled' => ( $departement != 93 ),
					'url' => array( 'controller' => 'contratsinsertion', 'action' => 'cohorte_valides' )
				),
			),
			'Créances' => array(
				'disabled' => ( !Configure::read('Module.Creances.GestionList.enabled') ),
				'Préparation' => array(
					'class' => 'search',
					'url' => array( 'controller' => 'creances', 'action' => 'cohorte_preparation' )
				),
				'Validation' => array(
					'class' => 'search',
					'url' => array( 'controller' => 'titrescreanciers', 'action' => 'cohorte_validation' )
				),
				'Transmission compta' => array(
					'class' => 'search',
					'url' => array( 'controller' => 'titrescreanciers', 'action' => 'cohorte_transmissioncompta' )
				),
				'Retour compta' => array(
					'class' => 'search',
					'url' => array( 'controller' => 'titrescreanciers', 'action' => 'csv_retourcompta' )
				),
			),
			'Fiches de candidature' => array(
				'disabled' => ( $departement != 66 ),
				'Fiches en attente' => array( 'class' => 'search', 'url' => array( 'controller' => 'actionscandidats_personnes', 'action' => 'cohorte_enattente' ) ),
				'Fiches en cours' => array( 'class' => 'search', 'url' => array( 'controller' => 'actionscandidats_personnes', 'action' => 'cohorte_encours' ) ),
			),
			'Dossiers PCGs' => array(
				'disabled' => ( $departement != 66 ),
				'Dossiers en attente d\'affectation' => array( 'class' => 'search', 'url' => array( 'controller' => 'dossierspcgs66', 'action' => 'cohorte_enattenteaffectation' ) ),
				'Dossiers affectés' => array( 'class' => 'search', 'url' => array( 'controller' => 'dossierspcgs66', 'action' => 'search_affectes' ) ),
				'Dossiers à imprimer' => array( 'class' => 'search', 'url' => array( 'controller' => 'dossierspcgs66', 'action' => 'cohorte_imprimer' ) ),
				'Dossiers à transmettre' => array( 'class' => 'search', 'url' => array( 'controller' => 'dossierspcgs66', 'action' => 'cohorte_atransmettre' ) ),
				'Requêtes PDU' => array(
					'RSA Majoré' => array(
						'class' => 'search',
						'url' => array( 'controller' => 'dossierspcgs66', 'action' => 'cohorte_rsamajore' ),
					),
					'Allocataires hebergés' => array(
						'class' => 'search',
						'url' => array( 'controller' => 'dossierspcgs66', 'action' => 'cohorte_heberge' ),
					)
				),
			),
			__d('dossiers', 'Dossier.nomcohorte') => array(
				'disabled' => !Configure::read('Module.ModifEtatDossier.enabled'),
				'class' => 'search',
				'url' => array( 'controller' => 'modifsetatsdossiers', 'action' => 'cohorte_modifetatdos' ),
			),
			'Non orientation' => array(
				'disabled' => ( $departement != 66 ),
				'Inscrits PE' => array( 'class' => 'search', 'url' => array( 'controller' => 'nonorientes66', 'action' => 'cohorte_isemploi' ) ),
				'Non inscrits PE' => array( 'class' => 'search', 'url' => array( 'controller' => 'nonorientes66', 'action' => 'cohorte_imprimeremploi' ) ),
				'Gestion des réponses' => array( 'class' => 'search', 'url' => array( 'controller' => 'nonorientes66', 'action' => 'cohorte_reponse' ) ),
				'Notifications à envoyer' => array( 'class' => 'search', 'url' => array( 'controller' => 'nonorientes66', 'action' => 'cohorte_imprimernotifications' ) ),
				'Orientés et notifiés' => array( 'class' => 'search', 'url' =>  array( 'controller' => 'nonorientes66', 'action' => 'recherche_notifie' ) ),
			),
			'Orientation' => array(
				__d( 'orientsstructs', 'Orientsstructs.nom_menu_haut.algo') => array(
					__d( 'orientsstructs', 'Orientsstructs.nom_menu.orient_algo' ) => array( 'class' => 'search', 'url' => array( 'controller' => 'algorithmeorientation', 'action' => 'orientation' ), 'disabled' => !Configure::read('Module.AlgorithmeOrientation.enabled'), ),
					__d( 'orientsstructs', 'Orientsstructs.nom_menu.cohorte_nouvelles' ) => array( 'class' => 'search', 'url' => array( 'controller' => 'orientsstructs', 'action' => 'cohorte_nouvelles' ) ),
					__d( 'orientsstructs', 'Orientsstructs.nom_menu.cohorte_enattente' ) => array( 'class' => 'search', 'url' => array( 'controller' => 'orientsstructs', 'action' => 'cohorte_enattente' ) ),
					__d( 'orientsstructs', 'Orientsstructs.nom_menu.cohorte_orientees' ) => array( 'class' => 'search', 'url' => array( 'controller' => 'orientsstructs', 'action' => 'cohorte_orientees' ) ),
					__d( 'orientsstructs', 'Orientsstructs.nom_menu.nouveauxorientes' ) => array( 'class' => 'search', 'url' => array( 'controller' => 'algorithmeorientation', 'action' => 'search' ) ),
				),
				__d( 'orientsstructs', 'Orientsstructs.nom_menu_haut.struct') => array(
					__d( 'orientsstructs', 'Orientsstructs.nom_menu.cohorte_validation' ) => array( 'class' => 'search', 'url' => array( 'controller' => 'orientsstructs', 'action' => 'cohorte_validation' ) ),
					__d( 'orientsstructs', 'Orientsstructs.nom_menu.cohorte_orientees_validees' ) => array( 'class' => 'search', 'url' => array( 'controller' => 'orientsstructs', 'action' => 'cohorte_orientees_validees' ) ),
				)
			),
			'PDOs' => array(
				'disabled' => ( $departement != 93 ),
				'Nouvelles demandes' => array(
					'class' => 'search',
					'url' => array( 'controller' => 'propospdos', 'action' => 'cohorte_nouvelles' ),
					'title' => 'Avis CD demandé',
				),
				'Liste PDOs' => array(
					'class' => 'search',
					'url' => array( 'controller' => 'propospdos', 'action' => 'cohorte_validees' ),
					'title' => 'PDOs validés',
				),
			),
			'EPs' => array(
				'disabled' => ( $departement != 93 || true === $user_externe ),
				'Relances (EP)' => array(
					__d( 'relancenonrespectsanctionep93', 'Relancesnonrespectssanctionseps93::cohorte', true ) => array( 'class' => 'search', 'url' => array( 'controller' => 'relancesnonrespectssanctionseps93', 'action' => 'cohorte' ) ),
					__d( 'relancenonrespectsanctionep93', 'Relancesnonrespectssanctionseps93::impressions', true ) => array( 'class' => 'search', 'url' => array( 'controller' => 'relancesnonrespectssanctionseps93', 'action' => 'impressions' ) ),
				),
				'Parcours social sans réorientation' => array( 'class' => 'search', 'url' => array( 'controller' => 'nonorientationsproseps', 'action' => 'index' ) ),
				'Radiés de Pôle Emploi' => array( 'class' => 'search', 'url' => array( 'controller' => 'nonrespectssanctionseps93', 'action' => 'selectionradies'  ) ),
			),
			'Transferts' => array(
				'disabled' => ( $departement != 93 ),
				'Allocataires à transférer' => array( 'class' => 'search', 'url' => array( 'controller' => 'transfertspdvs93', 'action' => 'cohorte_atransferer' ) ),
				'Allocataires transférés' => array( 'class' => 'search', 'url' => array( 'controller' => 'cohortestransfertspdvs93', 'action' => 'transferes' ) ),
			),
            'Clôture de la personne chargée du suivi' => array(
				'class' => 'search',
				'disabled' => ( $departement != 93 || false === $user_externe ),
                'url' => array( 'controller' => 'referents', 'action' => 'clotureenmasse' )
			),
            __d( 'cohortesd2pdvs93', '/Cohortesd2pdvs93/index/:heading' ) => array(
				'class' => 'search',
				'disabled' => ( $departement != 93 ),
                'url' => array( 'controller' => 'cohortesd2pdvs93', 'action' => 'index' )
			),
            __d( 'cohortesrendezvous', '/Cohortesrendezvous/cohorte/:heading' ) => array(
				'class' => 'search',
				'disabled' => ( $departement != 93 || false === $user_externe ),
                'url' => array( 'controller' => 'cohortesrendezvous', 'action' => 'cohorte' )
			),
			'Tags' => array(
				'url' => array( 'controller' => 'tags', 'action' => 'cohorte' )
			),
			__d( 'referent', 'Referent.Cohorte.Menu' ) => array(
				__d( 'referent', 'Referent.Cohorte.Ajout' ) => array(
					'class' => 'search',
					'url' => array( 'controller' => 'referents', 'action' => 'cohorte_ajout' )
				),
				__d( 'referent', 'Referent.Cohorte.Modif' ) => array(
					'class' => 'search',
					'url' => array( 'controller' => 'referents', 'action' => 'cohorte_modif' )
				)
			),
			//Au 58
			//Module.Cohorte.Plan.Pauvrete.Nouveaux.Menu
			__d( 'planpauvrete', 'Planpauvrete.Planpauvrete.Menu58.Nouveaux' ) => array(
				'disabled' => (!Configure::read ('Module.Cohorte.Plan.Pauvrete.Menu') || $departement != 58),
				// Inscrits PE
				__d( 'planpauvrete', 'Planpauvrete.Nouveaux.Menu.InscritPE' ) => array(
					'disabled' => !Configure::read ('Module.Cohorte.Plan.Pauvrete.Nouveaux.MenuInscritPE'),
					__d( 'planpauvrete', 'Planpauvrete.Menu.InscritPEavecPPAE' ) => array(
						'disabled' => !Configure::read ('Module.Cohorte.Plan.Pauvrete.Nouveaux.cohorte_isemploi'),
						'class' => 'search', 'url' => array( 'controller' => 'planpauvreteorientations', 'action' => 'cohorte_isemploi' ) ),
						__d( 'planpauvrete', 'Planpauvrete.OrientationPEImprime' ) => array(
						'disabled' => !Configure::read ('Module.Cohorte.Plan.Pauvrete.Nouveaux.cohorte_isemploi_imprime'),
						'class' => 'search', 'url' => array( 'controller' => 'planpauvreteorientations', 'action' => 'cohorte_isemploi_imprime' ) ),
				),
				// Non inscrits PE
				__d( 'planpauvrete', 'Planpauvrete.Menu.RDV' ) => array(
					'disabled' => !Configure::read ('Module.Cohorte.Plan.Pauvrete.Nouveaux.MenuNonInscritPE'),
					__d( 'planpauvrete', 'Planpauvrete.Menu.Info_Col' ) => array(
						__d( 'planpauvrete', 'Planpauvrete.RDV.Creation' ) => array(
							'disabled' => !Configure::read ('Module.Cohorte.Plan.Pauvrete.Nouveaux.cohorte_infocol'),
							'class' => 'search', 'url' => array( 'controller' => 'planpauvreterendezvous', 'action' => 'cohorte_infocol' ) ),
						__d( 'planpauvrete', 'Planpauvrete.RDV.ImpressionConvocation' ) => array(
							'disabled' => !Configure::read ('Module.Cohorte.Plan.Pauvrete.Nouveaux.cohorte_infocol_imprime'),
							'class' => 'search', 'url' => array( 'controller' => 'planpauvreterendezvous', 'action' => 'cohorte_infocol_imprime' ) ),
						__d( 'planpauvrete', 'Planpauvrete.RDV.MajStatut' ) => array(
							'disabled' => !Configure::read ('Module.Cohorte.Plan.Pauvrete.Nouveaux.cohorte_infocol_venu_nonvenu_nouveaux'),
							'class' => 'search', 'url' => array( 'controller' => 'planpauvreterendezvous', 'action' => 'cohorte_infocol_venu_nonvenu_nouveaux' ) ),
					),
					__d( 'planpauvrete', 'Planpauvrete.Menu.3en1' ) => array(
						__d( 'planpauvrete', 'Planpauvrete.RDV.Creation' ) => array(
							'disabled' => !Configure::read ('Module.Cohorte.Plan.Pauvrete.Nouveaux.cohorte_infocol_second_rdv_nouveaux'),
							'class' => 'search', 'url' => array( 'controller' => 'planpauvreterendezvous', 'action' => 'cohorte_infocol_second_rdv_nouveaux' ) ),
						__d( 'planpauvrete', 'Planpauvrete.RDV.ImpressionConvocation' ) => array(
							'disabled' => !Configure::read ('Module.Cohorte.Plan.Pauvrete.Nouveaux.cohorte_infocol_imprime_second_rdv_nouveaux'),
							'class' => 'search', 'url' => array( 'controller' => 'planpauvreterendezvous', 'action' => 'cohorte_infocol_imprime_second_rdv_nouveaux' ) ),
						__d( 'planpauvrete', 'Planpauvrete.RDV.MajStatut' ) => array(
							'disabled' => !Configure::read ('Module.Cohorte.Plan.Pauvrete.Nouveaux.cohorte_infocol_venu_nonvenu_second_rdv_nouveaux'),
							'class' => 'search', 'url' => array( 'controller' => 'planpauvreterendezvous', 'action' => 'cohorte_infocol_venu_nonvenu_second_rdv_nouveaux' ) ),
					),
					__d( 'planpauvrete', 'Planpauvrete.Menu.Rdv_cer' ) => array(
						__d( 'planpauvrete', 'Planpauvrete.RDV.Creation' ) => array(
							'disabled' => !Configure::read ('Module.Cohorte.Plan.Pauvrete.Nouveaux.cohorte_infocol_rdv_cer_nouveaux'),
							'class' => 'search', 'url' => array( 'controller' => 'planpauvreterendezvous', 'action' => 'cohorte_infocol_rdv_cer_nouveaux' ) ),
						__d( 'planpauvrete', 'Planpauvrete.RDV.ImpressionConvocation' ) => array(
							'disabled' => !Configure::read ('Module.Cohorte.Plan.Pauvrete.Nouveaux.cohorte_infocol_rdv_cer_imprime_nouveaux'),
							'class' => 'search', 'url' => array( 'controller' => 'planpauvreterendezvous', 'action' => 'cohorte_infocol_rdv_cer_imprime_nouveaux' ) ),
						__d( 'planpauvrete', 'Planpauvrete.RDV.MajStatut' ) => array(
							'disabled' => !Configure::read ('Module.Cohorte.Plan.Pauvrete.Nouveaux.cohorte_infocol_rdv_cer_venu_nonvenu_nouveaux'),
							'class' => 'search', 'url' => array( 'controller' => 'planpauvreterendezvous', 'action' => 'cohorte_infocol_rdv_cer_venu_nonvenu_nouveaux' ) ),
					)
				)
			),
			__d( 'planpauvrete', 'Planpauvrete.Planpauvrete.Menu58.Fileactive' ) => array(
				'disabled' => (!Configure::read ('Module.Cohorte.Plan.Pauvrete.Menu') || $departement != 58),
				// Inscrits PE
				__d( 'planpauvrete', 'Planpauvrete.Stock.Menu.InscritPE' ) => array(
					'disabled' => !Configure::read ('Module.Cohorte.Plan.Pauvrete.Stock.MenuInscritPE'),
					__d( 'planpauvrete', 'Planpauvrete.Menu.InscritPEavecPPAE' ) => array(
						'disabled' => !Configure::read ('Module.Cohorte.Plan.Pauvrete.Stock.cohorte_isemploi_stock'),
						'class' => 'search', 'url' => array( 'controller' => 'planpauvreteorientations', 'action' => 'cohorte_isemploi_stock' ) ),
					__d( 'planpauvrete', 'Planpauvrete.OrientationPEImprime' ) => array(
						'disabled' => !Configure::read ('Module.Cohorte.Plan.Pauvrete.Stock.cohorte_isemploi_stock_imprime'),
						'class' => 'search', 'url' => array( 'controller' => 'planpauvreteorientations', 'action' => 'cohorte_isemploi_stock_imprime' ) ),
				),
				// Non inscrits PE
				__d( 'planpauvrete', 'Planpauvrete.Menu.RDV' ) => array(
					'disabled' => !Configure::read ('Module.Cohorte.Plan.Pauvrete.Stock.MenuNonInscritPE'),
					__d( 'planpauvrete', 'Planpauvrete.Menu.Info_Col' ) => array(
						__d( 'planpauvrete', 'Planpauvrete.RDV.Creation' ) => array(
							'disabled' => !Configure::read ('Module.Cohorte.Plan.Pauvrete.Stock.cohorte_infocol_stock'),
							'class' => 'search', 'url' => array( 'controller' => 'planpauvreterendezvous', 'action' => 'cohorte_infocol_stock' ) ),
						__d( 'planpauvrete', 'Planpauvrete.RDV.ImpressionConvocation' ) => array(
							'disabled' => !Configure::read ('Module.Cohorte.Plan.Pauvrete.Stock.cohorte_infocol_imprime_stock'),
							'class' => 'search', 'url' => array( 'controller' => 'planpauvreterendezvous', 'action' => 'cohorte_infocol_imprime_stock' ) ),
						__d( 'planpauvrete', 'Planpauvrete.RDV.MajStatut' ) => array(
							'disabled' => !Configure::read ('Module.Cohorte.Plan.Pauvrete.Stock.cohorte_infocol_venu_nonvenu_stock'),
							'class' => 'search', 'url' => array( 'controller' => 'planpauvreterendezvous', 'action' => 'cohorte_infocol_venu_nonvenu_stock' ) ),
					),
					__d( 'planpauvrete', 'Planpauvrete.Menu.3en1' ) => array(
						__d( 'planpauvrete', 'Planpauvrete.RDV.Creation' ) => array(
							'disabled' => !Configure::read ('Module.Cohorte.Plan.Pauvrete.Stock.cohorte_infocol_second_rdv_stock'),
							'class' => 'search', 'url' => array( 'controller' => 'planpauvreterendezvous', 'action' => 'cohorte_infocol_second_rdv_stock' ) ),
						__d( 'planpauvrete', 'Planpauvrete.RDV.ImpressionConvocation' ) => array(
							'disabled' => !Configure::read ('Module.Cohorte.Plan.Pauvrete.Stock.cohorte_infocol_imprime_second_rdv_stock'),
							'class' => 'search', 'url' => array( 'controller' => 'planpauvreterendezvous', 'action' => 'cohorte_infocol_imprime_second_rdv_stock' ) ),
						__d( 'planpauvrete', 'Planpauvrete.RDV.MajStatut' ) => array(
							'disabled' => !Configure::read ('Module.Cohorte.Plan.Pauvrete.Stock.cohorte_infocol_venu_nonvenu_second_rdv_stock'),
							'class' => 'search', 'url' => array( 'controller' => 'planpauvreterendezvous', 'action' => 'cohorte_infocol_venu_nonvenu_second_rdv_stock' ) ),
					),
					__d( 'planpauvrete', 'Planpauvrete.Menu.Rdv_cer' ) => array(
						__d( 'planpauvrete', 'Planpauvrete.RDV.Creation' ) => array(
							'disabled' => !Configure::read ('Module.Cohorte.Plan.Pauvrete.Stock.cohorte_infocol_rdv_cer_stock'),
							'class' => 'search', 'url' => array( 'controller' => 'planpauvreterendezvous', 'action' => 'cohorte_infocol_rdv_cer_stock' ) ),
						__d( 'planpauvrete', 'Planpauvrete.RDV.ImpressionConvocation' ) => array(
							'disabled' => !Configure::read ('Module.Cohorte.Plan.Pauvrete.Stock.cohorte_infocol_rdv_cer_imprime_stock'),
							'class' => 'search', 'url' => array( 'controller' => 'planpauvreterendezvous', 'action' => 'cohorte_infocol_rdv_cer_imprime_stock' ) ),
						__d( 'planpauvrete', 'Planpauvrete.RDV.MajStatut' ) => array(
							'disabled' => !Configure::read ('Module.Cohorte.Plan.Pauvrete.Stock.cohorte_infocol_rdv_cer_venu_nonvenu_stock'),
							'class' => 'search', 'url' => array( 'controller' => 'planpauvreterendezvous', 'action' => 'cohorte_infocol_rdv_cer_venu_nonvenu_stock' ) ),
					)
				)

			),
			//Hors 58
			__d( 'planpauvrete', 'Planpauvrete.Planpauvrete.Menu' ) => array(
				'disabled' => (!Configure::read ('Module.Cohorte.Plan.Pauvrete.Menu') || $departement == 58),
				__d( 'planpauvrete', 'Planpauvrete.Nouveaux.Menu' ) => array(
					'disabled' => !Configure::read ('Module.Cohorte.Plan.Pauvrete.Nouveaux.Menu'),
					// Inscrits PE
					__d( 'planpauvrete', 'Planpauvrete.Nouveaux.Menu.InscritPE' ) => array(
						'disabled' => !Configure::read ('Module.Cohorte.Plan.Pauvrete.Nouveaux.MenuInscritPE'),
						__d( 'planpauvrete', 'Planpauvrete.Nouveaux.Menu.InscritPE' ) => array(
						'disabled' => !Configure::read ('Module.Cohorte.Plan.Pauvrete.Nouveaux.cohorte_isemploi'),
						'class' => 'search', 'url' => array( 'controller' => 'planpauvreteorientations', 'action' => 'cohorte_isemploi' ) ),
						__d( 'planpauvrete', 'Planpauvrete.Nouveaux.InscritPEImprime' ) => array(
						'disabled' => !Configure::read ('Module.Cohorte.Plan.Pauvrete.Nouveaux.cohorte_isemploi_imprime'),
						'class' => 'search', 'url' => array( 'controller' => 'planpauvreteorientations', 'action' => 'cohorte_isemploi_imprime' ) ),
					),
					// Non inscrits PE
					__d( 'planpauvrete', 'Planpauvrete.Nouveaux.Menu.NonInscritPE' ) => array(
						'disabled' => !Configure::read ('Module.Cohorte.Plan.Pauvrete.Nouveaux.MenuNonInscritPE'),
						__d( 'planpauvrete', 'Planpauvrete.Nouveaux.Info_Col' ) => array(
							'disabled' => !Configure::read ('Module.Cohorte.Plan.Pauvrete.Nouveaux.cohorte_infocol'),
							'class' => 'search', 'url' => array( 'controller' => 'planpauvreterendezvous', 'action' => 'cohorte_infocol' ) ),
						__d( 'planpauvrete', 'Planpauvrete.Nouveaux.Impression_Info_Col' ) => array(
							'disabled' => !Configure::read ('Module.Cohorte.Plan.Pauvrete.Nouveaux.cohorte_infocol_imprime'),
							'class' => 'search', 'url' => array( 'controller' => 'planpauvreterendezvous', 'action' => 'cohorte_infocol_imprime' ) ),
						__d( 'planpauvrete', 'Planpauvrete.Nouveaux.Convoq_Info_Col' ) => array(
							'disabled' => !Configure::read ('Module.Cohorte.Plan.Pauvrete.Nouveaux.cohorte_infocol_venu_nonvenu_nouveaux'),
							'class' => 'search', 'url' => array( 'controller' => 'planpauvreterendezvous', 'action' => 'cohorte_infocol_venu_nonvenu_nouveaux' ) ),
						__d( 'planpauvrete', 'Planpauvrete.Nouveaux.3en1' ) => array(
							'disabled' => !Configure::read ('Module.Cohorte.Plan.Pauvrete.Nouveaux.cohorte_infocol_second_rdv_nouveaux'),
							'class' => 'search', 'url' => array( 'controller' => 'planpauvreterendezvous', 'action' => 'cohorte_infocol_second_rdv_nouveaux' ) ),
						__d( 'planpauvrete', 'Planpauvrete.Nouveaux.Impression_3en1' ) => array(
							'disabled' => !Configure::read ('Module.Cohorte.Plan.Pauvrete.Nouveaux.cohorte_infocol_imprime_second_rdv_nouveaux'),
							'class' => 'search', 'url' => array( 'controller' => 'planpauvreterendezvous', 'action' => 'cohorte_infocol_imprime_second_rdv_nouveaux' ) ),
						__d( 'planpauvrete', 'Planpauvrete.Nouveaux.Convoq_3en1' ) => array(
							'disabled' => !Configure::read ('Module.Cohorte.Plan.Pauvrete.Nouveaux.cohorte_infocol_venu_nonvenu_second_rdv_nouveaux'),
							'class' => 'search', 'url' => array( 'controller' => 'planpauvreterendezvous', 'action' => 'cohorte_infocol_venu_nonvenu_second_rdv_nouveaux' ) ),
						__d( 'planpauvrete', 'Planpauvrete.Nouveaux.Rdv_cer' ) => array(
							'disabled' => !Configure::read ('Module.Cohorte.Plan.Pauvrete.Nouveaux.cohorte_infocol_rdv_cer_nouveaux'),
							'class' => 'search', 'url' => array( 'controller' => 'planpauvreterendezvous', 'action' => 'cohorte_infocol_rdv_cer_nouveaux' ) ),
						__d( 'planpauvrete', 'Planpauvrete.Nouveaux.Impression_rdv_cer' ) => array(
							'disabled' => !Configure::read ('Module.Cohorte.Plan.Pauvrete.Nouveaux.cohorte_infocol_rdv_cer_imprime_nouveaux'),
							'class' => 'search', 'url' => array( 'controller' => 'planpauvreterendezvous', 'action' => 'cohorte_infocol_rdv_cer_imprime_nouveaux' ) ),
						__d( 'planpauvrete', 'Planpauvrete.Nouveaux.Convoq_rdv_cer' ) => array(
							'disabled' => !Configure::read ('Module.Cohorte.Plan.Pauvrete.Nouveaux.cohorte_infocol_rdv_cer_venu_nonvenu_nouveaux'),
							'class' => 'search', 'url' => array( 'controller' => 'planpauvreterendezvous', 'action' => 'cohorte_infocol_rdv_cer_venu_nonvenu_nouveaux' ) ),
					)
				),
				__d( 'planpauvrete', 'Planpauvrete.Stock.Menu' ) => array(
					'disabled' => !Configure::read ('Module.Cohorte.Plan.Pauvrete.Stock.Menu'),
					// Inscrits PE
					__d( 'planpauvrete', 'Planpauvrete.Stock.Menu.InscritPE' ) => array(
						'disabled' => !Configure::read ('Module.Cohorte.Plan.Pauvrete.Stock.MenuInscritPE'),
						__d( 'planpauvrete', 'Planpauvrete.Stock.InscritPE' ) => array(
							'disabled' => !Configure::read ('Module.Cohorte.Plan.Pauvrete.Stock.cohorte_isemploi_stock'),
							'class' => 'search', 'url' => array( 'controller' => 'planpauvreteorientations', 'action' => 'cohorte_isemploi_stock' ) ),
						__d( 'planpauvrete', 'Planpauvrete.Stock.InscritPEImprime' ) => array(
							'disabled' => !Configure::read ('Module.Cohorte.Plan.Pauvrete.Stock.cohorte_isemploi_stock_imprime'),
							'class' => 'search', 'url' => array( 'controller' => 'planpauvreteorientations', 'action' => 'cohorte_isemploi_stock_imprime' ) ),
					),
					// Non inscrits PE
					__d( 'planpauvrete', 'Planpauvrete.Stock.Menu.NonInscritPE' ) => array(
						'disabled' => !Configure::read ('Module.Cohorte.Plan.Pauvrete.Stock.MenuNonInscritPE'),
						__d( 'planpauvrete', 'Planpauvrete.Stock.Info_Col' ) => array(
							'disabled' => !Configure::read ('Module.Cohorte.Plan.Pauvrete.Stock.cohorte_infocol_stock'),
							'class' => 'search', 'url' => array( 'controller' => 'planpauvreterendezvous', 'action' => 'cohorte_infocol_stock' ) ),
						__d( 'planpauvrete', 'Planpauvrete.Stock.Impression_Info_Col' ) => array(
							'disabled' => !Configure::read ('Module.Cohorte.Plan.Pauvrete.Stock.cohorte_infocol_imprime_stock'),
							'class' => 'search', 'url' => array( 'controller' => 'planpauvreterendezvous', 'action' => 'cohorte_infocol_imprime_stock' ) ),
						__d( 'planpauvrete', 'Planpauvrete.Stock.Convoq_Info_Col' ) => array(
							'disabled' => !Configure::read ('Module.Cohorte.Plan.Pauvrete.Stock.cohorte_infocol_venu_nonvenu_stock'),
							'class' => 'search', 'url' => array( 'controller' => 'planpauvreterendezvous', 'action' => 'cohorte_infocol_venu_nonvenu_stock' ) ),
						__d( 'planpauvrete', 'Planpauvrete.Stock.3en1' ) => array(
							'disabled' => !Configure::read ('Module.Cohorte.Plan.Pauvrete.Stock.cohorte_infocol_second_rdv_stock'),
							'class' => 'search', 'url' => array( 'controller' => 'planpauvreterendezvous', 'action' => 'cohorte_infocol_second_rdv_stock' ) ),
						__d( 'planpauvrete', 'Planpauvrete.Stock.Impression_3en1' ) => array(
							'disabled' => !Configure::read ('Module.Cohorte.Plan.Pauvrete.Stock.cohorte_infocol_imprime_second_rdv_stock'),
							'class' => 'search', 'url' => array( 'controller' => 'planpauvreterendezvous', 'action' => 'cohorte_infocol_imprime_second_rdv_stock' ) ),
						__d( 'planpauvrete', 'Planpauvrete.Stock.Convoq_3en1' ) => array(
							'disabled' => !Configure::read ('Module.Cohorte.Plan.Pauvrete.Stock.cohorte_infocol_venu_nonvenu_second_rdv_stock'),
							'class' => 'search', 'url' => array( 'controller' => 'planpauvreterendezvous', 'action' => 'cohorte_infocol_venu_nonvenu_second_rdv_stock' ) ),
						__d( 'planpauvrete', 'Planpauvrete.Stock.Rdv_cer' ) => array(
							'disabled' => !Configure::read ('Module.Cohorte.Plan.Pauvrete.Stock.cohorte_infocol_rdv_cer_stock'),
							'class' => 'search', 'url' => array( 'controller' => 'planpauvreterendezvous', 'action' => 'cohorte_infocol_rdv_cer_stock' ) ),
						__d( 'planpauvrete', 'Planpauvrete.Stock.Impression_rdv_cer' ) => array(
							'disabled' => !Configure::read ('Module.Cohorte.Plan.Pauvrete.Stock.cohorte_infocol_rdv_cer_imprime_stock'),
							'class' => 'search', 'url' => array( 'controller' => 'planpauvreterendezvous', 'action' => 'cohorte_infocol_rdv_cer_imprime_stock' ) ),
						__d( 'planpauvrete', 'Planpauvrete.Stock.Convoq_rdv_cer' ) => array(
							'disabled' => !Configure::read ('Module.Cohorte.Plan.Pauvrete.Stock.cohorte_infocol_rdv_cer_venu_nonvenu_stock'),
							'class' => 'search', 'url' => array( 'controller' => 'planpauvreterendezvous', 'action' => 'cohorte_infocol_rdv_cer_venu_nonvenu_stock' ) ),
					)
				)
			),
		),
		'Recherches' => array(
			'Par dossier / allocataire' => array( 'class' => 'search', 'url' => array( 'controller' => 'dossiers', 'action' => 'search' ) ),
			'Par Orientation' => array( 'class' => 'search', 'url' => array( 'controller' => 'orientsstructs', 'action' => 'search' ) ),
			'Par APREs' => array(
				'class' => 'search',
				'disabled' => ( $departement != 66 ),
				'url' => array( 'controller' => 'apres', 'action' => 'search' )
			),
			'Par Contrats' => array(
				'Par CER' => array( 'class' => 'search', 'url' => array( 'controller' => 'contratsinsertion', 'action' => 'search'  ) ),
				'Par CUI' => array(
					'class' => 'search',
					'url' => array( 'controller' => 'cuis', 'action' => 'search'  ),
					'disabled' => ( Configure::read( 'Module.Cui.enabled' ) !== true )
				),
			),
			'Par Entretiens' => array( 'class' => 'search', 'url' => array( 'controller' => 'entretiens', 'action' => 'search' ) ),
			'Par Fiches de candidature' => array(
				'class' => 'search',
				'disabled' => ( $departement != 66 ),
				'url' => array( 'controller' => 'actionscandidats_personnes', 'action' => 'search' )
			),
			'Par Indus' => array( 'class' => 'search', 'url' => array( 'controller' => 'indus', 'action' => 'search' ) ),
			'Par DSPs' => array( 'class' => 'search', 'url' => array( 'controller' => 'dsps', 'action' => 'search' ) ),
			'Par Rendez-vous' => array( 'class' => 'search', 'url' => array( 'controller' => 'rendezvous', 'action' => 'search'  ) ),
			'Par Dossiers PCGs' => array(
				'disabled' => ( $departement != 66 ),
				'Dossiers PCGs' => array( 'class' => 'search', 'url' => array( 'controller' => 'dossierspcgs66', 'action' => 'search'  ) ),
				'Traitements PCGs' => array( 'class' => 'search', 'url' => array( 'controller' => 'traitementspcgs66', 'action' => 'search'  ) ),
				'Gestionnaires PCGs' => array( 'class' => 'search', 'url' => array( 'controller' => 'dossierspcgs66', 'action' => 'search_gestionnaire'  ) ),
			),
			'Par PDOs' => array(
				'disabled' => ( $departement == 66 || null === Configure::read( 'nom_form_pdo_cg' ) ),
				'Nouvelles PDOs' => array( 'class' => 'search', 'url' => array( 'controller' => 'propospdos', 'action' => 'search_possibles'  ) ),
				'Liste des PDOs' => array( 'class' => 'search', 'url' => array( 'controller' => 'propospdos', 'action' => 'search' ) ),
			),
			'Par Dossiers COV' => array(
				'disabled' => ( $departement != 58 ),
				'url' => array( 'class' => 'search', 'controller' => 'criteresdossierscovs58', 'action' => 'index'  ),
				__d( 'nonorientationsproscovs58', '/Nonorientationsproscovs58/cohorte/:heading' ) => array( 'class' => 'search', 'url' => array( 'controller' => 'nonorientationsproscovs58', 'action' => 'cohorte' ) )
			),
			'Par Dossiers EP' => array(
				'disabled' => ( $departement != 58 ),
				'Radiation ou cessation de Pôle Emploi' => array( 'class' => 'search', 'url' => array( 'controller' => 'sanctionseps58', 'action' => 'cohorte_radiespe' ) ),
				'Non inscription à Pôle Emploi' => array( 'class' => 'search', 'url' => array( 'controller' => 'sanctionseps58', 'action' => 'cohorte_noninscritspe' ) ),
			),
			'Par Bilans de parcours' => array(
				'class' => 'search',
				'disabled' => ( $departement != 66 ),
				'url' => array( 'controller' => 'bilansparcours66', 'action' => 'search'  ),
			),
			'Par Pôle Emploi' => array(
				'disabled' => ( $departement != 66 ),
				'Non inscrits au Pôle Emploi' => array( 'class' => 'search', 'url' => array( 'controller' => 'defautsinsertionseps66', 'action' => 'search_noninscrits'  ) ),
				'Radiés de Pôle Emploi' => array( 'class' => 'search', 'url' => array( 'controller' => 'defautsinsertionseps66', 'action' => 'search_radies'  ) ),
			),
			'Par demande de maintien dans le social' => array(
				'class' => 'search',
				'disabled' => ( $departement != 66 ),
				'url' => array( 'controller' => 'nonorientationsproseps', 'action' => 'search'  )
			),
			'Par allocataires sortants' => array(
				'Intra-département' => array(
					'class' => 'search',
					'disabled' => ( $departement != 93 ),
					'url' => array( 'controller' => 'transfertspdvs93', 'action' => 'search'  )
				),
				'Hors département' => array(
					'class' => 'search',
					'url' => array( 'controller' => 'demenagementshorsdpts', 'action' => 'search'  )
				),
			),
			'Par fiches de positionnement' => array(
				'class' => 'search',
				'disabled' => ( $departement != 93 ),
				'url' => array( 'controller' => 'fichesprescriptions93', 'action' => 'search'  )
			),
			'Par changement d\'adresse' => array(
				'class' => 'search',
				'disabled' => ( $departement != 66 ),
				'url' => array( 'controller' => 'changementsadresses', 'action' => 'search'  )
			),
			'Par créances' => array(
				'Par créances' => array(
					'class' => 'search',
					'url' => array( 'controller' => 'creances', 'action' => 'search'  )
				),
				'Par titres de recette' => array(
					'disabled' => !Configure::read('Creances.Titrescreanciers.enabled'),
					'class' => 'search',
					'url' => array( 'controller' => 'titrescreanciers', 'action' => 'search'  )
				),
			),
			'Par Recours' => array(
				'Par Recours Gracieux' => array(
					'disabled' => !Configure::read('Module.Recoursgracieux.enabled'),
					'class' => 'search',
					'url' => array( 'controller' => 'recoursgracieux', 'action' => 'search'  )
				),
			),
			'Par données Pôle Emploi' => array(
				'disabled' => !Configure::read('Module.RecherchePoleEmploi.enabled'),
				'class' => 'search',
				'url' => array( 'controller' => 'fluxpoleemplois', 'action' => 'search' )
			),
			__d('planpauvrete','Planpauvrete.primoaccedant.menu.search') => array(
				'disabled' => !Configure::read('Module.Planpauvrete.primoaccedant.enabled'),
				'class' => 'search',
				'url' => array( 'controller' => 'planpauvrete', 'action' => 'searchprimoaccedant' )
			),
		),
		'APRE' => array(
			'disabled' => ( $departement != 93 || true === $user_externe ),
			'Liste des demandes d\'APRE' => array(
				'Toutes les APREs' => array( 'class' => 'search', 'url' => array( 'controller' => 'apres', 'action' => 'search' ) ),
				'Eligibilité des APREs' => array( 'class' => 'search', 'url' => array( 'controller' => 'apres', 'action' => 'search_eligibilite' ) ),
				'Demande de recours' => array( 'class' => 'search', 'url' => array( 'controller' => 'recoursapres', 'action' => 'demande' ) ),
				'Visualisation des recours' => array( 'class' => 'search', 'url' => array( 'controller' => 'recoursapres', 'action' => 'visualisation' ) ),
			),
			'Comité d\'examen' => array(
				'Recherche de Comité' => array( 'class' => 'search', 'url' => array( 'controller' => 'comitesapres', 'action' => 'index' ) ),
				'Gestion des décisions Comité' => array( 'class' => 'search', 'url' => array( 'controller' => 'cohortescomitesapres', 'action' => 'aviscomite' ) ),
				'Notifications décisions Comité' => array( 'class' => 'search', 'url' => array( 'controller' => 'cohortescomitesapres', 'action' => 'notificationscomite' ) ),
				'Liste des Comités' => array( 'class' => 'search', 'url' => array( 'controller' => 'comitesapres', 'action' => 'liste' ) ),
			),
			'Reporting bi-mensuel' => array(
				'Reporting bi-mensuel DDTEFP' => array( 'url' => array( 'controller' => 'repsddtefp', 'action' => 'index' ) ),
				'Suivi et contrôle de l\'enveloppe APRE' => array( 'url' => array( 'controller' => 'repsddtefp', 'action' => 'suivicontrole' ) ),
			),
			'Journal d\'intégration des fichiers CSV' => array( 'url' => array( 'controller' => 'integrationfichiersapre', 'action' => 'index' ) ),
			'États liquidatifs APRE' => array( 'url' => array( 'controller' => 'etatsliquidatifs', 'action' => 'index' ) ),
			'Budgets APRE' => array( 'url' => array( 'controller' => 'budgetsapres', 'action' => 'index' ) ),
		),
		'COV' => array(
			'disabled' => ( $departement != 58 ),
			'url' => array( 'controller' => 'covs58', 'action' => 'index' ),
		),
		'Offre d\'Insertion' => array(
			'disabled' => ( $departement != 66 ),
			'Paramétrages' => array(
				'Création des partenaires' => array(
					'url' => array( 'controller' => 'partenaires', 'action' => 'index' )
				),
				'Création des contacts' => array(
					'url' => array( 'controller' => 'contactspartenaires', 'action' => 'index' )
				),
				'Création des actions' => array(
					'url' => array( 'controller' => 'actionscandidats', 'action' => 'index' )
				),
                'Création des programmes région' => array(
					'url' => array( 'controller' => 'progsfichescandidatures66', 'action' => 'index' )
				),
                'Création des valeurs programmes région' => array(
					'url' => array( 'controller' => 'valsprogsfichescandidatures66', 'action' => 'index' )
				),
				'Motifs de sortie' => array(
					'url' => array( 'controller' => 'motifssortie', 'action' => 'index' )
				)
			),
			'Tableau global' => array(
				'url' => array( 'controller' => 'offresinsertion', 'action' => 'index' )
			)
		),
		'Eq. Pluri.' => array(
			( $departement == 66 ? '1. Gestion des EPs' : '1. Mise en place du dispositif' ) => array(
				'Courriers d\'information avant EPL Audition' => array(
					'disabled' => ( $departement != 66 ),
					'url' => array( 'controller' => 'defautsinsertionseps66', 'action' => 'courriersinformations'  ),
				),
				'Création des membres' => array( 'class' => 'search', 'url' => array( 'controller' => 'membreseps', 'action' => 'index' ) ),
				'Création des EPs' => array( 'url' => array( 'controller' => 'eps', 'action' => 'index' ) ),
				'Création des Commissions' => array( 'url' => array( 'controller' => 'commissionseps', 'action' => 'add' ) ),
			),
			( $departement == 66 ? '2. Recherche de commission' : '2. Constitution de la commission' ) => array(
				'class' => 'search',
				'url' => array( 'controller' => 'commissionseps', 'action' => 'recherche' ),
			),
			'3. Arbitrage EP' => array(
				'class' => 'search',
				'disabled' => ( $departement != 58 ),
				'url' => array( 'controller' => 'commissionseps', 'action' => 'arbitrageep' ),
			),
			( $departement == 66 ? '3. Avis/Décisions' : '3. Arbitrage' ) => array(
				'class' => 'search',
				'disabled' => !in_array( $departement, array( 66, 93 ) ),
				( $departement == 66 ? 'Avis EP' : 'EP' ) => array(
					'url' => array( 'controller' => 'commissionseps', 'action' => 'arbitrageep' ),
				),
				( $departement == 66 ? 'Décisions CD' : 'CD' ) => array(
					'url' => array( 'controller' => 'commissionseps', 'action' => 'arbitragecg' ),
				),
			),
			( $departement == 66 ? '4. Consultation et impression des avis et décisions' : '4. Consultation et impression des décisions' ) => array(
				'class' => 'search',
				'url' => array( 'controller' => 'commissionseps', 'action' => 'decisions' ),
			),
			'5. Gestion des sanctions' => array(
				'disabled' => ( $departement != 58 ),
				'Gestion des sanctions' => array( 'class' => 'search', 'url' => array( 'controller' => 'gestionssanctionseps58', 'action' => 'traitement' ) ),
				'Visualisation des sanctions' => array( 'class' => 'search', 'url' => array( 'controller' => 'gestionssanctionseps58', 'action' => 'visualisation' ) ),
			),
		),
		'CER' => array(
			'class' => 'search',
			'disabled' => ( $departement != 93 ),
			'1. Affectation de la personne chargée du suivi' => array(
				'disabled' => false === $user_cpdv,
				'url' => array( 'controller' => 'personnes_referents', 'action' => 'cohorte_affectation93'  )
			),
			'2. Saisie d\'un CER' => array(
				'class' => 'search',
				'disabled' => false === $user_externe,
				'url' => array( 'controller' => 'cohortescers93', 'action' => 'saisie'  )
			),
			'3. Validation Responsable' => array(
				'class' => 'search',
				'disabled' => false === $user_cpdv,
				'url' => array( 'controller' => 'cohortescers93', 'action' => 'avalidercpdv'  )
			),
			'4. Décision CD' => array(
				'4.1 Première lecture' => array(
					'class' => 'search',
					'disabled' => false === $user_cg,
					'url' => array( 'controller' => 'cohortescers93', 'action' => 'premierelecture'  )
				),
				'4.2 Validation CS' => array(
					'class' => 'search',
					'disabled' => false === $user_cg,
					'url' => array( 'controller' => 'cohortescers93', 'action' => 'validationcs'  )
				),
				'4.3 Validation Cadre' => array(
					'class' => 'search',
					'disabled' => false === $user_cg,
					'url' => array( 'controller' => 'cohortescers93', 'action' => 'validationcadre'  )
				),
			),
			'5. Tableau de suivi' => array( 'class' => 'search', 'url' => array( 'controller' => 'cohortescers93', 'action' => 'visualisation'  ) ),
		),
		'Tableaux de bord' => array(
			'Principal' => array(
				'disabled' => ((boolean)Configure::read('Module.Dashboards.enabled') === false),
				'url' => array('controller' => 'dashboards', 'action' => 'index'),
			),
			'Editeur de requete' => array(
				'disabled' => ( (boolean)Configure::read( 'Requestmanager.enabled' ) === false ),
				'url' => array( 'controller' => 'requestsmanager', 'action' => 'index' ),
			),
			'Indicateurs mensuels' => (
				( $departement == 66 )
				? array(
					'disabled' => ( $departement != 66 ),
					'Généralités' => array( 'url' => array( 'controller' => 'indicateursmensuels', 'action' => 'index' ) ),
					'Nombre d\'allocataires' => array( 'url' => array( 'controller' => 'indicateursmensuels', 'action' => 'nombre_allocataires' ) ),
					'Les orientations' => array( 'url' => array( 'controller' => 'indicateursmensuels', 'action' => 'orientations' ) ),
					'Les CER' => array( 'url' => array( 'controller' => 'indicateursmensuels', 'action' => 'contratsinsertion' ) ),
				)
				: array(
				    'disabled' => ( $departement == 66 ),
                    'Statistiques générales' => array( 'url' => array( 'controller' => 'indicateursmensuels', 'action' => 'index' ) ),
                    'RDV & CER' => array( 'url' => array( 'controller' => 'indicateursmensuels', 'action' => 'rdvcer' ) ),
                    'RDV & CER - Par vagues' => array( 'url' => array( 'controller' => 'indicateursmensuels', 'action' => 'rdvcervagues' ) ),
				)
			),
			'Statistiques ministérielles' => array(
				'Indicateurs d\'orientations' => array( 'url' => array( 'controller' => 'statistiquesministerielles', 'action' => 'indicateurs_orientations'  ) ),
				'Indicateurs d\'organismes' => array( 'url' => array( 'controller' => 'statistiquesministerielles', 'action' => 'indicateurs_organismes'  ) ),
				'Indicateurs de délais' => array( 'url' => array( 'controller' => 'statistiquesministerielles', 'action' => 'indicateurs_delais'  ) ),
				'Indicateurs de réorientations' => array( 'url' => array( 'controller' => 'statistiquesministerielles', 'action' => 'indicateurs_reorientations'  ) ),
				'Indicateurs de motifs de réorientation' => array( 'url' => array( 'controller' => 'statistiquesministerielles', 'action' => 'indicateurs_motifs_reorientation'  ) ),
				'Indicateurs de caractéristiques de contrats' => array( 'url' => array( 'controller' => 'statistiquesministerielles', 'action' => 'indicateurs_caracteristiques_contrats'  ) ),
				'Indicateurs de natures des actions des contrats' => array( 'url' => array( 'controller' => 'statistiquesministerielles', 'action' => 'indicateurs_natures_contrats'  ) ),
			),
			'Statistiques DREES' => array (
				'Tableau 1' => array( 'url' => array( 'controller' => 'statistiquesdrees', 'action' => 'indicateurs_tableau1'  ) ),
				'Tableau 2' => array( 'url' => array( 'controller' => 'statistiquesdrees', 'action' => 'indicateurs_tableau2'  ) ),
				'Tableau 3' => array( 'url' => array( 'controller' => 'statistiquesdrees', 'action' => 'indicateurs_tableau3'  ) ),
				'Tableau 4' => array( 'url' => array( 'controller' => 'statistiquesdrees', 'action' => 'indicateurs_tableau4'  ) ),
				'Tableau 5' => array( 'url' => array( 'controller' => 'statistiquesdrees', 'action' => 'indicateurs_tableau5'  ) ),
				'Tableau 6' => array( 'url' => array( 'controller' => 'statistiquesdrees', 'action' => 'indicateurs_tableau6'  ) ),
			),
			__d( 'statistiquesplanpauvrete', 'Statistiquesplanpauvrete.menu' ) => array (
				'disabled' => !Configure::read ('Module.Statistiques.Plan.Pauvrete.version1'),
				__d( 'statistiquesplanpauvrete', 'Statistiquesplanpauvrete.menu.indicateurs_tableau_a1' ) =>
					array( 'url' => array( 'controller' => 'statistiquesplanpauvrete', 'action' => 'indicateurs_tableau_a1' ) ),
				__d( 'statistiquesplanpauvrete', 'Statistiquesplanpauvrete.menu.indicateurs_tableau_a2' ) =>
					array( 'url' => array( 'controller' => 'statistiquesplanpauvrete', 'action' => 'indicateurs_tableau_a2' ) ),
				__d( 'statistiquesplanpauvrete', 'Statistiquesplanpauvrete.menu.indicateurs_tableau_b1' ) =>
					array( 'url' => array( 'controller' => 'statistiquesplanpauvrete', 'action' => 'indicateurs_tableau_b1' ) ),
				__d( 'statistiquesplanpauvrete', 'Statistiquesplanpauvrete.menu.indicateurs_tableau_b4' ) =>
					array( 'url' => array( 'controller' => 'statistiquesplanpauvrete', 'action' => 'indicateurs_tableau_b4' ) ),
				__d( 'statistiquesplanpauvrete', 'Statistiquesplanpauvrete.menu.indicateurs_tableau_b5' ) =>
					array( 'url' => array( 'controller' => 'statistiquesplanpauvrete', 'action' => 'indicateurs_tableau_b5' ) ),
			),
			__d( 'statistiquesplanpauvrete', 'Statistiquesplanpauvrete.menu.V2' ) => array (
				'disabled' => !Configure::read ('Module.Statistiques.Plan.Pauvrete.version2'),
				__d( 'statistiquesplanpauvrete', 'Statistiquesplanpauvrete.menu.indicateurs_tableau_a1.V2' ) =>
					array( 'url' => array( 'controller' => 'statistiquesplanpauvrete', 'action' => 'indicateurs_tableau_a1v2' ) ),
				__d( 'statistiquesplanpauvrete', 'Statistiquesplanpauvrete.menu.indicateurs_tableau_a2.V2' ) => array (
					__d( 'statistiquesplanpauvrete', 'Statistiquesplanpauvrete.menu.indicateurs_tableau_a2a.V2' ) =>
						array( 'url' => array( 'controller' => 'statistiquesplanpauvrete', 'action' => 'indicateurs_tableau_a2av2' ) ),
					__d( 'statistiquesplanpauvrete', 'Statistiquesplanpauvrete.menu.indicateurs_tableau_a2b.V2' ) =>
						array( 'url' => array( 'controller' => 'statistiquesplanpauvrete', 'action' => 'indicateurs_tableau_a2bv2' ) ),
				)
			),
			__d( 'statistiquesplanpauvrete', 'Statistiquesplanpauvrete.menu.V3' ) => array (
				'disabled' => !Configure::read ('Module.Statistiques.Plan.Pauvrete.version3'),
				__d( 'statistiquesplanpauvrete', 'Statistiquesplanpauvrete.menu.indicateurs_tableau_a1.V2' ) =>
					array( 'url' => array( 'controller' => 'statistiquesplanpauvrete', 'action' => 'indicateurs_tableau_a1v3' ) ),
				__d( 'statistiquesplanpauvrete', 'Statistiquesplanpauvrete.menu.indicateurs_tableau_a2.V2' ) => array (
					__d( 'statistiquesplanpauvrete', 'Statistiquesplanpauvrete.menu.indicateurs_tableau_a2a.V2' ) =>
						array( 'url' => array( 'controller' => 'statistiquesplanpauvrete', 'action' => 'indicateurs_tableau_a2av3' ) ),
					__d( 'statistiquesplanpauvrete', 'Statistiquesplanpauvrete.menu.indicateurs_tableau_a2b.V2' ) =>
						array( 'url' => array( 'controller' => 'statistiquesplanpauvrete', 'action' => 'indicateurs_tableau_a2bv3' ) ),
				)
			),
			'Indicateurs de suivi' => array( 'url' => array( 'controller' => 'indicateurssuivis', 'action' => 'search' ) ),
			'Tableaux de bord RSA' => array(
				'disabled' => ( $departement != 93 ),
				__d( 'tableauxbords93', '/Tableauxbords93/tableau1/:heading' ) => array( 'url' => array( 'controller' => 'tableauxbords93', 'action' => 'tableau1' ) ),
				__d( 'tableauxbords93', '/Tableauxbords93/tableau2/:heading' ) => array( 'url' => array( 'controller' => 'tableauxbords93', 'action' => 'tableau2' ) ),
				
			),
			'Tableaux de suivi d\'activité' => array(
				'disabled' => ( $departement != 93 ),
				__d( 'tableauxsuivispdvs93', '/Tableauxsuivispdvs93/index/:heading' ) => array( 'url' => array( 'controller' => 'tableauxsuivispdvs93', 'action' => 'index' ) ),
				__d( 'tableauxsuivispdvs93', '/Tableauxsuivispdvs93/tableaud1/:title' ) => array( 'url' => array( 'controller' => 'tableauxsuivispdvs93', 'action' => 'tableaud1' ) ),
				__d( 'tableauxsuivispdvs93', '/Tableauxsuivispdvs93/tableau1b3/:heading' ) => array( 'url' => array( 'controller' => 'tableauxsuivispdvs93', 'action' => 'tableau1b3' ) ),
				__d( 'tableauxsuivispdvs93', '/Tableauxsuivispdvs93/tableau1b4/:heading' ) => array( 'url' => array( 'controller' => 'tableauxsuivispdvs93', 'action' => 'tableau1b4' ) ),
				__d( 'tableauxsuivispdvs93', '/Tableauxsuivispdvs93/tableau1b5/:heading' ) => array( 'url' => array( 'controller' => 'tableauxsuivispdvs93', 'action' => 'tableau1b5' ) ),
				__d( 'tableauxsuivispdvs93', '/Tableauxsuivispdvs93/tableau1b6/:heading' ) => array( 'url' => array( 'controller' => 'tableauxsuivispdvs93', 'action' => 'tableau1b6' ) ),
				__d( 'tableauxsuivispdvs93', '/Tableauxsuivispdvs93/tableaub7/:menu' ) => array( 'url' => array( 'controller' => 'tableauxsuivispdvs93', 'action' => 'tableaub7' ) ),
				__d( 'tableauxsuivispdvs93', '/Tableauxsuivispdvs93/tableaub7d2typecontrat/:menu' ) => array( 'url' => array( 'controller' => 'tableauxsuivispdvs93', 'action' => 'tableaub7d2typecontrat' ) ),
				__d( 'tableauxsuivispdvs93', '/Tableauxsuivispdvs93/tableaub7d2familleprofessionnelle/:menu' ) => array( 'url' => array( 'controller' => 'tableauxsuivispdvs93', 'action' => 'tableaub7d2familleprofessionnelle' ) ),
				__d( 'tableauxsuivispdvs93', '/Tableauxsuivispdvs93/tableaub8/:heading' ) => array( 'url' => array( 'controller' => 'tableauxsuivispdvs93', 'action' => 'tableaub8' ) ),
				__d( 'tableauxsuivispdvs93', '/Tableauxsuivispdvs93/tableaub9/:heading' ) => array( 'url' => array( 'controller' => 'tableauxsuivispdvs93', 'action' => 'tableaub9' ) ),
				__d( 'tableauxsuivispdvs93', '/Tableauxsuivispdvs93/tableaud2/:heading' ) => array( 'url' => array( 'controller' => 'tableauxsuivispdvs93', 'action' => 'tableaud2' ) ),
			),
			'Rsa Query'  => array(
				'disabled' => ( $departement != 93 ),
				 'url' => array( 'controller' => 'requetes', 'action' => 'index' ),
			),
		),
		'Administration' => array(
			'Paramétrages' => array( 'url' => array( 'controller' => 'parametrages', 'action' => 'index'  ) ),
			'Paiement allocation' => array(
				'Listes nominatives' => array( 'url' => array( 'controller' => 'infosfinancieres', 'action' => 'indexdossier' ) ),
				'Mandats mensuels' => array( 'url' => array( 'controller' => 'totalisationsacomptes', 'action' => 'index' ) ),
			),
			'Administration Créances' => array(
				'Listes Entrants Créanciers' => array( 'url' => array( 'controller' => 'creances', 'action' => 'dossierEntrantsCreanciers' ) ),
			),
			__d('configuration', 'Configuration') => array(
				'url' => array( 'controller' => 'configurations', 'action' => 'index' )
			),
			'Gestion des anomalies' => array(
				'Doublons simples' => array(
					'class' => 'search',
					'url' => array( 'controller' => 'gestionsanomaliesbdds', 'action' => 'index'  ),
					'title' => 'Gestion des anomalies de doublons simples au sein d\'un foyer donné',
				),
				'Doublons complexes' => array(
					'class' => 'search',
					'url' => array( 'controller' => 'gestionsdoublons', 'action' => 'index'  ),
					'title' => 'Gestion des anomalies de doublons complexes au sein de foyers différents',
				),
			),
			__d( 'droit', 'controllers/Dossierseps/administration' ) => array(
				'class' => 'search',
				'url' => array( 'controller' => 'dossierseps', 'action' => 'administration'  ),
			),
			'Habilitations' => array(
				'Groupes' => array(
					'url' => array( 'controller' => 'groups', 'action' => 'index' ),
					'title' => 'Gestion des groupes d\'utilisateurs',
				),
				'Utilisateurs' => array(
					'class' => 'search',
					'url' => array( 'controller' => 'users', 'action' => 'index' ),
					'title' => 'Gestion des utilisateurs',
				),
				'Synthese' => array(
					'disabled' => !Configure::read('Module.Synthesedroits.enabled'),
					'url' => array( 'controller' => 'synthesedroits', 'action' => 'index' ),
					'title' => 'Synthese des droits',
				)
			),
			'Vérification de l\'application' => array(
				'url' => array( 'controller' => 'checks', 'action' => 'index' ),
			),
			'Rapport Talends' => array(
				'url' => array( 'controller' => 'visionneuses', 'action' => 'index' ),
				'title' => 'logs'
			),
			'Flux CNAF' => array(
				'disabled' => true !== Configure::read( 'Module.Fluxcnaf.enabled' ),
				'Résumé' => array(
					'url' => array( 'plugin' => 'fluxcnaf', 'controller' => 'fluxcnaf', 'action' => 'index' ),
					'title' => 'Comparaison des flux CNAF',
				),
				'VRSD0301, VRSD0101' => array(
					'url' => array( 'plugin' => 'fluxcnaf', 'controller' => 'fluxcnaf', 'action' => 'diffs' ),
					'title' => 'Comparaison entre les flux VRSD0301 et VRSD0101',
				),
			),
			'Log trace' => array(
				'disabled' => !Configure::read('Module.Logtrace.enabled'),
				'url' => array('controller' => 'logtraces', 'action' => 'index'),
			),
			'Gestion des vagues' => array(
				'disabled' => !(Configure::read('Indicateursmensuels.vaguesdorientations')),
				'url' => array( 'controller' => 'vaguesdorientations', 'action' => 'index' ),
			),
		),
		'Déconnexion '.$this->Session->read( 'Auth.User.username' ) => array(
			'url' => array( 'controller' => 'users', 'action' => 'logout' )
		)
	);

	echo $this->Menu->make2( $items, 'a' );
?>
	</div>
</div>
<script type="text/javascript">
//<![CDATA[
$$( '#menu1Wrapper li.branch' ).each(
	function( elmt ) {
		$(elmt).observe( 'mouseover', function() { $(this).addClassName( 'hover' ); } );
		$(elmt).observe( 'mouseout', function() { $(this).removeClassName( 'hover' ); } );
    }
);

$('menu1Wrapper').select('li').each(function(li){
	li.observe('click', function(event){
		var parent, active = li.hasClassName('forceHover');

		event.preventDefault();
		event.stopPropagation();

		// Reset all force* classes
		$('menu1Wrapper').select('li').each(function(subli){
			subli.removeClassName('forceHover');
			subli.removeClassName('forceHide');
		});

		if (!active) {
			// On cache les sous elements des li voisins
			li.siblings().each(function(subli){
				subli.select('li').each(function(subsubli){
					subsubli.addClassName('forceHide');
				});
			});

			// On applique forceHover sur l'element
			li.addClassName('forceHover');
		}

		if (li.hasClassName('forceHover')) {
			parent = li.up('li');
			while (true) {
				if (parent === undefined) {
					break;
				}

				parent.addClassName('forceHover');
				parent.removeClassName('forceHide');
				parent = parent.up('li');
			}
		}
	});
});

// Empeche le code ci-dessus de s'appliquer lors d'un clic sur un véritable lien
$('menu1Wrapper').select('a').each(function(a) {
	a.observe('click', function(event){
		if (a.getAttribute('href') !== '#') {
			event.stopPropagation();
		}
	});
});

document.observe('click', function(){
	// Reset all forceHover
	$('menu1Wrapper').select('li').each(function(subli){ subli.removeClassName('forceHover'); });
});
//]]>
</script>
<?php endif;?>