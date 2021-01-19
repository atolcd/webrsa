<?php
	/**
	 * Fichier source du plugin Statistiquesplanpauvrete.
	 *
	 * PHP 7.2
	 *
	 * @package Statistiquesplanpauvrete.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 * @author Atol CD
	 */
	App::uses( 'AppModel', 'Model' );

	// Obligatoire pour qu'il n'y ait pas l'erreur "Service Unavailable"
	ini_set('max_execution_time', 0);
	ini_set('memory_limit', '2048M');

	/**
	 * La classe Statistiqueplanpauvrete ...
	 *
	 * @package app.Model
	 */
	class Statistiqueplanpauvrete extends AppModel
	{
		/**
		 * Nom.
		 *
		 * @var string
		 */
		public $name = 'Statistiqueplanpauvrete';

		/**
		 * Ce modèle n'est lié à aucune table.
		 *
		 * @var boolean
		 */
		public $useTable = false;

		/**
		 * Behaviors utilisés.
		 *
		 * @var array
		 */
		public $actsAs = array(
			'Conditionnable'
		);

		/**
		 * Autres modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Historiquedroit',
			'Personne',
			'Foyer',
			'WebrsaCohortePlanpauvrete'
		);

		/**
		 * Retourne les types d'orientations par code_type_orient
		 *
		 * @return array
		 */
		protected function _getTypeOrientation() {
			$Orientation = ClassRegistry::init( 'Orientstruct' );
			$result = array();
			if( Configure::read('Cg.departement') == 66) {
				$emploi = $Orientation->query('SELECT t.id FROM typesorients t INNER JOIN typesorients AS t2 ON (t2.id = t.parentid AND t2.code_type_orient = \'EMPLOI\')' );
				$social = $Orientation->query('SELECT t.id FROM typesorients t INNER JOIN typesorients AS t2 ON (t2.id = t.parentid AND t2.code_type_orient = \'SOCIAL\')' );
				$prepro = $Orientation->query('SELECT t.id FROM typesorients t INNER JOIN typesorients AS t2 ON (t2.id = t.parentid AND t2.code_type_orient = \'PREPRO\')' );
				foreach($emploi as $emp) {
					$result['EMPLOI'][] = $emp[0]['id'];
				}
				foreach($social as $soc) {
					$result['SOCIAL'][] = $soc[0]['id'];
				}
				foreach($prepro as $pre) {
					$result['PREPRO'][] = $pre[0]['id'];
				}
			} else {
				$emploi = $Orientation->query('SELECT t.id FROM typesorients t WHERE t.code_type_orient = \'EMPLOI\'' );
				$social = $Orientation->query('SELECT t.id FROM typesorients t WHERE t.code_type_orient = \'SOCIAL\'' );
				$result['EMPLOI'][] = $emploi[0][0]['id'];
				$result['SOCIAL'][] = $social[0][0]['id'];
			}
			return $result;
		}

		/**
		* Complète le querydata, pour l'année donnée, afin de cibler les
		* allocataires soumis à droits et devoirs, via la table historiquesdroits
		* ou les tables situationsdossiersrsa et calculsdroitsrsa
		*
		* @see clé de configuration Statistiqueplanpauvrete.useHistoriquedroit
		* @see clé de configuration Statistiqueplanpauvrete.conditions_droits_et_devoirs
		*
		* @param array $query
		* @param integer $annee
		* @param boolean $soumisDd
		* @param array $conditions Conditions supplémentaires à utiliser dans la sous-requête.
		* @return array
		*/
		protected function _completeQuerySoumisDd( array $query, $annee, $soumisDd = null, array $conditions = array() ) {
			$Dossier = ClassRegistry::init( 'Dossier' );

			//@fixme: true au 93
			$useHistoriquedroit = (boolean)Configure::read( 'Statistiqueplanpauvrete.useHistoriquedroit' );
			if( true === $useHistoriquedroit ) {
				$query = $this->_completeQueryDernierHistoriqueDroit( $query, $annee, $soumisDd, $conditions );
			}
			else {
				$type = true === $soumisDd ? 'INNER' : 'LEFT OUTER';

				$addJoin = true ;
				foreach ($query['joins'] as $join) {
					if ($join['table'] == '"situationsdossiersrsa"') {
						$addJoin = false;
						break;
					}
				}
				if ($addJoin) {
					$query['joins'][] = $Dossier->join( 'Situationdossierrsa', array( 'type' => $type ) );
				}
				$addJoin = true ;
				foreach ($query['joins'] as $join) {
					if ($join['table'] == '"calculsdroitsrsa"') {
						$addJoin = false;
						break;
					}
				}
				if ($addJoin) {
					$query['joins'][] = $Dossier->Foyer->Personne->join( 'Calculdroitrsa', array( 'type' => $type ) );
				}

				if( null !== $soumisDd ) {
					if( true === $soumisDd ) {
						$conditions[] = $this->_getConditionsDroitsEtDevoirs();
					}
					else {
						$conditions[] = array(
							'OR' => array(
								'Calculdroitrsa.id IS NULL',
								'Situationdossierrsa.id IS NULL',
								array(
									array(
										'Calculdroitrsa.id IS NOT NULL',
										'Situationdossierrsa.id IS NOT NULL'
									),
									'NOT' => array( $this->_getConditionsDroitsEtDevoirs() )
								)
							)
						);
					}
				}

				$query['conditions'][] = $conditions;
			}

			return $query;
		}

		/**
		* Complète le querydata avec une jointure sur la table historiquesdroits
		* et l'ajout éventuel de conditions pour obtenir ou non des allocataires
		* soumis à droits et devoirs.
		*
		* @param array $query
		* @param integer $annee
		* @param boolean $soumisDd
		* @param array $conditions Conditions supplémentaires à utiliser dans la sous-requête.
		* @return array
		*/
		protected function _completeQueryDernierHistoriqueDroit( array $query, $annee, $soumisDd = null, array $conditions = array() ) {
			$Dossier = ClassRegistry::init( 'Dossier' );
			$replacements = array(
				'Situationdossierrsa' => 'Historiquedroit',
				'Calculdroitrsa' => 'Historiquedroit'
			);

			$sqHistoriquedroit = $Dossier->Foyer->Personne->sqLatest(
				'Historiquedroit',
				'created',
				array_merge(
					array(
						"Historiquedroit.created::DATE <= '{$annee}-12-31'",
						"Historiquedroit.modified::DATE >= '{$annee}-01-01'"
					),
					$conditions
				),
				false
			);

			$conditions = array();

			$query['joins'][] = $Dossier->Foyer->Personne->join(
				'Historiquedroit',
				array(
					'type' => true === $soumisDd ? 'INNER' : 'LEFT OUTER',
					'conditions' => array( "Historiquedroit.id IN ( {$sqHistoriquedroit} )" )
				)
			);

			if( null !== $soumisDd ) {
				if( true === $soumisDd ) {
					$conditions[] = alias( $this->_getConditionsDroitsEtDevoirs(), $replacements );
				}
				else {
					$conditions[] = array(
						'OR' => array(
							'Historiquedroit.id IS NULL',
							array(
								'Historiquedroit.id IS NOT NULL',
								'NOT' => array(
									alias( $this->_getConditionsDroitsEtDevoirs(), $replacements )
								)
							)
						)
					);
				}
			}

			$query['conditions'][] = $conditions;
			return $query;
		}

		/**
		 * Retourne les conditions permettant de s'assurer qu'un allocataire soit
		 * dans le champ des droits et devoirs.
		 *
		 * @see Statistiqueplanpauvrete.conditions_droits_et_devoirs dans le webrsa.inc
		 *
		 * @return array
		 */
		protected function _getConditionsDroitsEtDevoirs() {
			return (array)Configure::read( 'Statistiqueplanpauvrete.conditions_droits_et_devoirs' );
		}

		/**
		 * Récupère et initialise les jointures suivant la recherche initiale
		 * @param array $search
		 * @param boolean $addFoyer : permet de savoir si on doit ajouter la table foyer dans le tableau
		 * @param boolean $addFoyer : permet de savoir si on doit ajouter la table orientstruct dans le tableau
		 * @param boolean $addAdresse : permet de savoir si on doit ajouter la table orientstruct dans le tableau
		 * @return array
		 */
		protected function _getJoinsTableau(array $search, $addFoyer = false, $addOrient = false, $addAdresse = true) {
			$joinSearch = array();

			if(
				(isset($search['Adresse']) &&
				(
					$search['Adresse']['nomvoie'] != ''  ||
					$search['Adresse']['nomcom'] != '' ||
					$search['Adresse']['numcom'] != '' )
				)
				|| ( isset($search['Canton']) && $search['Canton']['canton'] != '')
				|| ( isset($search['Sitecov58']) && $search['Sitecov58']['id'] != '' )
			) {
				// Foyer
				$joinFoyer = array();
				if($addFoyer == true) {
					$joinFoyer = array(
						array(
							'table' => 'foyers',
							'alias' => 'Foyer',
							'type' => 'LEFT',
							'conditions' => array(
								'Foyer.id = Personne.foyer_id'
							)
						),
					);
				}
				// Adresse
				if($addAdresse == true) {
					$joinAdresse = array(
						array(
							'table' => 'adressesfoyers',
							'alias' => 'Adressefoyer',
							'type' => 'LEFT',
							'conditions' => array(
								'Adressefoyer.foyer_id = Foyer.id'
							)
						),
						array(
							'table' => 'adresses',
							'alias' => 'Adresse',
							'type' => 'LEFT',
							'conditions' => array(
								'Adresse.id = Adressefoyer.adresse_id'
							)
						),
						array(
							'table' => 'adresses_cantons',
							'alias' => 'AdresseCanton',
							'type' => 'LEFT',
							'conditions' => array(
								'AdresseCanton.adresse_id = Adresse.id'
							)
						),
						array(
							'table' => 'cantons',
							'alias' => 'Canton',
							'type' => 'LEFT',
							'conditions' => array(
								'Canton.id = AdresseCanton.adresse_id'
							)
						),
					);
				} else {
					$joinAdresse = array(
						array(
							'table' => 'adresses_cantons',
							'alias' => 'AdresseCanton',
							'type' => 'LEFT',
							'conditions' => array(
								'AdresseCanton.adresse_id = Adresse.id'
							)
						),
						array(
							'table' => 'cantons',
							'alias' => 'Canton',
							'type' => 'LEFT',
							'conditions' => array(
								'Canton.id = AdresseCanton.adresse_id'
							)
						),
					);
				}
				$joinSearch = array_merge($joinFoyer, $joinAdresse);

				// SiteCov58
				if( isset($search['Sitecov58']) && $search['Sitecov58']['id'] != '' ) {
					$joinSearch = array_merge($joinSearch, array(
						array(
							'table' => 'cantons_sitescovs58',
							'alias' => 'CantonSitecov58',
							'type' => 'LEFT',
							'conditions' => array(
								'CantonSitecov58.canton_id = Canton.id'
							)
						)
					));
				}
			}

			// Service instructeur
			if( isset($search['Search']['serviceinstructeur']) && $search['Search']['serviceinstructeur'] != '' ) {
				$joinOrient = array();
				if($addOrient == true) {
					$joinOrient = array(
						array(
							'table' => 'orientsstructs',
							'alias' => 'Orientstruct',
							'type' => 'LEFT',
							'conditions' => array(
								'Orientstruct.personne_id = Personne.id'
							)
						)
					);
				}

				$joinSearch = array_merge($joinSearch, $joinOrient, array(
					array(
						'table' => 'orientsstructs_servicesinstructeurs',
						'alias' => 'OrientstructServiceinstructeur',
						'type' => 'LEFT',
						'conditions' => array(
							'OrientstructServiceinstructeur.orientstruct_id = Orientstruct.id'
						)
					))
				);
			}

			return $joinSearch;
		}

		/**
		 * Récupère les conditions suivant la recherche initiale
		 * @param array $search
		 * @return array
		 */
		protected function _getConditionsTableau (array $search) {
			$conditions = array();

			// Conditions sur l'adresse de l'allocataire
			$conditions = $this->conditionsAdresse ( $conditions, $search, false, array(), false );

			// Condition sur le service instructeur
			$Statistiqueministerielle = ClassRegistry::init( 'Statistiqueministerielle' );
			$conditions[] = $Statistiqueministerielle->getConditionsServiceInstructeur ( $search );

			return $conditions;
		}

		/**
		 * Initislisation du tableau de résultat B1
		 */
		protected function _initializeTableauB1() {
			$configurationDelais = Configure::read('Statistiqueplanpauvrete.delais');
			$resultats = array (
				'total' => array(),
				'Orientes' => array(
					'total' => array(),
					'emploi' => array(),
					'prepro' => array(),
					'social' => array(),
					'pe' => array(),
					'cd' => array(),
					'oa' => array()
				),
				'NonOrientes' => array(
					'total' => array(),
					'prevu' => array(),
					'bilan' => array(),
					'autres' => array(),
				),
				'delai_moyen' => array(),
				'orient_31jours' => array(),
				'delai' => $configurationDelais,
				'taux_orient' => array()
			);

			for($i=0; $i<12; $i++) {
				$resultats['total'][$i] = 0;
				$resultats['Orientes']['total'][$i] = 0;
				$resultats['Orientes']['emploi'][$i] = 0;
				$resultats['Orientes']['prepro'][$i] = 0;
				$resultats['Orientes']['social'][$i] = 0;
				$resultats['Orientes']['pe'][$i] = 0;
				$resultats['Orientes']['cd'][$i] = 0;
				$resultats['Orientes']['oa'][$i] = 0;
				$resultats['NonOrientes']['total'][$i] = 0;
				$resultats['NonOrientes']['prevu'][$i] = 0;
				$resultats['NonOrientes']['bilan'][$i] = 0;
				$resultats['NonOrientes']['autres'][$i] = 0;
				$resultats['delai_moyen'][$i] = 0;
				$resultats['orient_31jours'][$i] = 0;
				$resultats['taux_orient'][$i] = 0;
				foreach( $configurationDelais as $key => $config) {
					if( is_array($resultats['delai'][$key]) == false ) {
						$resultats['delai'][$key] = array();
					}
					$resultats['delai'][$key][$i] = 0;
				}
			}
			return $resultats;
		}



		/**
		 *
		 * @param array $search
		 * @return array
		*/
		protected function _getQueryTableau_b1(array $search, $rdvPrevu) {
			$annee = (int)Hash::get( $search, 'Search.annee' );

			$query = "SELECT * FROM public.statppview ";
			$query .= "LEFT JOIN rendezvous rdv ON ( rdv.personne_id = idpersonne AND rdv.daterdv > historiquedroit__created AND rdv.statutrdv_id IN (";
			$query .= implode(",", $rdvPrevu);
			$query .= ") AND date_part('year', rdv.daterdv) = " . $annee . ")";
			$query .= " LEFT JOIN bilansparcours66 bilans ON ( bilans.personne_id = idpersonne AND bilans.datebilan > historiquedroit__created AND bilans.proposition LIKE 'audition')";
			$query .= " WHERE ( (annee = " . $annee . " AND (primo = TRUE OR nouvel_entrant = TRUE)) ";
			$query .= "OR (date_part('year',Orientstruct__date_valid) = " . $annee . " AND Orientstruct__rgorient = 1 ) ) ";
			$query .= " AND Historiquedroit__toppersdrodevorsa = '1'";

			$query = $this->getConditionsViewSearch($search, $query);

			return $query;
		}

		/**
		 * Ajoute les conditions lié à la recherche
		 */
		protected function getConditionsViewSearch($search, $query) {
			$conditions = " AND historiquedroit__etatdosrsa = '2'";

			// Adresse
			// Adresse nomvoie
			if(isset($search['Adresse']['nomvoie']) && !empty($search['Adresse']['nomvoie'])) {
				$conditions .= " AND nomvoie ILIKE '%" . $search['Adresse']['nomvoie'] . "%' ";
			}
			// Adresse nomcom
			if(isset($search['Adresse']['nomcom']) && !empty($search['Adresse']['nomcom'])) {
				$conditions .= " AND nomcom ILIKE '%" . $search['Adresse']['nomcom'] . "%' ";
			}
			// Adresse numcom
			if(isset($search['Adresse']['numcom']) && !empty($search['Adresse']['numcom'])) {
				$conditions .= " AND numcom ILIKE '%" . $search['Adresse']['numcom'] . "%' ";
			}

			// Canton
			if( isset($search['Canton']['canton']) && !empty($search['Canton']['canton']) ) {
				// Trim nécessaire car il y a des espaces
				$conditions .= " AND Canton.canton ILIKE '%" . trim($search['Canton']['canton']) . "%'";
			}

			// Service instructeur
			if( isset($search['Search']['serviceinstructeur']) && !empty($search['Search']['serviceinstructeur']) ) {
				// Ajout du join
				$queryArray = explode('WHERE', $query);
				$joinService = "LEFT JOIN orientsstructs_servicesinstructeurs os ON os.orientstruct_id = orientstruct__id ";
				$query = $queryArray[0] . $joinService . ' WHERE ' . $queryArray[1];

				// Conditions
				$conditions .= " AND os.serviceinstructeur_id = " . $search['Search']['serviceinstructeur'];
			}

			$query .= $conditions;
			return $query;
		}

		/**
		 * Initialise le tableau de résultat A1
		 * @return array
		 */
		protected function _initializeTableauA1() {
			$resultats = array (
				'totalDOV' => array(), // Total des personnes avec droit ouvert et versable
				'totalSDDDOV' => array(), // Total des personnes Soumis à droit et devoir et droit ouvert et versable
				'nbPersonnesConnues' => array(), // Nombre de personnes connues le mois précédent avec un droit radié ( = nouveaux entrant )
				'nbPersonnesInconnues' => array(), // Nombre de personnes inconnues en base ( = primo-arrivant )
				'nbPersonnes' => array() // Nouveaux entrant  + primo-arrivant
			);
			for($i=0; $i<12; $i++) {
				$resultats['totalDOV'][$i]=0;
				$resultats['totalSDDDOV'][$i]=0;
				$resultats['nbPersonnesConnues'][$i]=0;
				$resultats['nbPersonnesInconnues'][$i]=0;
				$resultats['nbPersonnes'][$i]=0;
			}
			return $resultats;
		}

		/**
		 *
		 * @param array $search
		 * @return array
		 */
		protected function _getQueryTableau_a1(array $search) {
			$annee = (int)Hash::get( $search, 'Search.annee' );
			$fields = array(
				'annee',
				'mois',
				'idpersonne',
				'historiquedroit__etatdosrsa',
				'historiquedroit__toppersdrodevorsa',
				'primo',
				'nouvel_entrant'
			);

			$query = "SELECT ";

			// Ajout des champs
			foreach($fields as $field) {
				$query .= $field . ', ';
			}
			$query = substr($query, 0, -2);

			// FROM
			$query .= " FROM public.statppview WHERE ";
			$query .= "annee = " . $annee;

			// Conditions
			$query = $this->getConditionsViewSearch($search, $query);

			return $query;
		}

		/**
		 * Initialise le tableau de résultat A2
		 * @return array
		 */
		protected function _initializeTableauA2() {
			$resultats = array (
				//Soumis Droit et Devoir
				'SSD' => array(),
				//Orientée dont
				'Orientes' => array(
					'total' => array(),
					'percent' => array(),
					'Emploi' => array(),
					'percentEmploi' => array(),
					'Prepro' => array(),
					'percentPrepro' => array(),
					'Social' => array(),
					'percentSocial' => array(),
					'PE' => array(),
					'percentPE' => array(),
					'CD' => array(),
					'percentCD' => array(),
					'OA' => array(),
					'percentOA' => array(),
				),
				//Avec contrats dont
				'Contrat' => array(
					'total' => array(),
					'percent' => array(),
					'PEPPAE' => array(),
					'percentPEPPAE' => array(),
					'CDCER' => array(),
					'percentCDCER' => array(),
					'PEAider' => array(),
					'percentPEAider' => array(),
					'PEAccomp' => array(),
					'percentPEAccomp' => array(),
				),
				//Avec CER et Orientation CD dont
				'CDCER' => array(
					'total' => array(),
					'Social' => array(),
					'percentSocial' => array(),
					'Prepro' => array(),
					'percentPrepro' => array(),
				),
				//RDVCER
				'RDVCER' => array()
			);
			for($i=0; $i<12; $i++) {
				//Soumis Droit et Devoir
				$resultats['SSD'][$i] = 0;
				//Soumis Droit et Devoir
				//Orientée dont
				$resultats['Orientes']['total'][$i] =
				$resultats['Orientes']['percent'][$i] =
				$resultats['Orientes']['Emploi'][$i] =
				$resultats['Orientes']['percentEmploi'][$i] =
				$resultats['Orientes']['Prepro'][$i] =
				$resultats['Orientes']['percentPrepro'][$i] =
				$resultats['Orientes']['Social'][$i] =
				$resultats['Orientes']['percentSocial'][$i] =
				$resultats['Orientes']['PE'][$i] =
				$resultats['Orientes']['percentPE'][$i] =
				$resultats['Orientes']['CD'][$i] =
				$resultats['Orientes']['percentCD'][$i] =
				$resultats['Orientes']['OA'][$i] =
				$resultats['Orientes']['percentOA'][$i] =
					0;
				//Avec contrats dont
				$resultats['Contrat']['total'][$i] =
				$resultats['Contrat']['CDCER'][$i] =
				$resultats['Contrat']['PEPPAE'][$i] =
				$resultats['Contrat']['PEAider'][$i] =
				$resultats['Contrat']['PEAccomp'][$i] =
				$resultats['Contrat']['percent'][$i] =
				$resultats['Contrat']['percentPEPPAE'][$i] =
				$resultats['Contrat']['percentCDCER'][$i] =
				$resultats['Contrat']['percentPEAider'][$i] =
				$resultats['Contrat']['percentPEAccomp'][$i] =
				0;
				//Avec contrats dont
				$resultats['CDCER']['total'][$i] = 0;
				$resultats['CDCER']['Social'][$i] = 0;
				$resultats['CDCER']['percentSocial'][$i] = 0;
				$resultats['CDCER']['Prepro'][$i] = 0;
				$resultats['CDCER']['percentPrepro'][$i] = 0;
				//RDVCER
				$resultats['RDVCER'][$i] = 0;
			}
			return $resultats;
		}

		/**
		 *
		 * @param array $search
		 * @return array
		 */
		protected function _getQueryTableau_a2(array $search , $annee) {
			$Dossier = ClassRegistry::init( 'Dossier' );

			$conditionsSearch = $this->_getConditionsTableau($search);
			$joinSearch = $this->_getJoinsTableau($search, true);
			$conditionsSDD = $this->_getConditionsDroitsEtDevoirs();
			$query = array(
				'fields' => array(
					'DISTINCT ON ("Personne"."id") "Personne"."id" AS "idPersonne"',
					'Historiquedroit.id',
					'Historiquedroit.etatdosrsa',
					'Historiquedroit.created',
					'Historiquedroit.modified',
					'Orientstruct.date_valid',
					'Orientstruct.statut_orient',
					'Typeorient.id',
					'Typeorient.lib_type_orient',
					'Typeorient.modele_notif',
					'Typeorient.parentid',
					'Structurereferente.typestructure',
					'Structurereferente.type_struct_stats',
					'Structurereferente.code_stats',
					'Contratinsertion.datevalidation_ci',
					'Contratinsertion.rg_ci',
					'Cui.faitle',
					'Cui.decision_cui'
					/*'Rendezvous.daterdv',
					'Rendezvous.typerdv_id'*/
				),
				'recursive' => -1,
				'joins' => array_merge(
					array(
						array(
							'table' => 'personnes',
							'alias' => 'Personne',
							'type' => 'INNER',
							'conditions' => array(
								'Personne.id = Historiquedroit.personne_id'
							)
						),
						array(
							'table' => 'orientsstructs',
							'alias' => 'Orientstruct',
							'type' => 'LEFT',
							'conditions' => array(
								'AND' => array(
									'Orientstruct.personne_id = Personne.id',
									array(
										'OR' => array(
											array('Orientstruct.date_valid' => NULL),
											array('Orientstruct.date_valid >= Historiquedroit.created')
										)
									)
								)
							)
						),
						array(
							'table' => 'typesorients',
							'alias' => 'Typeorient',
							'type' => 'LEFT',
							'conditions' => array(
								'Typeorient.id = Orientstruct.typeorient_id'
							)
						),
						array(
							'table' => 'structuresreferentes',
							'alias' => 'Structurereferente',
							'type' => 'LEFT',
							'conditions' => array(
								'Structurereferente.id = Orientstruct.structurereferente_id'
							)
						),
						array(
							'table' => 'contratsinsertion',
							'alias' => 'Contratinsertion',
							'type' => 'LEFT',
							'conditions' => array(
								'AND' => array(
									'Personne.id = Contratinsertion.personne_id',
									array(
										'OR' => array(
											array('Contratinsertion.datevalidation_ci' => NULL),
											array('Contratinsertion.datevalidation_ci >= Historiquedroit.created')
										)
									)
								)
							)
						),
						array(
							'table' => 'cuis',
							'alias' => 'Cui',
							'type' => 'LEFT',
							'conditions' => array(
								'AND' => array(
									'Personne.id = Cui.personne_id',
									'Cui.decision_cui = \'V\'',
									array(
										'OR' => array(
											array('Cui.faitle' => NULL),
											array('Cui.faitle >= Historiquedroit.created')
										)
									)
								)
							)
						),
					),
					$joinSearch
				),
				'conditions' => array_merge(
					array(
						'Historiquedroit.modified >= ' => $annee .'-01-01',
						'Historiquedroit.created <= ' => $annee .'-12-31',
						'Historiquedroit.etatdosrsa IN' => $conditionsSDD['Situationdossierrsa.etatdosrsa'],
					),
					$conditionsSearch
				),
			);
			return $query;
		}

		/**
		 * Calcul le pourcentage des résultats du tableau A2
		 * @param array
		 * @return array
		 */
		protected function _calculPercentTableauA2($resultats, $listPersonnes, $annee) {
			for($i=0; $i<12; $i++) {
				$Rendezvous = ClassRegistry::init( 'Rendezvous' );
				$rdv = $Rendezvous->find('all',
					array(
						'fields' => array(
							'DISTINCT ON ("Rendezvous"."id") "Rendezvous"."id" AS "idRendezvous"'
						),
						'recursive' => -1,
						'conditions' => array(
							'Rendezvous.typerdv_id' => Configure::read( 'Statistiqueplanpauvrete.type_rendezvous' ),
							'date_trunc(\'month\',Rendezvous.daterdv)' => "{$annee}-".($i+1)."-01",
							'Rendezvous.personne_id' => $listPersonnes
						)
					)
				);
				$resultats['RDVCER'][$i] = count($rdv);

				if($resultats['SSD'][$i] != 0) {
					$resultats['Orientes']['percent'][$i] = round( (100 * $resultats['Orientes']['total'][$i] ) / $resultats['SSD'][$i], 2)  . '%';
					if ( $resultats['Orientes']['total'][$i] != 0) {
						$resultats['Orientes']['percentEmploi'][$i] = round( (100 * $resultats['Orientes']['Emploi'][$i] ) / $resultats['Orientes']['total'][$i], 2)  . '%';
						$resultats['Orientes']['percentPrepro'][$i] = round( (100 * $resultats['Orientes']['Prepro'][$i] ) / $resultats['Orientes']['total'][$i], 2)  . '%';
						$resultats['Orientes']['percentSocial'][$i] = round( (100 * $resultats['Orientes']['Social'][$i] ) / $resultats['Orientes']['total'][$i], 2)  . '%';
						$resultats['Orientes']['percentPE'][$i] = round( (100 * $resultats['Orientes']['PE'][$i] ) / $resultats['Orientes']['total'][$i], 2)  . '%';
						$resultats['Orientes']['percentCD'][$i] = round( (100 * $resultats['Orientes']['CD'][$i] ) / $resultats['Orientes']['total'][$i], 2)  . '%';
						$resultats['Orientes']['percentOA'][$i] = round( (100 * $resultats['Orientes']['OA'][$i] ) / $resultats['Orientes']['total'][$i], 2)  . '%';
					}
					$resultats['Contrat']['percent'][$i] = round( (100 * $resultats['Contrat']['total'][$i] ) / $resultats['SSD'][$i], 2)  . '%';
					if ( $resultats['Contrat']['total'][$i] != 0) {
						$resultats['Contrat']['percentCDCER'][$i] = round( (100 * $resultats['Contrat']['CDCER'][$i] ) / $resultats['Contrat']['total'][$i], 2)  . '%';
						$resultats['Contrat']['percentPEPPAE'][$i] = round( (100 * $resultats['Contrat']['PEPPAE'][$i] ) / $resultats['Contrat']['total'][$i], 2)  . '%';
						$resultats['Contrat']['percentPEAider'][$i] = round( (100 * $resultats['Contrat']['PEAider'][$i] ) / $resultats['Contrat']['total'][$i], 2)  . '%';
						$resultats['Contrat']['percentPEAccomp'][$i] = round( (100 * $resultats['Contrat']['PEAccomp'][$i] ) / $resultats['Contrat']['total'][$i], 2)  . '%';
					}
					if ( $resultats['CDCER']['total'][$i] != 0) {
						$resultats['CDCER']['percentSocial'][$i] = round( (100 * $resultats['CDCER']['Social'][$i] ) / $resultats['CDCER']['total'][$i], 2)  . '%';
						$resultats['CDCER']['percentPrepro'][$i] = round( (100 * $resultats['CDCER']['Prepro'][$i] ) / $resultats['CDCER']['total'][$i], 2)  . '%';
					}
				}
			}
			return $resultats;
		}

		/**
		 * Enlève les données inutiles selon la configuration
		 * @param array $results
		 * @param string $nomTableau
		 * @return array
		 */
		protected function _adaptLignesTableaux($results, $nomTableau) {
			$lignesnonaffiches = Configure::read('StatistiquePP.' . $nomTableau . '.lignes_non_affichees');
			foreach ($lignesnonaffiches as $key1 => $ligneKey1) {
				if( is_array($ligneKey1) ) {
					foreach ($ligneKey1 as $key2 => $ligneKey2) {
						if( is_array($ligneKey2) ) {
							foreach( $ligneKey2 as $ligneKey3) {
								unset( $results[$key1][$key2][$ligneKey3] );
							}
						} else {
							unset( $results[$key1][$ligneKey2] );
						}
					}
				} else{
					unset($results[$ligneKey1] );
				}
			}

			return $results;
		}

		/**
		 * Initialisation du tableau de résultat B4
		 * @return array
		 */
		protected function _initializeTableauB4(){
			$configurationDelais = Configure::read('Statistiqueplanpauvrete.delais');
			$resultats = array (
				'total' => array(),
				'Social' => array(
					'total' => array(),
					'venu' => array(),
					'excuse_recevable' => array(),
					'sans_excuse' => array(),
					'delai15jrs' => array(),
					'delai_moyen' => array(),
					'delai' => $configurationDelais,
					'taux_presence' => array()
				),
				'Prepro' => array(
					'total' => array(),
					'venu' => array(),
					'excuse_recevable' => array(),
					'sans_excuse' => array(),
					'delai15jrs' => array(),
					'delai_moyen' => array(),
					'delai' => $configurationDelais,
					'taux_presence' => array()
				),
				'Pro' => array(
					'total' => array(),
					'venu' => array(),
					'excuse_recevable' => array(),
					'sans_excuse' => array(),
					'delai15jrs' => array(),
					'delai_moyen' => array(),
					'delai' => $configurationDelais,
					'taux_presence' => array()
				),
			);

			for($i=0; $i<12; $i++) {
				$resultats['total'][$i] = 0;
				$resultats['Social']['total'][$i] = 0;
				$resultats['Social']['venu'][$i] = 0;
				$resultats['Social']['excuse_recevable'][$i] = 0;
				$resultats['Social']['sans_excuse'][$i] = 0;
				$resultats['Social']['delai15jrs'][$i] = 0;
				$resultats['Social']['delai_moyen'][$i] = 0;
				$resultats['Social']['taux_presence'][$i] = 0;
				$resultats['Prepro']['total'][$i] = 0;
				$resultats['Prepro']['venu'][$i] = 0;
				$resultats['Prepro']['excuse_recevable'][$i] = 0;
				$resultats['Prepro']['sans_excuse'][$i] = 0;
				$resultats['Prepro']['delai15jrs'][$i] = 0;
				$resultats['Prepro']['delai_moyen'][$i] = 0;
				$resultats['Prepro']['taux_presence'][$i] = 0;
				$resultats['Pro']['total'][$i] = 0;
				$resultats['Pro']['venu'][$i] = 0;
				$resultats['Pro']['excuse_recevable'][$i] = 0;
				$resultats['Pro']['sans_excuse'][$i] = 0;
				$resultats['Pro']['delai15jrs'][$i] = 0;
				$resultats['Pro']['delai_moyen'][$i] = 0;
				$resultats['Pro']['taux_presence'][$i] = 0;
				foreach( $configurationDelais as $key => $config) {
					if( is_array($resultats['Social']['delai'][$key]) == false ) {
						$resultats['Social']['delai'][$key] = array();
					}
					if( is_array($resultats['Prepro']['delai'][$key]) == false ) {
						$resultats['Prepro']['delai'][$key] = array();
					}
					if( is_array($resultats['Pro']['delai'][$key]) == false ) {
						$resultats['Pro']['delai'][$key] = array();
					}
					$resultats['Social']['delai'][$key][$i] = 0;
					$resultats['Prepro']['delai'][$key][$i] = 0;
					$resultats['Pro']['delai'][$key][$i] = 0;
				}
			}
			return $resultats;
		}

		/**
		 *
		 * @param array $search
		 * @return array
		 */
		protected function _getQueryTableau_b4(array $search) {
			$annee = (int)Hash::get( $search, 'Search.annee' );

			$query = "SELECT * FROM public.statppview ";
			$query .= "INNER JOIN rendezvous rdv ON ( rdv.personne_id = idpersonne AND rdv.daterdv > Orientstruct__date_valid ";
			$query .= " AND date_part('year', rdv.daterdv) = " . $annee . ")";
			$query .= " WHERE date_part('year',Orientstruct__date_valid) = " . $annee . " AND Orientstruct__rgorient = 1 ";
			$query .= " AND Historiquedroit__toppersdrodevorsa = '1'";

			$query = $this->getConditionsViewSearch($search, $query);

			return $query;
		}

		/**
		 * Initialisation du tableau de résultat B5
		 * @return array
		 */
		protected function _initializeTableauB5() {
			$configurationDelais = Configure::read('Statistiqueplanpauvrete.delais');
			$resultats = array (
				'orient_valid' => array(),
				'cer_social' => array(),
				'cer_prepro' => array(),
				'delai_moyen' => array(),
				'delai_social' => array(),
				'delai_prepro' => array(),
				'signe15jrs' => array(),
				'signe2mois' => array(),
				'delai' => $configurationDelais,
				'taux_contrat' => array()
			);

			for($i=0; $i<12; $i++) {
				$resultats['orient_valid'][$i] = 0;
				$resultats['cer_social'][$i] = 0;
				$resultats['cer_prepro'][$i] = 0;
				$resultats['delai_moyen'][$i] = 0;
				$resultats['delai_social'][$i] = 0;
				$resultats['delai_prepro'][$i] = 0;
				$resultats['signe15jrs'][$i] = 0;
				$resultats['signe2mois'][$i] = 0;
				$resultats['taux_contrat'][$i] = 0;
				foreach( $configurationDelais as $key => $config) {
					if( is_array($resultats['delai'][$key]) == false ) {
						$resultats['delai'][$key] = array();
					}
					$resultats['delai'][$key][$i] = 0;
				}
			}
			return $resultats;
		}

		/**
		 *
		 * @param array $search
		 * @return array
		 */
		protected function _getQueryTableau_b5(array $search) {
			$annee = (int)Hash::get( $search, 'Search.annee' );

			$query = "SELECT * FROM public.statppview ";
			$query .= " WHERE date_part('year',Orientstruct__date_valid) = " . $annee . " AND Orientstruct__rgorient = 1 ";
			$query .= " AND Historiquedroit__toppersdrodevorsa = '1'";

			$query = $this->getConditionsViewSearch($search, $query);

			return $query;
		}

		/**
		 * Initalisation du tableau de résultat A1v2
		 * @return array
		 */
		protected function _initializeTableauA1v2() {
			$resultats = array (
				'Tous' => array (
					'total' => array(),
					'nbFoyerInconnu' => array(),
					'nbFoyerRadiSusp' => array(),
					'nbToppers' => array(),
					'nbFoyerJoin' => array(),
					'nbEMM' => array(),
					//Orientée dont
					'Orientes' => array(
						'total' => array(),
						'Emploi' => array(),
						'percentEmploi' => array(),
						'Prepro' => array(),
						'percentPrepro' => array(),
						'Social' => array(),
						'percentSocial' => array(),
						'PE' => array(),
						'percentPE' => array(),
						'CD' => array(),
						'percentCD' => array(),
						'OA' => array(),
						'percentOA' => array(),
					),
					'percentOrientes' => array(),
					'Nonvenu' => array (
						'RDV' => array(),
						'percentRDV' => array()
					)
				),
				'Horssuspendus' => array (
					'total' => array(),
					'nbFoyerInconnu' => array(),
					'nbFoyerRadiSusp' => array(),
					'nbToppers' => array(),
					'nbFoyerJoin' => array(),
					'nbEMM' => array(),
					//Orientée dont
					'Orientes' => array(
						'total' => array(),
						'Emploi' => array(),
						'percentEmploi' => array(),
						'Prepro' => array(),
						'percentPrepro' => array(),
						'Social' => array(),
						'percentSocial' => array(),
						'PE' => array(),
						'percentPE' => array(),
						'CD' => array(),
						'percentCD' => array(),
						'OA' => array(),
						'percentOA' => array(),
					),
					'percentOrientes' => array(),
					'Nonvenu' => array (
						'RDV' => array(),
						'percentRDV' => array()
					)
				),
				'Suspendus' => array (
					'total' => array(),
					'nbFoyerInconnu' => array(),
					'nbFoyerRadiSusp' => array(),
					'nbToppers' => array(),
					'nbFoyerJoin' => array(),
					'nbEMM' => array(),
					//Orientée dont
					'Orientes' => array(
						'total' => array(),
						'Emploi' => array(),
						'percentEmploi' => array(),
						'Prepro' => array(),
						'percentPrepro' => array(),
						'Social' => array(),
						'percentSocial' => array(),
						'PE' => array(),
						'percentPE' => array(),
						'CD' => array(),
						'percentCD' => array(),
						'OA' => array(),
						'percentOA' => array(),
					),
					'percentOrientes' => array(),
					'Nonvenu' => array (
						'RDV' => array(),
						'percentRDV' => array()
					)
				)
			);
			for($i=0; $i<12; $i++) {
				$resultats['Tous']['total'][$i]=0;
				$resultats['Tous']['nbFoyerInconnu'][$i]=0;
				$resultats['Tous']['nbFoyerRadiSusp'][$i]=0;
				$resultats['Tous']['nbToppers'][$i]=0;
				$resultats['Tous']['nbFoyerJoin'][$i]=0;
				$resultats['Tous']['nbEMM'][$i]=0;
				//Orientée dont
				$resultats['Tous']['Orientes']['total'][$i] =
				$resultats['Tous']['percentOrientes'][$i] =
				$resultats['Tous']['Orientes']['Emploi'][$i] =
				$resultats['Tous']['Orientes']['percentEmploi'][$i] =
				$resultats['Tous']['Orientes']['Prepro'][$i] =
				$resultats['Tous']['Orientes']['percentPrepro'][$i] =
				$resultats['Tous']['Orientes']['Social'][$i] =
				$resultats['Tous']['Orientes']['percentSocial'][$i] =
				$resultats['Tous']['Orientes']['PE'][$i] =
				$resultats['Tous']['Orientes']['percentPE'][$i] =
				$resultats['Tous']['Orientes']['CD'][$i] =
				$resultats['Tous']['Orientes']['percentCD'][$i] =
				$resultats['Tous']['Orientes']['OA'][$i] =
				$resultats['Tous']['Orientes']['percentOA'][$i] =
					0;
				$resultats['Tous']['Nonvenu']['RDV'][$i] =
				$resultats['Tous']['Nonvenu']['percentRDV'][$i] = 0;
				$resultats['Suspendus']['total'][$i]=0;
				$resultats['Suspendus']['nbFoyerInconnu'][$i]=0;
				$resultats['Suspendus']['nbFoyerRadiSusp'][$i]=0;
				$resultats['Suspendus']['nbToppers'][$i]=0;
				$resultats['Suspendus']['nbFoyerJoin'][$i]=0;
				$resultats['Suspendus']['nbEMM'][$i]=0;
				//Orientée dont
				$resultats['Suspendus']['Orientes']['total'][$i] =
				$resultats['Suspendus']['percentOrientes'][$i] =
				$resultats['Suspendus']['Orientes']['Emploi'][$i] =
				$resultats['Suspendus']['Orientes']['percentEmploi'][$i] =
				$resultats['Suspendus']['Orientes']['Prepro'][$i] =
				$resultats['Suspendus']['Orientes']['percentPrepro'][$i] =
				$resultats['Suspendus']['Orientes']['Social'][$i] =
				$resultats['Suspendus']['Orientes']['percentSocial'][$i] =
				$resultats['Suspendus']['Orientes']['PE'][$i] =
				$resultats['Suspendus']['Orientes']['percentPE'][$i] =
				$resultats['Suspendus']['Orientes']['CD'][$i] =
				$resultats['Suspendus']['Orientes']['percentCD'][$i] =
				$resultats['Suspendus']['Orientes']['OA'][$i] =
				$resultats['Suspendus']['Orientes']['percentOA'][$i] =
					0;
				$resultats['Suspendus']['Nonvenu']['RDV'][$i] =
				$resultats['Suspendus']['Nonvenu']['percentRDV'][$i] = 0;
				$resultats['Horssuspendus']['total'][$i]=0;
				$resultats['Horssuspendus']['nbFoyerInconnu'][$i]=0;
				$resultats['Horssuspendus']['nbFoyerRadiSusp'][$i]=0;
				$resultats['Horssuspendus']['nbToppers'][$i]=0;
				$resultats['Horssuspendus']['nbFoyerJoin'][$i]=0;
				$resultats['Horssuspendus']['nbEMM'][$i]=0;
				//Orientée dont
				$resultats['Horssuspendus']['Orientes']['total'][$i] =
				$resultats['Horssuspendus']['percentOrientes'][$i] =
				$resultats['Horssuspendus']['Orientes']['Emploi'][$i] =
				$resultats['Horssuspendus']['Orientes']['percentEmploi'][$i] =
				$resultats['Horssuspendus']['Orientes']['Prepro'][$i] =
				$resultats['Horssuspendus']['Orientes']['percentPrepro'][$i] =
				$resultats['Horssuspendus']['Orientes']['Social'][$i] =
				$resultats['Horssuspendus']['Orientes']['percentSocial'][$i] =
				$resultats['Horssuspendus']['Orientes']['PE'][$i] =
				$resultats['Horssuspendus']['Orientes']['percentPE'][$i] =
				$resultats['Horssuspendus']['Orientes']['CD'][$i] =
				$resultats['Horssuspendus']['Orientes']['percentCD'][$i] =
				$resultats['Horssuspendus']['Orientes']['OA'][$i] =
				$resultats['Horssuspendus']['Orientes']['percentOA'][$i] =
					0;
				$resultats['Horssuspendus']['Nonvenu']['RDV'][$i] =
				$resultats['Horssuspendus']['Nonvenu']['percentRDV'][$i] = 0;
			}
			return $resultats;
		}

		/**
		 *
		 * @param array $search
		 * @return array
		 */
		protected function _getQueryTableau_a1v2(array $search , $annee) {
			$jourFinMois = $this->jourDeFin ($annee);
			$conditionsSearch = $this->_getConditionsTableau($search);
			$joinSearch = $this->_getJoinsTableau($search, false, false, false);

			$etatSuspendus = Configure::read( 'Statistiqueplanpauvrete.etatSuspendus' );

			$fields = $this->_queryFields();
			$fields = array_merge(
				$fields,
				array(
					'Foyer.id',
					'Adressefoyer.rgadr',
					'Adressefoyer.dtemm',
					'AdressefoyerAct.dtemm',
					'Adresse.codepos',
					'Rendezvous.daterdv',
					'Dossier.dtdemrsa',
					'Dossiercaf.ddratdos'
				)
			);
			$joins = $this->_queryJoins();

			// Query First
			$query = array(
				'fields' => $fields,
				'recursive' => -1,
				'joins' => array_merge(
					array_merge(
						$joins,
						array(
							array(
								'table' => 'foyers',
								'alias' => 'Foyer',
								'type' => 'INNER',
								'conditions' => array(
									'Personne.foyer_id = Foyer.id'
								),
							),
							array(
								'table' => 'dossiers',
								'alias' => 'Dossier',
								'type' => 'INNER',
								'conditions' => array(
									'Dossier.id = Foyer.dossier_id'
								),
							),
							array(
								'table' => 'adressesfoyers',
								'alias' => 'Adressefoyer',
								'type' => 'LEFT',
								'conditions' => array(
									'Adressefoyer.foyer_id = Foyer.id',
									'Adressefoyer.id = (SELECT id FROM adressesfoyers
										WHERE foyer_id = "Foyer"."id" AND rgadr = \'02\' ORDER BY dtemm DESC LIMIT 1)'
								),
							),
							array(
								'table' => 'adresses',
								'alias' => 'Adresse',
								'type' => 'LEFT',
								'conditions' => array(
									'Adressefoyer.adresse_id = Adresse.id',
								),
							),
							array(
								'table' => 'adressesfoyers',
								'alias' => 'AdressefoyerAct',
								'type' => 'LEFT',
								'conditions' => array(
									'AdressefoyerAct.foyer_id = Foyer.id',
									'AdressefoyerAct.id = (SELECT id FROM adressesfoyers
										WHERE foyer_id = "Foyer"."id" AND rgadr = \'01\' ORDER BY dtemm DESC LIMIT 1)'
								),
							),
							array(
								'table' => 'rendezvous',
								'alias' => 'Rendezvous',
								'type' => 'LEFT',
								'conditions' => array(
									'Rendezvous.personne_id = Personne.id',
									'Personne.id = (SELECT personne_id FROM rendezvous
										INNER JOIN statutsrdvs ON rendezvous.statutrdv_id = statutsrdvs.id
										WHERE personne_id = "Personne"."id"
										AND  statutsrdvs.code_statut LIKE \''.Configure::read( 'Statistiqueplanpauvrete.statut_rendezvous' ).'\''
										.'ORDER BY daterdv DESC LIMIT 1)'
								),
							),
							array(
								'table' => 'dossierscaf',
								'alias' => 'Dossiercaf',
								'type' => 'LEFT',
								'conditions' => array(
									'Dossiercaf.personne_id = Personne.id',
									'Personne.id = (SELECT personne_id FROM dossierscaf
										WHERE personne_id = "Personne"."id"
										ORDER BY ddratdos DESC LIMIT 1)'
								),
							),
							array(
								'table' => 'statutsrdvs',
								'alias' => 'Statutrdv',
								'type' => 'LEFT',
								'conditions' => array(
									'Rendezvous.statutrdv_id = Statutrdv.id'
								),
							)
						)
					),
					$joinSearch
				),
				'conditions' => $conditionsSearch
			);

			$query['conditions'] = array_merge(
				$this->_queryConditionEtatdos($etatSuspendus,$annee, $jourFinMois),
				$query['conditions']
			);

			$query['ORDER'] = array( 'Personne.id DESC' ) ;
			return $query;
		}

		/**
		 * Calule le pourcentage des résultats du tableau A1v2
		 * @param array resultats
		 * @return array resultats
		 */
		protected function _calculPercentTableauA1v2($resultats) {
			for($i=0; $i<12; $i++) {
				if($resultats['Tous']['total'][$i] != 0) {
					$resultats['Tous']['percentOrientes'][$i] = round( (100 * $resultats['Tous']['Orientes']['total'][$i] ) / $resultats['Tous']['total'][$i], 2)  . '%';
					if ( $resultats['Tous']['Orientes']['total'][$i] != 0) {
						$resultats['Tous']['Orientes']['percentEmploi'][$i] = round( (100 * $resultats['Tous']['Orientes']['Emploi'][$i] ) / $resultats['Tous']['Orientes']['total'][$i], 2)  . '%';
						$resultats['Tous']['Orientes']['percentPrepro'][$i] = round( (100 * $resultats['Tous']['Orientes']['Prepro'][$i] ) / $resultats['Tous']['Orientes']['total'][$i], 2)  . '%';
						$resultats['Tous']['Orientes']['percentSocial'][$i] = round( (100 * $resultats['Tous']['Orientes']['Social'][$i] ) / $resultats['Tous']['Orientes']['total'][$i], 2)  . '%';
						$resultats['Tous']['Orientes']['percentPE'][$i] = round( (100 * $resultats['Tous']['Orientes']['PE'][$i] ) / $resultats['Tous']['Orientes']['total'][$i], 2)  . '%';
						$resultats['Tous']['Orientes']['percentCD'][$i] = round( (100 * $resultats['Tous']['Orientes']['CD'][$i] ) / $resultats['Tous']['Orientes']['total'][$i], 2)  . '%';
						$resultats['Tous']['Orientes']['percentOA'][$i] = round( (100 * $resultats['Tous']['Orientes']['OA'][$i] ) / $resultats['Tous']['Orientes']['total'][$i], 2)  . '%';
					}
					$resultats['Tous']['Nonvenu']['percentRDV'][$i]= round( (100 * $resultats['Tous']['Nonvenu']['RDV'][$i] ) / $resultats['Tous']['total'][$i], 2)  . '%';
				}
				if($resultats['Suspendus']['total'][$i] != 0) {
					$resultats['Suspendus']['percentOrientes'][$i] = round( (100 * $resultats['Suspendus']['Orientes']['total'][$i] ) / $resultats['Suspendus']['total'][$i], 2)  . '%';
					if ( $resultats['Suspendus']['Orientes']['total'][$i] != 0) {
						$resultats['Suspendus']['Orientes']['percentEmploi'][$i] = round( (100 * $resultats['Suspendus']['Orientes']['Emploi'][$i] ) / $resultats['Suspendus']['Orientes']['total'][$i], 2)  . '%';
						$resultats['Suspendus']['Orientes']['percentPrepro'][$i] = round( (100 * $resultats['Suspendus']['Orientes']['Prepro'][$i] ) / $resultats['Suspendus']['Orientes']['total'][$i], 2)  . '%';
						$resultats['Suspendus']['Orientes']['percentSocial'][$i] = round( (100 * $resultats['Suspendus']['Orientes']['Social'][$i] ) / $resultats['Suspendus']['Orientes']['total'][$i], 2)  . '%';
						$resultats['Suspendus']['Orientes']['percentPE'][$i] = round( (100 * $resultats['Suspendus']['Orientes']['PE'][$i] ) / $resultats['Suspendus']['Orientes']['total'][$i], 2)  . '%';
						$resultats['Suspendus']['Orientes']['percentCD'][$i] = round( (100 * $resultats['Suspendus']['Orientes']['CD'][$i] ) / $resultats['Suspendus']['Orientes']['total'][$i], 2)  . '%';
						$resultats['Suspendus']['Orientes']['percentOA'][$i] = round( (100 * $resultats['Suspendus']['Orientes']['OA'][$i] ) / $resultats['Suspendus']['Orientes']['total'][$i], 2)  . '%';
					}
					$resultats['Suspendus']['Nonvenu']['percentRDV'][$i]= round( (100 * $resultats['Suspendus']['Nonvenu']['RDV'][$i] ) / $resultats['Suspendus']['total'][$i], 2)  . '%';
				}
				if($resultats['Horssuspendus']['total'][$i] != 0) {
					$resultats['Horssuspendus']['percentOrientes'][$i] = round( (100 * $resultats['Horssuspendus']['Orientes']['total'][$i] ) / $resultats['Horssuspendus']['total'][$i], 2)  . '%';
					if ( $resultats['Horssuspendus']['Orientes']['total'][$i] != 0) {
						$resultats['Horssuspendus']['Orientes']['percentEmploi'][$i] = round( (100 * $resultats['Horssuspendus']['Orientes']['Emploi'][$i] ) / $resultats['Horssuspendus']['Orientes']['total'][$i], 2)  . '%';
						$resultats['Horssuspendus']['Orientes']['percentPrepro'][$i] = round( (100 * $resultats['Horssuspendus']['Orientes']['Prepro'][$i] ) / $resultats['Horssuspendus']['Orientes']['total'][$i], 2)  . '%';
						$resultats['Horssuspendus']['Orientes']['percentSocial'][$i] = round( (100 * $resultats['Horssuspendus']['Orientes']['Social'][$i] ) / $resultats['Horssuspendus']['Orientes']['total'][$i], 2)  . '%';
						$resultats['Horssuspendus']['Orientes']['percentPE'][$i] = round( (100 * $resultats['Horssuspendus']['Orientes']['PE'][$i] ) / $resultats['Horssuspendus']['Orientes']['total'][$i], 2)  . '%';
						$resultats['Horssuspendus']['Orientes']['percentCD'][$i] = round( (100 * $resultats['Horssuspendus']['Orientes']['CD'][$i] ) / $resultats['Horssuspendus']['Orientes']['total'][$i], 2)  . '%';
						$resultats['Horssuspendus']['Orientes']['percentOA'][$i] = round( (100 * $resultats['Horssuspendus']['Orientes']['OA'][$i] ) / $resultats['Horssuspendus']['Orientes']['total'][$i], 2)  . '%';
					}
					$resultats['Horssuspendus']['Nonvenu']['percentRDV'][$i]= round( (100 * $resultats['Horssuspendus']['Nonvenu']['RDV'][$i] ) / $resultats['Horssuspendus']['total'][$i], 2)  . '%';
				}
			}
			return $resultats;
		}

		/**
		 * Initialise le tableaux de résultat du tableau A2av2
		 * @return array
		 */
		protected function _initializeTableauA2av2(){
			$resultats = array (
				'Tous' => array (
					'Orientes_CD' => array(),
					//Orientée dont
					'Orientes' => array(
						'RDV' => array(),
						'RDV_Prepro' => array(),
						'RDV_Social' => array(),
					),
					'Orientes1m' => array(),
					'Orientes15j' => array(
						'RDV' => array(),
						'RDV_Prepro' => array(),
						'RDV_Social' => array(),
					),
					'Taux' => array(),
				),
				'Horssuspendus' => array (
					'Orientes_CD' => array(),
					//Orientée dont
					'Orientes' => array(
						'RDV' => array(),
						'RDV_Prepro' => array(),
						'RDV_Social' => array(),
					),
					'Orientes1m' => array(),
					'Orientes15j' => array(
						'RDV' => array(),
						'RDV_Prepro' => array(),
						'RDV_Social' => array(),
					),
					'Taux' => array(),
				),
				'Suspendus' => array (
					'Orientes_CD' => array(),
					//Orientée dont
					'Orientes' => array(
						'RDV' => array(),
						'RDV_Prepro' => array(),
						'RDV_Social' => array(),
					),
					'Orientes1m' => array(),
					'Orientes15j' => array(
						'RDV' => array(),
						'RDV_Prepro' => array(),
						'RDV_Social' => array(),
					),
					'Taux' => array(),
				)
			);
			for($i=0; $i<12; $i++) {

				$resultats['Tous']['Orientes_CD'][$i]=0;
				//Orientée dont
				$resultats['Tous']['Taux'][$i] =0;
				$resultats['Tous']['Orientes']['RDV'][$i] =
				$resultats['Tous']['Orientes']['RDV_Prepro'][$i] =
				$resultats['Tous']['Orientes']['RDV_Social'][$i] =
					0;
				$resultats['Tous']['Orientes1m'][$i] =0;
				$resultats['Tous']['Orientes15j']['RDV'][$i] =
				$resultats['Tous']['Orientes15j']['RDV_Prepro'][$i] =
				$resultats['Tous']['Orientes15j']['RDV_Social'][$i] =
					0;
				$resultats['Horssuspendus']['Orientes_CD'][$i]=0;
				//Orientée dont
				$resultats['Horssuspendus']['Taux'][$i] =0;
				$resultats['Horssuspendus']['Orientes']['RDV'][$i] =
				$resultats['Horssuspendus']['Orientes']['RDV_Prepro'][$i] =
				$resultats['Horssuspendus']['Orientes']['RDV_Social'][$i] =
					0;
				$resultats['Horssuspendus']['Orientes1m'][$i] =0;
				$resultats['Horssuspendus']['Orientes15j']['RDV'][$i] =
				$resultats['Horssuspendus']['Orientes15j']['RDV_Prepro'][$i] =
				$resultats['Horssuspendus']['Orientes15j']['RDV_Social'][$i] =
					0;
				$resultats['Suspendus']['Orientes_CD'][$i]=0;
				//Orientée dont
				$resultats['Suspendus']['Taux'][$i] =0;
				$resultats['Suspendus']['Orientes']['RDV'][$i] =
				$resultats['Suspendus']['Orientes']['RDV_Prepro'][$i] =
				$resultats['Suspendus']['Orientes']['RDV_Social'][$i] =
					0;
				$resultats['Suspendus']['Orientes1m'][$i] =0;
				$resultats['Suspendus']['Orientes15j']['RDV'][$i] =
				$resultats['Suspendus']['Orientes15j']['RDV_Prepro'][$i] =
				$resultats['Suspendus']['Orientes15j']['RDV_Social'][$i] =
					0;
			}
			return $resultats;
		}

		/**
		 *
		 * @param array $search
		 * @return array
		 */
		protected function _getQueryTableau_a2av2(array $search , $annee) {
			$jourFinMois = $this->jourDeFin ($annee);
			$conditionsSearch = $this->_getConditionsTableau($search);
			$joinSearch = $this->_getJoinsTableau($search, true, false);

			$fields = $this->_queryFields();
			$fields[] = 'Rendezvous.daterdv';

			$joins = $this->_queryJoins();

			// Query First
			$query = array(
				'fields' => $fields,
				'recursive' => -1,
				'joins' => array_merge(
					array_merge(
						$joins,
						array(
							array(
								'table' => 'rendezvous',
								'alias' => 'Rendezvous',
								'type' => 'LEFT',
								'conditions' => array(
									'Rendezvous.personne_id = Personne.id',
									'Rendezvous.typerdv_id' => Configure::read( 'Statistiqueplanpauvrete.type_rendezvous' ),
									'Personne.id = (SELECT personne_id FROM rendezvous
										WHERE personne_id = "Personne"."id" ORDER BY daterdv DESC LIMIT 1)'
								),
							)
						)
					),
					$joinSearch
				),
				'conditions' => $conditionsSearch
			);

			$query['conditions'] = array_merge(
				array(
					'Orientstruct.date_valid >= ' => $annee .'-01-01',
					'Structurereferente.type_struct_stats' => 'cd'
				),
				$query['conditions']
			);

			return $query;
		}

		/**
		 * Calcul le pourcentage des résultats du tableau A2av2
		 * @param array
		 * @return array
		 */
		protected function _calculPercentTableauA2av2($resultats) {
			for($i=0; $i<12; $i++) {
				if($resultats['Tous']['Orientes_CD'][$i] != 0) {
					$resultats['Tous']['Taux'][$i] =
							round( (100 * $resultats['Tous']['Orientes15j']['RDV'][$i] )
								/ $resultats['Tous']['Orientes_CD'][$i], 2)  . '%';
				}
				if($resultats['Horssuspendus']['Orientes_CD'][$i] != 0) {
					$resultats['Horssuspendus']['Taux'][$i] =
							round( (100 * $resultats['Horssuspendus']['Orientes15j']['RDV'][$i] )
								/ $resultats['Horssuspendus']['Orientes_CD'][$i], 2)  . '%';
				}
				if($resultats['Suspendus']['Orientes_CD'][$i] != 0) {
					$resultats['Suspendus']['Taux'][$i] =
						round( (100 * $resultats['Suspendus']['Orientes15j']['RDV'][$i] )
						/ $resultats['Suspendus']['Orientes_CD'][$i], 2)  . '%';
				}
			}
			return $resultats;
		}

		/**
		 * Initialisation du tableau de résultat A2bv2
		 * @return array
		 */
		protected function _initializeTableauA2bv2() {
			$resultats = array (
				'Tous' => array (
					'Orientes_CD' => array(),
					//Orientée dont
					'Orientes' => array(
						'CER' => array(),
						'CER_Prepro' => array(),
						'CER_Social' => array(),
					),
					'Orientes1m' => array(),
					'Orientes2m' => array(
						'CER' => array(),
						'CER_Prepro' => array(),
						'CER_Social' => array(),
					),
					'Taux' => array(),
				),
				'Suspendus' => array (
					'Orientes_CD' => array(),
					//Orientée dont
					'Orientes' => array(
						'CER' => array(),
						'CER_Prepro' => array(),
						'CER_Social' => array(),
					),
					'Orientes1m' => array(),
					'Orientes2m' => array(
						'CER' => array(),
						'CER_Prepro' => array(),
						'CER_Social' => array(),
					),
					'Taux' => array(),
				),
				'Horssuspendus'=> array (
					'Orientes_CD' => array(),
					//Orientée dont
					'Orientes' => array(
						'CER' => array(),
						'CER_Prepro' => array(),
						'CER_Social' => array(),
					),
					'Orientes1m' => array(),
					'Orientes2m' => array(
						'CER' => array(),
						'CER_Prepro' => array(),
						'CER_Social' => array(),
					),
					'Taux' => array(),
				)
			);
			for($i=0; $i<12; $i++) {
				$resultats['Tous']['Orientes_CD'][$i]=0;
				//Orientée dont
				$resultats['Tous']['Taux'][$i] =0;
				$resultats['Tous']['Orientes']['CER'][$i] =
				$resultats['Tous']['Orientes']['CER_Prepro'][$i] =
				$resultats['Tous']['Orientes']['CER_Social'][$i] =
					0;
				$resultats['Tous']['Orientes1m'][$i] =0;
				$resultats['Tous']['Orientes2m']['CER'][$i] =
				$resultats['Tous']['Orientes2m']['CER_Prepro'][$i] =
				$resultats['Tous']['Orientes2m']['CER_Social'][$i] =
					0;
				$resultats['Suspendus']['Orientes_CD'][$i]=0;
				//Orientée dont
				$resultats['Suspendus']['Taux'][$i] =0;
				$resultats['Suspendus']['Orientes']['CER'][$i] =
				$resultats['Suspendus']['Orientes']['CER_Prepro'][$i] =
				$resultats['Suspendus']['Orientes']['CER_Social'][$i] =
					0;
				$resultats['Suspendus']['Orientes1m'][$i] =0;
				$resultats['Suspendus']['Orientes2m']['CER'][$i] =
				$resultats['Suspendus']['Orientes2m']['CER_Prepro'][$i] =
				$resultats['Suspendus']['Orientes2m']['CER_Social'][$i] =
					0;
				$resultats['Horssuspendus']['Orientes_CD'][$i]=0;
				//Orientée dont
				$resultats['Horssuspendus']['Taux'][$i] =0;
				$resultats['Horssuspendus']['Orientes']['CER'][$i] =
				$resultats['Horssuspendus']['Orientes']['CER_Prepro'][$i] =
				$resultats['Horssuspendus']['Orientes']['CER_Social'][$i] =
					0;
				$resultats['Horssuspendus']['Orientes1m'][$i] =0;
				$resultats['Horssuspendus']['Orientes2m']['CER'][$i] =
				$resultats['Horssuspendus']['Orientes2m']['CER_Prepro'][$i] =
				$resultats['Horssuspendus']['Orientes2m']['CER_Social'][$i] =
					0;
			}
			return $resultats;
		}

		/**
		 *
		 * @param array $search
		 * @return array
		 */
		protected function _getQueryTableau_a2bv2(array $search , $annee) {
			$jourFinMois = $this->jourDeFin ($annee);
			$conditionsSearch = $this->_getConditionsTableau($search);
			$joinSearch = $this->_getJoinsTableau($search, true, false);

			$fields = $this->_queryFields();
			$fields[] = 'Contratinsertion.datevalidation_ci';

			$joins = $this->_queryJoins();

			// Query First
			$query = array(
				'fields' => $fields,
				'recursive' => -1,
				'joins' => array_merge(
					array_merge(
						$joins,
						array(
							array(
								'table' => 'contratsinsertion',
								'alias' => 'Contratinsertion',
								'type' => 'LEFT',
								'conditions' => array(
									'Contratinsertion.personne_id = Personne.id',
									'Contratinsertion.structurereferente_id = Structurereferente.id',
									'Personne.id = (SELECT personne_id FROM contratsinsertion
										WHERE personne_id = "Personne"."id" ORDER BY datevalidation_ci DESC LIMIT 1)'
								),
							)
						)
					),
					$joinSearch
				),
				'conditions' => $conditionsSearch
			);

			$query['conditions'] = array_merge(
				array(
					'Orientstruct.date_valid >= ' => $annee .'-01-01',
					'Structurereferente.type_struct_stats' => 'cd'
				),
				$query['conditions']
			);

			return $query;
		}

		/**
		 * Calule le pourcentage des résultats du tabkleau A2bv2
		 * @param array resultats
		 * @return array resultats
		 */
		protected function _calculPercentTableauA2bv2($resultats) {
			for($i=0; $i<12; $i++) {
				if($resultats['Tous']['Orientes_CD'][$i] != 0) {
					$resultats['Tous']['Taux'][$i] =
						round( (100 * $resultats['Tous']['Orientes2m']['CER'][$i] )
						/ $resultats['Tous']['Orientes_CD'][$i], 2)  . '%';
				}
				if($resultats['Horssuspendus']['Orientes_CD'][$i] != 0) {
					$resultats['Horssuspendus']['Taux'][$i] =
						round( (100 * $resultats['Horssuspendus']['Orientes2m']['CER'][$i] )
						/ $resultats['Horssuspendus']['Orientes_CD'][$i], 2)  . '%';
				}
				if($resultats['Suspendus']['Orientes_CD'][$i] != 0) {
					$resultats['Suspendus']['Taux'][$i] =
						round( (100 * $resultats['Suspendus']['Orientes2m']['CER'][$i] )
							/ $resultats['Suspendus']['Orientes_CD'][$i], 2)  . '%';
				}
			}
			return $resultats;
		}

		########################################################################################################################
		#V3
		########################################################################################################################

		/**
		 * Initialisation du tableau de résultat A1v3
		 * @return array
		 */
		protected function _initializeTableauA1v3() {
			$resultats = array (
				'total' => array(),
				'nbFoyerInconnu' => array(),
				'nbFoyerRadiSusp' => array(),
				'nbToppers' => array(),
				'nbFoyerJoin' => array(),
				'nbEMM' => array(),
				//Orientée dont
				'Orientes' => array(
					'total' => array(),
					'Emploi' => array(),
					'percentEmploi' => array(),
					'Prepro' => array(),
					'percentPrepro' => array(),
					'Social' => array(),
					'percentSocial' => array(),
					'PE' => array(),
					'percentPE' => array(),
					'CD' => array(),
					'percentCD' => array(),
					'OA' => array(),
					'percentOA' => array(),
				),
				'percentOrientes' => array(),
				'Nonvenu' => array (
					'RDV' => array(),
					'percentRDV' => array()
				)
			);
			for($i=0; $i<12; $i++) {
				$resultats['total'][$i]=0;
				$resultats['nbFoyerInconnu'][$i]=0;
				$resultats['nbFoyerRadiSusp'][$i]=0;
				$resultats['nbToppers'][$i]=0;
				$resultats['nbFoyerJoin'][$i]=0;
				$resultats['nbEMM'][$i]=0;
				//Orientée dont
				$resultats['Orientes']['total'][$i] =
				$resultats['percentOrientes'][$i] =
				$resultats['Orientes']['Emploi'][$i] =
				$resultats['Orientes']['percentEmploi'][$i] =
				$resultats['Orientes']['Prepro'][$i] =
				$resultats['Orientes']['percentPrepro'][$i] =
				$resultats['Orientes']['Social'][$i] =
				$resultats['Orientes']['percentSocial'][$i] =
				$resultats['Orientes']['PE'][$i] =
				$resultats['Orientes']['percentPE'][$i] =
				$resultats['Orientes']['CD'][$i] =
				$resultats['Orientes']['percentCD'][$i] =
				$resultats['Orientes']['OA'][$i] =
				$resultats['Orientes']['percentOA'][$i] =
					0;
				$resultats['Nonvenu']['RDV'][$i] =
				$resultats['Nonvenu']['percentRDV'][$i] = 0;
			}
			return $resultats;
		}

		/**
		 *
		 * @param array $search
		 * @return array
		 *
		 */
		protected function _getQueryTableau_a1v3(array $search, $etat, $annee) {
			$conditionsSearch = $this->_getConditionsTableau($search);
			$joinSearch = $this->_getJoinsTableau($search, false, false);

			$jourFinMois = $this->jourDeFin ($annee);
			$tmpDateRecherchePrevious = $annee.'-01-'.$jourFinMois;
			$tmpDateRechercheNext = ($annee+1).'-01-'.$jourFinMois;

			$fields = $this->_queryFields();
			$fields = array_merge(
				$fields,
				array(
					'Personne.id',
					'Foyer.id',
					'Rendezvous.daterdv',
					'Dossier.id',
					'Dossier.dtdemrsa',
					'Dossier.ddarrmut',
					'Dossiercaf.ddratdos'
				)
			);
			$joins = $this->_queryJoins();

			// Query First
			$query = array(
				'fields' => $fields,
				'recursive' => -1,
				'joins' => array_merge(
					array_merge(
						$joins,
						array(
							array(
								'table' => 'foyers',
								'alias' => 'Foyer',
								'type' => 'INNER',
								'conditions' => array(
									'Personne.foyer_id = Foyer.id'
								),
							),
							array(
								'table' => 'dossiers',
								'alias' => 'Dossier',
								'type' => 'INNER',
								'conditions' => array(
									'Dossier.id = Foyer.dossier_id'
								),
							),
							array(
								'table' => 'rendezvous',
								'alias' => 'Rendezvous',
								'type' => 'LEFT',
								'conditions' => array(
									'Rendezvous.personne_id = Personne.id',
									'Personne.id = (SELECT personne_id FROM rendezvous
										INNER JOIN statutsrdvs ON rendezvous.statutrdv_id = statutsrdvs.id
										WHERE personne_id = "Personne"."id"
										AND  statutsrdvs.code_statut LIKE \''.Configure::read( 'Statistiqueplanpauvrete.statut_rendezvous' ).'\''
										.'ORDER BY daterdv DESC LIMIT 1)'
								),
							),
							array(
								'table' => 'statutsrdvs',
								'alias' => 'Statutrdv',
								'type' => 'LEFT',
								'conditions' => array(
									'Rendezvous.statutrdv_id = Statutrdv.id'
								),
							),
							array(
								'table' => 'dossierscaf',
								'alias' => 'Dossiercaf',
								'type' => 'LEFT',
								'conditions' => array(
									'Dossiercaf.personne_id = Personne.id',
									'Personne.id = (SELECT personne_id FROM dossierscaf
										WHERE personne_id = "Personne"."id"
										ORDER BY ddratdos DESC LIMIT 1)'
								),
							),
						)
					),
					$joinSearch
				),
				'conditions' => $conditionsSearch
			);

			//Limitation à l'unicité de l'état de la personne : étatdosrsa 2 et toppersdrodevorsa 1
			$query = $this->_getConditionEtatdosUnique( $query, $etat, $annee);

			$query['ORDER'] = array( 'Personne.id DESC' ) ;
			return $query;
		}

		/**
		 * Calcul le pourcentage des résultats du tableau A1v3
		 * @param array
		 * @return array
		 */
		protected function _calculPercentTableauA1v3($resultats) {
			for($i=0; $i<12; $i++) {
				if($resultats['total'][$i] != 0) {
					$resultats['percentOrientes'][$i] = round( (100 * $resultats['Orientes']['total'][$i] ) / $resultats['total'][$i], 2)  . '%';
					if ( $resultats['Orientes']['total'][$i] != 0) {
						$resultats['Orientes']['percentEmploi'][$i] = round( (100 * $resultats['Orientes']['Emploi'][$i] ) / $resultats['Orientes']['total'][$i], 2)  . '%';
						$resultats['Orientes']['percentPrepro'][$i] = round( (100 * $resultats['Orientes']['Prepro'][$i] ) / $resultats['Orientes']['total'][$i], 2)  . '%';
						$resultats['Orientes']['percentSocial'][$i] = round( (100 * $resultats['Orientes']['Social'][$i] ) / $resultats['Orientes']['total'][$i], 2)  . '%';
						$resultats['Orientes']['percentPE'][$i] = round( (100 * $resultats['Orientes']['PE'][$i] ) / $resultats['Orientes']['total'][$i], 2)  . '%';
						$resultats['Orientes']['percentCD'][$i] = round( (100 * $resultats['Orientes']['CD'][$i] ) / $resultats['Orientes']['total'][$i], 2)  . '%';
						$resultats['Orientes']['percentOA'][$i] = round( (100 * $resultats['Orientes']['OA'][$i] ) / $resultats['Orientes']['total'][$i], 2)  . '%';
					}
					$resultats['Nonvenu']['percentRDV'][$i]= round( (100 * $resultats['Nonvenu']['RDV'][$i] ) / $resultats['total'][$i], 2)  . '%';
				}
			}
			return $resultats;
		}

		/**
		 * Initialisation du tableau de résultat A2av3
		 * @return array
		 */
		protected function _initializeTableauA2av3() {
			$resultats = array (
				'Orientes_CD' => array(),
				//Orientée dont
				'Orientes' => array(
					'RDV' => array(),
					'RDV_Prepro' => array(),
					'RDV_Social' => array(),
				),
				'Orientes1m' => array(),
				'Orientes15j' => array(
					'RDV' => array(),
					'RDV_Prepro' => array(),
					'RDV_Social' => array(),
				),
				'Taux' => array(),
			);
			for($i=0; $i<12; $i++) {

				$resultats['Orientes_CD'][$i]=0;
				//Orientée dont
				$resultats['Taux'][$i] =0;
				$resultats['Orientes']['RDV'][$i] =
				$resultats['Orientes']['RDV_Prepro'][$i] =
				$resultats['Orientes']['RDV_Social'][$i] =
					0;
				$resultats['Orientes1m'][$i] =0;
				$resultats['Orientes15j']['RDV'][$i] =
				$resultats['Orientes15j']['RDV_Prepro'][$i] =
				$resultats['Orientes15j']['RDV_Social'][$i] =
					0;
			}
			return $resultats;
		}

		/**
		 *
		 * @param array $search
		 * @return array
		 */
		protected function _getQueryTableau_a2av3(array $search, $etat, $annee) {
			$jourFinMois = $this->jourDeFin ($annee);
			$conditionsSearch = $this->_getConditionsTableau($search);
			$joinSearch = $this->_getJoinsTableau($search, true, false);

			$fields = $this->_queryFields();
			$fields[] = 'Rendezvous.daterdv';

			$joins = $this->_queryJoins();

			// Query First
			$query = array(
				'fields' => $fields,
				'recursive' => -1,
				'joins' => array_merge(
					array_merge(
						$joins,
						array(
							array(
								'table' => 'rendezvous',
								'alias' => 'Rendezvous',
								'type' => 'LEFT',
								'conditions' => array(
									'Rendezvous.personne_id = Personne.id',
									'Rendezvous.typerdv_id' => Configure::read( 'Statistiqueplanpauvrete.type_rendezvous' ),
									'Personne.id = (SELECT personne_id FROM rendezvous
										WHERE personne_id = "Personne"."id" ORDER BY daterdv DESC LIMIT 1)'
								),
							)
						)
					),
					$joinSearch
				),
				'conditions' => $conditionsSearch
			);

			//Limitation à l'unicité de l'état de la personne : étatdosrsa 2 et toppersdrodevorsa 1
			$query = $this->_getConditionEtatdosUnique( $query, $etat, $annee);

			$query['conditions'] = array_merge(
				array(
					'Orientstruct.date_valid >= ' => $annee .'-01-01',
					'Structurereferente.type_struct_stats' => 'cd'
				),
				$query['conditions']
			);

			return $query;
		}

		/**
		 * Initialise le tableaux de résultat du tableau A2bv3
		 * @return array
		 */
		protected function _initializeTableauA2bv3(){
			$resultats = array (
				'Orientes_CD' => array(),
				//Orientée dont
				'Orientes' => array(
					'CER' => array(),
					'CER_Prepro' => array(),
					'CER_Social' => array(),
				),
				'Orientes1m' => array(),
				'Orientes2m' => array(
					'CER' => array(),
					'CER_Prepro' => array(),
					'CER_Social' => array(),
				),
				'Taux' => array(),
			);
			for($i=0; $i<12; $i++) {
				$resultats['Orientes_CD'][$i]=0;
				//Orientée dont
				$resultats['Taux'][$i] =0;
				$resultats['Orientes']['CER'][$i] =
				$resultats['Orientes']['CER_Prepro'][$i] =
				$resultats['Orientes']['CER_Social'][$i] =
					0;
				$resultats['Orientes1m'][$i] =0;
				$resultats['Orientes2m']['CER'][$i] =
				$resultats['Orientes2m']['CER_Prepro'][$i] =
				$resultats['Orientes2m']['CER_Social'][$i] =
					0;
			}
			return $resultats;
		}

		/**
		 *
		 * @param array $search
		 * @return array
		 */
		protected function _getQueryTableau_a2bv3(array $search, $etat, $annee) {
			$jourFinMois = $this->jourDeFin ($annee);
			$conditionsSearch = $this->_getConditionsTableau($search);
			$joinSearch = $this->_getJoinsTableau($search, true, false);

			$fields = $this->_queryFields();
			$fields[] = 'Contratinsertion.datevalidation_ci';

			$joins = $this->_queryJoins();

			// Query First
			$query = array(
				'fields' => $fields,
				'recursive' => -1,
				'joins' => array_merge(
					array_merge(
						$joins,
						array(
							array(
								'table' => 'contratsinsertion',
								'alias' => 'Contratinsertion',
								'type' => 'LEFT',
								'conditions' => array(
									'Contratinsertion.personne_id = Personne.id',
									'Contratinsertion.structurereferente_id = Structurereferente.id',
									'Personne.id = (SELECT personne_id FROM contratsinsertion
										WHERE personne_id = "Personne"."id" ORDER BY datevalidation_ci DESC LIMIT 1)'
								),
							)
						)
					),
					$joinSearch
				),
				'conditions' => $conditionsSearch
			);

			//Limitation à l'unicité de l'état de la personne : étatdosrsa 2 et toppersdrodevorsa 1
			$query = $this->_getConditionEtatdosUnique( $query, $etat, $annee);

			$query['conditions'] = array_merge(
				array(
					'Orientstruct.date_valid >= ' => $annee .'-01-01',
					'Structurereferente.type_struct_stats' => 'cd'
				),
				$query['conditions']
			);

			return $query;
		}

		########################################################################################################################
		########################################################################################################################

		/**
		 *
		 * @param array $search
		 * @return array
		 */
		protected function _getQueryHistoriques($annee, $arrayIds) {
			$jourFinMois = $this->jourDeFin ($annee);
			$tmpDateRecherchePrevious = $annee.'-01-'.$jourFinMois;

			if ( is_array ( $arrayIds )){
				$ids = "'";
				$ids .= implode( "','",$arrayIds);
				$ids .= "'";
			}else{
				$ids = $arrayIds;
			}

			//Fields
			$sqlHistoriquedroit = " SELECT Historiquedroit.personne_id,";
			$sqlHistoriquedroit .= 'max(etatdosrsa) FILTER ( WHERE (\''.$tmpDateRecherchePrevious.'\'
				BETWEEN date_trunc(\'day\', created )
				AND  date_trunc(\'day\', modified )) ) AS etatdosrsa12, ';
			$sqlHistoriquedroit .= 'max (toppersdrodevorsa) FILTER ( WHERE (\''.$tmpDateRecherchePrevious.'\'
				BETWEEN date_trunc(\'day\', created )
				AND  date_trunc(\'day\', modified )) ) AS toppersdrodevorsa12 ';

			//Fields By Months
			for($month=0; $month<12; $month++) {
				$tmpDateRecherche = $this->_getDateString( $annee, $month, $jourFinMois, 2 );
				$sqlHistoriquedroit .= ', max(etatdosrsa) FILTER ( WHERE (\''.$tmpDateRecherche.'\'
					BETWEEN date_trunc(\'day\', created )
					AND  date_trunc(\'day\', modified )) ) AS etatdosrsa'.$month;
				$sqlHistoriquedroit .= ', max (toppersdrodevorsa) FILTER ( WHERE (\''.$tmpDateRecherche.'\'
					BETWEEN date_trunc(\'day\', created )
					AND  date_trunc(\'day\', modified )) ) AS toppersdrodevorsa'.$month;
			}

			$sqlHistoriquedroit .= ' FROM historiquesdroits AS Historiquedroit ';
			$sqlHistoriquedroit .= ' WHERE Historiquedroit.personne_id IN ( '. $ids .')';
			$sqlHistoriquedroit .= ' GROUP BY personne_id ORDER BY personne_id ';


			return $sqlHistoriquedroit;
		}

		/**
		 *
		 * @param array $search
		 * @return array
		 */
		protected function _getQueryDossier($annee, $arrayIds) {
			$jourFinMois = $this->jourDeFin ($annee);
			$tmpDateRecherchePrevious = $annee.'-01-'.$jourFinMois;

			if ( is_array ( $arrayIds )){
				$ids = "'";
				$ids .= implode( "','",$arrayIds);
				$ids .= "'";
			}else{
				$ids = $arrayIds;
			}

			//Fields
			$sqlHistoriquedossier = ' SELECT Personne.id,';
			$sqlHistoriquedossier .= ' dda.hasotherdossier AS hasotherdossier ';
			$sqlHistoriquedossier .= ' FROM personnes AS Personne ';
			$sqlHistoriquedossier .= ' INNER JOIN derniersdossiersallocataires AS dda ON dda.personne_id=Personne.id ';
			$sqlHistoriquedossier .= ' WHERE Personne.id IN ( '. $ids .')';
			return $sqlHistoriquedossier;
		}

		/**
		 *
		 * @param array $search
		 * @return array
		 */
		protected function _getConditionEtatdosUnique( $query, $etat, $annee) {
			$jourFinMois = $this->jourDeFin ($annee);
			$tmpDateRecherchePrevious = $annee.'-01-'.$jourFinMois;
			$tmpDateRechercheNext = ($annee+1).'-01-'.$jourFinMois;
			if ( is_array ( $etat )){
				$etats = "'";
				$etats .= implode( "','",$etat);
				$etats .= "'";
			}else{
				$etats = "'".$etat."'";
			}
			$tmpConditions = array (
				'Personne.id IN ( SELECT personne_id FROM historiquesdroits
				WHERE historiquesdroits.etatdosrsa IN ( '.$etats.' )
				AND date_trunc(\'day\', historiquesdroits.created ) < \''.$tmpDateRechercheNext.'\'
				AND date_trunc(\'day\', historiquesdroits.modified ) > \''.$tmpDateRecherchePrevious.'\'
				)'
			);
			$query['conditions'] = array_merge(
				$tmpConditions,
				$query['conditions']
			);

			$tmpConditions = array (
				'Personne.id NOT IN ( SELECT personne_id FROM historiquesdroits
				WHERE historiquesdroits.etatdosrsa IN ( '.$etats.' )
				AND historiquesdroits.toppersdrodevorsa = \'1\'
				AND date_trunc(\'day\', historiquesdroits.created ) < \''.$tmpDateRecherchePrevious.'\'
				)'
			);
			$query['conditions'] = array_merge(
				$tmpConditions,
				$query['conditions']
			);
			return $query;
		}

		########################################################################################################################
		########################################################################################################################

		/**
		 *
		 * @param array $search
		 * @return array
		 */
		protected function _getDateString($annee, $month, $jour, $step = 1) {
			$tmpMonth = '01';
			$tmpAnnee = $annee;
			if (($month + $step) < 10) {
				$tmpMonth = '0'.($month + 2);
			}
			elseif (($month + $step) > 12){
				$tmpAnnee = $annee + 1;
				$tmpMonth = '0'.($month - 12 + $step);
			}
			else {
				$tmpMonth = ($month + $step);
			}

			// Calcul du jour de fin du mois
			$jour = date ("t", mktime (0, 0, 0, $tmpMonth, 1, $tmpAnnee));

			$tmpDateRecherche = $tmpAnnee.'-'.$tmpMonth.'-'.$jour;

			return $tmpDateRecherche;
		}

		private function _queryFields () {

			$fields = array (
				'DISTINCT ON ("Personne"."id") "Personne"."id" AS "Personne__id"',
				'Orientstruct.id',
				'Orientstruct.date_valid',
				'Orientstruct.statut_orient',
				'Typeorient.id',
				'Typeorient.parentid',
				'Structurereferente.type_struct_stats',
				'Structurereferente.code_stats',
			);
			return $fields;
		}

		private function _queryJoins () {
			$Foyer = ClassRegistry::init( 'Foyer' );

			$joins = array(
				array(
					'table' => 'orientsstructs',
					'alias' => 'Orientstruct',
					'type' => 'LEFT',
					'conditions' => array(
						'Personne.id = Orientstruct.personne_id',
						'Personne.id = (SELECT personne_id FROM orientsstructs
						WHERE personne_id = "Personne"."id"
						ORDER BY date_valid DESC LIMIT 1)'
					),
				),
				array(
					'table' => 'typesorients',
					'alias' => 'Typeorient',
					'type' => 'LEFT',
					'conditions' => array(
						'Typeorient.id = Orientstruct.typeorient_id'
					)
				),
				array(
					'table' => 'structuresreferentes',
					'alias' => 'Structurereferente',
					'type' => 'LEFT',
					'conditions' => array(
						'Structurereferente.id = Orientstruct.structurereferente_id'
					)
				),
			);
			return $joins;
		}

		private function _queryHistoriqueDroitFields () {

			$fields = array (
				'Historiquedroit12.etatdosrsa',
				'Historiquedroit12.toppersdrodevorsa',
			);
			//Fields By Months
			for($month=0; $month<12; $month++) {
				$fields = array_merge(
					$fields,
					array(
						'Historiquedroit'.$month.'.etatdosrsa',
						'Historiquedroit'.$month.'.toppersdrodevorsa',
					)
				);
			}
			return $fields;
		}

		private function _queryHistoriqueDroitJoins ($annee, $jourFinMois) {
			$tmpDateRecherchePrevious = $annee.'-01-'.$jourFinMois;

			$joins = array(
				array(
					'table' => 'historiquesdroits',
					'alias' => 'Historiquedroit12',
					'type' => 'LEFT',
					'conditions' => array(
						'Personne.id = Historiquedroit12.personne_id',
						'Personne.id = (SELECT personne_id FROM historiquesdroits
							WHERE personne_id = "Personne"."id"
							AND	(\''.$tmpDateRecherchePrevious.'\' BETWEEN date_trunc(\'day\', created )
							AND  date_trunc(\'day\', modified ))
							ORDER BY created DESC LIMIT 1
						)'
					),
				)
			);
			for($month=0; $month<12; $month++) {
				$tmpDateRecherche = $this->_getDateString( $annee, $month, $jourFinMois, 2 );
				$joins = array_merge(
					$joins,
					array(
						array(
							'table' => 'historiquesdroits',
							'alias' => 'Historiquedroit'.$month,
							'type' => 'LEFT',
							'conditions' => array(
								'Personne.id = Historiquedroit'.$month.'.personne_id',
								'Personne.id = (SELECT personne_id FROM historiquesdroits
									WHERE personne_id = "Personne"."id"
									AND (\''.$tmpDateRecherche.'\' BETWEEN date_trunc(\'day\', created )
									AND  date_trunc(\'day\', modified ))
									ORDER BY created DESC LIMIT 1
								)'
							),
						)
					)
				);
			}
			return $joins;
		}

		private function _queryConditionEtatdos($etatSuspendus,$annee, $jourFinMois){
			$tmpDateRecherchePrevious = $annee.'-01-'.$jourFinMois;
			$tmpDateRechercheNext = ($annee+1).'-01-'.$jourFinMois;

			if ( is_array ( $etatSuspendus )){
				$etats = "'";
				$etats .= implode( "','",$etatSuspendus);
				$etats .= "'";
			}else{
				$etats = "'".$etatSuspendus."'";
			}
			$conditions = array (
				'Personne.id IN ( SELECT personne_id FROM historiquesdroits
				WHERE historiquesdroits.etatdosrsa IN ( '.$etats.' )
				AND date_trunc(\'day\', historiquesdroits.created ) < \''.$tmpDateRechercheNext.'\'
				AND date_trunc(\'day\', historiquesdroits.modified ) > \''.$tmpDateRecherchePrevious.'\'
				)'
			);
			return $conditions;
		}

		########################################################################################################################
		########################################################################################################################

		/**
		 * Retourn les résultats de la partie Tableau de bord – Instructon RSA (de l’instructon de la demande à un droit Rsa)
		 *
		 * @param array $search
		 * @return array
		 */
		public function getIndicateursTableauA1( array $search ) {
			$results = array();

			// Query de base
			$query = $this->_getQueryTableau_a1 ($search);
			$results = $this->Personne->query($query);

			// Initialisation tableau de résultats
			$resultats = $this->_initializeTableauA1();

			// Traitement des résultats
			foreach($results as $result) {
				$mois = $result[0]['mois']-1;
				$resultats['totalDOV'][$mois]++;
				if( $result['historiquedroit']['toppersdrodevorsa'] == 1 ) {
					$resultats['totalSDDDOV'][$mois]++;
					if($result[0]['primo'] == true) {
						$resultats['nbPersonnesInconnues'][$mois]++;
						$resultats['nbPersonnes'][$mois]++;
					} else if( $result[0]['nouvel_entrant'] == true ) {
						$resultats['nbPersonnesConnues'][$mois]++;
						$resultats['nbPersonnes'][$mois]++;
					}
				}
			}

			// Calcul des cumuls
			foreach($resultats as $key => $resultat) {
				$resultats[$key][] = array_sum($resultats[$key]);
			}

			return $resultats;
		}

		########################################################################################################################
		########################################################################################################################

		/**
		 * ...
		 *
		 *
		 *
		 * @param array $search
		 * @return array
		 */
		public function getIndicateursTableauA2( array $search ) {
			$Historiquedroit = ClassRegistry::init( 'Historiquedroit' );
			$annee = Hash::get( $search, 'Search.annee' );
			$results = array();
			$arrayPersonneID = array();
			$testOrient = $this->_getTypeOrientation();
			// Query de base
			$query = $this->_getQueryTableau_a2 ($search, $annee);
			$results = $Historiquedroit->find('all', $query);

			// Initialisation tableau de résultats
			$resultats = $this->_initializeTableauA2();

			// Traitement des résultats
			foreach($results as $result) {
				$arrayPersonneID[]=$result[0]['idPersonne'];
				$yearStartHistorique = intval( date('Y', strtotime($result['Historiquedroit']['created']) ) );
				$yearEndHistorique = intval( date('Y', strtotime($result['Historiquedroit']['modified']) ) );
				if ($yearStartHistorique < $annee ){
					$monthStartHistorique = 0;
				}else{
					$monthStartHistorique = intval( date('n', strtotime($result['Historiquedroit']['created']) ) ) -1;
				}
				if ($yearEndHistorique > $annee ){
					$monthEndHistorique = 11;
				}else{
					$monthEndHistorique = intval( date('n', strtotime($result['Historiquedroit']['modified']) ) ) -1;
				}
				for ($month = $monthStartHistorique; $month <= $monthEndHistorique; $month ++ ) {
					$flagOrienteCD = $flagOrientePE = $flagOrienteSocial = $flagOrientePrepro = $flagOrienteCDCER = FALSE;
					// Nombre de personnes Soumises à droits et devoirs (Pers. SDD) avec un droit ouvert par mois
					$resultats['SSD'][$month] ++;
					// Si on a une date d'orientation valide
					if ( $result['Orientstruct']['date_valid'] != null){
						// Récupération de l'année d'orientation
						$yearOrient = intval( date('Y', strtotime($result['Orientstruct']['date_valid']) ) );
						// Si l'année n'est pas celle de la recherche alors on change le mois
						if ($yearOrient < $annee ){
							$monthOrient = 0;
						}else{
							$monthOrient = intval( date('n', strtotime($result['Orientstruct']['date_valid']) ) ) -1;
						}
						// Si le mois d'orientation correspond au mois étudié dans la boucle
						if ($monthOrient <= $month ){
							// Nombre de Pers. SDD orientées avec un droit ouvert par mois
							$resultats['Orientes']['total'][$month] ++;

							if(!empty($testOrient['SOCIAL']) && in_array($result['Typeorient']['id'], $testOrient['SOCIAL'] ) ) {
								$resultats['Orientes']['Social'][$month]++;
								$flagOrienteSocial = true;
							} elseif(!empty($testOrient['EMPLOI']) &&  in_array( $result['Typeorient']['id'], $testOrient['EMPLOI'] ) ) {
								$resultats['Orientes']['Emploi'][$month]++;
							} elseif (!empty($testOrient['PREPRO']) && in_array( $result['Typeorient']['id'], $testOrient['PREPRO'] ) ) {
								$resultats['Orientes']['Prepro'][$month]++;
								$flagOrientePrepro = true;
							}

							if( $result['Structurereferente']['type_struct_stats'] == 'oa' ) {
								$resultats['Orientes']['OA'][$month]++;
							}
							if( $result['Structurereferente']['type_struct_stats'] == 'pe' ) {
								$resultats['Orientes']['PE'][$month]++;
								$flagOrientePE = true;
							}
							if( $result['Structurereferente']['type_struct_stats'] == 'cd' ) {
								$resultats['Orientes']['CD'][$month]++;
								$flagOrienteCD = true;
							}
						}
					}
					// Si la personne a un contrat d'insertion
					if ( $result['Contratinsertion']['datevalidation_ci'] != null) {
						$yearCER = intval( date('Y', strtotime($result['Contratinsertion']['datevalidation_ci']) ) );
						if ($yearCER < $annee ){
							$monthCER = 0;
						}else{
							$monthCER = intval( date('n', strtotime($result['Contratinsertion']['datevalidation_ci']) ) ) -1;
						}
						if ($monthCER <= $month ){
							// Avec contrats dont
							$resultats['Contrat']['total'][$month] ++;
							if ( $flagOrienteCD ) {
								$resultats['Contrat']['CDCER'][$month] ++;
								$flagOrienteCDCER = true;
							}
							if ( $flagOrientePE ) {
								// Detection d'un contrat aidé
								if ( in_array( $result['Structurereferente']['code_stats'], Configure::read( 'Statistiqueplanpauvrete.code_stats') ) ){
									$resultats['Contrat']['PEAccomp'][$month] ++;
								}
							}
						}
					}elseif ($result['Cui']['faitle']  != null) {
						$yearCUI= intval( date('Y', strtotime($result['Cui']['faitle']) ) );
						if ($yearCUI < $annee ){
							$monthCUI = 0;
						}else{
							$monthCUI = intval( date('n', strtotime($result['Cui']['faitle']) ) ) -1;
						}
						if ($monthCUI <= $month ){
							// Avec contrats dont
							$resultats['Contrat']['total'][$month] ++;
							if ( $flagOrientePE ) {
								// Detection d'un CUI
								$resultats['Contrat']['PEAider'][$month] ++;
							}
						}
					}

					if ( $flagOrienteCDCER ){
						$resultats['CDCER']['total'][$month]++;
						if( $flagOrienteSocial ) {
							$resultats['CDCER']['Social'][$month] ++;
						}
						if( $flagOrientePrepro ) {
							$resultats['CDCER']['Prepro'][$month] ++;
						}
					}
				}
			}
			$resultats = $this->_calculPercentTableauA2($resultats, $arrayPersonneID, $annee);

			// Calcul des cumuls
			foreach($resultats as $key => $resultat) {
				if( in_array($key, array('Orientes', 'Contrat', 'CDCER' ))) {
					foreach($resultat as $key2 => $osef) {
						if(strpos($key2, 'percent') === false) {
							$resultats[$key][$key2][] = array_sum($resultats[$key][$key2]);
						}
					}
				} else {
					$resultats[$key][] = array_sum($resultats[$key]);
				}
			}

			$resultats = $this->_adaptLignesTableaux($resultats, 'tableauA2');
			return $resultats;
		}

		########################################################################################################################
		########################################################################################################################

		/**
		 * Tableau B1 : Tableau de suivi de la première orientation
		 * @param array $search
		 * @return array
		*/
		public function getIndicateursTableauB1( array $search ) {
			$results = array();
			$annee = (int)Hash::get( $search, 'Search.annee' );
			$testOrient = $this->_getTypeOrientation();

			// Récupération des variables de configuration
			$statutRdv = Configure::read('Statistiqueplanpauvrete.orientationRdv');
			// Query de base
			$query = $this->_getQueryTableau_b1($search, $statutRdv['prevu']);

			$results = $this->Historiquedroit->query($query);

			$resultats = array();

			// Initialisation tableau de résultats
			$resultats = $this->_initializeTableauB1();
			$arrayIdPersonne = array();
			for($i = 0; $i <12; $i++) {
				$arrayIdPersonne[$i] = array();
			}

			// Traitement des résultats
			foreach($results as $result) {
				$isThisYear = false;
				if($result[0]['annee'] == $annee) {
					$month = $result[0]['mois'] -1;
					if( ($result[0]['primo'] == true || $result[0]['nouvel_entrant'] == true) &&
						in_array($result[0]['idpersonne'], $arrayIdPersonne[$month]) == false ) {
						$arrayIdPersonne[$month][] = $result[0]['idpersonne'];
						$resultats['total'][$month]++;
					}
					$isThisYear = true;
				}

				// Orientés
				if( !is_null($result['orientstruct']['date_valid']) && $result['orientstruct']['rgorient'] == 1 ) {
					$moisOrient = (int)date('n', strtotime($result['orientstruct']['date_valid'])) -1;
					$resultats['Orientes']['total'][$moisOrient]++;
					// Type d'orientations
					if( $result['structurereferente']['type_struct_stats'] == 'oa' ) {
						$resultats['Orientes']['oa'][$moisOrient]++;
					}
					if( $result['structurereferente']['type_struct_stats'] == 'pe' ) {
						$resultats['Orientes']['pe'][$moisOrient]++;
					}
					if( $result['structurereferente']['type_struct_stats'] == 'cd' ) {
						$resultats['Orientes']['cd'][$moisOrient]++;
					}

					if(!empty($testOrient['SOCIAL']) && in_array($result['typeorient']['id'], $testOrient['SOCIAL'] ) ) {
						$resultats['Orientes']['social'][$moisOrient]++;
					} elseif(!empty($testOrient['EMPLOI']) &&  in_array( $result['typeorient']['id'], $testOrient['EMPLOI'] ) ) {
						$resultats['Orientes']['emploi'][$moisOrient]++;
					} elseif (!empty($testOrient['PREPRO']) && in_array( $result['typeorient']['id'], $testOrient['PREPRO'] ) ) {
						$resultats['Orientes']['prepro'][$moisOrient]++;
					}

					// Délais
					$dateCreaHisto = new DateTime($result['historiquedroit']['created']);
					$dateOrient = new DateTime($result['orientstruct']['date_valid']);
					$delai = $dateCreaHisto->diff($dateOrient)->days;

					if($delai <= 31) {
						$resultats['orient_31jours'][$month]++;
					}
					$resultats['delai_moyen'][$month] += $delai;
					$delaiMois = $dateCreaHisto->diff($dateOrient)->m;
					foreach($resultats['delai']as $key => $osef) {
						$moisDelais = explode('_', $key);
						if( $delaiMois >= intval($moisDelais[0]) && $delaiMois < intval($moisDelais[1]) ) {
							$resultats['delai'][$key][$month] ++;
						}
					}
				}
				 // Non orientés
				elseif($isThisYear && is_null($result['orientstruct']['date_valid'])) {
					$resultats['NonOrientes']['total'][$month]++;
					if( !is_null($result[0]['statutrdv_id']) && in_array($result[0]['statutrdv_id'], $statutRdv['prevu']) ) {
						$resultats['NonOrientes']['prevu'][$month]++;
					} elseif( !is_null($result[0]['proposition']) && $result[0]['proposition'] ==  'audition' ) {
						$resultats['NonOrientes']['bilan'][$month]++;
					} else {
						$resultats['NonOrientes']['autres'][$month]++;
					}
				}
			}

			// Calcul des moyennes
			for( $i=0; $i<12; $i++){
				if($resultats['Orientes']['total'][$i] != 0) {
					$resultats['delai_moyen'][$i] = intval($resultats['delai_moyen'][$i] / $resultats['Orientes']['total'][$i]);}
				if($resultats['total'][$i] != 0) {
					$resultats['taux_orient'][$i] = round( (100 * $resultats['Orientes']['total'][$i] ) / $resultats['total'][$i], 2) . '%';}
			}

			// Calcul des cumuls
			foreach($resultats as $key => $resultat) {
				if( in_array($key, array('Orientes', 'NonOrientes', 'delai' ))) {
					foreach($resultat as $key2 => $osef) {
						$resultats[$key][$key2][] = array_sum($resultats[$key][$key2]);
					}
				} elseif($key != 'taux_orient' && strpos($key, 'delai') === false) {
					$resultats[$key][] = array_sum($resultats[$key]);
				}
			}

			$resultats = $this->_adaptLignesTableaux($resultats, 'tableauB1');
			return $resultats;
		}

		########################################################################################################################
		########################################################################################################################

		/**
		 * ...
		 *
		 *
		 *
		 * @param array $search
		 * @return array
		 */
		public function getIndicateursTableauB4( array $search ) {
			$results = array();

			$testOrient = $this->_getTypeOrientation();

			// Récupération des variables de configuration
			$statutRdv = Configure::read('Statistiqueplanpauvrete.orientationRdv');

			// Query de base
			$query = $this->_getQueryTableau_b4 ($search);

			$results = $this->Personne->query($query);

			// Initialisation tableau de résultats
			$resultats = $this->_initializeTableauB4();

			// Traitement des résultats
			foreach($results as $result) {
				$month = intval( date('n', strtotime($result['orientstruct']['date_valid']) ) ) -1;
				$resultats['total'][$month]++;

				if(in_array($result['typeorient']['id'], $testOrient['SOCIAL'])) {
					$orientation = 'Social';
				} elseif(in_array($result['typeorient']['id'], $testOrient['EMPLOI'])) {
					$orientation = 'Pro';
				} else {
					$orientation = 'Prepro';
				}

				if( in_array($result[0]['statutrdv_id'], $statutRdv['prevu'] ) === false ) {
					$resultats[$orientation]['total'][$month]++;
					if( in_array($result[0]['statutrdv_id'], $statutRdv['venu'] ) !== false ) {
						$resultats[$orientation]['venu'][$month]++;
					}

					if( in_array($result[0]['statutrdv_id'], $statutRdv['excuses_recevables'] ) !== false ) {
						$resultats[$orientation]['excuse_recevable'][$month]++;
					}

					if( in_array($result[0]['statutrdv_id'], $statutRdv['excuses_non_recevables'] ) !== false ) {
						$resultats[$orientation]['sans_excuse'][$month]++;
					}
				}

				$dateOrient = new DateTime($result['orientstruct']['date_valid']);
				$dateRdv = new DateTime($result[0]['daterdv']);
				$delai = $dateOrient->diff($dateRdv)->days;

				$dateRdvCreated = new DateTime($result[0]['created']);
				$delaiRdvCreated = $dateOrient->diff($dateRdvCreated)->days;

				if($delaiRdvCreated <= 15) {
					$resultats[$orientation]['delai15jrs'][$month]++;
				}

				$resultats[$orientation]['delai_moyen'][$month] += $delai;
				$delaiMonth = $dateOrient->diff($dateRdv)->m;

				foreach($resultats[$orientation]['delai']as $key => $osef) {
					$joursDelais = explode('_', $key);
					if( $delaiMonth >= intval($joursDelais[0]) && $delaiMonth < intval($joursDelais[1]) ) {
						$resultats[$orientation]['delai'][$key][$month] ++;
					}
				}
			}

			// Calcul des moyennes
			for( $i=0; $i<12; $i++){
				if($resultats['Social']['total'][$i] != 0) {
					$resultats['Social']['delai_moyen'][$i] = intval($resultats['Social']['delai_moyen'][$i] / $resultats['Social']['total'][$i]);
					$resultats['Social']['taux_presence'][$i] = round( (100 * $resultats['Social']['venu'][$i] ) / $resultats['Social']['total'][$i], 2)  . '%';
				}
				if($resultats['Prepro']['total'][$i] != 0) {
					$resultats['Prepro']['delai_moyen'][$i] = intval($resultats['Prepro']['delai_moyen'][$i] / $resultats['Prepro']['total'][$i]);
					$resultats['Prepro']['taux_presence'][$i] = round( (100 * $resultats['Prepro']['venu'][$i] ) / $resultats['Prepro']['total'][$i], 2) . '%';
				}
				if($resultats['Pro']['total'][$i] != 0) {
					$resultats['Pro']['delai_moyen'][$i] = intval($resultats['Pro']['delai_moyen'][$i] / $resultats['Pro']['total'][$i]);
					$resultats['Pro']['taux_presence'][$i] = round( (100 * $resultats['Pro']['venu'][$i] ) / $resultats['Pro']['total'][$i], 2) . '%';
				}
			}

			// Calcul des cumuls
			foreach($resultats as $key => $resultat) {
				if($key == 'total') {
					$resultats[$key][] = array_sum($resultats[$key]);
				} else {
					foreach($resultat as $key2 => $result) {
						if($key2 == 'delai') {
							foreach($result as $key3 => $delai) {
								$resultats[$key][$key2][$key3][] = array_sum($resultats[$key][$key2][$key3]);
							}
						}elseif($key2 != 'taux_presence') {
							$resultats[$key][$key2][] = array_sum($resultats[$key][$key2]);
						}
					}
				}
			}

			$resultats = $this->_adaptLignesTableaux($resultats, 'tableauB4');
			return $resultats;
		}

		/**
		 * Retourne le résultat pour le tableau B5 : Suivi des CER
		 * @param array $search
		 * @return array
		 */
		public function getIndicateursTableauB5( array $search ) {
			$annee = Hash::get( $search, 'Search.annee' );
			$results = array();

			$testOrient = $this->_getTypeOrientation();

			// Query de base
			$query = $this->_getQueryTableau_b5 ($search);
			$results = $this->Personne->query($query);

			// Initialisation du tableau de résultats
			$resultats = $this->_initializeTableauB5();

			// Traitement des résultats
			foreach($results as $result) {
				$monthOrient = intval( date('n', strtotime($result['orientstruct']['date_valid']) ) ) -1;
				$resultats['orient_valid'][$monthOrient]++;

				$dateOrient = new DateTime($result['orientstruct']['date_valid']);
				$dateCer = new DateTime($result['contratinsertion']['datevalidation_ci']);
				$delai = $dateOrient->diff($dateCer)->days;
				if( $delai > 0 && $result['contratinsertion']['rg_ci'] == 1) {
					$resultats['delai_moyen'][$monthOrient] += $delai;

					if( $delai < 15 ) {
						$resultats['signe15jrs'][$monthOrient]++;
					}

					if( !empty($testOrient['SOCIAL']) && in_array($result['typeorient']['id'], $testOrient['SOCIAL'] ) ) {
						$resultats['cer_social'][$monthOrient] ++;
						$resultats['delai_social'][$monthOrient] += $delai;
					} else if( !empty($testOrient['PREPRO']) && in_array($result['typeorient']['id'], $testOrient['PREPRO'] )  ) {
						$resultats['cer_prepro'][$monthOrient] ++;
						$resultats['delai_prepro'][$monthOrient] += $delai;
					}
					$delaiMonth = $dateOrient->diff($dateCer)->m;
					if($delaiMonth <= 2) {
						$resultats['signe2mois'][$monthOrient]++;
					}
					foreach($resultats['delai']as $key => $osef) {
						$joursDelais = explode('_', $key);
						if( $delaiMonth >= intval($joursDelais[0]) && $delaiMonth < intval($joursDelais[1]) ) {
							$resultats['delai'][$key][$monthOrient] ++;
						}
					}
				}
			}

			// Calcul des moyennes
			for( $i=0; $i<12; $i++){
				if ( ( $resultats['cer_social'][$i] + $resultats['cer_prepro'][$i]) != 0){
					$resultats['delai_moyen'][$i] = intval($resultats['delai_moyen'][$i] / ( $resultats['cer_social'][$i] + $resultats['cer_prepro'][$i]) );}
				if ($resultats['cer_social'][$i] != 0 ) {
					$resultats['delai_social'][$i] = intval($resultats['delai_social'][$i] / $resultats['cer_social'][$i] );}
				if ($resultats['cer_prepro'][$i] != 0 ) {
					$resultats['delai_prepro'][$i] = intval($resultats['delai_prepro'][$i] / $resultats['cer_prepro'][$i] );}
				if ($resultats['orient_valid'][$i] != 0 ) {
					$resultats['taux_contrat'][$i] = round( (100 * ( $resultats['cer_social'][$i] + $resultats['cer_prepro'][$i] )  / $resultats['orient_valid'][$i]) , 2) . '%';}
			}

			// Calcul des cumuls
			foreach($resultats as $key => $resultat) {
				if($key == 'delai') {
					foreach($resultat as $key2 => $delai) {
						$resultats[$key][$key2][] = array_sum($resultats[$key][$key2]);
					}
				}elseif($key != 'taux_contrat' && strpos($key, 'delai') === false) {
					$resultats[$key][] = array_sum($resultats[$key]);
				}
			}

			$resultats = $this->_adaptLignesTableaux($resultats, 'tableauB5');
			return $resultats;
		}

		########################################################################################################################
		########################################################################################################################

		/**
		 * Retourne les résultats de la partie Tableau de bord – Instructon RSA (de l’instructon de la demande à un droit Rsa)
		 *
		 * @param array $search
		 * @return array
		 */
		public function getIndicateursTableauA1V2( array $search ) {
			$useHistoriquedroit = (boolean)Configure::read( 'Statistiqueplanpauvrete.useHistoriquedroit' );

			$Personne = ClassRegistry::init( 'Personne' );
			$Historiquedroit = ClassRegistry::init( 'Historiquedroit' );

			$annee = Hash::get( $search, 'Search.annee' );
			$testOrient = $this->_getTypeOrientation();

			// Query de base
			$query = $this->_getQueryTableau_a1v2 ($search, $annee);
			$results = $Personne->find('all', $query);

			// Récupère les résultats suivant l'historique des personnes passées en paramètre
			if( $useHistoriquedroit ) {
				$arrayIds = array( );
				foreach($results as $result) {
					$arrayIds[] = $result["Personne"]["id"];
				}
				if ( ! empty ($arrayIds) ){
					$query = $this->_getQueryHistoriques ($annee, $arrayIds);
					$resultsHistoriques = $Historiquedroit->query($query);
				}else{
					$resultsHistoriques = null;
				}
			}

			// Initialisation tableau de résultats
			$resultats = $this->_initializeTableauA1v2();
			$etatSuspendus = Configure::read( 'Statistiqueplanpauvrete.etatSuspendus' );

			// Traitement des résultats
			foreach($results as $key => $result) {
				if ( $useHistoriquedroit ){
					// Récupère l'historique du mois précédent selon la personne
					$historiqueKey = null;
					foreach ($resultsHistoriques as $historiqueKey => $resultHistorique) {
						if ( $resultHistorique[0]['personne_id'] == $result['Personne']['id'] ){
							$historiquesPreviousMonth = $resultsHistoriques[$historiqueKey][0]['etatdosrsa12'];
							$historiquesToppersPreviousMonth = $resultsHistoriques[$historiqueKey][0]['toppersdrodevorsa12'];
							break;
						}
					}

					for( $month=0; $month<12; $month++ ) {
						if ( ! is_null($historiqueKey) ){
							$historiquesMonth = $resultsHistoriques[$historiqueKey][0]['etatdosrsa'.$month];
							$historiquesToppersMonth = $resultsHistoriques[$historiqueKey][0]['toppersdrodevorsa'.$month];
						}else{
							$historiquesMonth = $historiquesToppersMonth = null;
						}

						// Si la personne est un nouvel entrant suspendu ou pas alors
						if ( (
							 in_array ( $historiquesMonth, $etatSuspendus)
							&&  $historiquesToppersMonth == 1
						) && (
							in_array ($historiquesPreviousMonth, $etatSuspendus)
							|| $historiquesToppersPreviousMonth != 1
						)) {
							$departement = Configure::read('Cg.departement');
							$jourDebMois = $this->jourDeDebut($annee);
							$tmpDate = $this->_getDateString( $annee, $month, $jourDebMois, 2 );

							// Nombre de personnes entrantes en SDD ce mois ci
							$resultats['Tous']['total'][$month] ++;

							// On vérifie les Hors suspendus
							if ((
								$historiquesMonth == 2
								&& $historiquesToppersMonth == 1
							)&& (
								 $historiquesPreviousMonth != 2
								|| $historiquesToppersPreviousMonth != 1
							)) {
								$keySuspendu = 'Horssuspendus';
							} else {
								$keySuspendu = 'Suspendus';
							}
							$resultats[$keySuspendu]['total'][$month]++;

							// dont BRSA rejoignant un foyer RSA
							if (
								!is_null($result['Dossiercaf']['ddratdos'])
								&& $result['Dossiercaf']['ddratdos'] >=  $result['Dossier']['dtdemrsa']
								&& (date('m',strtotime($result['Dossiercaf']['ddratdos']))-1) == $month
							){
								$resultats['Tous']['nbFoyerJoin'][$month]++;
								$resultats[$keySuspendu]['nbFoyerJoin'][$month]++;
							}elseif (
								!is_null($result['Adresse']['codepos'])
								&& strpos($result['Adresse']['codepos'], $departement) === false
								&&  (
									(date('m',strtotime($result['AdressefoyerAct']['dtemm'])-1) == $month)
									|| (date('m',strtotime($result['AdressefoyerAct']['dtemm'])-1) == ($month-1))
								)
							) {
								//- dont BRSA venant de s’installer sur le Dpt (mutation)
									$resultats['Tous']['nbEMM'][$month]++;
									$resultats[$keySuspendu]['nbEMM'][$month]++;
							} elseif ( $historiquesPreviousMonth == null ){
							/*
							 * - dont Personne SDD ayant effectué une demande pour la 1ʳᵉ fois (primo arrivants)
							 * sont considère primo arrivants les personnes sans historique droit à l'exclusion des personnes
							 *  - se rattachant à un foyer dont la demande est déjà ouverte
							 *  - venant de s’installer sur le Dpt
							*/
								$resultats['Tous']['nbFoyerInconnu'][$month]++;
								$resultats[$keySuspendu]['nbFoyerInconnu'][$month]++;
							}
							//- dont PSDD ayant déjà eu des droits ouverts par le passé (suspendus et non orientés ou radiés)
							if (
								//radiés
								$historiquesPreviousMonth == 5 || $historiquesPreviousMonth == 6
								//suspendus et non orientés
								|| (
									($historiquesPreviousMonth == 3 || $historiquesPreviousMonth == 4)
									&& $result['Orientstruct']['id'] != null
								)
							){
								$resultats['Tous']['nbFoyerRadiSusp'][$month]++;
								$resultats[$keySuspendu]['nbFoyerRadiSusp'][$month]++;
							}
							//- dont BRSA non-soumis aux droits et devoirs qui le sont désormais
							if (
								$historiquesToppersPreviousMonth == 0 &&
								in_array ( $historiquesPreviousMonth, $etatSuspendus)
							) {
								$resultats['Tous']['nbToppers'][$month]++;
								$resultats[$keySuspendu]['nbToppers'][$month]++;
							}

							// Si la personne est un nouvel entrant et qu'on a une date d'orientation valide
							if ( $result['Orientstruct']['date_valid'] != null ){

								// Si l'orientation n'est pas inférieur au changement de droits
								if (strtotime($result['Orientstruct']['date_valid']) >= strtotime($tmpDate) ){
									// On calcule la date du changement de droits +1 mois
									$date1mois = strtotime($tmpDate.' + 1 month');

									// Calcul du mois de l'orientation
									$tmpmonth = date('m', strtotime($result['Orientstruct']['date_valid'])) -1 ;

									// Nombre d'orientées en moins d'un mois
									if ( $date1mois > strtotime($result['Orientstruct']['date_valid']) ) {
										//- Nombre de nouveaux entrants orientés en moins d’un mois
										$resultats['Tous']['Orientes']['total'][$tmpmonth] ++;
										$resultats[$keySuspendu]['Orientes']['total'][$tmpmonth]++;

										//	- dont nbre de pers. SDD orientées Social / Emploi / Prépro
										if(!empty($testOrient['SOCIAL']) && in_array($result['Typeorient']['id'], $testOrient['SOCIAL'] ) ) {
											$resultats['Tous']['Orientes']['Social'][$tmpmonth]++;
											$resultats[$keySuspendu]['Orientes']['Social'][$tmpmonth]++;
										}elseif(!empty($testOrient['EMPLOI']) &&  in_array( $result['Typeorient']['id'], $testOrient['EMPLOI'] ) ) {
											$resultats['Tous']['Orientes']['Emploi'][$tmpmonth]++;
											$resultats[$keySuspendu]['Orientes']['Emploi'][$tmpmonth]++;
										}elseif (!empty($testOrient['PREPRO']) && in_array( $result['Typeorient']['id'], $testOrient['PREPRO'] ) ) {
											$resultats['Tous']['Orientes']['Prepro'][$tmpmonth]++;
											$resultats[$keySuspendu]['Orientes']['Prepro'][$tmpmonth]++;
										}

										//	- dont nbre de pers. SDD orientées OA / PE / CD
										if( $result['Structurereferente']['type_struct_stats'] == 'oa' ) {
											$resultats['Tous']['Orientes']['OA'][$tmpmonth]++;
											$resultats[$keySuspendu]['Orientes']['OA'][$tmpmonth]++;
										}elseif( $result['Structurereferente']['type_struct_stats'] == 'pe' ) {
											$resultats['Tous']['Orientes']['PE'][$tmpmonth]++;
											$resultats[$keySuspendu]['Orientes']['PE'][$tmpmonth]++;
										}elseif( $result['Structurereferente']['type_struct_stats'] == 'cd' ) {
											$resultats['Tous']['Orientes']['CD'][$tmpmonth]++;
											$resultats[$keySuspendu]['Orientes']['PE'][$tmpmonth]++;
										}
									}
								}
							}
							// Si le dernier rendez-vous de la personne est un rendez-vous non venu.
							if ( $result['Rendezvous']['daterdv'] != null ){
								// Si le rendez-vous n'est pas inférieur au changement de droits
								if (strtotime($result['Rendezvous']['daterdv']) >= strtotime($tmpDate) ){
									$resultats['Tous']['Nonvenu']['RDV'][$month] ++;
									$resultats[$keySuspendu]['Nonvenu']['RDV'][$month]++;
								}
							}
						}
						$historiquesPreviousMonth = $historiquesMonth;
						$historiquesToppersPreviousMonth = $historiquesToppersMonth;
					}
				}
				unset($resultsHistoriques[$historiqueKey]);
				unset($results[$key]);
			}

			$resultats = $this->_calculPercentTableauA1v2($resultats);
			$resultats = $this->_adaptLignesTableaux($resultats, 'tableauA1v2');
			return $resultats;
		}

		########################################################################################################################
		########################################################################################################################

		/**
		 * ...
		 *
		 *
		 *
		 * @param array $search
		 * @return array
		 */
		public function getIndicateursTableauA2AV2( array $search ) {
			$useHistoriquedroit = (boolean)Configure::read( 'Statistiqueplanpauvrete.useHistoriquedroit' );

			$Personne = ClassRegistry::init( 'Personne' );
			$Historiquedroit = ClassRegistry::init( 'Historiquedroit' );

			$annee = Hash::get( $search, 'Search.annee' );
			$testOrient = $this->_getTypeOrientation();
			$results = array();

			// Query de base
			$query = $this->_getQueryTableau_a2av2 ($search, $annee);
			$results = $Personne->find('all', $query);

			if( $useHistoriquedroit ) {
				$arrayIds = array( );
				foreach($results as $result) {
					$arrayIds[] = $result["Personne"]["id"];
				}
				if ( ! empty ($arrayIds) ){
					$query = $this->_getQueryHistoriques ($annee, $arrayIds);
					$resultsHistoriques = $Historiquedroit->query($query);
				}else{
					$resultsHistoriques = null;
				}
			}

			// Initialisation tableau de résultats
			$resultats = $this->_initializeTableauA2av2();

			// Initialisation des valeurs fixes
			$jourDebMois = $this->jourDeDebut ($annee);

			// Traitement des résultats
			foreach($results as $key => $result) {
				$etatSuspendus = Configure::read( 'Statistiqueplanpauvrete.etatSuspendus' );

				if ( $useHistoriquedroit ){
					$historiqueKey = null;
					foreach ($resultsHistoriques as $historiqueKey => $resultHistorique) {
						if ( $resultHistorique[0]['personne_id'] == $result['Personne']['id'] ){
							$historiquesPreviousMonth = $resultsHistoriques[$historiqueKey][0]['etatdosrsa12'];
							$historiquesToppersPreviousMonth = $resultsHistoriques[$historiqueKey][0]['toppersdrodevorsa12'];
							break;
						}
					}

					$flagOrienteCD = false;
					$flagOrientePrepro = false;
					$flagOrienteSocial = false;
					// Initialisation des valeurs
					if( $result['Structurereferente']['type_struct_stats'] == 'cd' ) {
						$flagOrienteCD = true;
						// Pers. orientées Social
						if(!empty($testOrient['SOCIAL']) && in_array($result['Typeorient']['id'], $testOrient['SOCIAL'] ) ) {
								$flagOrienteSocial = true;
						}
						// Pers. orientées Pré pro
						elseif (!empty($testOrient['PREPRO']) && in_array( $result['Typeorient']['id'], $testOrient['PREPRO'] ) ) {
								$flagOrientePrepro = true;
						}
					}

					for( $month=0; $month<12; $month++ ) {
						if ( ! is_null($historiqueKey) ){
							$historiquesMonth = $resultsHistoriques[$historiqueKey][0]['etatdosrsa'.$month];
							$historiquesToppersMonth = $resultsHistoriques[$historiqueKey][0]['toppersdrodevorsa'.$month];
						}else{
							$historiquesMonth = $historiquesToppersMonth = null;
						}
						// Si la personne est un nouvel entrant suspendu ou pas
						if ( (
							 in_array ( $historiquesMonth, $etatSuspendus)
							&& $historiquesToppersMonth == 1
						) && (
							 in_array ($historiquesPreviousMonth, $etatSuspendus)
							|| $historiquesToppersPreviousMonth != 1
						)) {
							// On a une date d'orientation valide
							if ( $result['Orientstruct']['date_valid'] != null){
								$tmpDate = $this->_getDateString( $annee, $month, $jourDebMois, 2 );
								if (// Si l'orientation n'est pas inférieur au changement de droits
									strtotime($result['Orientstruct']['date_valid']) >= strtotime($tmpDate)
									// Nombre de nouveaux entrants orientés orientées CD
									&& $flagOrienteCD
								){
									// Calcul du mois de l'orientation
									$tmpmonth = date('m', strtotime($result['Orientstruct']['date_valid'])) -1 ;

									$resultats['Tous']['Orientes_CD'][$tmpmonth]++;
									// On vérifie les Hors suspendus
									if (
										($historiquesMonth == 2
										&& $historiquesToppersMonth == 1)
										&& ( $historiquesPreviousMonth != 2
										|| $historiquesToppersPreviousMonth != 1 )
									) {
										$keySuspendu = 'Horssuspendus';
									} else {
										$keySuspendu = 'Suspendus';
									}
									$resultats[$keySuspendu]['Orientes_CD'][$tmpmonth]++;

									//- Nombre de nouveaux entrants orientés CD avec un 1er rendez-vous fixé suite à une orientation CD
									if ( $result['Rendezvous']['daterdv'] != null
										&& strtotime($result['Rendezvous']['daterdv']) >= strtotime($tmpDate)
									){
										$tmpmonthrdv = date('m', strtotime($result['Rendezvous']['daterdv'])) -1 ;
										$resultats['Tous']['Orientes']['RDV'][$tmpmonthrdv] ++;
										$resultats[$keySuspendu]['Orientes']['RDV'][$tmpmonthrdv]++;
										if ( $flagOrientePrepro ) {
											//	- dont nbre de 1er rdv fixés suite à une orientation Pré pro
											$resultats['Tous']['Orientes']['RDV_Prepro'][$tmpmonthrdv] ++;
											$resultats[$keySuspendu]['Orientes']['RDV_Prepro'][$tmpmonthrdv]++;
										}
										if ($flagOrienteSocial) {
											//	- dont nbre de 1er rdv fixés suite à une orientation Sociale
											$resultats['Tous']['Orientes']['RDV_Social'][$tmpmonthrdv] ++;
											$resultats[$keySuspendu]['Orientes']['RDV_Social'][$tmpmonthrdv]++;
										}
									}

									// On calcule la date du changement de droits +1 mois
									$date1mois = strtotime($tmpDate.' + 1 month');
									// Nombre d'orientées en moins d'un mois
									if ( $date1mois  >  strtotime($result['Orientstruct']['date_valid']) ) {
										$resultats['Tous']['Orientes1m'][$tmpmonth]++;
										$resultats[$keySuspendu]['Orientes1m'][$tmpmonth]++;
									}

									if ( $result['Orientstruct']['date_valid'] < $result['Rendezvous']['daterdv'] ){
										$diff = abs(strtotime($result['Rendezvous']['daterdv']) - strtotime($result['Orientstruct']['date_valid']));
										// 60*60*24*15 = 15 jours
										if ( (60*60*24*15) > $diff ) {
											//- Nombre de 1er rendez-vous fixé suite à une orientation CD dans un délai de 15 jours
											$resultats['Tous']['Orientes15j']['RDV'][$tmpmonthrdv] ++;
											$resultats[$keySuspendu]['Orientes15j']['RDV'][$tmpmonthrdv]++;
											if ( $flagOrientePrepro ) {
												//	- dont nbre de 1er rdv fixés suite à une orientation Pré pro
												$resultats['Tous']['Orientes15j']['RDV_Prepro'][$tmpmonthrdv] ++;
												$resultats[$keySuspendu]['Orientes15j']['RDV_Prepro'][$tmpmonthrdv]++;
											}
											if ($flagOrienteSocial) {
												//	- dont nbre de 1er rdv fixés suite à une orientation Sociale
												$resultats['Tous']['Orientes15j']['RDV_Social'][$tmpmonthrdv] ++;
												$resultats[$keySuspendu]['Orientes15j']['RDV_Social'][$tmpmonthrdv]++;
											}
										}
									}

								}
							}
							$historiquesPreviousMonth = $historiquesMonth;
							$historiquesToppersPreviousMonth = $historiquesToppersMonth;
						}
					}
				}
				unset($resultsHistoriques[$historiqueKey]);
				unset($results[$key]);
			}
			$resultats = $this->_calculPercentTableauA2av2($resultats);
			$resultats = $this->_adaptLignesTableaux($resultats, 'tableauA2av2');
			return $resultats;
		}

		########################################################################################################################
		########################################################################################################################

		/**
		 * ...
		 *
		 *
		 *
		 * @param array $search
		 * @return array
		 */
		public function getIndicateursTableauA2BV2( array $search ) {
			$useHistoriquedroit = (boolean)Configure::read( 'Statistiqueplanpauvrete.useHistoriquedroit' );

			$Personne = ClassRegistry::init( 'Personne' );
			$Historiquedroit = ClassRegistry::init( 'Historiquedroit' );

			$annee = Hash::get( $search, 'Search.annee' );
			$testOrient = $this->_getTypeOrientation();
			$results = array();

			// Query de base
			$query = $this->_getQueryTableau_a2bv2 ($search, $annee);
			$results = $Personne->find('all', $query);

			if( $useHistoriquedroit ) {
				$arrayIds = array( );
				foreach($results as $result) {
					$arrayIds[] = $result["Personne"]["id"];
				}
				if ( ! empty ($arrayIds) ){
					$query = $this->_getQueryHistoriques ($annee, $arrayIds);
					$resultsHistoriques = $Historiquedroit->query($query);
				}else{
					$resultsHistoriques = null;
				}
			}

			// Initialisation tableau de résultats
			$resultats = $this->_initializeTableauA2bv2();

			// Initialisation des valeurs fixes
			$jourDebMois = $this->jourDeDebut ($annee);

			// Traitement des résultats
			foreach($results as $key => $result) {
				$etatSuspendus = Configure::read( 'Statistiqueplanpauvrete.etatSuspendus' );

				if ( $useHistoriquedroit ){
					$historiqueKey = null;
					foreach ($resultsHistoriques as $historiqueKey => $resultHistorique) {
						if ( $resultHistorique[0]['personne_id'] == $result['Personne']['id'] ){
							$historiquesPreviousMonth = $resultsHistoriques[$historiqueKey][0]['etatdosrsa12'];
							$historiquesToppersPreviousMonth = $resultsHistoriques[$historiqueKey][0]['toppersdrodevorsa12'];
							break;
						}
					}

					$flagOrienteCD = false;
					$flagOrientePrepro = false;
					$flagOrienteSocial = false;
					// Initialisation des valeurs
					if( $result['Structurereferente']['type_struct_stats'] == 'cd' ) {
						$flagOrienteCD = true;
						// Pers. orientées Social
						if(!empty($testOrient['SOCIAL']) && in_array($result['Typeorient']['id'], $testOrient['SOCIAL'] ) ) {
								$flagOrienteSocial = true;
						}
						// Pers. orientées Pré pro
						elseif (!empty($testOrient['PREPRO']) && in_array( $result['Typeorient']['id'], $testOrient['PREPRO'] ) ) {
								$flagOrientePrepro = true;
						}
					}

					for( $month=0; $month<12; $month++ ) {
						if ( ! is_null($historiqueKey) ){
							$historiquesMonth = $resultsHistoriques[$historiqueKey][0]['etatdosrsa'.$month];
							$historiquesToppersMonth = $resultsHistoriques[$historiqueKey][0]['toppersdrodevorsa'.$month];
						}else{
							$historiquesMonth = $historiquesToppersMonth = null;
						}

						// Si la personne est un nouvel entrant suspendu ou pas
						if ( (
							 in_array ( $historiquesMonth, $etatSuspendus)
							&& $historiquesToppersMonth == 1
						) && (
							 in_array ($historiquesPreviousMonth, $etatSuspendus)
							|| $historiquesToppersPreviousMonth != 1
						)) {
							// On a une date d'orientation valide
							if ( $result['Orientstruct']['date_valid'] != null){
								$tmpDate = $this->_getDateString( $annee, $month, $jourDebMois, 2 );
								if (// Si l'orientation n'est pas inférieur au changement de droits
									strtotime($result['Orientstruct']['date_valid']) >= strtotime($tmpDate)
									// Nombre de nouveaux entrants orientés orientées CD
									&& $flagOrienteCD
								){
									// Calcul du moi de l'orientation
									$tmpmonth = date('m', strtotime($result['Orientstruct']['date_valid'])) -1 ;
									$resultats['Tous']['Orientes_CD'][$tmpmonth]++;
									// On vérifie les Hors suspendus
									if (
										($historiquesMonth == 2 && $historiquesToppersMonth == 1)
										&& ( $historiquesPreviousMonth != 2
										|| $historiquesToppersPreviousMonth != 1 )
									) {
										$keySuspendu = 'Horssuspendus';
									} else {
										$keySuspendu = 'Suspendus';
									}
									$resultats[$keySuspendu]['Orientes_CD'][$tmpmonth]++;

									//- Nombre de nouveaux entrants orientés CD avec un CER suite à une orientation CD
									if ( $result['Contratinsertion']['datevalidation_ci'] != null
										&& strtotime($result['Contratinsertion']['datevalidation_ci']) >= strtotime($tmpDate)
									){

										// Calcul du mois de l'orientation
										$tmpcermonth = date('m', strtotime($result['Orientstruct']['date_valid'])) -1 ;

										$resultats['Tous']['Orientes']['CER'][$tmpcermonth] ++;
										$resultats[$keySuspendu]['Orientes']['CER'][$tmpcermonth]++;

										if ( $flagOrientePrepro ) {
											//	- dont nbre de 1er CER suite à une orientation Pré pro
											$resultats['Tous']['Orientes']['CER_Prepro'][$tmpcermonth] ++;
											$resultats[$keySuspendu]['Orientes']['CER_Prepro'][$tmpcermonth]++;
										}
										if ($flagOrienteSocial) {
											//	- dont nbre de 1er CER suite à une orientation Sociale
											$resultats['Tous']['Orientes']['CER_Social'][$tmpcermonth] ++;
											$resultats[$keySuspendu]['Orientes']['CER_Social'][$tmpcermonth]++;
										}
									}

									// On calcule la date +1 mois
									$date1mois = strtotime($tmpDate.' + 1 month');
									// Nombre d'orientées en moins d'un mois
									if ( $date1mois  >  strtotime($result['Orientstruct']['date_valid']) ) {
										$resultats['Tous']['Orientes1m'][$tmpmonth]++;
										$resultats[$keySuspendu]['Orientes1m'][$tmpmonth]++;
									}

									if ( $result['Orientstruct']['date_valid'] < $result['Contratinsertion']['datevalidation_ci'] ){
										if ( $date1mois  >  strtotime($result['Contratinsertion']['datevalidation_ci']) ) {
											//- Nombre de CER suite à une orientation CD dans un délai de 60 jours
											$resultats['Tous']['Orientes2m']['CER'][$tmpcermonth] ++;
											$resultats[$keySuspendu]['Orientes2m']['CER'][$tmpcermonth]++;
											if ( $flagOrientePrepro ) {
												//	- dont nbre de CER suite à une orientation Pré pro
												$resultats['Tous']['Orientes2m']['CER_Prepro'][$tmpcermonth] ++;
												$resultats[$keySuspendu]['Orientes2m']['CER_Prepro'][$tmpcermonth]++;
											}
											if ($flagOrienteSocial) {
												//	- dont nbre de CER suite à une orientation Sociale
												$resultats['Tous']['Orientes2m']['CER_Social'][$tmpcermonth] ++;
												$resultats[$keySuspendu]['Orientes2m']['CER_Social'][$tmpcermonth]++;
											}
										}
									}

								}
							}
							$historiquesPreviousMonth = $historiquesMonth;
							$historiquesToppersPreviousMonth = $historiquesToppersMonth;
						}
					}
				}
				unset($resultsHistoriques[$historiqueKey]);
				unset($results[$key]);
			}
			$resultats = $this->_calculPercentTableauA2bv2($resultats);
			$resultats = $this->_adaptLignesTableaux($resultats, 'tableauA2bv2');
			return $resultats;
		}

		########################################################################################################################
		########################################################################################################################

		/**
		 * Retourne les résultats de la partie Tableau de bord – Instructon RSA (de l’instructon de la demande à un droit Rsa)
		 *
		 * @param array $search
		 * @return array
		 */
		public function getIndicateursTableauA1V3( array $search ) {
			$useHistoriquedroit = (boolean)Configure::read( 'Statistiqueplanpauvrete.useHistoriquedroit' );

			$Personne = ClassRegistry::init( 'Personne' );
			$Historiquedroit = ClassRegistry::init( 'Historiquedroit' );

			$annee = Hash::get( $search, 'Search.annee' );
			$testOrient = $this->_getTypeOrientation();
			$results = array();

			// Query de base
			$etat = '2';
			$query = $this->_getQueryTableau_a1v3($search, $etat, $annee);
			$results = $Personne->find('all', $query);

			if( $useHistoriquedroit ) {
				$arrayIds = array( );
				foreach($results as $result) {
					$arrayIds[] = $result["Personne"]["id"];
				}
				if ( ! empty ($arrayIds) ){
					$query = $this->_getQueryHistoriques ($annee, $arrayIds);
					$resultsHistoriques = $Historiquedroit->query($query);
					$query = $this->_getQueryDossier($annee, $arrayIds);
					$resultsDossiersOld = $Personne->query($query);
				}else{
					$resultsHistoriques = null;
					$resultsDossiersOld=null;
				}
			}

			// Initialisation tableau de résultats
			$resultats = $this->_initializeTableauA1v3();

			foreach($results as $key => $result) {

				if ( $useHistoriquedroit ){
					$historiqueKey = null;
					foreach ($resultsHistoriques as $historiqueKey => $resultHistorique) {
						if ( $resultHistorique[0]['personne_id'] == $result['Personne']['id'] ){
							$historiquesPreviousMonth = $resultsHistoriques[$historiqueKey][0]['etatdosrsa12'];
							$historiquesToppersPreviousMonth = $resultsHistoriques[$historiqueKey][0]['toppersdrodevorsa12'];
							break;
						}
					}
					$historiquesDossierOld = $dossierOldKey = null;
					foreach ($resultsDossiersOld as $dossierOldKey => $resultDossierOld) {
						if ( $resultDossierOld[0]['id'] == $result['Personne']['id'] ){
							$historiquesDossierOld = $resultsDossiersOld[$dossierOldKey][0]['hasotherdossier'];
							break;
						}
					}

					for( $month=0; $month<12; $month++ ) {
						if ( ! is_null($historiqueKey) ){
							$historiquesMonth = $resultsHistoriques[$historiqueKey][0]['etatdosrsa'.$month];
							$historiquesToppersMonth = $resultsHistoriques[$historiqueKey][0]['toppersdrodevorsa'.$month];
						}else{
							$historiquesMonth = $historiquesToppersMonth = null;
						}

						//Si La personne est un nouvel entrant Droit ouvert et Versable (DOV) = etatdosrsa 2
						//et Soumis à Droit et Devoirs (SDD) toppersdrodevorsa 1
						//et ne l'était le mois précédent
						if ( (
							 $historiquesMonth =='2'
							&&  $historiquesToppersMonth == 1
						) && !(
							 $historiquesPreviousMonth == '2'
							&& $historiquesToppersPreviousMonth == 1
						)) {
							$jourDebMois = $this->jourDeDebut($annee);
							$tmpDate = $this->_getDateString( $annee, $month, $jourDebMois, 2 );

							//- Nombre de personnes entrentes en SDD ce mois ci
							$resultats['total'][$month] ++;

							if (
								!is_null($result['Dossiercaf']['ddratdos'])
								&& $result['Dossiercaf']['ddratdos'] >=  $result['Dossier']['dtdemrsa']
								&& (date('m',strtotime($result['Dossiercaf']['ddratdos']))-1) == $month
							){
								//- dont BRSA rejoignant un foyer RSA
								$resultats['nbFoyerJoin'][$month]++;
							}elseif (
								!is_null($result['Dossier']['ddarrmut'])
								&& $result['Dossier']['ddarrmut'] >=  $result['Dossier']['dtdemrsa']
								&& (date('m',strtotime($result['Dossier']['ddarrmut']))-1) == $month
							){
								//- dont BRSA venant de s’installer sur le Dpt (mutation)
								$resultats['nbEMM'][$month]++;
							}elseif (
								!is_null($historiquesDossierOld)
								&& !$historiquesDossierOld
							){
								/*
								 * - dont Personne SDD ayant effectué une demande pour la 1ʳᵉ fois (primo arrivants)
								 *sont considère primo arrivants les personnes sans historique droit à l'exclusion des personnes
								 *  - se rattachant à un foyer dont la demande est déjà ouverte
								 *  - venant de s’installer sur le Dpt
								*/
								$resultats['nbFoyerInconnu'][$month]++;
							}
							//- dont PSDD ayant déjà eu des droits ouverts par le passé (suspendus et non orientés ou radiés)
							if (
								!is_null($historiquesDossierOld)
								&& $historiquesDossierOld
							){
								$resultats['nbFoyerRadiSusp'][$month]++;
							}
							//- dont BRSA non-soumis aux droits et devoirs qui le sont désormais
							if (
								$historiquesToppersPreviousMonth == 0
								&& $historiquesPreviousMonth == '2'
							) {
								$resultats['nbToppers'][$month]++;
							}

							// Si La personne est un nouvel entrant et qu'on a une date d'orientation valide
							if ( $result['Orientstruct']['date_valid'] != null ){
								// Si l'orientation n'est pas inférieur au changement de droits
								if (strtotime($result['Orientstruct']['date_valid']) >= strtotime($tmpDate) ){
									// On calcule la date du changement de droits +1 mois
									$date1mois = strtotime($tmpDate.' + 1 month');

									// Calcul du mois de l'orientation
									$tmpmonth = date('m', strtotime($result['Orientstruct']['date_valid'])) -1 ;

									// Nombre d'orientées en moins d'un mois
									if ( $date1mois > strtotime($result['Orientstruct']['date_valid']) ) {
										//- Nombre de nouveaux entrants orientés en moins d’un mois
										$resultats['Orientes']['total'][$tmpmonth] ++;

										//	- dont nbre de pers. SDD orientées Social + équivalent en %
										if(!empty($testOrient['SOCIAL']) && in_array($result['Typeorient']['id'], $testOrient['SOCIAL'] ) ) {
											$resultats['Orientes']['Social'][$tmpmonth]++;
										}
										//	- dont nbre de pers. SDD orientées Emploi + équivalent en %
										elseif(!empty($testOrient['EMPLOI']) &&  in_array( $result['Typeorient']['id'], $testOrient['EMPLOI'] ) ) {
											$resultats['Orientes']['Emploi'][$tmpmonth]++;
										}
										//	- dont nbre de pers. SDD orientées Pré pro + équivalent en %
										elseif (!empty($testOrient['PREPRO']) && in_array( $result['Typeorient']['id'], $testOrient['PREPRO'] ) ) {
											$resultats['Orientes']['Prepro'][$tmpmonth]++;
										}

										//	- dont nbre de pers. SDD orientées OA + équivalent en %
										if( $result['Structurereferente']['type_struct_stats'] == 'oa' ) {
											$resultats['Orientes']['OA'][$tmpmonth]++;
										}
										//	- dont nbre de pers. SDD orientées PE + équivalent en %
										if( $result['Structurereferente']['type_struct_stats'] == 'pe' ) {
											$resultats['Orientes']['PE'][$tmpmonth]++;
										}
										//	- dont nbre de pers. SDD orientées CD + équivalent en %
										if( $result['Structurereferente']['type_struct_stats'] == 'cd' ) {
											$resultats['Orientes']['CD'][$tmpmonth]++;
										}
									}
								}
							}
							// Si le dernier rendez-vous de la personne est un rendez-vous non venu.
							if ( $result['Rendezvous']['daterdv'] != null ){
								// Si le rendez-vous n'est pas inférieur au changement de droits
								if (strtotime($result['Rendezvous']['daterdv']) >= strtotime($tmpDate) ){
									$resultats['Nonvenu']['RDV'][$month] ++;
								}
							}
							unset($resultsHistoriques[$historiqueKey]);
							unset($resultsDossiersOld[$dossierOldKey]);
							unset($results[$key]);
							break;
						}
						$historiquesPreviousMonth = $historiquesMonth;
						$historiquesToppersPreviousMonth = $historiquesToppersMonth;
					}
					unset($resultsHistoriques[$historiqueKey]);
				}
				unset($resultsDossiersOld[$dossierOldKey]);
				unset($results[$key]);
			}
			$resultats = $this->_calculPercentTableauA1v3($resultats);
			$resultats = $this->_adaptLignesTableaux($resultats, 'tableauA1v3');
			return $resultats;
		}

		/**
		 * ...
		 *
		 *
		 * @param array $search
		 * @return array
		 */
		public function getIndicateursTableauA2AV3( array $search ) {
			$useHistoriquedroit = (boolean)Configure::read( 'Statistiqueplanpauvrete.useHistoriquedroit' );

			$Personne = ClassRegistry::init( 'Personne' );
			$Historiquedroit = ClassRegistry::init( 'Historiquedroit' );

			$annee = Hash::get( $search, 'Search.annee' );
			$testOrient = $this->_getTypeOrientation();
			$results = array();

			// Query de base
			$etat = '2';
			$query = $this->_getQueryTableau_a2av3 ( $search, $etat, $annee);
			$results = $Personne->find('all', $query);

			if( $useHistoriquedroit ) {
				$arrayIds = array( );
				foreach($results as $result) {
					$arrayIds[] = $result["Personne"]["id"];
				}
				if ( ! empty ($arrayIds) ){
					$query = $this->_getQueryHistoriques ($annee, $arrayIds);
					$resultsHistoriques = $Historiquedroit->query($query);
				}else{
					$resultsHistoriques = null;
				}
			}

			// Initialisation tableau de résultats
			$resultats = $this->_initializeTableauA2av3();

			// Initialisation des valeurs fixes
			$jourDebMois = $this->jourDeDebut ($annee);

			// Traitement du résultat
			foreach($results as $key => $result) {

				if ( $useHistoriquedroit ){
					$historiqueKey = null;
					foreach ($resultsHistoriques as $historiqueKey => $resultHistorique) {
						if ( $resultHistorique[0]['personne_id'] == $result['Personne']['id'] ){
							$historiquesPreviousMonth = $resultsHistoriques[$historiqueKey][0]['etatdosrsa12'];
							$historiquesToppersPreviousMonth = $resultsHistoriques[$historiqueKey][0]['toppersdrodevorsa12'];
							break;
						}
					}

					$flagOrienteCD = false;
					$flagOrientePrepro = false;
					$flagOrienteSocial = false;
					// Initialisation des valeurs
					if( $result['Structurereferente']['type_struct_stats'] == 'cd' ) {
						$flagOrienteCD = true;
						// Pers. orientées Social
						if(!empty($testOrient['SOCIAL']) && in_array($result['Typeorient']['id'], $testOrient['SOCIAL'] ) ) {
								$flagOrienteSocial = true;
						}
						// Pers. orientées Pré pro
						elseif (!empty($testOrient['PREPRO']) && in_array( $result['Typeorient']['id'], $testOrient['PREPRO'] ) ) {
								$flagOrientePrepro = true;
						}
					}

					for( $month=0; $month<12; $month++ ) {
						if ( ! is_null($historiqueKey) ){
							$historiquesMonth = $resultsHistoriques[$historiqueKey][0]['etatdosrsa'.$month];
							$historiquesToppersMonth = $resultsHistoriques[$historiqueKey][0]['toppersdrodevorsa'.$month];
						}else{
							$historiquesMonth = $historiquesToppersMonth = null;
						}

						// Si La personne est un nouvel entrant Droit ouvert et Versable (DOV) = etatdosrsa 2
						// et Soumis à Droit et Devoirs (SDD) toppersdrodevorsa 1
						// et ne l'était le mois précédent
						if ( (
							 $historiquesMonth == '2'
							&& $historiquesToppersMonth == 1
						) && !(
							 $historiquesPreviousMonth == '2'
							&& $historiquesToppersPreviousMonth == 1
						) ) {
							// On a une date d'orientation valide
							if ( $result['Orientstruct']['date_valid'] != null){
								$tmpDate = $this->_getDateString( $annee, $month, $jourDebMois, 2 );
								if (// Si l'orientation n'est pas inférieur au changement de droits
									strtotime($result['Orientstruct']['date_valid']) >= strtotime($tmpDate)
									// Nombre de nouveaux entrants orientés orientées CD
									&& $flagOrienteCD
								){

									// Calcul du mois de l'orientation
									$tmpmonth = date('m', strtotime($result['Orientstruct']['date_valid'])) -1 ;
									$resultats['Orientes_CD'][$tmpmonth]++;

									//- Nombre de nouveaux entrants orientés CD avec un 1er rendez-vous fixé suite à une orientation CD
									if ( $result['Rendezvous']['daterdv'] != null
										&& strtotime($result['Rendezvous']['daterdv']) >= strtotime($tmpDate)
									){
										$tmpmonthrdv = date('m', strtotime($result['Rendezvous']['daterdv'])) -1 ;
										$resultats['Orientes']['RDV'][$tmpmonthrdv] ++;
										if ( $flagOrientePrepro ) {
											//	- dont nbre de 1er rdv fixés suite à une orientation Pré pro
											$resultats['Orientes']['RDV_Prepro'][$tmpmonthrdv] ++;
										}
										if ($flagOrienteSocial) {
											//	- dont nbre de 1er rdv fixés suite à une orientation Sociale
											$resultats['Orientes']['RDV_Social'][$tmpmonthrdv] ++;
										}
									}

									// On calcul la date du changement de droits +1 mois
									$date1mois = strtotime($tmpDate.' + 1 month');
									// Nombre d'orientées en moins d'un mois
									if ( $date1mois  >  strtotime($result['Orientstruct']['date_valid']) ) {
										$resultats['Orientes1m'][$tmpmonth]++;
									}

									if ( $result['Orientstruct']['date_valid'] < $result['Rendezvous']['daterdv'] ){
										$diff = abs(strtotime($result['Rendezvous']['daterdv']) - strtotime($result['Orientstruct']['date_valid']));
										if ( (60*60*24*15) > $diff ) {
											//- Nombre de 1er rendez-vous fixé suite à une orientation CD dans un délai de 15 jours
											$resultats['Orientes15j']['RDV'][$tmpmonthrdv] ++;

											if ( $flagOrientePrepro ) {
												//	- dont nbre de 1er rdv fixés suite à une orientation Pré pro
												$resultats['Orientes15j']['RDV_Prepro'][$tmpmonthrdv] ++;
											}
											if ($flagOrienteSocial) {
												//	- dont nbre de 1er rdv fixés suite à une orientation Sociale
												$resultats['Orientes15j']['RDV_Social'][$tmpmonthrdv] ++;
											}
										}
									}

								}
							}
							unset($resultsHistoriques[$historiqueKey]);
							unset($results[$key]);
							break;
						}
						$historiquesPreviousMonth = $historiquesMonth;
						$historiquesToppersPreviousMonth = $historiquesToppersMonth;
					}
					unset($resultsHistoriques[$historiqueKey]);
				}
				unset($results[$key]);
			}
			for($i=0; $i<12; $i++) {
				if($resultats['Orientes_CD'][$i] != 0) {
					$resultats['Taux'][$i] =
							round( (100 * $resultats['Orientes15j']['RDV'][$i] )
								/ $resultats['Orientes_CD'][$i], 2)  . '%';
				}
			}
			$resultats = $this->_adaptLignesTableaux($resultats, 'tableauA2av3');
			return $resultats;
		}

		/**
		 * ...
		 *
		 *
		 *
		 * @param array $search
		 * @return array
		 */
		public function getIndicateursTableauA2BV3( array $search ) {
			$useHistoriquedroit = (boolean)Configure::read( 'Statistiqueplanpauvrete.useHistoriquedroit' );

			$Personne = ClassRegistry::init( 'Personne' );
			$Historiquedroit = ClassRegistry::init( 'Historiquedroit' );

			$annee = Hash::get( $search, 'Search.annee' );
			$testOrient = $this->_getTypeOrientation();
			$results = array();

			// Query de base
			$etat = '2';
			$query = $this->_getQueryTableau_a2bv3 ($search, $etat, $annee);
			$results = $Personne->find('all', $query);

			if( $useHistoriquedroit ) {
				$arrayIds = array( );
				foreach($results as $result) {
					$arrayIds[] = $result["Personne"]["id"];
				}
				if ( ! empty ($arrayIds) ){
					$query = $this->_getQueryHistoriques ($annee, $arrayIds);
					$resultsHistoriques = $Historiquedroit->query($query);
				}else{
					$resultsHistoriques = null;
				}
			}

			// Initialisation tableau de résultats
			$resultats = $this->_initializeTableauA2bv3();

			// Initialisation des valeurs fixes
			$jourDebMois = $this->jourDeDebut ($annee);

			// Traitement du résultat
			foreach($results as $key => $result) {

				if ( $useHistoriquedroit ){
					$historiqueKey = null;
					foreach ($resultsHistoriques as $historiqueKey => $resultHistorique) {
						if ( $resultHistorique[0]['personne_id'] == $result['Personne']['id'] ){
							$historiquesPreviousMonth = $resultsHistoriques[$historiqueKey][0]['etatdosrsa12'];
							$historiquesToppersPreviousMonth = $resultsHistoriques[$historiqueKey][0]['toppersdrodevorsa12'];
							break;
						}
					}

					$flagOrienteCD = false;
					$flagOrientePrepro = false;
					$flagOrienteSocial = false;
					// Initialisation des valeurs
					if( $result['Structurereferente']['type_struct_stats'] == 'cd' ) {
						$flagOrienteCD = true;
						// Pers. orientées Social
						if(!empty($testOrient['SOCIAL']) && in_array($result['Typeorient']['id'], $testOrient['SOCIAL'] ) ) {
								$flagOrienteSocial = true;
						}
						// Pers. orientées Pré pro
						elseif (!empty($testOrient['PREPRO']) && in_array( $result['Typeorient']['id'], $testOrient['PREPRO'] ) ) {
								$flagOrientePrepro = true;
						}
					}

					for( $month=0; $month<12; $month++ ) {
						if ( ! is_null($historiqueKey) ){
							$historiquesMonth = $resultsHistoriques[$historiqueKey][0]['etatdosrsa'.$month];
							$historiquesToppersMonth = $resultsHistoriques[$historiqueKey][0]['toppersdrodevorsa'.$month];
						}else{
							$historiquesMonth = $historiquesToppersMonth = null;
						}

						// Si La personne est un nouvel entrant Droit ouvert et Versable (DOV) = etatdosrsa 2
						// et Soumis à Droit et Devoirs (SDD) toppersdrodevorsa 1
						// et ne l'était le mois précédent
						if ( (
							 $historiquesMonth == '2'
							&& $historiquesToppersMonth == 1
						) && !(
							$historiquesPreviousMonth == '2'
							&& $historiquesToppersPreviousMonth == 1
						)) {
							// On a une date d'orientation valide
							if ( $result['Orientstruct']['date_valid'] != null){
								$tmpDate = $this->_getDateString( $annee, $month, $jourDebMois, 2 );
								if (// Si l'orientation n'est pas inférieur au changement de droits
									strtotime($result['Orientstruct']['date_valid']) >= strtotime($tmpDate)
									// Nombre de nouveaux entrants orientés orientées CD
									&& $flagOrienteCD
								){
									// Calcul du mois de l'orientation
									$tmpmonth = date('m', strtotime($result['Orientstruct']['date_valid'])) -1 ;
									$resultats['Orientes_CD'][$tmpmonth]++;

									//- Nombre de nouveaux entrants orientés CD avec un CER suite à une orientation CD
									if ( $result['Contratinsertion']['datevalidation_ci'] != null
										&& strtotime($result['Contratinsertion']['datevalidation_ci']) >= strtotime($tmpDate)
									){
										//Calcul du mois du CER
										$tmpcermonth = date('m', strtotime($result['Orientstruct']['date_valid'])) -1 ;
										$resultats['Orientes']['CER'][$tmpcermonth] ++;

										if ( $flagOrientePrepro ) {
											//	- dont nbre de 1er CER suite à une orientation Pré pro
											$resultats['Orientes']['CER_Prepro'][$tmpcermonth] ++;
										}
										if ($flagOrienteSocial) {
											//	- dont nbre de 1er CER suite à une orientation Sociale
											$resultats['Orientes']['CER_Social'][$tmpcermonth] ++;
										}
									}

									// On calcule la date +1 mois
									$date1mois = strtotime($tmpDate.' + 1 month');
									// Nombre d'orientées en moins d'un mois
									if ( $date1mois  >  strtotime($result['Orientstruct']['date_valid']) ) {
										$resultats['Orientes1m'][$tmpmonth]++;
									}

									if ( $result['Orientstruct']['date_valid'] < $result['Contratinsertion']['datevalidation_ci'] ){
										if ( $date1mois  >  strtotime($result['Contratinsertion']['datevalidation_ci']) ) {
											//- Nombre de CER suite à une orientation CD dans un délai de 60 jours
											$resultats['Orientes2m']['CER'][$tmpcermonth] ++;

											if ( $flagOrientePrepro ) {
												//	- dont nbre de CER suite à une orientation Pré pro
												$resultats['Orientes2m']['CER_Prepro'][$tmpcermonth] ++;
											}
											if ($flagOrienteSocial) {
												//	- dont nbre de CER suite à une orientation Sociale
												$resultats['Orientes2m']['CER_Social'][$tmpcermonth] ++;
											}
										}
									}
								}
							}
							unset($resultsHistoriques[$historiqueKey]);
							unset($results[$key]);
							break;
						}
						$historiquesPreviousMonth = $historiquesMonth;
						$historiquesToppersPreviousMonth = $historiquesToppersMonth;
					}
					unset($resultsHistoriques[$historiqueKey]);
				}
				unset($results[$key]);
			}
			for($i=0; $i<12; $i++) {
				if($resultats['Orientes_CD'][$i] != 0) {
					$resultats['Taux'][$i] =
						round( (100 * $resultats['Orientes2m']['CER'][$i] )
						/ $resultats['Orientes_CD'][$i], 2)  . '%';
				}
			}
			$resultats = $this->_adaptLignesTableaux($resultats, 'tableauA2bv3');
			return $resultats;
		}

		########################################################################################################################
		########################################################################################################################

		/**
		 * Jour du début de la période
		 */
		public function jourDeDebut () {
			return $this->periodeStatistique ();
		}

		/**
		 * Jour du fin de la période
		 */
		public function jourDeFin ($annee = null) {
			return $this->periodeStatistique ('fin',$annee);
		}

		/**
		 * Récupération de la période
		 */
		public function periodeStatistique ($extremite = 'deb',$annee = null) {
			$periode = $this->WebrsaCohortePlanpauvrete->datePeriodeStatistique($annee);
			$date = new DateTime ($periode[$extremite]);

			return $date->format ('d');
		}
	}
