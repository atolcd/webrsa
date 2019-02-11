<?php
	/**
	 * Code source de la classe WebrsaCheck.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppControllers', 'AppClasses.Utility' );
	App::uses( 'AppModel', 'Model' );
	App::uses( 'Folder', 'Utility' );
	App::uses( 'WebrsaSessionAclUtility', 'Utility' );
	require_once  APPLIBS.'cmis.php' ;

	/**
	 * Classe permettant de connaître la liste des modèles de documents (odt),
	 * la liste des chemins devant être configurés dans le webrsa.inc et
	 * qui permet de varifier que les intervalles sont correctement paramétrés
	 * pour l'installation courante.partenaire
	 *
	 * @package app.Model
	 */
	class WebrsaCheck extends AppModel
	{
		/**
		 * @var string
		 */
		public $name = 'WebrsaCheck';

		/**
		 * @var string
		 */
		public $useTable = false;

		/**
		 * Modèles utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array();

		/**
		 * Liste des modèles n'appartenant pas à un département donné.
		 *
		 * @var array
		 */
		public $notMyModels = array(
			58 => array(
				'Cohorterendezvous',
				'Tableausuivipdv93',
				'WebrsaRechercheActioncandidatPersonne',
				'WebrsaRechercheApre',
				'WebrsaRechercheApreEligibilite',
				'WebrsaRechercheBilanparcours66',
				'WebrsaRechercheCui',
				'WebrsaRechercheDossierpcg66',
				'WebrsaRechercheNoninscrit',
				'WebrsaRechercheNonorientationproep',
				'WebrsaRechercheSelectionradie',
				'WebrsaRechercheTraitementpcg66',
			),
			66 => array(
				'Cohorterendezvous',
				'Tableausuivipdv93',
				'WebrsaRechercheApreEligibilite',
				'WebrsaRecherchePropopdo'
			),
			93 => array(
				'WebrsaRechercheActioncandidatPersonne',
				'WebrsaRechercheBilanparcours66',
				'WebrsaRechercheCui',
				'WebrsaRechercheDossierpcg66',
				'WebrsaRechercheNoninscrit',
				'WebrsaRechercheNonorientationproep', // INFO: au 93, c'est dans une cohorte
				'WebrsaRechercheSelectionradie',
				'WebrsaRechercheTraitementpcg66',
			),
			976 => array(
				'Cohorterendezvous',
				'Tableausuivipdv93',
				'WebrsaRechercheActioncandidatPersonne',
				'WebrsaRechercheApre',
				'WebrsaRechercheApreEligibilite',
				'WebrsaRechercheBilanparcours66',
				'WebrsaRechercheCui',
				'WebrsaRechercheDossierpcg66',
				'WebrsaRechercheNoninscrit',
				'WebrsaRechercheNonorientationproep',
				'WebrsaRechercheSelectionradie',
				'WebrsaRechercheTraitementpcg66',
			)
		);

		/**
		 * Liste des modèles ODT ne ressortant pas dans le check de l'appli
		 *
		 * @var array
		 */
		public $modelesStatiquesEnPlus = array();

		/**
		 * Liste des modèles ODT ne ressortant pas dans le check de l'appli
		 *
		 * @var array
		 */
		public $modelesParametrablesEnPlus = array(
			58 => array(),
			66 => array(),
			93 => array(
				// 3.2.6
				'Bilanparcours/bilanparcourspe_audition.odt',
				'Bilanparcours/bilanparcourspe_parcours.odt',
				'Commissionep/ordredujour_participant_audition.odt',
				'Commissionep/ordredujour_participant_parcours.odt',
				'Entretien/impression.odt',
				'Orientation/ADRH.odt',
				'Orientation/changement_referent_cgcg.odt',
				'Orientation/changement_referent_cgoa.odt',
				'Orientation/changement_referent_oacg.odt',
				'Orientation/orientationpe.odt',
				'Orientation/orientationpedefait.odt',
				'Orientation/orientationsociale.odt',
				'Orientation/orientationsocialeauto.odt',
				'Orientation/orientationsystematiquepe.odt',
				'Orientation/proposition_orientation_vers_SS_ou_PDV_prestadefaut.odt',
				'Orientation/proposition_orientation_vers_SS_ou_PDV_prestadiagno.odt',
				'Orientation/proposition_orientation_vers_pole_emploi_prestadefaut.odt',
				'Orientation/proposition_orientation_vers_pole_emploi_prestadiagno.odt',
			),
			976 => array(),
		);

		/**
		 * Liste des modèles ODT n'étant plus utilisés à enlever
		 *
		 * @var array
		 */
		public $modelesStatiquesEnMoins = array();

		/**
		 * Liste des modèles ODT n'étant plus utilisés à enlever
		 *
		 * @var array
		 */
		public $modelesParametrablesEnMoins = array();

		/**
		 * Liste des clefs de type ValidateAllowEmpty
		 *
		 * @var array
		 */
		protected $_validationAllowEmptyKeys = array();

		/**
		 * Fonction utilitaire permettant de charger l'ensemble des fichiers de
		 * configuration se trouvant dans le répertoire du département connecté:
		 * app/Config/CgXXX (où XXX représente le n° du département)
		 *
		 * @param integer $departement
		 */
		protected function _includeConfigFiles( $departement ) {
			$path = APP.'Config'.DS.'Cg'.Configure::read( 'Cg.departement' );

			$Dir = new Folder( $path );
			foreach( $Dir->find( '.*\.php' ) as $file ) {
				include_once $path.DS.$file;
			}
		}

		/**
		 * Surcharge du constructeur afin d'inclure les fichiers de configuration
		 * se trouvant dans app/Config/CgXXX (où XXX représente le n° du département).
		 *
		 * @param integer|string|array $id Set this ID for this model on startup, can also be an array of options, see above.
		 * @param string $table Name of database table to use.
		 * @param string $ds DataSource connection name.
		 */
		public function __construct( $id = false, $table = null, $ds = null ) {
			parent::__construct( $id, $table, $ds );
			$departement = (int)Configure::read( 'Cg.departement' );

			$this->_includeConfigFiles( Configure::read( 'Cg.departement' ) );
		}

		/**
		 * Lecture des modèles de documents nécessaires pour chacune des
		 * classes de modèle grâce à la variable modelesOdt et à la fonction
		 * modelesOdt lorsque celle-ci est présente dans les classes de modèles
		 * (ModelesodtConditionnablesBehavior complète la variable modelesOdt
		 * lorsque le modèle est instancié).
		 *
		 * @see grep -lri "\.odt" "app/models/" | sed "s/app\/models\/\(.*\)\.php/\1/p" | sort | uniq
		 * @see grep -lri "\.odt" "app/controllers/" | sed "s/app\/controllers\/\(.*\)\.php/\1/p" | sort | uniq
		 *
		 * @return array
		 */
		public function allModelesOdt( $departement ) {
			$connections = array_keys( ConnectionManager::enumConnectionObjects() );
			$modelesStatiques = array( );
			$modelesParametrables = array();

			if (isset ($this->modelesStatiquesEnPlus[$departement])) {
				$modelesStatiques = $this->modelesStatiquesEnPlus[$departement];
			}
			if (isset ($this->modelesParametrablesEnPlus[$departement])) {
				$modelesParametrables = $this->modelesParametrablesEnPlus[$departement];
			}

			foreach( App::objects( 'model' ) as $modelName ) {
				// Si le CG se sert de la classe
				if( !preg_match( '/([0-9]{2})$/', $modelName, $matches ) || ( $matches[1] == $departement ) ) {
					App::uses( $modelName, 'Model' );
					$attributes = get_class_vars( $modelName );

					// Peut-on instancier la classe ?
					if( $attributes['useTable'] !== false && in_array( $attributes['useDbConfig'], $connections ) ) {
						// Récupération de la valeur de l'attribut modelesOdt (avec utilisation possible de ModelsodtConditionnablesBehavior)
						$modelClass = ClassRegistry::init( $modelName );
						$varModelesOdt = $modelClass->modelesOdt;

						// Récupération des valeurs de la méthode modelesOdt lorsqu'elle est présente
						if( in_array( 'modelesOdt', get_class_methods( $modelName ) ) ) {
							$modelesParametrables = Set::merge( $modelesParametrables, $modelClass->modelesOdt() );
						}
					}
					else {
						// Récupération de la valeur de l'attribut modelesOdt (sans utilisation possible de ModelsodtConditionnablesBehavior)
						$varModelesOdt = (array) ( isset( $attributes['modelesOdt'] ) ? $attributes['modelesOdt'] : array( ) );
					}

					if( !empty( $varModelesOdt ) ) {
						$alias = ( isset( $attributes['alias'] ) ? $attributes['alias'] : $modelName );
						foreach( $varModelesOdt as $modeleOdt ) {
							$modelesStatiques[] = str_replace( '%s', $alias, $modeleOdt );
						}
					}
				}
			}

			if (isset ($this->modelesStatiquesEnMoins[$departement])) {
				$modelesStatiques = array_diff($modelesStatiques, $this->modelesStatiquesEnMoins[$departement]);
			}
			if (isset ($this->modelesParametrablesEnMoins[$departement])) {
				$modelesParametrables = array_diff($modelesParametrables, $this->modelesParametrablesEnMoins[$departement]);
			}

			return array(
				'parametrables' => array_unique( $modelesParametrables ),
				'statiques' => array_unique( $modelesStatiques ),
			);
		}

		/**
		 * Retourne les clés de configuration communes aux différents CGs.
		 *
		 * @return array
		 */
		protected function _allConfigureKeysCommon() {
			$departement = (int)Configure::read( 'Cg.departement' );

			$result = array_merge(
				array(
					'AjoutOrientationPossible.situationetatdosrsa' => array(
						array(
							'rule' => 'inListArray',
							array( 'Z', '0', '1', '2', '3', '4', '5', '6' ),
							'allowEmpty' => true
						)
					),
					'AjoutOrientationPossible.toppersdrodevorsa' => array(
						array(
							'rule' => 'inListArray',
							array( null, '0', '1' ),
							'allowEmpty' => true
						)
					),
					'AncienAllocataire.enabled' => 'boolean',
					'CG.cantons' => 'boolean',
					'Cg.departement' => array(
						array( 'rule' => 'inList', array( 58, 66, 93, 976 ) ),
					),
					'Cohorte.dossierTmpPdfs' => 'string',
					'Criterecer.delaiavanteecheance' => 'string',
					'Detailcalculdroitrsa.natpf.socle' => 'isarray',
					'Dossierep.delaiavantselection' => array(
						array( 'rule' => 'string', 'allowEmpty' => true ),
					),
					'Gestiondoublon.Situationdossierrsa2.etatdosrsa' => array(
						array( 'rule' => 'isarray' ),
						array( 'rule' => 'inListArray', array( 'Z', 1, 2, 3, 4, 5, 6 ) ),
					),
					'Jetons2.disabled' => 'boolean',
					'Optimisations.progressivePaginate' => 'boolean',
					'Optimisations.useTableDernierdossierallocataire' => array(
						array( 'rule' => 'boolean', 'allowEmpty' => true ),
					),
					'Recherche.identifiantpecourt' => 'boolean',
					'Recherche.qdFilters.Serviceinstructeur' => 'boolean',
				),
				(
					true === in_array( $departement, array( 58, 66 ), true )
						? array(
							'Selectionnoninscritspe.intervalleDetection' => array(
								array(
									'rule' => 'string',
									'allowEmpty' => false
								)
							)
						)
						: array()
				),
				array(
					'Situationdossierrsa.etatdosrsa.ouvert' => 'isarray',
					'UI.menu.large' => 'boolean',
					'UI.menu.lienDemandeur' => array(
						array( 'rule' => 'url', 'allowEmpty' =>true ),
					),
					'User.adresse' => 'boolean',
					'Utilisateurs.multilogin' => 'boolean',
					'Zonesegeographiques.CodesInsee' => 'boolean',
					'alerteFinSession' => 'boolean',
					'nb_limit_print' => 'integer',
				),
				(
					true === in_array( $departement, array( 66, 93 ), true )
						? array(
							'nom_form_apre_cg' => array(
								array( 'rule' => 'inList', array( 'cg66', 'cg93' ) ),
							)
						)
						: array()
				),
				array(
					'nom_form_ci_cg' => array(
						array( 'rule' => 'inList', array( 'cg58', 'cg66', 'cg93', 'cg976' ) ),
					),
					'nom_form_pdo_cg' => array(
						array(
							'rule' => 'inList',
							array( 'cg66', 'cg93' ),
							'allowEmpty' => false === in_array( $departement, array( 66, 93 ), true )
						),
					),
					'with_parentid' => 'boolean',
					'Utilisateurs.reconnection' => 'boolean',
					'Rendezvous.useThematique' => 'boolean',
					'Statistiqueministerielle.conditions_droits_et_devoirs' => 'isarray',
					'Statistiqueministerielle.conditions_types_parcours.professionnel' => 'isarray',
					'Statistiqueministerielle.conditions_types_parcours.socioprofessionnel' => 'isarray',
					'Statistiqueministerielle.conditions_types_parcours.social' => 'isarray',
					'Statistiqueministerielle.conditions_indicateurs_organismes' => 'isarray',
					'Statistiqueministerielle.conditions_types_cers.ppae' => 'isarray',
					'Statistiqueministerielle.conditions_types_cers.cer_pro' => 'isarray',
					'Statistiqueministerielle.conditions_types_cers.cer_pro_social' => 'isarray',
					'Statistiqueministerielle.conditions_organismes.SPE' => 'isarray',
					'Statistiqueministerielle.conditions_organismes.SPE_PoleEmploi' => 'isarray',
					'Statistiqueministerielle.conditions_organismes.HorsSPE' => 'isarray',
					'Statistiqueministerielle.conditions_indicateurs_motifs_reorientation' => 'isarray',
					'Statistiqueministerielle.structure_cer_orientation' => array(
						array( 'rule' => 'boolean', 'allowEmpty' => true )
					),
					'Statistiqueministerielle.useHistoriquedroit' => array(
						array( 'rule' => 'boolean', 'allowEmpty' => true )
					),
					'WebrsaEmailConfig.testEnvironments' => array(
						array( 'rule' => 'isarray', 'allowEmpty' => true )
					),
					'Romev3.enabled' => 'boolean',
					'MultiDomainsTranslator.prefix' => 'string',
					'Etatjetons.enabled' => array(
						array( 'rule' => 'boolean', 'allowEmpty' => true )
					),
					'Requestmanager.enabled' => array(
						array( 'rule' => 'boolean', 'allowEmpty' => true )
					),
					'Canton.useAdresseCanton' => array(
						array( 'rule' => 'boolean', 'allowEmpty' => !Configure::read('CG.cantons') )
					),
					'Alerte.changement_adresse.enabled' => array(
						array( 'rule' => 'boolean', 'allowEmpty' => true )
					),
					'Alerte.changement_adresse.delai' => array(
						array( 'rule' => 'integer', 'allowEmpty' => !Configure::read('Canton.useAdresseCanton') )
					),
					'MultiDomainsTranslator.prefix' => array(
						array( 'rule' => 'string', 'allowEmpty' => true )
					),
					'Module.Cui.enabled' => array(
						array( 'rule' => 'boolean', 'allowEmpty' => true )
					),
					'Gestionsdoublons.index.useTag' => array(
						array( 'rule' => 'boolean', 'allowEmpty' => true )
					),
					'Gestionsdoublons.index.Tag.valeurtag_id' => array(
						array( 'rule' => 'integer', 'allowEmpty' => !Configure::read('Gestionsdoublons.index.useTag') )
					),
					'Module.Savesearch.enabled' => array(
						array( 'rule' => 'boolean', 'allowEmpty' => !Configure::read('Module.Savesearch.mon_menu.enabled') )
					),
					'Module.Savesearch.mon_menu.enabled' => array(
						array( 'rule' => 'boolean', 'allowEmpty' => !Configure::read('Module.Savesearch.enabled') )
					),
					'Module.Savesearch.mon_menu.name' => array(
						array( 'rule' => 'string', 'allowEmpty' => true )
					),
					'Anciensmoteurs.enabled' => array(
						array( 'rule' => 'boolean', 'allowEmpty' => true )
					),
					'MultiDomainsTranslator.prefix' => array(
						array( 'rule' => 'inList', array( 'cg58', 'cg66', 'cg93', 'cg976' ) ),
					),
					'Module.Synthesedroits.enabled' => array(
						array( 'rule' => 'boolean', 'allowEmpty' => true )
					),
					'ConfigurableQuery.common.filters.Adresse.numcom.multiple' => array(
						array( 'rule' => 'boolean', 'allowEmpty' => true )
					),
					'ConfigurableQuery.common.filters.Adresse.numcom.multiple_larger_1' => array(
						array( 'rule' => 'boolean', 'allowEmpty' => true )
					),
					'Module.Dashboards.enabled' => array(
						array( 'rule' => 'boolean', 'allowEmpty' => true )
					),
					'ConfigurableQuery.common.two_ways_order.enabled' => array(
						array( 'rule' => 'boolean', 'allowEmpty' => true )
					),
					'Module.Attributiondroits.enabled' => array(
						array( 'rule' => 'boolean', 'allowEmpty' => true )
					),
					'Search.Options.enums' => array(
						array( 'rule' => 'isarray', 'allowEmpty' => true )
					),
					'Module.Donneescaf.enabled' => array(
						array( 'rule' => 'boolean', 'allowEmpty' => true )
					),
					'WebrsaTranslator.suffix' => 'string',
					'Module.Logtrace.enabled' => array(
						array( 'rule' => 'boolean', 'allowEmpty' => true )
					),
					'Module.Logtrace.total_duration' => array(
						array( 'rule' => 'integer', 'allowEmpty' => !Configure::read('Module.Logtrace.enabled') )
					),
					'Module.Datepicker.enabled' => array(
						array( 'rule' => 'boolean', 'allowEmpty' => true )
					),
					'UI.beforeLogo.text' => array(
						array( 'rule' => 'string', 'allowEmpty' => true )
					),
					'UI.afterLogo.text' => array(
						array( 'rule' => 'string', 'allowEmpty' => true )
					),
					'textarea.auto_resize' => array(
						array( 'rule' => 'isarray', 'allowEmpty' => true )
					),
					'ConfigurableQuery.common.filters.has_prestation' => array(
						array( 'rule' => 'isarray', 'allowEmpty' => true )
					),
					'Module.DisplayValidationErrors.enabled' => array(
						array( 'rule' => 'boolean', 'allowEmpty' => true )
					),
					'Module.Permissions.all' => array(
						array( 'rule' => 'boolean', 'allowEmpty' => true )
					),
					'Module.Fluxcnaf.enabled' => array(
						array( 'rule' => 'boolean', 'allowEmpty' => true )
					),
					'Correspondancepersonne.max' => array(
						array( 'rule' => 'integer', 'allowEmpty' => true )
					),
					'Foyer.refreshSoumisADroitsEtDevoirs.ajoutOrientstruct' => array(
						array( 'rule' => 'boolean', 'allowEmpty' => true )
					),
				)
			);

			$tmp = Configure::read( 'Rendezvous.thematiqueAnnuelleParStructurereferente' );
			if( !empty( $tmp ) ) {
				if( is_array( $tmp ) ) {
					$result['Rendezvous.thematiqueAnnuelleParStructurereferente'] = 'isarray';
				}
				else {
					$result['Rendezvous.thematiqueAnnuelleParStructurereferente'] = 'integer';
				}
			}

			// Pour tous les départements sauf le 976
			$departement = (int)Configure::read( 'Cg.departement' );
			if( $departement !== 976 ) {
				// Pour les thématiques traitées, on cherche à savoir à quel niveau maximum
				$Commissionep = ClassRegistry::init( 'Commissionep' );
				$themes = $Commissionep->Ep->themes();

				$niveaux = array();
				$rule = array( array( 'rule' => 'isarray', 'allowEmpty' => true ) );
				$paths = array();

				$regroupementseps = $Commissionep->Ep->Regroupementep->find( 'all', array( 'contain' => false ) );
				foreach( $regroupementseps as $regroupementep ) {
					foreach( $themes as $theme ) {
						$niveau = $regroupementep['Regroupementep'][$theme];
						if( 'nontraite' !== $niveau ) {
							$niveaux[] = $regroupementep['Regroupementep'][$theme];
						}
					}
				}

				if( in_array( 'decisioncg', $niveaux, true ) ) {
					$paths = array(
						'Dossierseps.choose.order',
						'Commissionseps.decisionep.order',
						'Commissionseps.decisioncg.order',
						'Commissionseps.printOrdresDuJour.order',
						'Commissionseps.traiterep.order',
						'Commissionseps.traitercg.order'
					);
				}
				else if( in_array( 'decisionep', $niveaux, true ) ) {
					$paths = array(
						'Dossierseps.choose.order',
						'Commissionseps.decisionep.order',
						'Commissionseps.printOrdresDuJour.order',
						'Commissionseps.traiterep.order'
					);
				}

				$result = array_merge(
					$result,
					array_fill_keys( $paths, $rule )
				);
			}

			// L'APRE n'est utilisée que par deux départements
			$departement = (int)Configure::read( 'Cg.departement' );
			if( in_array( $departement, array( 66, 93 ) ) ) {
				$result = array_merge(
					$result,
					array(
						'Apre.forfaitaire.montantbase' => 'numeric',
						'Apre.forfaitaire.montantenfant12' => 'numeric',
						'Apre.forfaitaire.nbenfant12max' => 'integer',
						'Apre.montantMaxComplementaires' => 'numeric',
						'Apre.periodeMontantMaxComplementaires' => 'integer',
						'Apre.pourcentage.montantversement' => 'numeric',
						'Apre.suffixe' =>  array(
							array( 'rule' => 'inList', array( 66, '66' ), 'allowEmpty' =>true ),
						),
					)
				);
			}

			if ($departement === 66) {
				$result = array_merge(
					$result,
					array(
						'Tag.Options.enums.Personne.trancheage' => 'isarray',
						'Tag.Options.enums.Foyer.nb_enfants' => 'isarray',
						'Tag.Options.enums.Detailcalculdroitrsa.mtrsavers' => 'isarray',
						'Commissionseps.defautinsertionep66.decision.type' => 'isarray',
						'Commissionseps.defautinsertionep66.decision.type.maintienorientsoc' => 'isarray',
						'Commissionseps.defautinsertionep66.decision.type.reorientationprofverssoc' => 'isarray',
						'Commissionseps.defautinsertionep66.decision.type.reorientationsocversprof' => 'isarray',
						'Commissionseps.defautinsertionep66.isemploi' => 'isarray',
						'Fichedeliaisons.typepdo_id' => 'integer',
					)
				);
			}

			// Utilise-t-on les plages horaires ?
			$plagesHorairesEnabled = ( true === Configure::read( 'Module.PlagesHoraires.enabled' ) );
			$allowEmpty = ( false === $plagesHorairesEnabled );
			$this->loadModel( 'User' );
			$groups_ids = array_keys( $this->User->Group->find( 'list', array( 'contain' => false ) ) );

			$result = array_merge(
				$result,
				array(
					'Module.PlagesHoraires.enabled' =>  array(
						array( 'rule' => 'boolean', 'allowEmpty' => true ),
					),
					'Module.PlagesHoraires.heure_debut' =>  array(
						array( 'rule' => 'inList', range( 0, 23 ), 'allowEmpty' => $allowEmpty ),
					),
					'Module.PlagesHoraires.heure_fin' =>  array(
						array( 'rule' => 'inList', range( 0, 23 ), 'allowEmpty' => $allowEmpty ),
					),
					'Module.PlagesHoraires.jours_weekend' =>  array(
						array( 'rule' => 'inListArray', array( 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun' ), 'allowEmpty' => $allowEmpty ),
					),
					'Module.PlagesHoraires.groupes_acceptes' =>  array(
						array( 'rule' => 'inListArray', $groups_ids, 'allowEmpty' => true ),
					)
				)
			);

			return $result;
		}

		/**
		 * Retourne les clés de configuration propres au CG 58.
		 *
		 * @return array
		 */
		protected function _allConfigureKeys58() {
			$return = array(
				'Nonorientationprocov58.delaiCreationContrat' => 'integer',
				'Sanctionep58.nonrespectcer.dureeTolerance' => 'integer',
				'Selectionradies.conditions' => 'isarray',
				'Typeorient.emploi_id' => 'integer',
				'Dossierseps.conditionsSelection' => 'isarray',
				'Rendezvous.elaborationCER.typerdv_id' => 'integer',
			);

			$structurereferente_id = Configure::read( 'Sanctionseps58.selection.structurereferente_id' );
			if( is_array( $structurereferente_id ) ) {
				$return['Sanctionseps58.selection.structurereferente_id'] = 'isarray';
			}
			else {
				$return['Sanctionseps58.selection.structurereferente_id'] = 'integer';
			}

			return $return;
		}

		/**
		 * Retourne les clés de configuration propres au CG 66.
		 *
		 * @return array
		 */
		protected function _allConfigureKeys66() {
			return array(
				'AjoutOrientationPossible.toppersdrodevorsa' => 'isarray',
				'Fraisdeplacement66.forfaithebergt' => 'numeric',
				'Fraisdeplacement66.forfaitrepas' => 'numeric',
				'Fraisdeplacement66.forfaitvehicule' => 'numeric',
				'Chargeinsertion.Secretaire.group_id' => 'isarray',
				'Contratinsertion.Cg66.updateEncoursbilan' => 'string',
				'Criterecer.delaidetectionnonvalidnotifie' => 'string',
				'Nonorientationproep66.delaiCreationContrat' => 'integer',
				'Orientstruct.typeorientprincipale.Emploi' => 'isarray',
				'Orientstruct.typeorientprincipale.SOCIAL' => 'isarray',
				'Periode.modifiablecer.nbheure' => 'integer',
				'Periode.modifiableorientation.nbheure' => 'integer',
				'Traitementpcg66.fichecalcul_abattbicsrv' => 'integer',
				'Traitementpcg66.fichecalcul_abattbicvnt' => 'integer',
				'Traitementpcg66.fichecalcul_abattbncsrv' => 'integer',
				'Traitementpcg66.fichecalcul_abattagriagri' => 'integer',
				'Traitementpcg66.fichecalcul_casrvmax' => 'integer',
				'Traitementpcg66.fichecalcul_cavntmax' => 'integer',
				'Traitementpcg66.fichecalcul_caagrimax' => 'integer',
				'Traitementpcg66.fichecalcul_coefannee1' => 'numeric',
				'Traitementpcg66.fichecalcul_coefannee2' => 'numeric',
				'Nonoriente66.notisemploi.typeorientId' => 'isarray',
				'Nonoriente66.TypeorientIdSocial' => 'integer',
				'Nonoriente66.TypeorientIdPrepro' => 'integer',
				'Contratinsertion.Cg66.Rendezvous' => 'isarray',
				'Corbeillepcg.descriptionpdoId' => 'isarray',
                'ActioncandidatPersonne.Actioncandidat.typeregionId' => 'isarray',
                'ActioncandidatPersonne.Partenaire.id' => 'isarray',
                'Rendezvous.Ajoutpossible.statutrdv_id' => 'integer',
				'Nonorganismeagree.Structurereferente.id' => 'isarray',
                'ActioncandidatPersonne.Actioncandidat.typeregionPoursuitecgId' => 'isarray',
                'Contratinsertion.Cg66.toleranceDroitClosCerComplexe' => 'string',
				'Cui.taux.financementexclusif' => 'numeric',
				'Cui.taux.fixe' => 'numeric',
				'Cui.taux.prisencharge' => 'numeric',
                'Cui.Numeroconvention' => 'string',
				'Dossierspcgs66.imprimer.Impression.RectoVerso' => array(
					array( 'rule' => 'boolean', 'allowEmpty' => true )
				),
				'Dossierspcgs66.imprimer_cohorte.Impression.RectoVerso' => array(
					array( 'rule' => 'boolean', 'allowEmpty' => true )
				),
			);
		}

		/**
		 * Retourne les clés de configuration propres au CG 93.
		 *
		 * @return array
		 */
		protected function _allConfigureKeys93() {
			$return = array(
				'Dossierep.nbJoursEntreDeuxPassages' => 'integer',
				'Filtresdefaut.Cohortes_enattente' => 'isarray',
				'Filtresdefaut.Cohortes_nouvelles' => 'isarray',
				'Filtresdefaut.Cohortes_orientees' => 'isarray',
				'Nonorientationproep93.delaiCreationContrat' => 'integer',
				'Nonrespectsanctionep93.decisionep.delai' => 'integer',
				'Nonrespectsanctionep93.delaiRegularisation' => 'integer',
				'Nonrespectsanctionep93.dureeSursis' => 'integer',
				'Nonrespectsanctionep93.intervalleCerDo19' => 'string',
				'Nonrespectsanctionep93.montantReduction' => 'numeric',
				'Nonrespectsanctionep93.relanceCerCer1' => 'integer',
				'Nonrespectsanctionep93.relanceCerCer2' => 'integer',
				'Nonrespectsanctionep93.relanceDecisionNonRespectSanctions' => 'integer',
				'Nonrespectsanctionep93.relanceOrientstructCer1' => 'integer',
				'Nonrespectsanctionep93.relanceOrientstructCer2' => 'integer',
				'Signalementep93.decisionep.delai' => 'integer',
				'Signalementep93.dureeSursis' => 'integer',
				'Signalementep93.dureeTolerance' => 'integer',
				'Signalementep93.montantReduction' => 'numeric',
				'apache_bin' => 'string',
				'Cohortescers93.saisie.periodeRenouvellement' => 'string',
				'Contratinsertion.RdvAuto.active' => 'boolean',
				'Tableausuivipdv93.typerdv_id' => 'isarray',
				'Tableausuivipdv93.statutrdv_id' => 'isarray',
				'Tableausuivipdv93.numcodefamille.acteurs_sociaux' => 'isarray',
				'Tableausuivipdv93.numcodefamille.acteurs_sante' => 'isarray',
				'Tableausuivipdv93.numcodefamille.acteurs_culture' => 'isarray',
				'Tableausuivipdv93.conditionsPdv' => 'isarray',
				'Tableausuivipdv93.Tableau1b6.typerdv_id' => 'isarray',
				'Tableausuivipdv93.Tableau1b6.statutrdv_id_prevu_honore' => 'isarray',
				'Tableausuivipdv93.Tableau1b6.map_thematiques_themes' => 'isarray',
				'Cataloguepdifp93.urls' => 'isarray',
				'Ficheprescription93.regexpNumconventionFictif' => 'string',
				'Tableausuivi93.tableau1b4.conditions' => 'isarray',
				'Tableausuivi93.tableau1b4.categories' => 'isarray',
				'Tableausuivi93.tableau1b5.conditions' => 'isarray',
				'Tableausuivi93.tableau1b5.categories' => 'isarray',
				'Rendezvous.Typerdv.collectif_id' => 'integer',
				'Rendezvous.Typerdv.individuel_id' => 'integer',
				'Cohortesrendezvous.cohorte.fields' => 'isarray',
				'Cohortesrendezvous.exportcsv' => 'isarray',
				// Valeurs par défaut des filtres de recherche des tableaux de suivi PDV
				'Tableauxsuivispdvs93.tableaud1.defaults' => 'isarray',
				'Tableauxsuivispdvs93.tableaud2.defaults' => 'isarray',
				'Tableauxsuivispdvs93.tableau1b3.defaults' => 'isarray',
				'Tableauxsuivispdvs93.tableau1b4.defaults' => 'isarray',
				'Tableauxsuivispdvs93.tableau1b5.defaults' => 'isarray',
				'Tableauxsuivispdvs93.tableau1b6.defaults' => 'isarray',
				// Export CSV du corpus des tableaux de suivi PDV
				'Tableauxsuivispdvs93.tableau1b3.exportcsvcorpus' => 'isarray',
				'Tableauxsuivispdvs93.tableau1b4.exportcsvcorpus' => 'isarray',
				'Tableauxsuivispdvs93.tableau1b5.exportcsvcorpus' => 'isarray',
				'Tableauxsuivispdvs93.tableau1b6.exportcsvcorpus' => 'isarray',
				'Statistiqueministerielle.conditions_natures_contrats' => 'isarray',
			);

			if( Configure::read( 'Contratinsertion.RdvAuto.active' ) ) {
				$return = Hash::merge(
					$return,
					array(
						'Contratinsertion.RdvAuto.typerdv_id' => 'integer',
						'Contratinsertion.RdvAuto.statutrdv_id' => 'integer',
						'Contratinsertion.RdvAuto.thematiquerdv_id' => 'integer',
					)
				);
			}

			if( Configure::read( 'Romev3.enabled' ) ) {
				$return = Hash::merge(
					$return,
					array(
						'Cer93.Sujetcer93.Romev3.path' =>  array(
							array( 'rule' => 'inList', array(
								'Sujetcer93.Sujetcer93.{n}.sujetcer93_id',
								'Sujetcer93.Sujetcer93.{n}.soussujetcer93_id'
							) ),
						),
						'Cer93.Sujetcer93.Romev3.values' => 'isarray'
					)
				);
			}

			// Cer93.Sujetcer93.Romev3

			return $return;
		}

		/**
		 * Supprime les valeurs corespondant à des clés de configuration.
		 *
		 * @param array $configure
		 * @param array $remove Si remove est vide, les clés correspondent à celles du coeur de Cake
		 * @return array
		 */
		protected function _removeConfigureKeys( array $configure, array $remove = array() ) {
			if( empty( $remove ) ) {
				$remove = array( 'Acl', 'App', 'Cache', 'Cake', 'Config', 'Dispatcher', 'Error', 'Exception', 'Security', 'Session' );
			}

			$removeRegexp = '/^(('.implode( '|', $remove ).')\.|debug$)/';

			foreach( $configure as $key => $value ) {

				if( preg_match( $removeRegexp, $value ) ) {
					unset( $configure[$key] );
				}
			}

			$configure = array_values( $configure );

			return $configure;
		}

		/**
		 * Retourne les clés de configuration actuellement utilisées.
		 *
		 * @return array
		 */
		protected function _existingConfigureKeys( $core = false ) {
			$existing = array();
			foreach( Hash::flatten( (array)Configure::read() ) as $key => $value ) {
				$existing[] = preg_replace( '/\.[0-9]+$/', '', $key );
			}
			$existing = array_unique( $existing );
			sort( $existing );

			// On enlève les clés du coeur de Cake
			if( $core === false ) {
				$existing = $this->_removeConfigureKeys( $existing );
			}

			return $existing;
		}

		protected function _configureKeysDiff() {
			$existing = $this->_existingConfigureKeys();
			$expected = array_keys( $this->allConfigureKeys( Configure::read( 'Cg.departement' ) ) );

			$remove = array( 'Cmis', 'Filtresdefaut', 'Optimisations', 'Password' );
			$existing = $this->_removeConfigureKeys( $existing, $remove );
			$expected = $this->_removeConfigureKeys( $expected, $remove );

			debug( array_diff( $expected, $existing ) ); // Vérifiées mais non existantes
			debug( array_diff( $existing, $expected ) ); // Existantes mais non vérifiées
			// debug( $existing );
			// debug( $expected );
			// $this->_configureKeysDiff();
		}

		/**
		 * Retourne la liste des chemins devant être configurés, suivant le département.
		 * Chaque entrée a en clé le chemin et en valeur le type de valeur
		 * (array, boolean, integer, numeric, string) autorisé.
		 *
		 * TODO: à utiliser dans chacun des modèles concernés ? ... plus contrôleurs, ....
		 *
		 * @return array
		 */
		public function allConfigureKeys( $departement ) {
			$method = '_allConfigureKeys'.Configure::read( 'Cg.departement' );

			$configure = method_exists( $this, $method ) ? $this->{$method}() : array();
			$configure = Hash::merge( $this->_allConfigureKeysCommon(), $configure, $this->_allValidateAllowEmptyKeys() );

			uksort( $configure, 'strnatcasecmp' );

			return $configure;
		}

		/**
		 * Permet d'obtenir la liste des configs type ValidateAllowEmpty.Model.field
		 *
		 * @return array
		 */
		protected function _allValidateAllowEmptyKeys() {
			$results = array();

			foreach ( $this->_getValidationAllowEmptyKeys() as $key ) {
				$results[$key] = array( array( 'rule' => 'boolean', 'allowEmpty' => true ) );
			}

			return $results;
		}

		/**
		 * FIXME:
		 *	1°) au changement de checks_controller, supprimer
		 *		- AppModel::_checkSqlIntervalSyntax
		 *		- Informationpe::checkConfigUpdateIntervalleDetectionNonInscritsPe
		 *		- Dossierep::checkConfigDossierepDelaiavantselection
		 *		- Nonrespectsanctionep93::checkConfigUpdateIntervalleCerDo19Cg93
		 *		- Contratinsertion::checkConfigUpdateEncoursbilanCg66
		 *	2°) Bouger AppModel::_checkPostgresqlIntervals ici ?
		 *  3°) Voir si on ne peut pas combiner la boucle avec celle ci-dessus ?
		 *  4°) Les anciennes fonctions se trouvant dans les modèles sont-elles encore utilisées ?
		 *
		 * app/models/informationpe.php:299:                       return $this->_checkSqlIntervalSyntax( Configure::read( 'Selectionnoninscritspe.intervalleDetection' ) );
		 * app/models/nonrespectsanctionep93.php:1080:                     return $this->_checkSqlIntervalSyntax( Configure::read( 'Nonrespectsanctionep93.intervalleCerDo19' ) );
		 * app/models/contratinsertion.php:852:                    return $this->_checkSqlIntervalSyntax( Configure::read( 'Contratinsertion.Cg66.updateEncoursbilan' ) );
		 * app/models/dossierep.php:548:                   return $this->_checkSqlIntervalSyntax( $delaiavantselection );
		 */
		public function checkAllPostgresqlIntervals( $departement ) {
			$connections = array_keys( ConnectionManager::enumConnectionObjects() );
			$results = array( );

			foreach( App::objects( 'model' ) as $modelName ) {
				// Si le CG se sert de la classe
				if( !preg_match( '/([0-9]{2})$/', $modelName, $matches ) || ( $matches[1] == $departement ) ) {
					App::uses( $modelName, 'Model' );
					$attributes = get_class_vars( $modelName );
					$methods = get_class_methods( $modelName );

					// Possède-t-on la classe et la fonction existe-t'elle ?
					if( in_array( $attributes['useDbConfig'], $connections ) && in_array( 'checkPostgresqlIntervals', $methods ) ) {
						$modelClass = ClassRegistry::init( $modelName );

						$results = Set::merge( $results, $modelClass->checkPostgresqlIntervals() );
					}
				}
			}

			ksort( $results );

			return $results;
		}

		/**
		 * Récupère les enregistrements incomplèts de tous les modèles possédant
		 * la méthode storedDataErrors.
		 * TODO: vérifier la présence de la fonction comme ci-dessus, mais attention aux sous-classes (	covstructurereferentes)
		 */
		public function allStoredDataErrors() {
			return array(
				'derniersdossiersallocataires' => ClassRegistry::init( 'Dernierdossierallocataire' )->storedDataErrors(),
				'regroupementseps' => ClassRegistry::init( 'Regroupementep' )->storedDataErrors(),
				'servicesinstructeurs' => ClassRegistry::init( 'Serviceinstructeur' )->storedDataErrors(),
				'users' => ClassRegistry::init( 'WebrsaUser' )->storedDataErrors()
			);
		}

		/**
		 * Vérifie le bon fonctionnement du service Gedooo
		 *
		 * @return array
		 */
		protected function _serviceGedooo() {
			App::uses( 'GedoooBehavior', 'Gedooo.Model/Behavior' );

			$GedModel = ClassRegistry::init( 'User' );
			$GedModel->Behaviors->attach( 'Gedooo.Gedooo' );

			return array(
				'configure' => @$GedModel->Behaviors->Gedooo->gedConfigureKeys( $GedModel ),
				'tests' => @$GedModel->gedTests() // FIXME: le faire sur les autres aussi
			);
		}

		/**
		 * Vérifie la configuration et le bon fonctionnement du service CMIS.
		 *
		 * @return array
		 */
		protected function _serviceCmis() {
			$config = Hash::filter(
				array(
					'url' => Configure::read( 'Cmis.url' ),
					'username' => Configure::read( 'Cmis.username' ),
					'password' => Configure::read( 'Cmis.password' ),
					'prefix' => Configure::read( 'Cmis.prefix' )
				)
			);

			$configured = false === empty( $config );
			$connected = $configured && Cmis::configured();

			$stringRule = array(
				'rule' => 'string',
				'allowEmpty' => false === $configured
			);

			if( false === $configured ) {
				$message = 'Connexion au serveur non paramétrée';
			}
			else {
				$message = ( $connected ? null : 'Impossible de se connecter au serveur' );
			}

			$result = array(
				'configure' => array(
					'Cmis.url' => array(
						$stringRule,
						array( 'rule' => 'url', 'allowEmpty' => false === $configured )
					),
					'Cmis.username' => array( $stringRule ),
					'Cmis.password' => array( $stringRule ),
					'Cmis.prefix' => array( $stringRule )
				),
				'tests' => array(
					'Connexion au serveur' => array(
						'success' => false === $configured || $connected,
						'message' => $message
					)
				)
			);


			return $result;
		}

		/**
		 * Retourne la liste des serveurs configurés, les configurations prises
		 * en compte et les erreurs.
		 *
		 * @return array
		 */
		public function services() {
			return array(
				'Alfresco' => $this->_serviceCmis(),
				'Gedooo' => $this->_serviceGedooo(),
			);
		}

		/**
		 *
		 * @see WebrsaCheck::querydataFragmentsErrors()
		 * @see Allocataire::testSearchConditions()
		 *
		 * @return array
		 */
		public function allSqRechercheErrors() {
			$Serviceinstructeur = ClassRegistry::init( 'Serviceinstructeur' );
			return $Serviceinstructeur->sqRechercheErrors();
		}

		/**
		 * Liste des clés de configurations de mails pour le CG 58.
		 *
		 * @return array
		 */
		protected function _allEmailConfigKeys58() {
			$return = array();

			if( Configure::read( 'Password.mail_forgotten' ) ) {
				$return[] = 'user_generation_mdp';
			}

			return $return;
		}

		/**
		 * Liste des clés de configurations de mails pour le CG 66.
		 *
		 * @return array
		 */
		protected function _allEmailConfigKeys66() {
			$return = array( 'apre66_piecesmanquantes', 'fiche_candidature', 'avis_technique_cui', 'mail_employeur_cui' );


			if( Configure::read( 'Password.mail_forgotten' ) ) {
				$return[] = 'user_generation_mdp';
			}
//$return = array( );
			return $return;
		}

		/**
		 * Liste des clés de configurations de mails pour le CG 93.
		 *
		 * @return array
		 */
		protected function _allEmailConfigKeys93() {
			$return = array();

			if( Configure::read( 'Password.mail_forgotten' ) ) {
				$return[] = 'user_generation_mdp';
			}

			return $return;
		}

		/**
		 * Vérification de la présence des configurations de mails suivant le CG.
		 *
		 * @return array
		 */
		public function allEmailConfigs() {
			$method = '_allEmailConfigKeys'.Configure::read( 'Cg.departement' );

			$configs = method_exists( $this, $method ) ? $this->{$method}() : array();

			return $configs;
		}

		/**
		 * Retourne les clés de configuration ainsi que le nom du modèle concerné,
		 * contenant une référence vers une clé primaire d'une table, suivant le
		 * CG connecté.
		 *
		 * @return array
		 */
		public function allConfigurePrimaryKeys() {
			$return = array();

			if( Configure::read( 'Cg.departement' ) == 58 ) {
				$return = array(
					'Typeorient.emploi_id' => 'Typeorient',
					// TODO: 'Selectionradies.conditions' ?
					'Sanctionseps58.selection.structurereferente_id' => 'Structurereferente',
				);
			}
			else if( Configure::read( 'Cg.departement' ) == 66 ) {
				$return = array(
					'Orientstruct.typeorientprincipale.SOCIAL' => 'Typeorient',
					'Orientstruct.typeorientprincipale.Emploi' => 'Typeorient',
					'Chargeinsertion.Secretaire.group_id' => 'Group',
					'Nonoriente66.notisemploi.typeorientId' => 'Typeorient',
					'Nonoriente66.TypeorientIdSocial' => 'Typeorient',
					'Nonoriente66.TypeorientIdPrepro' => 'Typeorient',
					// TODO: Contratinsertion.Cg66.Rendezvous ?
					'Corbeillepcg.descriptionpdoId' => 'Descriptionpdo',
                    'Rendezvous.Ajoutpossible.statutrdv_id' => 'Statutrdv',
					'Nonorganismeagree.Structurereferente.id' => 'Structurereferente',
					'ActioncandidatPersonne.Partenaire.id' => 'Partenaire',
					'ActioncandidatPersonne.Actioncandidat.typeregionId' => 'Actioncandidat',
					'ActioncandidatPersonne.Actioncandidat.typeregionPoursuitecgId' => 'Actioncandidat',
				);

			}
			else if( Configure::read( 'Cg.departement' ) == 93 ) {
				$return = array(
					'Chargeinsertion.Secretaire.group_id' => 'Group',
					'Orientstruct.typeorientprincipale.Socioprofessionnelle' => 'Typeorient',
					'Orientstruct.typeorientprincipale.Social' => 'Typeorient',
					'Orientstruct.typeorientprincipale.Emploi' => 'Typeorient',
					'Questionnaired1pdv93.rendezvous.statutrdv_id' => 'Statutrdv',
					// Tableaux PDV
					'Tableausuivipdv93.typerdv_id' => 'Typerdv',
					'Tableausuivipdv93.statutrdv_id' => 'Statutrdv',
					'Tableausuivipdv93.Tableau1b6.typerdv_id' => 'Typerdv',
					'Tableausuivipdv93.Tableau1b6.statutrdv_id_prevu_honore' => 'Statutrdv',
					'Tableausuivipdv93.Tableau1b6.map_thematiques_themes' => array(
						'modelName' => 'Thematiquerdv',
						'array_keys' => true
					),
					'Rendezvous.Typerdv.collectif_id' => 'Typerdv',
					'Rendezvous.Typerdv.individuel_id' => 'Typerdv',
				);

				if( Configure::read( 'Contratinsertion.RdvAuto.active' ) ) {
					$return = Hash::merge(
						$return,
						array(
							'Contratinsertion.RdvAuto.typerdv_id' => 'Typerdv',
							'Contratinsertion.RdvAuto.statutrdv_id' => 'Statutrdv',
							'Contratinsertion.RdvAuto.thematiquerdv_id' => 'Thematiquerdv',
						)
					);
				}

				if( Configure::read( 'Romev3.enabled' ) ) {
					$modelName = Configure::read( 'Cer93.Sujetcer93.Romev3.path' ) === 'Sujetcer93.Sujetcer93.{n}.sujetcer93_id' ? 'Sujetcer93' : 'Soussujetcer93';
					$return = Hash::merge(
						$return,
						array(
							'Cer93.Sujetcer93.Romev3.values' => $modelName
						)
					);
				}

				$tmp = (array)Configure::read( 'Rendezvous.checkThematiqueAnnuelleParStructurereferente.statutrdv_id' );
				if( !empty( $tmp ) ) {
					$return['Rendezvous.checkThematiqueAnnuelleParStructurereferente.statutrdv_id'] = 'Statutrdv';
				}

				$tmp = (array)Configure::read( 'Rendezvous.thematiqueAnnuelleParStructurereferente' );
				if( !empty( $tmp ) ) {
					$return['Rendezvous.thematiqueAnnuelleParStructurereferente'] = 'Thematiquerdv';
				}
			}

			return $return;
		}


		/**
		 * Vérifie les fragments de querydata se trouvant en paramétrage dans le
		 * webrsa.inc pour tous les modèles concernés.
		 *
		 * @see WebrsaCheck::allSqRechercheErrors()
		 *
		 * @return array
		 */
		public function allQuerydataFragmentsErrors() {
			$errors = array( );
			$modelNames = array( 'Statistiqueministerielle', 'Dossierep', 'Commissionep' );

			if( Configure::read( 'Cg.departement' ) == 93 ) {
				$modelNames[] = 'WebrsaTableausuivipdv93';
			}

			foreach( $modelNames as $modelName ) {
				$Model = ClassRegistry::init( $modelName );

				$errors[$modelName] = $Model->querydataFragmentsErrors();

			}

			return $errors;
		}

		/**
		 * Vérifie la présence de l'ensemble des fonctions de la librairie
		 * PostgreSQL fuzzystrmatch.
		 *
		 * @return array
		 */
		public function checkPostgresFuzzystrmatchFunctions() {
			$Dbo = ClassRegistry::init( 'User' )->getDataSource();

			$version = $Dbo->getPostgresVersion();
			$shortversion = preg_replace( '/^([0-9]+\.[0-9]+).*/', '\1', $version );

			$functions = array(
				'levenshtein',
				'metaphone',
				'soundex',
				'text_soundex',
				'difference',
				'dmetaphone',
				'dmetaphone_alt',
				'cakephp_validate_in_list'
			);
			$conditions = array(
				'pg_proc.proname IN ( \''.implode( '\', \'', $functions ).'\' )'
			);
			$results = $Dbo->getPostgresFunctions( $conditions );
			$results = array_unique( Hash::extract( $results, '{n}.Function.name' ) );

			$missing = array_diff( $functions, $results );
			if( empty( $missing ) ) {
				$check = array(
					'success' => true,
					'message' => null
				);
			}
			else {
				$check = array(
					'success' => false,
					'message' => sprintf(
							"Problème avec les fonctions fuzzystrmatch (les fonctions suivantes sont manquantes: %s)<br/>Sous Ubuntu, il vous faut vérifier que le paquet postgresql-contrib-%s est bien installé. <br />Une fois fait, dans une console postgresql, en tant qu'administrateur, tapez: ",
							implode( ', ', $missing ),
							$shortversion
						) . '<code>' . (
							(float)$shortversion < 9
							? sprintf('\i /usr/share/postgresql/%s/contrib/fuzzystrmatch.sql', $shortversion)
							: 'CREATE EXTENSION fuzzystrmatch;'
						)
						. '</code>'
				);
			}

			return $check;
		}

		/**
		 * Vérifie si la date du serveur PostgreSQL correspond à la date du serveur Web.
		 * La tolérance est de moins d'une minute.
		 *
		 * @return array
		 */
		public function checkPostgresTimeDifference() {
			$Dbo = ClassRegistry::init( 'User' )->getDataSource();

			$message = 'Différence de date entre le serveur Web et le serveur de base de données trop importante.';

			$sqlAge = 'AGE( DATE_TRUNC( \'second\', localtimestamp ), \''.date( 'Y-m-d H:i:s' ).'\' )';
			$sqlAgeSuccess = "{$sqlAge} < '1 min'";
			$sql = "SELECT
						{$sqlAge} as value,
						$sqlAgeSuccess AS success,
						( CASE WHEN {$sqlAgeSuccess} THEN NULL ELSE '{$message}' END ) AS message;";
			$result = $Dbo->query( $sql );
			return $result[0][0];
		}

		/**
		 * Retourne la liste de toutes les clés contenant des expressions rationnelles
		 * configurées dans le webrsa.inc, par CG.
		 */
		public function allConfigureRegexps() {
			$return = array();

			$departement = Configure::read( 'Cg.departement' );
			if( $departement == 93 ) {
				$return[] = 'Ficheprescription93.regexpNumconventionFictif';
			}

			return $return;
		}

		/**
		 * Vérifie les expressions rationnelles configurées dans le fichier
		 * webrsa.inc.
		 */
		public function allConfigureRegexpsErrors() {
			$return = array();
			$paths = $this->allConfigureRegexps();

			foreach( $paths as $path ) {
				$pattern = Configure::read( $path );

				if( preg_test( $pattern ) ) {
					$check = array(
						'success' => true,
						'message' => null
					);
				}
				else {
					$check = array(
						'success' => false,
						'message' => sprintf(
							'L\'expression rationnelle «%s» définie par la clé «%s» dans le webrsa.inc est incorrecte.',
							$pattern,
							$path
						)
					);
				}

				$return[$path] = $check;
			}

			return $return;
		}

		/**
		 * ...
		 */
		public function allCheckParametrage() {
			$departement = (int)Configure::read( 'Cg.departement' );
			$errors = array();

			// @deprecated 3.0.00 (faire le tric moteurs de recherche / autres)
			$ignore = Configure::read( 'ConfigurableQueryFields.ignore' );
			$ignore[] = 'Dossier.locked';
			if( (int)Configure::read( 'Cg.departement' ) === 93 ) {
				$ignore[] = 'Referent.horszone';
			}
			// TODO
			Configure::write( 'ConfigurableQueryFields.ignore', $ignore );

			foreach( App::objects( 'model' ) as $modelName ) {
				if( !in_array( $modelName, $this->notMyModels[$departement] ) ) {
					App::uses( $modelName, 'Model' );
					$Reflection = new ReflectionClass( $modelName );
					if( $Reflection->isAbstract() === false ) {
						$Model = ClassRegistry::init( $modelName );

						if( $Model instanceof WebrsaRechercheInterface ) {
							$errors = Hash::merge( $errors, $Model->checkParametrage() );
						}
						else if( method_exists( $Model,'checkParametrage' ) ) {
							$errors = Hash::merge( $errors, $Model->checkParametrage() );
						}
					}
				}
			}

			return $errors;
		}

		/**
		 * Liste les clef de conf inutile
		 *
		 * @return type
		 */
		public function allCheckBadKeys() {
			$errors = array();
			$badKeys = $this->_badValidateAllowEmptyKeys();

			$errors[ValidateAllowEmptyUtility::$confKey] = array(
				'success' => empty( $badKeys ),
				'value' => implode( ', ', $badKeys ),
				'message' => empty( $badKeys )
					? null
					: 'Il n\'est actuellement pas possible de configurer la validation de ces champs'
			);

			return $errors;
		}

		/**
		 * Récupère la liste des clefs potentielles de configuration allowEmpty d'un champ
		 *
		 * @return array
		 */
		protected function _getValidationAllowEmptyKeys() {
			if ( empty($this->_validationAllowEmptyKeys) ) {
				$modelNames = App::objects('model');
				$results = array();

				foreach ( $modelNames as $modelName ) {
					App::uses( $modelName, 'Model' );
					$Reflection = new ReflectionClass( $modelName );
					if( $Reflection->isAbstract() === false ) {
						$Model = ClassRegistry::init( $modelName );

						foreach ( $Model->configuredAllowEmptyFields as $fieldName ) {
							$results[] = ValidateAllowEmptyUtility::configureKey( "{$Model->alias}.{$fieldName}" );
						}
					}
				}

				$this->_validationAllowEmptyKeys = $results;
			}

			return $this->_validationAllowEmptyKeys;
		}

		/**
		 * Liste les clefs de ValidateAllowEmpty inutile
		 *
		 * @return array
		 */
		protected function _badValidateAllowEmptyKeys() {
			$results = array();
			$list = $this->_getValidationAllowEmptyKeys();

			foreach ( ValidateAllowEmptyUtility::allConf() as $modelName => $params ) {
				foreach ( array_keys($params) as $fieldName ) {
					$presence = false;
					foreach ( $list as $key => $value ) {
						if ( ValidateAllowEmptyUtility::configureKey( "{$modelName}.{$fieldName}" ) === $value ) {
							$presence = true;
							unset($list[$key]);
							break;
						}
					}

					if ( $presence === false ) {
						$results[] = "{$modelName}.{$fieldName}";
					}
				}
			}

			return $results;
		}

		/**
		 * XXX
		 *
		 * @return array
		 */
		public function allConfigureEvidence() {
			$keys = array( 'fields', 'options' );
			$attrs = array( 'title', 'class' );
			$results = array();
			$config = (array)Configure::read( 'Evidence' );

			foreach( $config as $controller => $ctrParams ) {
				$controllerClass = $controller.'Controller';
				App::uses( $controllerClass, 'Controller' );

				if( false === class_exists( $controllerClass ) ) {
					foreach( array_keys( $ctrParams ) as $action ) {
						$results[$controller.'.'.$action] = array(
							'success' => false,
							'value' => var_export( $config[$controller][$action], true ),
							'message' => sprintf( 'Le contrôleur %s n\'existe pas.', $controller )
						);
					}
				}
				else {
					$errors = array();
					$actions = get_class_methods( $controllerClass );

					foreach( $ctrParams as $action => $actionParams ) {
						$path = "{$controller}.{$action}";
						$actionParams = (array)$actionParams;

						if( false === array_search( $action, $actions ) ) {
							$errors[] = sprintf( 'L\'action %s n\'existe pas dans le contrôleur %s.', $action, $controller );
						}
						else {
							// Clés non prises en compte ?
							$diff = array_diff( array_keys( $actionParams ), $keys );
							if( !empty( $diff ) ) {
								foreach( $diff as $key ) {
									$errors[] = sprintf( 'La clé %s n\'existe pas dans la configuration de %s, utilisez une des clés %s.', $key, $path, implode( ', ', $keys ) );
								}
							}

							foreach( $keys as $key ) {
								if( isset( $actionParams[$key] ) ) {
									foreach( Hash::normalize( $actionParams[$key] ) as $selector => $params ) {
										$params = (array)$params;
										$diff = array_diff( array_keys( $params ), $attrs );
										if( !empty( $diff ) ) {
											foreach( $diff as $d ) {
												$errors[] = sprintf( 'L\'attribut %s n\'existe pas dans la configuration de %s, utilisez une des clés %s.', $d, $path.'.'.$selector, implode( ', ', $keys ) );
											}
										}
									}
								}
							}
						}

						$results[$controller.'.'.$action] = array(
							'success' => empty($errors),
							'value' => var_export( $config[$controller][$action], true ),
							'message' => empty($errors) ? null : implode( "\n", $errors )
						);
					}
				}
			}

			return $results;
		}

		/**
		 * Vérifie, pour l'ensemble des contrôleurs utilisés par le département
		 * et de leurs actions (méthodes publiques) si une configuration existe
		 * dans le fichier webrsa.inc (sous la clé <Contrôleur>.<action>.ini-set)
		 * et teste les valeurs de cette configuration.
		 *
		 * @return array
		 */
		public function allConfigureIniSet() {
			$results = array();

			foreach( AppControllers::listControllers() as $className ) {
				$name = preg_replace( '/Controller$/', '', $className );
				if( departement_uses_class( $name ) ) {
					foreach( AppControllers::listActions( $name ) as $action ) {
						$path = "{$name}.{$action}.ini_set";
						$configuration = Configure::read( $path );
						$errors = array();

						if( $configuration !== null ) {
							foreach( $configuration as $varname => $newvalue ) {
								$oldvalue = ini_get( $varname );
								if( ini_set( $varname, $newvalue ) === false || (string)ini_get( $varname ) !== (string)$newvalue ) {
									$msgstr = 'Erreur lors de la configuration de %s.%s à la valeur \'%s\'';
									$errors[] = sprintf( $msgstr, $path, $varname, $newvalue );
								}
								ini_set( $varname, $oldvalue );
							}

							$results[$path] = array(
								'success' => empty( $errors ),
								'value' => var_export( $configuration, true ),
								'message' => empty( $errors ) ? null : implode( ', ', $errors )
							);
						}
					}
				}
			}

			return $results;
		}

		/**
		 *
		 * @return array
		 */
		public function allConfigureTableauxConditions() {
			$departement = (int)Configure::read( 'Cg.departement' );
			$results = array();

			foreach( App::objects( 'model' ) as $modelName ) {
				if( !in_array( $modelName, $this->notMyModels[$departement] ) ) {
					App::uses( $modelName, 'Model' );
					$Reflection = new ReflectionClass( $modelName );
					if( $Reflection->isAbstract() === false ) {
						$Model = ClassRegistry::init( $modelName );

						if( method_exists( $Model,'getTableauxConditions' ) ) {
							$results = Hash::merge( $results, $Model->getTableauxConditions() );
						}
					}
				}
			}

			return $results;
		}

		/**
		 * Verifie les droits stockés en base par-rapport avec les controllers de l'application
		 *
		 * @return array
		 */
		public function allControllersAcos() {
			WebrsaSessionAclUtility::initialize();
			$check = WebrsaSessionAclUtility::checkControllersActionsAcos();

			$cmd = 'sudo -u www-data lib/Cake/Console/cake WebrsaSessionAcl update Aco';
			$errormsg = "Les permissions de la base de données et de l'application ne sont pas syncronisées (%s)<br/>Merci de lancer la commande shell suivante:<br/><code>%s</code>";

			return array(
				'extra' => array(
					'success' => empty($check['extra']),
					'value' => var_export($check['extra'], true),
					'message' => empty($check['extra']) ? null : sprintf( $errormsg, 'il existe des acos en trop', $cmd )
				),
				'missing' => array(
					'success' => empty($check['missing']),
					'value' => var_export($check['missing'], true),
					'message' => empty($check['missing']) ? null : sprintf( $errormsg, 'il manque des acos', $cmd )
				),
			);
		}
	}
?>