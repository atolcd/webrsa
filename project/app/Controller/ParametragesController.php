<?php
	/**
	 * Code source de la classe ParametragesController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppController', 'Controller' );

	/**
	 * La classe ParametragesController ...
	 *
	 * @package app.Controller
	 */
	class ParametragesController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Parametrages';

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Default3' => array(
				'className' => 'Default.DefaultDefault'
			)
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Cataloguepdifp93',
			'Catalogueromev3',
			'Contactpartenaire',
			'Courrierpdo',
			'Partenaire',
			'Pieceaide66',
			'Themeapre66'
		);

		/**
		 * Paramétrages des APRE/ADRE
		 *
		 * @return array
		 */
		protected function _apres() {
			$departement = (integer)Configure::read( 'Cg.departement' );

			if( 66 === $departement ) {
				$items = array(
					'Liste des aides de l\'APRE/ADRE' => array(
						'url' => array( 'controller' => 'typesaidesapres66', 'action' => 'index' )
					),
					'Liste des pièces administratives' => array(
						'url' => array( 'controller' => 'piecesaides66', 'action' => 'index' )
					),
					'Liste des pièces comptables' => array(
						'url' => array( 'controller' => 'piecescomptables66', 'action' => 'index' )
					),
					'Thèmes de la demande d\'aide APRE/ADRE' => array(
						'url' => array( 'controller' => 'themesapres66', 'action' => 'index' )
					)
				);
			}
			else if( 93 === $departement ) {
				$items = array(
					'Paramètres financiers pour la gestion de l\'APRE' => array(
						'url' => array( 'controller' => 'parametresfinanciers', 'action' => 'index' )
					),
					'Participants comités APRE' => array(
						'url' => array( 'controller' => 'participantscomites', 'action' => 'index' )
					),
					'Personnes chargées du suivi des aides APRE' => array(
						'url' => array( 'controller' => 'suivisaidesapres', 'action' => 'index' )
					),
					'Tiers prestataires de l\'APRE' => array(
						'url' => array( 'controller' => 'tiersprestatairesapres', 'action' => 'index' )
					),
					'Types d\'aides liées aux personnes chargées du suivi de l\'APRE' => array(
						'url' => array( 'controller' => 'suivisaidesaprestypesaides', 'action' => 'index' )
					)
				);
			}
			else {
				$items = array( 'disabled' => true );
			}

			return $items;
		}

		/**
		 * Paramétrages des CER.
		 *
		 * @return array
		 */
		protected function _contratsinsertion() {
			$departement = (integer)Configure::read( 'Cg.departement' );

			if( 66 === $departement ) {
				$items = array(
					'Motifs de non validation de CER' => array(
						'url' => array( 'controller' => 'motifscersnonvalids66', 'action' => 'index' )
					)
				);
			}
			else if( 93 === $departement ) {
				$items = array(
					'Commentaires normés' => array(
						'url' => array( 'controller' => 'commentairesnormescers93', 'action' => 'index' )
					),
					'Métiers exercés' => array(
						'url' => array( 'controller' => 'metiersexerces', 'action' => 'index' )
					),
					'Natures de contrat' => array(
						'url' => array( 'controller' => 'naturescontrats', 'action' => 'index' )
					),
					'Secteurs d\'activité' => array(
						'url' => array( 'controller' => 'secteursactis', 'action' => 'index' )
					),
					'Sujets du CER' => array(
						'url' => array( 'controller' => 'sujetscers93', 'action' => 'index' )
					),
					'Sous-sujets du CER' => array(
						'url' => array( 'controller' => 'soussujetscers93', 'action' => 'index' )
					),
					'Valeurs par sous-sujets du CER' => array(
						'url' => array( 'controller' => 'valeursparsoussujetscers93', 'action' => 'index' )
					)
				);
			}
			else {
				$items = array( 'disabled' => true );
			}

			return $items;
		}

		/**
		 * Paramétrages des code ROME v.3.
		 *
		 * @return array
		 */
		protected function _cataloguesromesv3() {
			if( true == Configure::read( 'Romev3.enabled' ) ) {
				$items = array();
				foreach( $this->Catalogueromev3->modelesParametrages as $modelName ) {
					$tableName = Inflector::tableize( $modelName );
					$items[__d( 'cataloguesromesv3', "/Cataloguesromesv3/{$tableName}/:heading" )] = array(
						'url' => array( 'controller' => 'cataloguesromesv3', 'action' => $tableName )
					);
				}
			}
			else {
				$items = array( 'disabled' => true );
			}

			return $items;
		}

		/**
		 * Paramétrages des CREANCES.
		 *
		 * @return array
		 */
		protected function _creances() {
			$activateTitreCreancier = Configure::read( 'Creances.titrescreanciers' );
			$activateRecoursGracieux = Configure::read( 'Creances.recoursgracieux' );

			$items = array ();
			if( $activateTitreCreancier || $activateRecoursGracieux ) {
				if( $activateTitreCreancier) {
					$items['Titres de recette'] = array(
							'Type du Titre' => array(
								'url' => array( 'controller' => 'typestitrescreanciers', 'action' => 'index' )
							),
							'Motif d\'émission d\'un titre de recette' => array(
								'url' => array( 'controller' => 'motifsemissionstitrescreanciers', 'action' => 'index' )
							)
						);
					$items['Suivi des titres de recette'] = array(
							'Type d\'annulation/réduction' => array(
								'url' => array( 'controller' => 'typestitrescreanciersannulationsreductions', 'action' => 'index' )
							),
							'Type d\'informations payeur' => array(
								'url' => array( 'controller' => 'typestitrescreanciersinfospayeurs', 'action' => 'index' )
							),
							'Type d\'autres informations' => array(
								'url' => array( 'controller' => 'typestitrescreanciersautresinfos', 'action' => 'index' )
							)
						);
				}
				if($activateRecoursGracieux ) {
					$items['Recours Gracieux'] = array(
							'Origines' => array(
								'url' => array( 'controller' => 'originesrecoursgracieux', 'action' => 'index' )
							),
							'Types' => array(
								'url' => array( 'controller' => 'typesrecoursgracieux', 'action' => 'index' )
							),
							'Motifs Propositions' => array(
								'url' => array( 'controller' => 'motifsproposrecoursgracieux', 'action' => 'index' )
							)
						);
				}
			}else{
				$items = array( 'disabled' => true );
			}

			return $items;
		}

		/**
		 * Paramétrages des CUI.
		 *
		 * @return array
		 */
		protected function _cuis() {
			$departement = (integer)Configure::read( 'Cg.departement' );

			if( 66 === $departement && true == Configure::read( 'Module.Cui.enabled' ) ) {
				$items = array(
					'Motifs de rupture' => array(
						'url' => array( 'controller' => 'motifsrupturescuis66', 'action' => 'index' )
					),
					'Motifs de suspension' => array(
						'url' => array( 'controller' => 'motifssuspensioncuis66', 'action' => 'index' )
					),
					'Motifs de décision de refus' => array(
						'url' => array( 'controller' => 'motifsrefuscuis66', 'action' => 'index' )
					),
					'Pièces liées aux mails employeur' => array(
						'url' => array( 'controller' => 'piecesmailscuis66', 'action' => 'index' )
					),
					__d( 'piecemanquantecui66', 'Pièces manquantes mails employeur' ) => array(
						'url' => array( 'controller' => 'piecesmanquantescuis66', 'action' => 'index' )
					),
					'Lien entre les secteurs et les taux' => array(
						'url' => array( 'controller' => 'tauxcgscuis66', 'action' => 'index' )
					),
					'Modèles de mails pour les employeurs' => array(
						'url' => array( 'controller' => 'textsmailscuis66', 'action' => 'index' )
					),
					'Types de contrats liés aux secteurs du CUI (marchand/non marchand)' => array(
						'url' => array( 'controller' => 'typescontratscuis66', 'action' => 'index' )
					),
				);
			}
			else {
				$items = array( 'disabled' => true );
			}

			return $items;
		}

		/**
		 * Paramétrages des DSP.
		 *
		 * @return array
		 */
		protected function _dsps() {
			$items = array(
				'Codes ROME pour les secteurs' => array(
					'url' => array( 'controller' => 'codesromesecteursdsps66', 'action' => 'index' )
				),
				'Codes ROME pour les métiers' => array(
					'url' => array( 'controller' => 'codesromemetiersdsps66', 'action' => 'index' )
				)
			);

			return $items;
		}

		/**
		 * Paramétrages de l'éditeur de requêtes.
		 *
		 * @return array
		 */
		protected function _requestsmanager() {
			if( true == Configure::read( 'Requestmanager.enabled' ) ) {
				$items = array(
					'Catégories' => array(
						'url' => array( 'controller' => 'requestgroups', 'action' => 'index' )
					),
					'Requêtes' => array(
						'url' => array( 'controller' => 'requestsmanager', 'action' => 'savedindex' )
					)
				);
			}
			else {
				$items = array( 'disabled' => true );
			}

			return $items;
		}

		/**
		 * Paramétrages des équipes pluridisciplinaires.
		 *
		 * @return array
		 */
		protected function _eps() {
			$departement = (integer)Configure::read( 'Cg.departement' );

			if( true == in_array( $departement, array( 58, 66, 93 ), true ) ) {
				$items = array(
					'Fonctions des membres des EP' => array(
						'url' => array( 'controller' => 'fonctionsmembreseps', 'action' => 'index' )
					),
					'Regroupements des EP' => array(
						'url' => array( 'controller' => 'regroupementseps', 'action' => 'index' )
					),
					'Motifs de demandes de réorientation' => array(
						'disabled' => 93 !== $departement,
						'url' => array( 'controller' => 'motifsreorientseps93', 'action' => 'index' )
					),
					'Compositions des regroupements des EP' => array(
						'disabled' => 66 !== $departement,
						'url' => array( 'controller' => 'compositionsregroupementseps', 'action' => 'index' )
					),
					'Sanctions pour les EP' => array(
						'disabled' => 58 !== $departement,
						'url' => array( 'controller' => 'listesanctionseps58', 'action' => 'index' )
					)
				);
			}
			else {
				$items = array( 'disabled' => true );
			}

			return $items;
		}

		/**
		 * Paramétrages des entretiens.
		 *
		 * @return array
		 */
		protected function _entretiens() {
			$items = array(
				'Objets de l\'entretien' => array(
					'url' => array( 'controller' => 'objetsentretien', 'action' => 'index' )
				),
				'Permanences' => array(
					'url' => array( 'controller' => 'permanences', 'action' => 'index' )
				)
			);

			return $items;
		}

		/**
		 * Paramétrages des fiches de candidature (CD 66).
		 *
		 * @return array
		 */
		protected function _actionscandidats_personnes() {
			$departement = (integer)Configure::read( 'Cg.departement' );

			if( 66 === $departement ) {
				$items = array(
					'Création des partenaires / prestataires' => array(
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
					),
					'Raisons sociales des partenaires' => array(
						'url' => array( 'controller' => 'raisonssocialespartenairescuis66', 'action' => 'index' )
					)
				);
			}
			else {
				$items = array( 'disabled' => true );
			}

			return $items;
		}

		/**
		 * Paramétrages des fiches de liaisons (CD 66).
		 *
		 * @return array
		 */
		protected function _fichedeliaisons() {
			$departement = (integer)Configure::read( 'Cg.departement' );

			if( 66 === $departement ) {
				$items = array(
					'Motifs de fiche de liaison' => array(
						'url' => array( 'controller' => 'motiffichedeliaisons', 'action' => 'index' )
					),
					'Logiciels ou sites consultés' => array(
						'url' => array( 'controller' => 'logicielprimos', 'action' => 'index' )
					),
					'Proposition de primoanalyse' => array(
						'url' => array( 'controller' => 'propositionprimos', 'action' => 'index' )
					)
				);
			}
			else {
				$items = array( 'disabled' => true );
			}

			return $items;
		}

		/**
		 * Paramétrages des fiches de prescription (CD 93).
		 *
		 * @return array
		 */
		protected function _fichesprescriptions93() {
			$departement = (integer)Configure::read( 'Cg.departement' );

			if( 93 === $departement ) {
				$items = array(
					__d( 'cataloguespdisfps93', '/Cataloguespdisfps93/search/:heading' ) => array(
						'url' => array( 'controller' => 'cataloguespdisfps93', 'action' => 'search' )
					)
				);
				foreach( $this->Cataloguepdifp93->modelesParametrages as $modelName ) {
					$items[__d( 'cataloguespdisfps93', "/Cataloguespdisfps93/index/{$modelName}/:heading" )] = array(
						'url' => array( 'controller' => 'cataloguespdisfps93', 'action' => 'index', $modelName )
					);
				}
			}
			else {
				$items = array( 'disabled' => true );
			}

			return $items;
		}

		/**
		 * Paramétrages du module FSE (CD 93).
		 *
		 * @return array
		 */
		protected function _modulefse93() {
			$departement = (integer)Configure::read( 'Cg.departement' );

			if( 93 === $departement ) {
				$items = array(
					__d( 'sortiesaccompagnementsd2pdvs93', '/Sortiesaccompagnementsd2pdvs93/index/:heading' ) => array(
						'url' => array( 'controller' => 'sortiesaccompagnementsd2pdvs93', 'action' => 'index' )
					)
				);
			}
			else {
				$items = array( 'disabled' => true );
			}

			return $items;
		}

		/**
		 * Paramétrages des PDO/PCG.
		 *
		 * @return array
		 */
		protected function _pdos() {
			$departement = (integer)Configure::read( 'Cg.departement' );

			$items = array(
				'Décisions PDO' => array(
					'url' => array( 'controller' => 'decisionspdos', 'action' => 'index' )
				),
				'Descriptions des traitements PDO' => array(
					'url' => array( 'controller' => 'descriptionspdos', 'action' => 'index' )
				),
				'Origines de PDO' => array(
					'url' => array( 'controller' => 'originespdos', 'action' => 'index' )
				),
				'Module de courriers PCG' => array(
					'disabled' => ( 66 !== $departement ),
					'Type de courriers PCG' => array(
						'url' => array( 'controller' => 'typescourrierspcgs66', 'action' => 'index' )
					),
					'Modèles liés aux types de courriers' => array(
						'url' => array( 'controller' => 'modelestypescourrierspcgs66', 'action' => 'index' )
					),
					'Pièces liées aux modèles de courriers' => array(
						'url' => array( 'controller' => 'piecesmodelestypescourrierspcgs66', 'action' => 'index' )
					)
				),
				'Organismes auxquels seront transmis les dossiers PCG' => array(
					'disabled' => ( 66 !== $departement ),
					'url' => array( 'controller' => 'orgstransmisdossierspcgs66', 'action' => 'index' )
				),
				'Décisions de dossiers PCG' => array(
					'disabled' => ( 66 !== $departement ),
					'Compositions des foyers PCG' => array(
						'url' => array( 'controller' => 'composfoyerspcgs66', 'action' => 'index' )
					),
					'Décisions PCG' => array(
						'url' => array( 'controller' => 'decisionspcgs66', 'action' => 'index' )
					),
					'Questions PCG' => array(
						'url' => array( 'controller' => 'questionspcgs66', 'action' => 'index' )
					)
				),
				'Pôles chargés des dossiers PCG' => array(
					'disabled' => ( 66 !== $departement ),
					'url' => array( 'controller' => 'polesdossierspcgs66', 'action' => 'index' )
				),
				'Situations PDO' => array(
 					'url' => array( 'controller' => 'situationspdos', 'action' => 'index' )
				),
				'Statuts PDO' => array(
					'url' => array( 'controller' => 'statutspdos', 'action' => 'index' )
				),
				'Types de notifications PDO' => array(
					'url' => array( 'controller' => 'typesnotifspdos', 'action' => 'index' )
				),
				'Types de traitements PDO' => array(
					'disabled' => ( 66 === $departement ),
					'url' => array( 'controller' => 'traitementstypespdos', 'action' => 'index' )
				),
				'Types de PDO' => array(
					'url' => array( 'controller' => 'typespdos', 'action' => 'index' )
				),
				'Types de RSA' => array(
					'disabled' => ( 66 !== $departement ),
					'url' => array( 'controller' => 'typesrsapcgs66', 'action' => 'index' )
				),
				'Courriers pour les traitements PDO' => array(
					'url' => array( 'controller' => 'courrierspdos', 'action' => 'index' )
				),
				'Zones supplémentaires pour les courriers de traitements PDO' => array(
					'url' => array( 'controller' => 'textareascourrierspdos', 'action' => 'index' )
				)
			);

			return $items;
		}

		/**
		 * Paramétrages des rendez-vous.
		 *
		 * @return array
		 */
		protected function _rendezvous() {
			$departement = (integer)Configure::read( 'Cg.departement' );

			$items = array(
				'Objets de rendez-vous' => array(
					'url' => array( 'controller' => 'typesrdv', 'action' => 'index' )
				),
				'Statuts de rendez-vous' => array(
					'url' => array( 'controller' => 'statutsrdvs', 'action' => 'index' )
				),
				'Thématiques des rendez-vous' => array(
					'disabled' => false == Configure::read( 'Rendezvous.useThematique' ),
					'url' => array( 'controller' => 'thematiquesrdvs', 'action' => 'index' )
				),
				'Passages en commissions des rendez-vous' => array(
					'disabled' => ( 58 !== $departement ),
					'url' => array( 'controller' => 'statutsrdvs_typesrdv', 'action' => 'index' )
				)
			);

			return $items;
		}

		/**
		 * Paramétrages du tableau de bord.
		 *
		 * @return array
		 */
		protected function _dashboards() {
			if( true == Configure::read( 'Module.Dashboards.enabled' ) ) {
				$items = array(
					'Définir les rôles' => array(
						'url' => array('controller' => 'roles', 'action' => 'index')
					),
					'Catégories de rôles (onglets)' => array(
						'url' => array('controller' => 'categoriesactionroles', 'action' => 'index')
					),
					'Action des rôles' => array(
						'url' => array('controller' => 'actionroles', 'action' => 'index')
					)
				);
			}
			else {
				$items = array( 'disabled' => true );
			}

			return $items;
		}

		/**
		 * Paramétrages des tags.
		 *
		 * @return array
		 */
		protected function _tags() {
			$items = array(
				'Catégories de tags' => array(
					'url' => array( 'controller' => 'categorietags', 'action' => 'index' )
				),
				'Valeurs de tags' => array(
					'url' => array( 'controller' => 'valeurstags', 'action' => 'index' )
				)
			);

			return $items;
		}

		/**
		 * Paramétrages de l'application, en fonction du département et des
		 * habilitations.
		 */
		public function index() {
			$departement = (integer)Configure::read( 'Cg.departement' );

			$items = array(
				'Page d\'accueil' => array(
					'Article en page d\'accueil' => array(
						'url' => array( 'controller' => 'accueilsarticles', 'action' => 'index' )
					),
					'Correspondance Utilisateurs / Référents' => array(
						'url' => array( 'controller' => 'accueilscorrespondances', 'action' => 'index' )
					),
				),
				'Actions d\'insertion' => array(
					'disabled' => 66 === $departement,
					'url' => array( 'controller' => 'actions', 'action' => 'index' )
				),
				66 === $departement ? 'APRE/ADRE' : 'APRE' => $this->_apres(),
				'Cantons' => array(
					'disabled' => false == Configure::read( 'CG.cantons' ),
					'url' => array( 'controller' => 'cantons', 'action' => 'index' )
				),
				'CER' => $this->_contratsinsertion(),
				__d( 'parametrages', '/Parametrages/cataloguesromesv3/:heading' ) => $this->_cataloguesromesv3(),
				__d( 'communautessrs', '/Communautessrs/index/:heading' ) => array(
					'disabled' => ( 93 !== $departement ),
					'url' => array( 'controller' => 'communautessrs', 'action' => 'index' )
				),
				'Créances' => $this->_creances(),
				'CUI' => $this->_cuis(),
				'DSP' => $this->_dsps(),
				'Editeur de requêtes' => $this->_requestsmanager(),
				'Équipes pluridisciplinaires' => $this->_eps(),
				'Entretiens' => $this->_entretiens(),
				'Fiches de candidature' => $this->_actionscandidats_personnes(),
				'Fiches de liaisons' => $this->_fichedeliaisons(),
				'Fiches de positionnement' => $this->_fichesprescriptions93(),
				'Module FSE' => $this->_modulefse93(),
				'PDO' => $this->_pdos(),
				'Référents pour les structures' => array(
					'url' => array( 'controller' => 'referents', 'action' => 'index' )
				),
				'Relances SMS' => array(
					'disabled' => 66 !== $departement,
					'Paramétrage des relances' => array(
						'url' => array( 'controller' => 'relances', 'action' => 'index' )
					),
					'Logs des relances' => array(
						'url' => array( 'controller' => 'relanceslogs', 'action' => 'index' )
					),
				),
				'Rendez-vous' => $this->_rendezvous(),
				'Services' => array(
					'disabled' => 66 !== $departement,
					'url' => array( 'controller' => 'services66', 'action' => 'index' )
				),
				'Services instructeurs' => array(
					'url' => array( 'controller' => 'servicesinstructeurs', 'action' => 'index' )
				),
				'Sites d\'actions médico-sociale COVs' => array(
					'disabled' => ( 58 !== $departement ),
					'url' => array( 'controller' => 'sitescovs58', 'action' => 'index' )
				),
				'Statistiques DREES' => array(
					'Paramétrage des organismes DREES' => array(
						'url' => array('controller' => 'dreesorganismes', 'action' => 'index' )
					),
					'Paramétrage des actions CER DREES' => array(
						'url' => array('controller' => 'dreesactionscers', 'action' => 'index' )
					),
				),
				'Structures référentes' => array(
					'url' => array( 'controller' => 'structuresreferentes', 'action' => 'index' )
				),
				'Tableau de bord' => $this->_dashboards(),
				'Tags' => $this->_tags(),
				'Types d\'actions' => array(
					'disabled' => 66 === $departement,
					'url' => array( 'controller' => 'typesactions', 'action' => 'index' )
				),
				'Types d\'orientations' => array(
					'url' => array( 'controller' => 'typesorients', 'action' => 'index' )
				),
				'Vagues d\'orientation' => array(
					'Paramétrage des dates des vagues' => array(
						'url' => array( 'controller' => 'vaguesdorientations', 'action' => 'index' )
					),
				),
				'Zones géographiques' => array(
					'url' => array( 'controller' => 'zonesgeographiques', 'action' => 'index' )
				)
			);

			$this->set( compact( 'items' ) );
		}
	}
