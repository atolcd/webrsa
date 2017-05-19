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
			'Non orientation' => array(
				'disabled' => ( $departement != 66 ),
				'Inscrits PE' => array( 'class' => 'search', 'url' => array( 'controller' => 'nonorientes66', 'action' => 'cohorte_isemploi' ) ),
				'Non inscrits PE' => array( 'class' => 'search', 'url' => array( 'controller' => 'nonorientes66', 'action' => 'cohorte_imprimeremploi' ) ),
				'Gestion des réponses' => array( 'class' => 'search', 'url' => array( 'controller' => 'nonorientes66', 'action' => 'cohorte_reponse' ) ),
				'Notifications à envoyer' => array( 'class' => 'search', 'url' => array( 'controller' => 'nonorientes66', 'action' => 'cohorte_imprimernotifications' ) ),
				'Orientés et notifiés' => array( 'class' => 'search', 'url' =>  array( 'controller' => 'nonorientes66', 'action' => 'recherche_notifie' ) ),
			),
			'Orientation' => array(
				'Demandes non orientées' => array( 'class' => 'search', 'url' => array( 'controller' => 'orientsstructs', 'action' => 'cohorte_nouvelles' ) ),
				'Demandes en attente de validation d\'orientation' => array( 'class' => 'search', 'url' => array( 'controller' => 'orientsstructs', 'action' => 'cohorte_enattente' ) ),
				'Demandes orientées' => array( 'class' => 'search', 'url' => array( 'controller' => 'orientsstructs', 'action' => 'cohorte_orientees' ) ),
			),
			'PDOs' => array(
				'disabled' => ( $departement != 93 ),
				'Nouvelles demandes' => array(
					'class' => 'search',
					'url' => array( 'controller' => 'propospdos', 'action' => 'cohorte_nouvelles' ),
					'title' => 'Avis CG demandé',
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
			'Transferts PDV' => array(
				'disabled' => ( $departement != 93 ),
				'Allocataires à transférer' => array( 'class' => 'search', 'url' => array( 'controller' => 'transfertspdvs93', 'action' => 'cohorte_atransferer' ) ),
				'Allocataires transférés' => array( 'class' => 'search', 'url' => array( 'controller' => 'cohortestransfertspdvs93', 'action' => 'transferes' ) ),
			),
            'Clôture référents' => array(
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
				'disabled' => ( $departement != 66 ),
				'url' => array( 'controller' => 'tags', 'action' => 'cohorte' )
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
				__d( 'nonorientationsproscovs58', '/Nonorientationsproscovs58/cohorte1/:heading' ) => array( 'url' => array( 'class' => 'search', 'controller' => 'nonorientationsproscovs58', 'action' => 'cohorte1' ) ),
				__d( 'nonorientationsproscovs58', '/Nonorientationsproscovs58/cohorte/:heading' ) => array( 'url' => array( 'class' => 'search', 'controller' => 'nonorientationsproscovs58', 'action' => 'cohorte' ) )
			),
			'Par Dossiers EP' => array(
				'disabled' => ( $departement != 58 ),
				'Radiation de Pôle Emploi' => array( 'class' => 'search', 'url' => array( 'controller' => 'sanctionseps58', 'action' => 'cohorte_radiespe' ) ),
				'Non inscription à Pôle Emploi' => array( 'class' => 'search', 'url' => array( 'controller' => 'sanctionseps58', 'action' => 'cohorte_noninscritspe' ) ),
			),
			'Par Bilans de parcours' => array(
				'class' => 'search',
				'disabled' => ( $departement != 66 ),
				'url' => array( 'controller' => 'bilansparcours66', 'action' => 'search'  ),
			),
			'Pôle Emploi' => array(
				'disabled' => ( $departement != 66 ),
				'Non inscrits au Pôle Emploi' => array( 'class' => 'search', 'url' => array( 'controller' => 'defautsinsertionseps66', 'action' => 'search_noninscrits'  ) ),
				'Radiés de Pôle Emploi' => array( 'class' => 'search', 'url' => array( 'controller' => 'defautsinsertionseps66', 'action' => 'search_radies'  ) ),
			),
			'Demande de maintien dans le social' => array(
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
			'Par fiches de prescription' => array(
				'class' => 'search',
				'disabled' => ( $departement != 93 ),
				'url' => array( 'controller' => 'fichesprescriptions93', 'action' => 'search'  )
			),
			'Par changement d\'adresse' => array(
				'class' => 'search',
				'disabled' => ( $departement != 66 ),
				'url' => array( 'controller' => 'changementsadresses', 'action' => 'search'  )
			),
		),
		'Anciens moteurs' => array(
			'disabled' => (!Configure::read('Anciensmoteurs.enabled')),
			( $departement == 66 ? 'Gestion de listes' : 'Cohortes' ) => array(
				'APRE' => array(
					'disabled' => ( $departement != 66 ),
					'À valider' => array( 'class' => 'search', 'url' => array( 'controller' => 'cohortesvalidationapres66', 'action' => 'apresavalider' ) ),
					'À notifier' => array( 'class' => 'search', 'url' => array( 'controller' => 'cohortesvalidationapres66', 'action' => 'validees' ) ),
					'Notifiées' => array( 'class' => 'search', 'url' => array( 'controller' => 'cohortesvalidationapres66', 'action' => 'notifiees' ) ),
					'Transfert cellule' => array( 'class' => 'search', 'url' => array( 'controller' => 'cohortesvalidationapres66', 'action' => 'transfert' ) ),
					'Traitement cellule' => array( 'class' => 'search', 'url' =>  array( 'controller' => 'cohortesvalidationapres66', 'action' => 'traitement' ) ),
				),
				'CER' => array(
					'disabled' => ( !in_array( $departement, array( 66, 93 ) ) ),
					'Contrats Simples à valider' => array(
						'class' => 'search',
						'disabled' => ( $departement != 66 ),
						'url' => array( 'controller' => 'cohortesci', 'action' => 'nouveauxsimple' )
					),
					'Contrats Particuliers à valider' => array(
						'class' => 'search',
						'disabled' => ( $departement != 66 ),
						'url' => array( 'controller' => 'cohortesci', 'action' => 'nouveauxparticulier' )
					),
					'Décisions prises' => array(
						'class' => 'search',
						'disabled' => ( $departement != 66 ),
						'url' => array( 'controller' => 'cohortesci', 'action' => 'valides' )
					),
					'Contrats à valider' => array(
						'class' => 'search',
						'disabled' => ( $departement != 93 ),
						'url' => array( 'controller' => 'cohortesci', 'action' => 'nouveaux' )
					),
					'Contrats validés' => array(
						'class' => 'search',
						'disabled' => ( $departement != 93 ),
						'url' => array( 'controller' => 'cohortesci', 'action' => 'valides' )
					),
				),
				'Fiches de candidature' => array(
					'disabled' => ( $departement != 66 ),
					'Fiches en attente' => array( 'class' => 'search', 'url' => array( 'controller' => 'cohortesfichescandidature66', 'action' => 'fichesenattente' ) ),
					'Fiches en cours' => array( 'class' => 'search', 'url' => array( 'controller' => 'cohortesfichescandidature66', 'action' => 'fichesencours' ) ),
				),
				'Dossiers PCGs' => array(
					'disabled' => ( $departement != 66 ),
					'Dossiers en attente d\'affectation' => array( 'class' => 'search', 'url' => array( 'controller' => 'cohortesdossierspcgs66', 'action' => 'enattenteaffectation' ) ),
					'Dossiers affectés' => array( 'class' => 'search', 'url' => array( 'controller' => 'cohortesdossierspcgs66', 'action' => 'affectes' ) ),
					'Dossiers à imprimer' => array( 'class' => 'search', 'url' => array( 'controller' => 'cohortesdossierspcgs66', 'action' => 'aimprimer' ) ),
					'Dossiers à transmettre' => array( 'class' => 'search', 'url' => array( 'controller' => 'cohortesdossierspcgs66', 'action' => 'atransmettre' ) ),
				),
				'Non orientation' => array(
					'disabled' => ( $departement != 66 ),
					'Inscrits PE' => array( 'class' => 'search', 'url' => array( 'controller' => 'cohortesnonorientes66', 'action' => 'isemploi' ) ),
					'Non inscrits PE' => array( 'class' => 'search', 'url' => array( 'controller' => 'cohortesnonorientes66', 'action' => 'notisemploiaimprimer' ) ),
					'Gestion des réponses' => array( 'class' => 'search', 'url' => array( 'controller' => 'cohortesnonorientes66', 'action' => 'notisemploi' ) ),
					'Notifications à envoyer' => array( 'class' => 'search', 'url' => array( 'controller' => 'cohortesnonorientes66', 'action' => 'notifaenvoyer' ) ),
					'Orientés et notifiés' => array( 'class' => 'search', 'url' =>  array( 'controller' => 'cohortesnonorientes66', 'action' => 'oriente' ) ),
				),
				'Orientation' => array(
					'Demandes non orientées' => array( 'class' => 'search', 'url' => array( 'controller' => 'cohortes', 'action' => 'nouvelles' ) ),
					'Demandes en attente de validation d\'orientation' => array( 'class' => 'search', 'url' => array( 'controller' => 'cohortes', 'action' => 'enattente' ) ),
					'Demandes orientées' => array( 'class' => 'search', 'url' => array( 'controller' => 'cohortes', 'action' => 'orientees' ) ),
				),
				'PDOs' => array(
					'disabled' => ( $departement != 93 ),
					'Nouvelles demandes' => array(
						'class' => 'search',
						'url' => array( 'controller' => 'cohortespdos', 'action' => 'avisdemande' ),
						'title' => 'Avis CG demandé',
					),
					'Liste PDOs' => array(
						'class' => 'search',
						'url' => array( 'controller' => 'cohortespdos', 'action' => 'valide' ),
						'title' => 'PDOs validés',
					),
				),
				'EPs' => array(
					'disabled' => ( $departement != 93 ),
					'Relances (EP)' => array(
						__d( 'relancenonrespectsanctionep93', 'Relancesnonrespectssanctionseps93::cohorte', true ) => array( 'class' => 'search', 'url' => array( 'controller' => 'relancesnonrespectssanctionseps93', 'action' => 'cohorte' ) ),
						__d( 'relancenonrespectsanctionep93', 'Relancesnonrespectssanctionseps93::impressions', true ) => array( 'class' => 'search', 'url' => array( 'controller' => 'relancesnonrespectssanctionseps93', 'action' => 'impressions' ) ),
					),
					'Parcours social sans réorientation' => array( 'class' => 'search', 'url' => array( 'controller' => 'nonorientationsproseps', 'action' => 'index' ) ),
					'Radiés de Pôle Emploi' => array( 'class' => 'search', 'url' => array( 'controller' => 'nonrespectssanctionseps93', 'action' => 'selectionradies'  ) ),
				),
				'Transferts PDV' => array(
					'disabled' => ( $departement != 93 ),
					'Allocataires à transférer' => array( 'class' => 'search', 'url' => array( 'controller' => 'cohortestransfertspdvs93', 'action' => 'atransferer' ) ),
					'Allocataires transférés' => array( 'class' => 'search', 'url' => array( 'controller' => 'cohortestransfertspdvs93', 'action' => 'transferes' ) ),
				),
				'Clôture référents' => array(
					'class' => 'search',
					'disabled' => ( $departement != 93 ),
					'url' => array( 'controller' => 'referents', 'action' => 'clotureenmasse' )
				),
				__d( 'cohortesd2pdvs93', '/Cohortesd2pdvs93/index/:heading' ) => array(
					'class' => 'search',
					'disabled' => ( $departement != 93 ),
					'url' => array( 'controller' => 'cohortesd2pdvs93', 'action' => 'index' )
				),
				__d( 'cohortesrendezvous', '/Cohortesrendezvous/cohorte/:heading' ) => array(
					'class' => 'search',
					'disabled' => ( $departement != 93 ),
					'url' => array( 'controller' => 'cohortesrendezvous', 'action' => 'cohorte' )
				),
			),
			'Recherches' => array(
				'Par dossier / allocataire' => array( 'class' => 'search', 'url' => array( 'controller' => 'dossiers', 'action' => 'index' ) ),
				'Par Orientation' => array( 'class' => 'search', 'url' => array( 'controller' => 'criteres', 'action' => 'index' ) ),
				'Par APREs' => array(
					'class' => 'search',
					'disabled' => ( $departement != 66 ),
					'url' => array( 'controller' => 'criteresapres', 'action' => 'all' )
				),
				'Par Contrats' => array(
					'Par CER' => array( 'class' => 'search', 'url' => array( 'controller' => 'criteresci', 'action' => 'index'  ) ),
					'Par CUI' => array(
						'class' => 'search',
						'url' => array( 'controller' => 'criterescuis', 'action' => 'search'  ),
						'disabled' => ( $departement != 66 )
					),
				),
				'Par Entretiens' => array( 'class' => 'search', 'url' => array( 'controller' => 'criteresentretiens', 'action' => 'index' ) ),
				'Par Fiches de candidature' => array(
					'class' => 'search',
					'disabled' => ( $departement != 66 ),
					'url' => array( 'controller' => 'criteresfichescandidature', 'action' => 'index' )
				),
				'Par Indus' => array( 'class' => 'search', 'url' => array( 'controller' => 'cohortesindus', 'action' => 'index' ) ),
				'Par DSPs' => array( 'class' => 'search', 'url' => array( 'controller' => 'dsps', 'action' => 'index' ) ),
				'Par Rendez-vous' => array( 'class' => 'search', 'url' => array( 'controller' => 'criteresrdv', 'action' => 'index'  ) ),
				'Par Dossiers PCGs' => array(
					'disabled' => ( $departement != 66 ),
					'Dossiers PCGs' => array( 'class' => 'search', 'url' => array( 'controller' => 'criteresdossierspcgs66', 'action' => 'dossier'  ) ),
					'Traitements PCGs' => array( 'class' => 'search', 'url' => array( 'controller' => 'criterestraitementspcgs66', 'action' => 'index'  ) ),
					'Gestionnaires PCGs' => array( 'class' => 'search', 'url' => array( 'controller' => 'criteresdossierspcgs66', 'action' => 'gestionnaire'  ) ),
				),
				'Par PDOs' => array(
					'disabled' => ( $departement == 66 ),
					'Nouvelles PDOs' => array( 'class' => 'search', 'url' => array( 'controller' => 'criterespdos', 'action' => 'nouvelles'  ) ),
					'Liste des PDOs' => array( 'class' => 'search', 'url' => array( 'controller' => 'criterespdos', 'action' => 'index'  ) ),
				),
				'Par Dossiers COV' => array(
					'class' => 'search',
					'disabled' => ( $departement != 58 ),
					'url' => array( 'controller' => 'criteresdossierscovs58', 'action' => 'index'  ),
					__d( 'nonorientationsproscovs58', '/Nonorientationsproscovs58/cohorte1/:heading' ) => array( 'class' => 'search', 'url' => array( 'controller' => 'nonorientationsproscovs58', 'action' => 'cohorte1' ) ),
					__d( 'nonorientationsproscovs58', '/Nonorientationsproscovs58/cohorte/:heading' ) => array( 'class' => 'search', 'url' => array( 'controller' => 'nonorientationsproscovs58', 'action' => 'cohorte' ) )
				),
				'Par Dossiers EP' => array(
					'disabled' => ( $departement != 58 ),
					'Radiation de Pôle Emploi' => array( 'class' => 'search', 'url' => array( 'controller' => 'sanctionseps58', 'action' => 'selectionradies' ) ),
					'Non inscription à Pôle Emploi' => array( 'class' => 'search', 'url' => array( 'controller' => 'sanctionseps58', 'action' => 'selectionnoninscrits' ) ),
				),
				'Par Bilans de parcours' => array(
					'class' => 'search',
					'disabled' => ( $departement != 66 ),
					'url' => array( 'controller' => 'criteresbilansparcours66', 'action' => 'index'  ),
				),
				'Pôle Emploi' => array(
					'class' => 'search',
					'disabled' => ( $departement != 66 ),
					'Non inscrits au Pôle Emploi' => array( 'url' => array( 'controller' => 'defautsinsertionseps66', 'action' => 'selectionnoninscrits'  ) ),
					'Radiés de Pôle Emploi' => array( 'url' => array( 'controller' => 'defautsinsertionseps66', 'action' => 'selectionradies'  ) ),
				),
				'Demande de maintien dans le social' => array(
					'class' => 'search',
					'disabled' => ( $departement != 66 ),
					'url' => array( 'controller' => 'nonorientationsproseps', 'action' => 'index'  )
				),
				'Par allocataires sortants' => array(
					'Intra-département' => array(
						'class' => 'search',
						'disabled' => ( $departement != 93 ),
						'url' => array( 'controller' => 'criterestransfertspdvs93', 'action' => 'index'  )
					),
					'Hors département' => array(
						'class' => 'search',
						'url' => array( 'controller' => 'demenagementshorsdpts', 'action' => 'search1'  )
					),
				),
				'Par fiches de prescription' => array(
					'class' => 'search',
					'disabled' => ( $departement != 93 ),
					'url' => array( 'controller' => 'fichesprescriptions93', 'action' => 'search1'  )
				),
			),
			'CER' => array(
				'disabled' => ( $departement != 93 ),
				'1. Affectation d\'un référent' => array( 'class' => 'search', 'url' => array( 'controller' => 'cohortesreferents93', 'action' => 'affecter'  ) ),
			),
		),
		'APRE' => array(
			'disabled' => ( $departement != 93 || true === $user_externe ),
			'Liste des demandes d\'APRE' => array(
				'Toutes les APREs' => array( 'class' => 'search', 'url' => array( 'controller' => 'criteresapres', 'action' => 'all' ) ),
				'Toutes les APREs (nouveau)' => array( 'class' => 'search', 'url' => array( 'controller' => 'apres', 'action' => 'search' ) ),
				'Eligibilité des APREs' => array( 'class' => 'search', 'url' => array( 'controller' => 'criteresapres', 'action' => 'eligible' ) ),
				'Eligibilité des APREs (nouveau)' => array( 'class' => 'search', 'url' => array( 'controller' => 'apres', 'action' => 'search_eligibilite' ) ),
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
				( $departement == 66 ? 'Décisions CG' : 'CG' ) => array(
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
			'1. Affectation d\'un référent' => array(
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
			'4. Décision CG' => array(
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
					'url' => array( 'controller' => 'indicateursmensuels', 'action' => 'index' ),
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
			'Indicateurs de suivi' => array( 'url' => array( 'controller' => 'indicateurssuivis', 'action' => 'search' ) ),
			'Tableaux de suivi d\'activité' => array(
				'disabled' => ( $departement != 93 ),
				__d( 'tableauxsuivispdvs93', '/Tableauxsuivispdvs93/index/:heading' ) => array( 'url' => array( 'controller' => 'tableauxsuivispdvs93', 'action' => 'index' ) ),
				__d( 'tableauxsuivispdvs93', '/Tableauxsuivispdvs93/tableaud1/:heading' ) => array( 'url' => array( 'controller' => 'tableauxsuivispdvs93', 'action' => 'tableaud1' ) ),
				__d( 'tableauxsuivispdvs93', '/Tableauxsuivispdvs93/tableaud2/:heading' ) => array( 'url' => array( 'controller' => 'tableauxsuivispdvs93', 'action' => 'tableaud2' ) ),
				__d( 'tableauxsuivispdvs93', '/Tableauxsuivispdvs93/tableau1b3/:heading' ) => array( 'url' => array( 'controller' => 'tableauxsuivispdvs93', 'action' => 'tableau1b3' ) ),
				__d( 'tableauxsuivispdvs93', '/Tableauxsuivispdvs93/tableau1b4/:heading' ) => array( 'url' => array( 'controller' => 'tableauxsuivispdvs93', 'action' => 'tableau1b4' ) ),
				__d( 'tableauxsuivispdvs93', '/Tableauxsuivispdvs93/tableau1b5/:heading' ) => array( 'url' => array( 'controller' => 'tableauxsuivispdvs93', 'action' => 'tableau1b5' ) ),
				__d( 'tableauxsuivispdvs93', '/Tableauxsuivispdvs93/tableau1b6/:heading' ) => array( 'url' => array( 'controller' => 'tableauxsuivispdvs93', 'action' => 'tableau1b6' ) ),
			),
		),
		'Administration' => array(
			'Paramétrages' => array( 'url' => array( 'controller' => 'parametrages', 'action' => 'index'  ) ),
			'Paiement allocation' => array(
				'Listes nominatives' => array( 'url' => array( 'controller' => 'infosfinancieres', 'action' => 'indexdossier' ) ),
				'Mandats mensuels' => array( 'url' => array( 'controller' => 'totalisationsacomptes', 'action' => 'index' ) ),
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
			__d( 'droit', 'Dossierseps:administration' ) => array(
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
				'Par Controllers' => array(
					'disabled' => !Configure::read('Module.Attributiondroits.enabled'),
					'url' => array('controller' => 'accesses', 'action' => 'setbygroups'),
					'title' => 'Attribution des droits par controller (multi groupes)',
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
			'Visionneuse' => array(
				'logs' => array( 'url' => array( 'controller' => 'visionneuses', 'action' => 'index' ) ),
			),
			'Flux CNAF' => array(
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
			)
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