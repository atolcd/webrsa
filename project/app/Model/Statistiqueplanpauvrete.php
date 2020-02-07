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
		public $uses = array( 'Foyer' );

		/**
		 * Retourne les types d'orientations par code_type_orient
		 *
		 * @return array
		 */
		protected function _getTypeOrientation() {
			$Orientation = ClassRegistry::init( 'Orientstruct' );
			$emploi = $Orientation->query('SELECT t.id FROM typesorients t INNER JOIN typesorients AS t2 ON (t2.id = t.parentid AND t2.code_type_orient = \'EMPLOI\')' );
			$social = $Orientation->query('SELECT t.id FROM typesorients t INNER JOIN typesorients AS t2 ON (t2.id = t.parentid AND t2.code_type_orient = \'SOCIAL\')' );
			$prepro = $Orientation->query('SELECT t.id FROM typesorients t INNER JOIN typesorients AS t2 ON (t2.id = t.parentid AND t2.code_type_orient = \'PREPRO\')' );
			$result = array();
			foreach($emploi as $emp) {
				$result['EMPLOI'][] = $emp[0]['id'];
			}
			foreach($social as $soc) {
				$result['SOCIAL'][] = $soc[0]['id'];
			}
			foreach($prepro as $pre) {
				$result['PREPRO'][] = $pre[0]['id'];
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
		 * @return array
		 */
		protected function _getJoinsTableau(array $search, $addFoyer = false, $addOrient = false) {
			$joinSearch = array();
			if(
				(isset($search['Adresse']) &&
				( $search['Adresse']['nomvoie'] != ''  ||
				$search['Adresse']['nomcom'] != '' ||
				$search['Adresse']['numcom']  != '' )
				)
				|| ( isset($search['Canton']) && $search['Canton']['canton'] != '')
			) {
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

				$joinSearch = array_merge($joinFoyer, array(
					array(
						'table' => 'adresses',
						'alias' => 'Adresse',
						'type' => 'LEFT',
						'conditions' => array(
							'Adresse.foyerid = Foyer.id'
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
				));
			}

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
		protected function _getConditionsTableau(array $search) {
			$conditionsSearch = array();
			// Adresse
			if( isset($search['Adresse']) ) {
				if( $search['Adresse']['nomvoie'] != '' ) {
					$conditionsSearch = array_merge($conditionsSearch, array(
						'Adresse.nomvoie' => $search['Adresse']['nomvoie']
					));
				}
				if( $search['Adresse']['nomcom'] != '' ) {
					$conditionsSearch = array_merge($conditionsSearch, array(
						'Adresse.nomcom' => $search['Adresse']['nomcom']
					));
				}
				if( $search['Adresse']['numcom']  != '') {
					$conditionsSearch = array_merge($conditionsSearch, array(
						'Adresse.numcom' => $search['Adresse']['numcom']
					));
				}
			}

			// Canton
			if( isset($search['Canton']) && $search['Canton']['canton'] != '') {
				$conditionsSearch = $conditionsSearch = array_merge($conditionsSearch, array(
					'Canton.canton' => $search['Canton']['canton']
				));
			}

			// Service instructeur
			if( isset($search['Search']['serviceinstructeur']) && $search['Search']['serviceinstructeur'] != '' ) {
				$conditionsSearch = array_merge($conditionsSearch, array(
					'OrientstructServiceinstructeur.serviceinstructeur_id' => $search['Search']['serviceinstructeur']
				));
			}

			return $conditionsSearch;
		}

		/**
		 *
		 * @param array $search
		 * @return array
		*/
		protected function _getQueryTableau_b1(array $search , $annee) {
			$conditionsSearch = $this->_getConditionsTableau($search);
			$joinSearch = $this->_getJoinsTableau($search, true);
			$conditionsSDD = Configure::read('Statistiqueplanpauvrete.conditions_droits_et_devoirs');

			// Query finale
			$query = array(
				'fields' => array(
					'DISTINCT ON ("Historiquedroit"."id") "Historiquedroit"."id" AS "idHistoriquedroit"',
					'Historiquedroit.created',
					'Orientstruct.date_valid',
					'Orientstruct.rgorient',
					'Structurereferente.typestructure',
					'Structurereferente.type_struct_stats',
					'Structurereferente.code_stats',
					'Typeorient.id',
					'Typeorient.modele_notif',
					'Rendezvous.daterdv',
					'Statutrdv.id',
					'Bilanparcours66.modified',
					'Bilanparcours66.proposition',
				),
				'recursive' => -1,
				'joins' => array_merge( array(
					array(
						'table' => 'personnes',
						'alias' => 'Personne',
						'type' => 'LEFT',
						'conditions' => array(
							'Personne.id = Historiquedroit.personne_id'
						)
					),
					array(
						'table' => 'orientsstructs',
						'alias' => 'Orientstruct',
						'type' => 'LEFT',
						'conditions' => array(
							'Orientstruct.personne_id = Personne.id'
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
						'table' => 'rendezvous',
						'alias' => 'Rendezvous',
						'type' => 'LEFT',
						'conditions' => array(
							'Rendezvous.personne_id = Personne.id'
						)
					),
					array(
						'table' => 'statutsrdvs',
						'alias' => 'Statutrdv',
						'type' => 'LEFT',
						'conditions' => array(
							'Statutrdv.id = Rendezvous.statutrdv_id'
						)
					),
					array(
						'table' => 'structuresreferentes',
						'alias' => 'Structurereferente',
						'type' => 'LEFT',
						'conditions' => array(
							'Structurereferente.typeorient_id = Typeorient.id'
						)
					),
					array(
						'table' => 'bilansparcours66',
						'alias' => 'Bilanparcours66',
						'type' => 'LEFT',
						'conditions' => array(
							'Bilanparcours66.personne_id = Personne.id'
						)
					) ),
					$joinSearch
				),
				'conditions' => array_merge( array(
					'Historiquedroit.created >= ' => $annee .'-01-01',
					'Historiquedroit.created <= ' => $annee .'-12-31',
					'Historiquedroit.etatdosrsa IN' => $conditionsSDD['Situationdossierrsa.etatdosrsa'],
					'AND' => array(
						'OR' => array(
							array('Rendezvous.daterdv' => NULL),
							array('Rendezvous.daterdv >= Historiquedroit.created')
						),
						array(
							'OR' => array(
								array('Orientstruct.date_valid' => NULL),
								array('Orientstruct.date_valid >= Historiquedroit.created')
							)
						)
					)
					),
					$conditionsSearch
				)
			);
			return $query;
		}

		/**
		 *
		 * @param array $search
		 * @return array
		 */
		protected function _getQueryTableau_a1(array $search , $annee) {
			$Dossier = ClassRegistry::init( 'Dossier' );
			$Foyer = ClassRegistry::init( 'Foyer' );
			$conditionsSearch = $this->_getConditionsTableau($search);
			$joinSearch = $this->_getJoinsTableau($search, false, true);
			// Query finale
			$query = array(
				'fields' => array(
					'DISTINCT ON ("Foyer"."id") "Foyer"."id" AS "idFoyer"',
					'Personne.id',
				),
				'recursive' => 0,
				'joins' => array_merge(
					array(
						$Dossier->join( 'Detaildroitrsa', array( 'type' => 'INNER' ) ),
						$Dossier->Detaildroitrsa->join( 'Detailcalculdroitrsa', array( 'type' => 'INNER' ) ),
						$Foyer->join( 'Personne', array( 'type' => 'INNER' ) )
					),
					$joinSearch
				),
				'conditions' => $conditionsSearch
			);
			$useHistoriquedroit = (boolean)Configure::read( 'Statistiqueplanpauvrete.useHistoriquedroit' );
			if ( $useHistoriquedroit ){
				$query['fields'] = array_merge(
					$query['fields'],
					array(
						'Historiquedroit12.etatdosrsa'
					)
				);
				for($month=0; $month<12; $month++) {
					$query['fields'] = array_merge(
						$query['fields'],
						array(
							'Historiquedroit'.$month.'.etatdosrsa'
						)
					);
				}
				$query['joins'] = array_merge(
					$query['joins'],
					array(
						array(
							'table' => 'historiquesdroits',
							'alias' => 'Historiquedroit12',
							'type' => 'LEFT',
							'conditions' => array(
								'Personne.id = Historiquedroit12.personne_id',
								'(\''.($annee-1).'-12-01\' BETWEEN date_trunc(\'month\', Historiquedroit12.created )
								AND  date_trunc(\'month\', Historiquedroit12.modified ))'
							),
							'ORDER BY' => 'Historiquedroit12.created DESC',
							'LIMIT' => 1
						)
					)
				);
				for($month=0; $month<12; $month++) {
					if (($month+1) <10){$tmpMonth = '0'.($month+1);}else{$tmpMonth = ($month+1);}
					$query['joins'] = array_merge(
						$query['joins'],
						array(
							array(
								'table' => 'historiquesdroits',
								'alias' => 'Historiquedroit'.$month,
								'type' => 'LEFT',
								'conditions' => array(
									'Personne.id = Historiquedroit'.$month.'.personne_id',
									'(date_trunc(\'month\',to_date(\''.$annee.'-'.$tmpMonth.'-01\',\'YYYY-MM-DD\'))
									BETWEEN date_trunc(\'month\', Historiquedroit'.$month.'.created )
									AND  date_trunc(\'month\', Historiquedroit'.$month.'.modified ) )'
								),
								'ORDER BY' => 'Historiquedroit'.$month.'.created DESC',
								'LIMIT' => 1
							)
						)
					);
				}
			}
			$query['conditions'] = array_merge(
				array(
					/*'Orientstruct.date_valid >= ' => $annee .'-01-01',
					'Orientstruct.date_valid <= ' => $annee .'-12-31',
					'Rendezvous.daterdv !=' =>  NULL,
					'Rendezvous.daterdv >= Orientstruct.date_valid'*/
				),
				$query['conditions']
			);

			$query = $this->_completeQuerySoumisDd($query, $annee, true);
			return $query;
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
					'DISTINCT ON ("Historiquedroit"."id") "Historiquedroit"."id" AS "idHistoriquedroit"',
					'Personne.id',
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
						/*array(
							'table' => 'rendezvous',
							'alias' => 'Rendezvous',
							'type' => 'LEFT',
							'conditions' => array(
								'AND' => array(
									'Rendezvous.personne_id = Personne.id',
									 array(
										'OR' => array(
											array('Rendezvous.daterdv' => NULL),
											array('Rendezvous.daterdv >= Historiquedroit.created')
										),
									)
								)
							)
						),
						array(
							'table' => 'statutsrdvs',
							'alias' => 'Statutrdv',
							'type' => 'LEFT',
							'conditions' => array(
								'Statutrdv.id = Rendezvous.statutrdv_id'
							)
						),*/
					),
					$joinSearch
				),
				'conditions' => array_merge(
					array(
						'Historiquedroit.modified >= ' => $annee .'-01-01',
						'Historiquedroit.created <= ' => $annee .'-12-31',
						'Historiquedroit.etatdosrsa IN' => $conditionsSDD['Situationdossierrsa.etatdosrsa'],
						/*'AND' => array(
							array(
								'OR' => array(
									array('Rendezvous.daterdv' => NULL),
									array('Rendezvous.daterdv >= Historiquedroit.created')
								),
							),
							array(
								'OR' => array(
									array('Orientstruct.date_valid' => NULL),
									array('Orientstruct.date_valid >= Historiquedroit.created')
								)
							),
							array(
								'OR' => array(
									array('Contratinsertion.datevalidation_ci' => NULL),
									array('Contratinsertion.datevalidation_ci >= Historiquedroit.created')
								)
							),
							array(
								'OR' => array(
									array('Cui.signaturele' => NULL),
									array('Cui.signaturele >= Historiquedroit.created')
								)
							)
						)*/
					),
					$conditionsSearch
				),
			);
			return $query;
		}

		/**
		 *
		 * @param array $search
		 * @return array
		 */
		protected function _getQueryTableau_b4(array $search , $annee) {
			$conditionsSearch = $this->_getConditionsTableau($search);
			$joinSearch = $this->_getJoinsTableau($search);

			// Query finale
			$query = array(
				'fields' => array(
					'DISTINCT ON ("Personne"."id") "Personne"."id" AS "idPersonne"',
					'Orientstruct.date_valid',
					'Orientstruct.statut_orient',
					'Typeorient.id',
					'Rendezvous.daterdv',
					'Rendezvous.typerdv_id',
					'Statutrdv.id'
				),
				'recursive' => -1,
				'joins' => array_merge( array(
					array(
						'table' => 'foyers',
						'alias' => 'Foyer',
						'type' => 'LEFT',
						'conditions' => array(
							'Foyer.id = Personne.foyer_id'
						)
					),
					array(
						'table' => 'orientsstructs',
						'alias' => 'Orientstruct',
						'type' => 'LEFT',
						'conditions' => array(
							'Orientstruct.personne_id = Personne.id'
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
						'table' => 'rendezvous',
						'alias' => 'Rendezvous',
						'type' => 'LEFT',
						'conditions' => array(
							'Rendezvous.personne_id = Personne.id'
						)
					),
					array(
						'table' => 'statutsrdvs',
						'alias' => 'Statutrdv',
						'type' => 'LEFT',
						'conditions' => array(
							'Statutrdv.id = Rendezvous.statutrdv_id'
						)
					),
					array(
						'table' => 'dossiers',
						'alias' => 'Dossier',
						'type' => 'LEFT',
						'conditions' => array(
							'Dossier.id = Foyer.dossier_id'
						)
					),),
					$joinSearch
				),
				'conditions' => array_merge( array(
					'Orientstruct.date_valid >= ' => $annee .'-01-01',
					'Orientstruct.date_valid <= ' => $annee .'-12-31',
					'Orientstruct.rgorient' => 1,
					'Rendezvous.daterdv !=' =>  NULL,
					'Rendezvous.daterdv >= Orientstruct.date_valid'
					),
					$conditionsSearch
				)
			);

			$query = $this->_completeQuerySoumisDd($query, $annee, true);
			return $query;
		}

		/**
		 *
		 * @param array $search
		 * @return array
		 */
		protected function _getQueryTableau_b5(array $search , $annee) {
			$conditionsSearch = $this->_getConditionsTableau($search);
			$joinSearch = $this->_getJoinsTableau($search);

			// Query finale
			$query = array(
				'fields' => array(
					'DISTINCT ON ("Personne"."id") "Personne"."id" AS "idPersonne"',
					'Orientstruct.date_valid',
					'Orientstruct.statut_orient',
					'Typeorient.id',
					'Contratinsertion.datevalidation_ci',
					'Contratinsertion.rg_ci',
				),
				'recursive' => -1,
				'joins' => array_merge( array(
					array(
						'table' => 'orientsstructs',
						'alias' => 'Orientstruct',
						'type' => 'LEFT',
						'conditions' => array(
							'Orientstruct.personne_id = Personne.id'
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
						'table' => 'foyers',
						'alias' => 'Foyer',
						'type' => 'LEFT',
						'conditions' => array(
							'Foyer.id = Personne.foyer_id'
						)
					),
					array(
						'table' => 'dossiers',
						'alias' => 'Dossier',
						'type' => 'LEFT',
						'conditions' => array(
							'Dossier.id = Foyer.dossier_id'
						)
					),
					array(
						'table' => 'contratsinsertion',
						'alias' => 'Contratinsertion',
						'type' => 'LEFT',
						'conditions' => array(
							'Contratinsertion.personne_id = Personne.id'
						)
					),
				),
				$joinSearch
				),
				'conditions' => array_merge( array(
					'Orientstruct.date_valid >= ' => $annee .'-01-01',
					'Orientstruct.date_valid <= ' => $annee .'-12-31',
					'Orientstruct.rgorient' => 1,
					),
					$conditionsSearch
				)
			);

			$query = $this->_completeQuerySoumisDd($query, $annee, true);
			return $query;
		}

		/**
		 *
		 * @param array $search
		 * @return array
		 */
		protected function _getQueryTableau_a1v2(array $search , $annee) {
			$Dossier = ClassRegistry::init( 'Dossier' );
			$Foyer = ClassRegistry::init( 'Foyer' );
			$jourFinMois = Configure::read('PlanPauvrete.Stats.Moisprecedent.fin');
			$conditionsSearch = $this->_getConditionsTableau($search);
			$joinSearch = $this->_getJoinsTableau($search, false, true);
			// Query First
			$query = array(
				'fields' => array(
					'DISTINCT ON ("Foyer"."id") "Foyer"."id" AS "idFoyer"',
					'Personne.id',
					'Orientstruct.id',
					'Orientstruct.date_valid',
					'Orientstruct.statut_orient',
					'Orientstruct.date_valid',
					'Orientstruct.statut_orient',
					'Typeorient.id',
					'Typeorient.lib_type_orient',
					'Typeorient.modele_notif',
					'Typeorient.parentid',
					'Structurereferente.typestructure',
					'Structurereferente.type_struct_stats',
					'Structurereferente.code_stats',
				),
				'recursive' => -1,
				'joins' => array_merge(
					array(
						$Foyer->join( 'Dossier', array( 'type' => 'INNER' ) ),
						$Dossier->join( 'Detaildroitrsa', array( 'type' => 'INNER' ) ),
						$Dossier->Detaildroitrsa->join( 'Detailcalculdroitrsa', array( 'type' => 'INNER' ) ),
						$Foyer->join( 'Personne', array( 'type' => 'INNER' ) ),
						array(
							'table' => 'orientsstructs',
							'alias' => 'Orientstruct',
							'type' => 'LEFT',
							'conditions' => array(
								'Orientstruct.personne_id = Personne.id',
							),
							'ORDER BY' => 'Orientstruct.date_valid DESC',
							'LIMIT' => 1
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
					),
					$joinSearch
				),
				'conditions' => $conditionsSearch
			);
			$useHistoriquedroit = (boolean)Configure::read( 'Statistiqueplanpauvrete.useHistoriquedroit' );
			if ( $useHistoriquedroit ){
				//Fields
				$query['fields'] = array_merge(
					$query['fields'],
					array(
						'Historiquedroit12.etatdosrsa',
						'Historiquedroit12.toppersdrodevorsa',
						/*'Adressefoyer12.rgadr',
						'Adressefoyer12.dtemm',
						'Adresse12.codepos'*/
					)
				);
				//Fields By Months
				for($month=0; $month<12; $month++) {
					$query['fields'] = array_merge(
						$query['fields'],
						array(
							'Historiquedroit'.$month.'.etatdosrsa',
							'Historiquedroit'.$month.'.toppersdrodevorsa',
							/*'Adressefoyer'.$month.'.rgadr',
							'Adressefoyer'.$month.'.dtemm',
							'Adresse'.$month.'.codepos'*/
						)
					);
				}

				$tmpDateRecherchePrevious = $annee.'-01-'.$jourFinMois;
				//Joins by month
				$query['joins'] = array_merge(
					$query['joins'],
					array(
						array(
							'table' => 'historiquesdroits',
							'alias' => 'Historiquedroit12',
							'type' => 'LEFT',
							'conditions' => array(
								'Personne.id = Historiquedroit12.personne_id',
								'(\''.$tmpDateRecherchePrevious.'\' BETWEEN date_trunc(\'day\', Historiquedroit12.created )
								AND  date_trunc(\'day\', Historiquedroit12.modified ))'
							),
							'ORDER BY' => 'Historiquedroit12.created DESC',
							'LIMIT' => 1
						),
						/*array(
							'table' => 'adressesfoyers',
							'alias' => 'Adressefoyer12',
							'type' => 'LEFT',
							'conditions' => array(
								'Foyer.id = Adressefoyer12.foyer_id',
								'Adressefoyer12.rgadr' => '01',
								'(\''.$tmpDateRecherchePrevious.'\' > date_trunc(\'day\', Adressefoyer12.dtemm ))'
							),
							'ORDER BY' => 'Adressefoyer12.dtemm DESC',
							'LIMIT' => 1
						),
						array(
							'table' => 'adresses',
							'alias' => 'Adresse12',
							'type' => 'LEFT',
							'conditions' => array('Adresse12.id = Adressefoyer12.adresse_id'),
						)*/
					)
				);
				for($month=0; $month<12; $month++) {
					$tmpDateRecherche = $this->_getDateString( $annee, $month, $jourFinMois, 2 );
					$query['joins'] = array_merge(
						$query['joins'],
						array(
							array(
								'table' => 'historiquesdroits',
								'alias' => 'Historiquedroit'.$month,
								'type' => 'LEFT',
								'conditions' => array(
									'Personne.id = Historiquedroit'.$month.'.personne_id',
									'(date_trunc(\'day\',to_date(\''.$tmpDateRecherche.'\',\'YYYY-MM-DD\'))
									BETWEEN date_trunc(\'day\', Historiquedroit'.$month.'.created )
									AND  date_trunc(\'day\', Historiquedroit'.$month.'.modified ) )'
								),
								'ORDER BY' => 'Historiquedroit'.$month.'.created DESC',
								'LIMIT' => 1
							),
							/*array(
								'table' => 'adressesfoyers',
								'alias' => 'Adressefoyer'.$month,
								'type' => 'LEFT',
								'conditions' => array(
									'Foyer.id = Adressefoyer'.$month.'.foyer_id',
									'Adressefoyer'.$month.'.rgadr' => '01',
									'(\''.$tmpDateRecherche.'\' >= date_trunc(\'day\', Adressefoyer'.$month.'.dtemm ))',
									'(\''.$tmpDateRecherchePrevious.'\' < date_trunc(\'day\', Adressefoyer'.$month.'.dtemm ))'
								),
								'ORDER BY' => 'Adressefoyer'.$month.'.dtemm DESC',
								'LIMIT' => 1
							),
							array(
								'table' => 'adresses',
								'alias' => 'Adresse'.$month,
								'type' => 'LEFT',
								'conditions' => array('Adresse'.$month.'.id = Adressefoyer'.$month.'.adresse_id'),
							)*/
						)
					);
					$tmpDateRecherchePrevious = $tmpDateRecherche;
				}
			}
			$query['conditions'] = array_merge(
				array(
					/*'Orientstruct.date_valid >= ' => $annee .'-01-01',
					'Orientstruct.date_valid <= ' => $annee .'-12-31',
					'Rendezvous.daterdv !=' =>  NULL,
					'Rendezvous.daterdv >= Orientstruct.date_valid'*/
				),
				$query['conditions']
			);

			$query = $this->_completeQuerySoumisDd($query, $annee, true);
			return $query;
		}

		/**
		 *
		 * @param array $search
		 * @return array
		 */
		protected function _getQueryTableau_a2av2(array $search , $annee) {
			$Dossier = ClassRegistry::init( 'Dossier' );
			$Foyer = ClassRegistry::init( 'Foyer' );
			$jourFinMois = Configure::read('PlanPauvrete.Stats.Moisprecedent.fin');
			$conditionsSearch = $this->_getConditionsTableau($search);
			$joinSearch = $this->_getJoinsTableau($search, false, true);
			// Query First
			$query = array(
				'fields' => array(
					'DISTINCT ON ("Foyer"."id") "Foyer"."id" AS "idFoyer"',
					'Personne.id',
					'Orientstruct.id',
					'Orientstruct.date_valid',
					'Orientstruct.statut_orient',
					'Orientstruct.date_valid',
					'Orientstruct.statut_orient',
					'Typeorient.id',
					'Typeorient.lib_type_orient',
					'Typeorient.modele_notif',
					'Typeorient.parentid',
					'Structurereferente.typestructure',
					'Structurereferente.type_struct_stats',
					'Structurereferente.code_stats',
					//'Rendezvous.daterdv'
				),
				'recursive' => -1,
				'joins' => array_merge(
					array(
						$Foyer->join( 'Dossier', array( 'type' => 'INNER' ) ),
						$Dossier->join( 'Detaildroitrsa', array( 'type' => 'INNER' ) ),
						$Dossier->Detaildroitrsa->join( 'Detailcalculdroitrsa', array( 'type' => 'INNER' ) ),
						$Foyer->join( 'Personne', array( 'type' => 'INNER' ) ),
						array(
							'table' => 'orientsstructs',
							'alias' => 'Orientstruct',
							'type' => 'LEFT',
							'conditions' => array(
								'Orientstruct.personne_id = Personne.id',
								//'Orientstruct.rgorient' => 1,
							),
							'ORDER BY' => 'Orientstruct.date_valid DESC',
							'LIMIT' => 1
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
						/*array(
							'table' => 'rendezvous',
							'alias' => 'Rendezvous',
							'type' => 'LEFT',
							'conditions' => array(
								'Rendezvous.personne_id = Personne.id',
								'Rendezvous.typerdv_id' => Configure::read( 'Statistiqueplanpauvrete.type_rendezvous' ),
							),
							'ORDER BY' => 'Rendezvous.daterdv DESC',
							'LIMIT' => 1
						),*/
					),
					$joinSearch
				),
				'conditions' => $conditionsSearch
			);
			$useHistoriquedroit = (boolean)Configure::read( 'Statistiqueplanpauvrete.useHistoriquedroit' );
			if ( $useHistoriquedroit ){
				//Fields
				$query['fields'] = array_merge(
					$query['fields'],
					array(
						'Historiquedroit12.etatdosrsa',
						'Historiquedroit12.toppersdrodevorsa',
					)
				);
				//Fields By Months
				for($month=0; $month<12; $month++) {
					$query['fields'] = array_merge(
						$query['fields'],
						array(
							'Historiquedroit'.$month.'.etatdosrsa',
							'Historiquedroit'.$month.'.toppersdrodevorsa',
						)
					);
				}

				$tmpDateRecherchePrevious = $annee.'-01-'.$jourFinMois;
				//Joins by month
				$query['joins'] = array_merge(
					$query['joins'],
					array(
						array(
							'table' => 'historiquesdroits',
							'alias' => 'Historiquedroit12',
							'type' => 'LEFT',
							'conditions' => array(
								'Personne.id = Historiquedroit12.personne_id',
								'(\''.$tmpDateRecherchePrevious.'\' BETWEEN date_trunc(\'day\', Historiquedroit12.created )
								AND  date_trunc(\'day\', Historiquedroit12.modified ))'
							),
							'ORDER BY' => 'Historiquedroit12.created DESC',
							'LIMIT' => 1
						),
					)
				);
				for($month=0; $month<12; $month++) {
					$tmpDateRecherche = $this->_getDateString( $annee, $month, $jourFinMois, 2 );
					$query['joins'] = array_merge(
						$query['joins'],
						array(
							array(
								'table' => 'historiquesdroits',
								'alias' => 'Historiquedroit'.$month,
								'type' => 'LEFT',
								'conditions' => array(
									'Personne.id = Historiquedroit'.$month.'.personne_id',
									'(date_trunc(\'day\',to_date(\''.$tmpDateRecherche.'\',\'YYYY-MM-DD\'))
									BETWEEN date_trunc(\'day\', Historiquedroit'.$month.'.created )
									AND  date_trunc(\'day\', Historiquedroit'.$month.'.modified ) )'
								),
								'ORDER BY' => 'Historiquedroit'.$month.'.created DESC',
								'LIMIT' => 1
							),
						)
					);
					$tmpDateRecherchePrevious = $tmpDateRecherche;
				}
			}
			$query['conditions'] = array_merge(
				array(
					/*'Orientstruct.date_valid >= ' => $annee .'-01-01',
					'Orientstruct.date_valid <= ' => $annee .'-12-31',
					'Rendezvous.daterdv !=' =>  NULL,
					'Rendezvous.daterdv >= Orientstruct.date_valid'*/
				),
				$query['conditions']
			);

			$query = $this->_completeQuerySoumisDd($query, $annee, true);
			return $query;
		}

		/**
		 *
		 * @param array $search
		 * @return array
		 */
		protected function _getDateString($annee, $month, $jour, $step = 1) {
			$tmpMonth = '01';
			$tmpAnnee = $annee;
			if (($month+$step) <10){$tmpMonth = '0'.($month+2);}
			elseif (($month+$step) >12){
				$tmpAnnee = $annee+1;
				$tmpMonth = '0'.($month-12+$step);
			}else{$tmpMonth = ($month+$step);}
			$tmpDateRecherche = $tmpAnnee.'-'.$tmpMonth.'-'.$jour;
			return $tmpDateRecherche;
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
			$Foyer = ClassRegistry::init( 'Foyer' );
			$Historiquedroit = ClassRegistry::init( 'Historiquedroit' );
			$annee = Hash::get( $search, 'Search.annee' );
			$results = array();

			// Query de base
			$query = $this->_getQueryTableau_a1 ($search, $annee);

			$results = $Foyer->find('all', $query);

			// Initialisation tableau de résultats
			$resultats = array (
				'total' => array(),
				'nbFoyerConnu' => array(),
				'nbFoyerInconnu' => array()
			);
			for($i=0; $i<12; $i++) {
				$resultats['total'][$i]=0;
				$resultats['nbFoyerConnu'][$i]=0;
				$resultats['nbFoyerInconnu'][$i]=0;
			}
			$tmp = 0;
			foreach($results as $result) {
				$useHistoriquedroit = (boolean)Configure::read( 'Statistiqueplanpauvrete.useHistoriquedroit' );
				if ( $useHistoriquedroit ){
					$historiquesPreviousMonth = $result['Historiquedroit12']['etatdosrsa'];
					for( $month=0; $month<12; $month++ ) {
						if ( $result['Historiquedroit'.$month]['etatdosrsa'] == 2 ) {
							// Nombre de foyers avec un droit ouvert par mois
							$resultats['total'][$month] ++;
							//- Dont le nbre de foyers connus le mois précédent avec un droit radié (les nouveaux entrants)
							if (
								$historiquesPreviousMonth == 5
								|| $historiquesPreviousMonth == 6
							){
								$resultats['nbFoyerConnu'][$month]++;
							}
							//- Dont le nbre de foyers inconnus dans la base (les primio-arrivants)
							if ( $historiquesPreviousMonth == null ){
								$resultats['nbFoyerInconnu'][$month]++;
							}
						}
						$historiquesPreviousMonth = $result['Historiquedroit'.$month]['etatdosrsa'];
					}
				}
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

			// Récupération des variables de configuration
			$configurationDelais = Configure::read('Statistiqueplanpauvrete.delais');

			$testOrient = $this->_getTypeOrientation();

			// Query de base
			$query = $this->_getQueryTableau_a2 ($search, $annee);
			$results = $Historiquedroit->find('all', $query);

			// Initialisation tableau de résultats
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
			$arrayPersonneID = array();
			foreach($results as $result) {
				$arrayPersonneID[]=$result['Personne']['id'];
				$yearStartHistorique = intval( date('Y', strtotime($result['Historiquedroit']['created']) ) );
				$yearEndHistorique = intval( date('Y', strtotime($result['Historiquedroit']['modified']) ) );
				if ($yearStartHistorique < $annee ){$monthStartHistorique = 0;
				}else{ $monthStartHistorique = intval( date('n', strtotime($result['Historiquedroit']['created']) ) ) -1;}
				if ($yearEndHistorique > $annee ){$monthEndHistorique = 11;
				}else{$monthEndHistorique = intval( date('n', strtotime($result['Historiquedroit']['modified']) ) ) -1;}
				for ($month = $monthStartHistorique; $month <= $monthEndHistorique; $month ++ ) {
					$flagOrienteCD = $flagOrientePE = $flagOrienteSocial = $flagOrientePrepro = $flagOrienteCDCER = FALSE;
					//Nombre de personnes Soumises à droits et devoirs (Pers. SDD) avec un droit ouvert par mois
					$resultats['SSD'][$month] ++;
					//Si on as une date d'orientation valide
					if ( $result['Orientstruct']['date_valid'] != null){
						//Récupération de l'année d'orientation
						$yearOrient = intval( date('Y', strtotime($result['Orientstruct']['date_valid']) ) );
						//si l'année n'est pas celle de la recherche alors on change le mois
						if ($yearOrient < $annee ){$monthOrient = 0;}else{
							$monthOrient = intval( date('n', strtotime($result['Orientstruct']['date_valid']) ) ) -1;}
						//Si le mois d'orientation correspond au mois étudié dans la boucle
						if ($monthOrient <= $month ){
							//Nombre de Pers. SDD orientées avec un droit ouvert par mois
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
					// Si la personne as un contrat d'insertion
					if ( $result['Contratinsertion']['datevalidation_ci'] != null) {
						$yearCER = intval( date('Y', strtotime($result['Contratinsertion']['datevalidation_ci']) ) );
						if ($yearCER < $annee ){$monthCER = 0; }else{
						$monthCER = intval( date('n', strtotime($result['Contratinsertion']['datevalidation_ci']) ) ) -1;}
						if ($monthCER <= $month ){
							//Avec contrats dont
							$resultats['Contrat']['total'][$month] ++;
							if ( $flagOrienteCD ) {
								$resultats['Contrat']['CDCER'][$month] ++;
								$flagOrienteCDCER = true;
							}
							if ( $flagOrientePE ) {
								//Detection d'un contrat aider
								if ( in_array( $result['Structurereferente']['code_stats'], Configure::read( 'Statistiqueplanpauvrete.code_stats') ) ){
									$resultats['Contrat']['PEAccomp'][$month] ++;
								}
							}
						}
					}elseif ($result['Cui']['faitle']  != null) {
						$yearCUI= intval( date('Y', strtotime($result['Cui']['faitle']) ) );
						if ($yearCUI < $annee ){$monthCUI = 0; }else{
						$monthCUI = intval( date('n', strtotime($result['Cui']['faitle']) ) ) -1;}
						if ($monthCER <= $month ){
							//Avec contrats dont
							$resultats['Contrat']['total'][$month] ++;
							if ( $flagOrientePE ) {
								//Detection d'un CUI
								$resultats['Contrat']['PEAider'][$month] ++;
							}
						}
					}
					/*
					if ( $flagOrientePE && $result['InformationPE']['ppae_date_signature'] != null ) {
						$yearPPAE = intval( date('Y', strtotime($result['InformationPE']['ppae_date_signature']) ) );
						if ($yearPPAE < $annee ){$monthPPAE = 0; }else{
						$monthPPAE = intval( date('n', strtotime($result['InformationPE']['ppae_date_signature']) ) ) -1;}
						//En attente de Flux Pole emploi PPAE,
						$resultats['Contrat']['PEPPAE'][$month] ++;
					}
					*/
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
							'Rendezvous.personne_id' => $arrayPersonneID
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

		########################################################################################################################
		########################################################################################################################

		/**
		 * Tableau B1 : Tableau de suivi de la première orientation
		 * @param array $search
		 * @return array
		*/
		public function getIndicateursTableauB1( array $search ) {
			$Historiquedroit = ClassRegistry::init( 'Historiquedroit' );
			$annee = Hash::get( $search, 'Search.annee' );
			$results = array();

			$testOrient = $this->_getTypeOrientation();

			// Récupération des variables de configuration
			$configurationDelais = Configure::read('Statistiqueplanpauvrete.delais');
			$statutRdv = Configure::read('Statistiqueplanpauvrete.orientationRdv');
			// Query de base
			$query = $this->_getQueryTableau_b1 ($search, $annee);
			$results = $Historiquedroit->find('all', $query);
			$resultats = array();

			// Initialisation tableau de résultats
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

			// Traitement des résultats
			foreach($results as $result) {
				$month = intval( date('n', strtotime($result['Historiquedroit']['created']) ) ) -1;
				$resultats['total'][$month]++;
				// Orientés
				if( !is_null($result['Orientstruct']['date_valid']) && $result['Orientstruct']['rgorient'] == 1 ) {
					$resultats['Orientes']['total'][$month]++;
					// Type d'orientations
					if( $result['Structurereferente']['type_struct_stats'] == 'oa' ) {
						$resultats['Orientes']['oa'][$month]++;
					}
					if( $result['Structurereferente']['type_struct_stats'] == 'pe' ) {
						$resultats['Orientes']['pe'][$month]++;
					}
					if( $result['Structurereferente']['type_struct_stats'] == 'cd' ) {
						$resultats['Orientes']['cd'][$month]++;
					}

					if(!empty($testOrient['SOCIAL']) && in_array($result['Typeorient']['id'], $testOrient['SOCIAL'] ) ) {
						$resultats['Orientes']['social'][$month]++;
					} elseif(!empty($testOrient['EMPLOI']) &&  in_array( $result['Typeorient']['id'], $testOrient['EMPLOI'] ) ) {
						$resultats['Orientes']['emploi'][$month]++;
					} elseif (!empty($testOrient['PREPRO']) && in_array( $result['Typeorient']['id'], $testOrient['PREPRO'] ) ) {
						$resultats['Orientes']['prepro'][$month]++;
					}

					// Délais
					$dateCreaHisto = new DateTime($result['Historiquedroit']['created']);
					$dateOrient = new DateTime($result['Orientstruct']['date_valid']);
					$delai = $dateCreaHisto->diff($dateOrient)->days;

					if($delai <= 31) {
						$resultats['orient_31jours'][$month]++;
					}
					$resultats['delai_moyen'][$month] += $delai;
					foreach($resultats['delai']as $key => $osef) {
						$joursDelais = explode('_', $key);
						if( $delai >= intval($joursDelais[0]) && $delai < intval($joursDelais[1]) ) {
							$resultats['delai'][$key][$month] ++;
						}
					}
				}
				// Non orientés
				 else {
					$resultats['NonOrientes']['total'][$month]++;
					if( !is_null($result['Statutrdv']['id']) && in_array($result['Statutrdv']['id'], $statutRdv['prevu']) ) {
						$resultats['NonOrientes']['prevu'][$month]++;
					} elseif( !is_null($result['Bilanparcours66']['proposition']) && $result['Bilanparcours66']['proposition'] ==  'audition' ) {
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
			$Personne = ClassRegistry::init( 'Personne' );
			$annee = Hash::get( $search, 'Search.annee' );
			$results = array();

			$testOrient = $this->_getTypeOrientation();

			// Récupération des variables de configuration
			$configurationDelais = Configure::read('Statistiqueplanpauvrete.delais');
			$statutRdv = Configure::read('Statistiqueplanpauvrete.orientationRdv');

			// Query de base
			$query = $this->_getQueryTableau_b4 ($search, $annee);
			$results = $Personne->find('all', $query);

			// Initialisation tableau de résultats
			$resultats = array (
				'total' => array(),
				'Social' => array(
					'total' => array(),
					'venu' => array(),
					'excuse_recevable' => array(),
					'sans_excuse' => array(),
					'delai_moyen' => array(),
					'delai' => $configurationDelais,
					'taux_presence' => array()
				),
				'Prepro' => array(
					'total' => array(),
					'venu' => array(),
					'excuse_recevable' => array(),
					'sans_excuse' => array(),
					'delai_moyen' => array(),
					'delai' => $configurationDelais,
					'taux_presence' => array()
				)
			);

			for($i=0; $i<12; $i++) {
				$resultats['total'][$i] = 0;
				$resultats['Social']['total'][$i] = 0;
				$resultats['Social']['venu'][$i] = 0;
				$resultats['Social']['excuse_recevable'][$i] = 0;
				$resultats['Social']['sans_excuse'][$i] = 0;
				$resultats['Social']['delai_moyen'][$i] = 0;
				$resultats['Social']['taux_presence'][$i] = 0;
				$resultats['Prepro']['total'][$i] = 0;
				$resultats['Prepro']['venu'][$i] = 0;
				$resultats['Prepro']['excuse_recevable'][$i] = 0;
				$resultats['Prepro']['sans_excuse'][$i] = 0;
				$resultats['Prepro']['delai_moyen'][$i] = 0;
				$resultats['Prepro']['taux_presence'][$i] = 0;
				foreach( $configurationDelais as $key => $config) {
					if( is_array($resultats['Social']['delai'][$key]) == false ) {
						$resultats['Social']['delai'][$key] = array();
					}
					if( is_array($resultats['Prepro']['delai'][$key]) == false ) {
						$resultats['Prepro']['delai'][$key] = array();
					}
					$resultats['Social']['delai'][$key][$i] = 0;
					$resultats['Prepro']['delai'][$key][$i] = 0;
				}
			}

			foreach($results as $result) {
				$month = intval( date('n', strtotime($result['Orientstruct']['date_valid']) ) ) -1;
				$resultats['total'][$month]++;
				if(
					( !empty($testOrient['SOCIAL']) && in_array($result['Typeorient']['id'], $testOrient['SOCIAL']) )
				||
					( !empty($testOrient['PREPRO']) &&  in_array($result['Typeorient']['id'], $testOrient['PREPRO'] ) )
				) {
					$orientation = in_array($result['Typeorient']['id'], $testOrient['SOCIAL']) ? 'Social' : 'Prepro';
					if( in_array($result['Statutrdv']['id'], $statutRdv['prevu'] ) === false ) {
						$resultats[$orientation]['total'][$month]++;
						if( in_array($result['Statutrdv']['id'], $statutRdv['venu'] ) !== false ) {
							$resultats[$orientation]['venu'][$month]++;
						}

						if( in_array($result['Statutrdv']['id'], $statutRdv['excuses_recevables'] ) !== false ) {
							$resultats[$orientation]['excuse_recevable'][$month]++;
						}

						if( in_array($result['Statutrdv']['id'], $statutRdv['excuses_non_recevables'] ) !== false ) {
							$resultats[$orientation]['sans_excuse'][$month]++;
						}
					}

					$dateOrient = new DateTime($result['Orientstruct']['date_valid']);
					$dateRdv = new DateTime($result['Rendezvous']['daterdv']);
					$delai = $dateOrient->diff($dateRdv)->days;

					$resultats[$orientation]['delai_moyen'][$month] += $delai;
					foreach($resultats[$orientation]['delai']as $key => $osef) {
						$joursDelais = explode('_', $key);
						if( $delai >= intval($joursDelais[0]) && $delai < intval($joursDelais[1]) ) {
							$resultats[$orientation]['delai'][$key][$month] ++;
						}
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
			}

			return $resultats;
		}

		/**
		 * Retourne le résultat pour le tableau B5 : Suivi des CER
		 * @param array $search
		 * @return array
		 */
		public function getIndicateursTableauB5( array $search ) {
			$Personne = ClassRegistry::init( 'Personne' );
			$annee = Hash::get( $search, 'Search.annee' );
			$results = array();

			$testOrient = $this->_getTypeOrientation();

			// Récupération des variables de configuration
			$configurationDelais = Configure::read('Statistiqueplanpauvrete.delais');

			// Query de base
			$query = $this->_getQueryTableau_b5 ($search, $annee);
			$results = $Personne->find('all', $query);

			// Initialisation tableau de résultats
			$resultats = array (
				'orient_valid' => array(),
				'cer_social' => array(),
				'cer_prepro' => array(),
				'delai_moyen' => array(),
				'delai_social' => array(),
				'delai_prepro' => array(),
				'signe15jrs' => array(),
				'delai' => $configurationDelais,
				'taux_contrat' => array()
			);
			$orientValide = array();
			for($i=0; $i<12; $i++) {
				$resultats['orient_valid'][$i] = 0;
				$resultats['cer_social'][$i] = 0;
				$resultats['cer_prepro'][$i] = 0;
				$resultats['delai_moyen'][$i] = 0;
				$resultats['delai_social'][$i] = 0;
				$resultats['delai_prepro'][$i] = 0;
				$resultats['signe15jrs'][$i] = 0;
				$resultats['taux_contrat'][$i] = 0;
				foreach( $configurationDelais as $key => $config) {
					if( is_array($resultats['delai'][$key]) == false ) {
						$resultats['delai'][$key] = array();
					}
					$resultats['delai'][$key][$i] = 0;
				}
			}

			foreach($results as $result) {
				$monthOrient = intval( date('n', strtotime($result['Orientstruct']['date_valid']) ) ) -1;
				$resultats['orient_valid'][$monthOrient]++;

				$dateOrient = new DateTime($result['Orientstruct']['date_valid']);
				$dateCer = new DateTime($result['Contratinsertion']['datevalidation_ci']);
				$delai = $dateOrient->diff($dateCer)->days;
				if( $delai > 0 && $result['Contratinsertion']['rg_ci'] == 1) {
					$resultats['delai_moyen'][$monthOrient] += $delai;

					if( $delai < 15 ) {
						$resultats['signe15jrs'][$monthOrient]++;
					}

					if( !empty($testOrient['SOCIAL']) && in_array($result['Typeorient']['id'], $testOrient['SOCIAL'] ) ) {
						$resultats['cer_social'][$monthOrient] ++;
						$resultats['delai_social'][$monthOrient] += $delai;
					} else {
						$resultats['cer_prepro'][$monthOrient] ++;
						$resultats['delai_prepro'][$monthOrient] += $delai;
					}

					foreach($resultats['delai']as $key => $osef) {
						$joursDelais = explode('_', $key);
						if( $delai >= intval($joursDelais[0]) && $delai < intval($joursDelais[1]) ) {
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

			return $resultats;
		}

		########################################################################################################################
		########################################################################################################################

		/**
		 * Retourn les résultats de la partie Tableau de bord – Instructon RSA (de l’instructon de la demande à un droit Rsa)
		 *
		 * @param array $search
		 * @return array
		 */
		public function getIndicateursTableauA1V2( array $search ) {
			$Foyer = ClassRegistry::init( 'Foyer' );
			$Historiquedroit = ClassRegistry::init( 'Historiquedroit' );
			$annee = Hash::get( $search, 'Search.annee' );
			$testOrient = $this->_getTypeOrientation();
			$results = array();

			// Query de base
			$query = $this->_getQueryTableau_a1v2 ($search, $annee);
			$results = $Foyer->find('all', $query);

			// Initialisation tableau de résultats
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
			}

			foreach($results as $result) {
				$useHistoriquedroit = (boolean)Configure::read( 'Statistiqueplanpauvrete.useHistoriquedroit' );
				if ( $useHistoriquedroit ){
					$historiquesPreviousMonth = $result['Historiquedroit12']['etatdosrsa'];
					$historiquesToppersPreviousMonth = $result['Historiquedroit12']['toppersdrodevorsa'];
					/*
					$rgadrPreviousMonth = $result['Adressefoyer12']['rgadr'];
					$dtemmPreviousMonth = $result['Adressefoyer12']['dtemm'];
					$codeposPreviousMonth = $result['Adresse12']['codepos'];
					*/
					for( $month=0; $month<12; $month++ ) {
						if (
							($result['Historiquedroit'.$month]['etatdosrsa'] == 2
							&& $result['Historiquedroit'.$month]['toppersdrodevorsa'] == 1)
							&& ( $historiquesPreviousMonth != 2
							|| $historiquesToppersPreviousMonth != 1 )
						) {
							//- Nombre de personnes entrentes en SDD ce mois ci
							$resultats['total'][$month] ++;
							//- dont PSDD ayant effectué une demande pour la 1ʳᵉ fois (primo arrivants)
							if ( $historiquesPreviousMonth == null ){
								$resultats['nbFoyerInconnu'][$month]++;
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
								$resultats['nbFoyerRadiSusp'][$month]++;
							}
							//- dont BRSA non-soumis aux droits et devoirs qui le sont désormais
							if ( $historiquesToppersPreviousMonth == 0) {
								$resultats['nbToppers'][$month]++;
							}
							//- dont BRSA rejoignant un foyer RSA
							//	$resultats['nbFoyerJoin'][$month]++;

							//- dont BRSA venant de s’installer sur le Dpt (mutation)
							/*
							if ($dtemmPreviousMonth != $result['Adressefoyer'.$month]['dtemm']
								&& strpos($codeposPreviousMonth, '66') == 0
							) {
								$resultats['nbEMM'][$month]++;
							}
							*/

							//Si La personne est un nouvel entrant et
							//Qu'on as une date d'orientation valide
							if ( $result['Orientstruct']['date_valid'] != null){
								$jourDebMois = Configure::read('PlanPauvrete.Stats.Moisprecedent.deb');
								$jours = Configure::read('PlanPauvrete.Stats.Orientation.Jours');
								$tmpDate = $this->_getDateString( $annee, $month, $jourDebMois, 2 );

								//Si l'orientation n'est pas inférieur au changement de droits
								if (strtotime($result['Orientstruct']['date_valid']) >= strtotime($tmpDate) ){
									//On calcul la différence entre la date de reception et la date d'orientation
									$diff = abs(strtotime($tmpDate) - strtotime($result['Orientstruct']['date_valid']));

									//Si la différence est de moins de 30 jours
									if ( (60*60*24*$jours) > $diff  ) {
										//- Nombre de nouveaux entrants orientés en moins d’un mois
										$resultats['Orientes']['total'][$month] ++;

										//	- dont nbre de pers. SDD orientées Social + équivalent en %
										if(!empty($testOrient['SOCIAL']) && in_array($result['Typeorient']['id'], $testOrient['SOCIAL'] ) ) {
											$resultats['Orientes']['Social'][$month]++;
												$flagOrienteSocial = true;
										}
										//	- dont nbre de pers. SDD orientées Emploi + équivalent en %
										elseif(!empty($testOrient['EMPLOI']) &&  in_array( $result['Typeorient']['id'], $testOrient['EMPLOI'] ) ) {
											$resultats['Orientes']['Emploi'][$month]++;
										}
										//	- dont nbre de pers. SDD orientées Pré pro + équivalent en %
										elseif (!empty($testOrient['PREPRO']) && in_array( $result['Typeorient']['id'], $testOrient['PREPRO'] ) ) {
											$resultats['Orientes']['Prepro'][$month]++;
												$flagOrientePrepro = true;
										}

										//	- dont nbre de pers. SDD orientées OA + équivalent en %
										if( $result['Structurereferente']['type_struct_stats'] == 'oa' ) {
											$resultats['Orientes']['OA'][$month]++;
										}
										//	- dont nbre de pers. SDD orientées PE + équivalent en %
										if( $result['Structurereferente']['type_struct_stats'] == 'pe' ) {
											$resultats['Orientes']['PE'][$month]++;
											$flagOrientePE = true;
										}
										//	- dont nbre de pers. SDD orientées CD + équivalent en %
										if( $result['Structurereferente']['type_struct_stats'] == 'cd' ) {
											$resultats['Orientes']['CD'][$month]++;
											$flagOrienteCD = true;
										}
									}
								}
							}
						}
						$historiquesPreviousMonth = $result['Historiquedroit'.$month]['etatdosrsa'];
						$historiquesToppersPreviousMonth = $result['Historiquedroit'.$month]['toppersdrodevorsa'];
						/*$rgadrPreviousMonth = $result['Adressefoyer'.$month]['rgadr'];
						$dtemmPreviousMonth = $result['Adressefoyer'.$month]['dtemm'];
						$codeposPreviousMonth = $result['Adresse'.$month]['codepos'];*/

					}
				}
			}
			for($i=0; $i<12; $i++) {
				if($resultats['total'][$i] != 0) {
					$resultats['percentOrientes'][$i] = round( (100 * $resultats['Orientes']['total'][$i] ) / $resultats['total'][$i], 2)  . '%';
					if ( $resultats['percentOrientes'][$i] != 0) {
						$resultats['Orientes']['percentEmploi'][$i] = round( (100 * $resultats['Orientes']['Emploi'][$i] ) / $resultats['Orientes']['total'][$i], 2)  . '%';
						$resultats['Orientes']['percentPrepro'][$i] = round( (100 * $resultats['Orientes']['Prepro'][$i] ) / $resultats['Orientes']['total'][$i], 2)  . '%';
						$resultats['Orientes']['percentSocial'][$i] = round( (100 * $resultats['Orientes']['Social'][$i] ) / $resultats['Orientes']['total'][$i], 2)  . '%';
						$resultats['Orientes']['percentPE'][$i] = round( (100 * $resultats['Orientes']['PE'][$i] ) / $resultats['Orientes']['total'][$i], 2)  . '%';
						$resultats['Orientes']['percentCD'][$i] = round( (100 * $resultats['Orientes']['CD'][$i] ) / $resultats['Orientes']['total'][$i], 2)  . '%';
						$resultats['Orientes']['percentOA'][$i] = round( (100 * $resultats['Orientes']['OA'][$i] ) / $resultats['Orientes']['total'][$i], 2)  . '%';
					}
				}
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
		public function getIndicateursTableauA2AV2( array $search ) {
			$Foyer = ClassRegistry::init( 'Foyer' );
			$Historiquedroit = ClassRegistry::init( 'Historiquedroit' );
			$annee = Hash::get( $search, 'Search.annee' );
			$testOrient = $this->_getTypeOrientation();
			$results = array();

			// Query de base
			$query = $this->_getQueryTableau_a2av2 ($search, $annee);
			$results = $Foyer->find('all', $query);

			// Initialisation tableau de résultats
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
			
			//Initialisation des valeurs fixes :
			$jourDebMois = Configure::read('PlanPauvrete.Stats.Moisprecedent.deb');
			$joursMois = Configure::read('PlanPauvrete.Stats.Orientation.Jours');

			//Pour chaque résultat
			foreach($results as $result) {
				$useHistoriquedroit = (boolean)Configure::read( 'Statistiqueplanpauvrete.useHistoriquedroit' );
				if ( $useHistoriquedroit ){
					$historiquesPreviousMonth = $result['Historiquedroit12']['etatdosrsa'];
					$historiquesToppersPreviousMonth = $result['Historiquedroit12']['toppersdrodevorsa'];

					$flagOrienteCD = false;
					$flagOrientePrepro = false;
					$flagOrienteSocial = false;
					//Initialisation des valeurs :
					if( $result['Structurereferente']['type_struct_stats'] == 'cd' ) {
						$flagOrienteCD = true;
						//Pers. orientées Social
						if(!empty($testOrient['SOCIAL']) && in_array($result['Typeorient']['id'], $testOrient['SOCIAL'] ) ) {
								$flagOrienteSocial = true;
						}
						//Pers. orientées Pré pro
						elseif (!empty($testOrient['PREPRO']) && in_array( $result['Typeorient']['id'], $testOrient['PREPRO'] ) ) {
								$flagOrientePrepro = true;
						}
					}

					for( $month=0; $month<12; $month++ ) {
						//Si La personne est un nouvel entrant et
						$flagOrienteSocial = true;
						if (
							($result['Historiquedroit'.$month]['etatdosrsa'] == 2
							&& $result['Historiquedroit'.$month]['toppersdrodevorsa'] == 1)
							&& ( $historiquesPreviousMonth != 2
							|| $historiquesToppersPreviousMonth != 1 )
						) {
							//Qu'on as une date d'orientation valide
							if ( $result['Orientstruct']['date_valid'] != null){
								$tmpDate = $this->_getDateString( $annee, $month, $jourDebMois, 2 );

								if (//Si l'orientation n'est pas inférieur au changement de droits
									strtotime($result['Orientstruct']['date_valid']) >= strtotime($tmpDate) 
									//Nombre de nouveaux entrants orientés orientées CD
									&& $flagOrienteCD 
								){
									$resultats['Orientes_CD'][$month]++;

									//- Nombre de nouveaux entrants orientés CD avec un 1er rendez-vous fixé suite à une orientation CD
									/*if ( $result['Rendezvous']['daterdv'] != null
										&& strtotime($result['Rendezvous']['daterdv']) >= strtotime($tmpDate)
									){
										if ( $flagOrientePrepro ) {
											//	- dont nbre de 1er rdv fixés suite à une orientation Pré pro
											$resultats['Orientes']['RDV_Prepro'][$month] ++;
										}
										if ($flagOrienteSocial) {
											//	- dont nbre de 1er rdv fixés suite à une orientation Sociale
											$resultats['Orientes']['RDV_Social'][$month] ++;
										}
									}*/

									//On calcul la différence entre la date de reception et la date d'orientation
									$diff = abs(strtotime($tmpDate) - strtotime($result['Orientstruct']['date_valid']));
									//Si la différence est de moins de 30 jours
									if ( (60*60*24*$joursMois) > $diff  ) {
										$resultats['Orientes1m'][$month]++;
									}
									/*if ( $result['Orientstruct']['date_valid'] < $result['Rendezvous']['daterdv'] ){
										$diff = abs(strtotime($result['Rendezvous']['daterdv']) - strtotime($result['Orientstruct']['date_valid']));
										if ( (60*60*24*15) > $diff  ) {
											//- Nombre de 1er rendez-vous fixé suite à une orientation CD dans un délai de 15 jours
											$resultats['Orientes15j']['RDV'][$month] ++;
											if ( $flagOrientePrepro ) {
												//	- dont nbre de 1er rdv fixés suite à une orientation Pré pro
												$resultats['Orientes15j']['RDV_Prepro'][$month] ++;
											}
											if ($flagOrienteSocial) {
												//	- dont nbre de 1er rdv fixés suite à une orientation Sociale
												$resultats['Orientes15j']['RDV_Social'][$month] ++;
											}
										}
									}*/

								}
							}
							$historiquesPreviousMonth = $result['Historiquedroit'.$month]['etatdosrsa'];
							$historiquesToppersPreviousMonth = $result['Historiquedroit'.$month]['toppersdrodevorsa'];
						}
					}
				}
			}
			for($i=0; $i<12; $i++) {
				if($resultats['Orientes_CD'][$i] != 0) {
					$resultats['Taux'][$i] = round( (100 * $resultats['Orientes1m'][$i] ) / $resultats['Orientes_CD'][$i], 2)  . '%';
				}
			}
			return $resultats;
		}

		########################################################################################################################
		########################################################################################################################
		

	}
?>