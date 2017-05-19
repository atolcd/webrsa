<?php
	/**
	 * Code source de la classe WebrsaRechercheContratinsertion.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaRecherche', 'Model/Abstractclass' );
	App::uses( 'ConfigurableQueryFields', 'ConfigurableQuery.Utility' );

	/**
	 * La classe WebrsaRechercheContratinsertion ...
	 *
	 * @package app.Model
	 */
	class WebrsaRechercheContratinsertion extends AbstractWebrsaRecherche
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaRechercheContratinsertion';

		/**
		 * Modèles utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array( 'Allocataire', 'Contratinsertion', 'Canton', 'Option' );

		/**
		 * Liste des clés de configuration utilisées par le moteur de recherche,
		 * pour vérification du paramétrage.
		 *
		 * @see checkParametrage()
		 *
		 * @var array
		 */
		public $keysRecherche = array(
			'ConfigurableQueryContratsinsertion.search.fields',
			'ConfigurableQueryContratsinsertion.search.innerTable',
			'ConfigurableQueryContratsinsertion.exportcsv'
		);

		/**
		 * Retourne le querydata de base, en fonction du département, à utiliser
		 * dans le moteur de recherche.
		 *
		 * @param array $types Les types de jointure alias => type
		 * @return array
		 */
		public function searchQuery( array $types = array() ) {
			$departement = (int)Configure::read( 'Cg.departement' );
			$types += array(
				'Calculdroitrsa' => 'LEFT OUTER',
				'Foyer' => 'INNER',
				'Prestation' => 'LEFT OUTER',
				'Adressefoyer' => 'LEFT OUTER',
				'Dossier' => 'INNER',
				'Adresse' => 'LEFT OUTER',
				'Situationdossierrsa' => 'INNER',
				'Detaildroitrsa' => 'LEFT OUTER',
				'PersonneReferent' => 'LEFT OUTER',
				'Personne' => 'INNER',
				'Typeorient' => 'LEFT OUTER',
				'Structurereferente' => 'LEFT OUTER',
				'Structurereferenteparcours' => 'LEFT OUTER',
				'Orientstruct' => 'LEFT OUTER',
				'Referent' => 'LEFT OUTER',
				'Dernierreferent' => 'LEFT OUTER',
			);

			$cacheKey = Inflector::underscore( $this->useDbConfig ).'_'.Inflector::underscore( $this->alias ).'_'.Inflector::underscore( __FUNCTION__ ).'_'.sha1( serialize( $types ) );
			$query = Cache::read( $cacheKey );

			if( $query === false ) {
				$query = $this->Allocataire->searchQuery( $types, 'Contratinsertion', false );

				// 1. Ajout des champs supplémentaires
				$query['fields'] = array_merge(
					$query['fields'],
					ConfigurableQueryFields::getModelsFields(
						array(
							$this->Contratinsertion,
							$this->Contratinsertion->Referent,
							$this->Contratinsertion->Personne->PersonneReferent,
							$this->Contratinsertion->Structurereferente,
							$this->Contratinsertion->Personne->Orientstruct->Typeorient
						)
					),
					// Champs nécessaires au traitement de la search
					array(
						'Contratinsertion.id',
						'Contratinsertion.personne_id',
						'Contratinsertion.dd_ci',
					)
				);

				// 2. Jointure
				$query['joins'] = array_merge(
					$query['joins'],
					array(
						$this->Contratinsertion->join( 'Structurereferente', array( 'type' => $types['Structurereferente'] ) ),
						$this->Contratinsertion->join( 'Referent', array( 'type' => $types['Referent'] ) ),
						$this->Contratinsertion->Referent->join( 'Dernierreferent', array( 'type' => $types['Dernierreferent'] ) ),
						$this->Contratinsertion->Personne->join(
							'Orientstruct',
							array(
								'type' => $types['Orientstruct'],
								'conditions' => array(
									'Orientstruct.id IN ( '.$this->Contratinsertion->Personne->Orientstruct->WebrsaOrientstruct->sqDerniere().' )'
								)
							)
						),
						$this->Contratinsertion->Personne->Orientstruct->join( 'Typeorient', array( 'type' => $types['Typeorient'] ) )
					)
				);

				// 5. Ajout de champs et de jointures spécifiques au département connecté
				if( $departement === 93 ) {
					$query['fields'] = array_merge(
						$query['fields'],
						ConfigurableQueryFields::getModelsFields(
							array(
								$this->Contratinsertion->Cer93
							)
						)
					);

					$query['joins'][] = $this->Contratinsertion->join( 'Cer93', array( 'type' => 'INNER' ) );
				}

				Cache::write( $cacheKey, $query );
			}

			return $query;
		}

		/**
		 * Complète les conditions du querydata avec le contenu des filtres de
		 * recherche.
		 *
		 * @param array $query
		 * @param array $search
		 * @return array
		 */
		public function searchConditions( array $query, array $search ) {
			$departement = (int)Configure::read( 'Cg.departement' );
			$query = $this->Allocataire->searchConditions( $query, $search );

			/**
			 * Generateur de conditions
			 */
			$paths = array(
				'Contratinsertion.forme_ci',
				'Contratinsertion.positioncer',
				'Contratinsertion.structurereferente_id',
				'Personne.etat_dossier_orientation',
				'Dernierreferent.dernierreferent_id',
			);

			// Fils de dependantSelect
			$pathsToExplode = array(
				'Contratinsertion.referent_id',
			);

			$pathsDate = array(
				'Contratinsertion.created',
				'Contratinsertion.datevalidation_ci',
				'Contratinsertion.dd_ci',
				'Contratinsertion.df_ci',
			);

			foreach( $paths as $path ) {
				$value = Hash::get( $search, $path );
				if( $value !== null && $value !== '' ) {
					$query['conditions'][$path] = $value;
				}
			}

			foreach( $pathsToExplode as $path ) {
				$value = Hash::get( $search, $path );
				if( $value !== null && $value !== '' && strpos($value, '_') > 0 ) {
					list(,$value) = explode('_', $value);
					$query['conditions'][$path] = $value;
				}
			}

			$query['conditions'] = $this->conditionsDates( $query['conditions'], $search, $pathsDate );

			/**
			 * Conditions spéciales
			 */
			if( Hash::get( $search, 'Contratinsertion.dernier' ) ) {
				$query['conditions'][] = array(
					"Contratinsertion.id IN (SELECT
						contratsinsertion.id
					FROM
						contratsinsertion
						WHERE
							contratsinsertion.personne_id = Contratinsertion.personne_id
						ORDER BY
							contratsinsertion.dd_ci DESC,
							contratsinsertion.id DESC
						LIMIT 1)"
				);
			}
			if( Hash::get( $search, 'Contratinsertion.periode_validite' ) ) {
				$debutValidite = date_cakephp_to_sql($search['Contratinsertion']['periode_validite_from']);
				$finValidite = date_cakephp_to_sql($search['Contratinsertion']['periode_validite_to']);
				// INFO: OVERLAPS ne prend les bornes (lorsque dd et df sont les mêmes)
				//(StartA <= EndB) and (EndA >= StartB)
				// @source http://stackoverflow.com/a/325964
				$query['conditions'][] = array(
					'Contratinsertion.decision_ci' => 'V',
					'Contratinsertion.dd_ci <=' => $finValidite,
					'Contratinsertion.df_ci >=' => $debutValidite
				);
			}
			if( Hash::get( $search, 'Contratinsertion.arriveaecheance' ) ) {
				$query['conditions'][] = 'Contratinsertion.id IN (
					SELECT
						contratsinsertion.id
					FROM
						contratsinsertion
						WHERE
							date_trunc( \'day\', contratsinsertion.df_ci ) <= DATE( NOW() )
 				)';
			}
			if( Hash::get( $search, 'Contratinsertion.echeanceproche' ) ) {
				$query['conditions'][] = 'Contratinsertion.id IN (
					SELECT
						contratsinsertion.id
					FROM
						contratsinsertion
						WHERE
							date_trunc( \'day\', contratsinsertion.df_ci ) >= DATE( NOW() )
							AND date_trunc( \'day\', contratsinsertion.df_ci ) <= ( DATE( NOW() ) + INTERVAL \''.Configure::read( 'Criterecer.delaiavanteecheance' ).'\' )
 				)';
			}

			if( Hash::get( $search, 'Contratinsertion.istacitereconduction' ) ) {
				$query['conditions'][] = 'Contratinsertion.datetacitereconduction IS NULL';
			}

			// Filtre par durée du contrat, avec des subtilités pour les CG 58 et 93
			$duree_engag = preg_replace( '/^[^0-9]*([0-9]+)[^0-9]*$/', '\1', Hash::get( $search, 'Contratinsertion.duree_engag' ) );
			if( !empty( $duree_engag ) ) {
				if( $departement !== 93 ) {
					$query['conditions']['Contratinsertion.duree_engag'] = $duree_engag;
				}
				else {
					$durees_engags = $this->Option->duree_engag();
					$query['conditions'][] = array(
						'OR' => array(
							'Contratinsertion.duree_engag' => $duree_engag,
							'Cer93.duree' => str_replace( ' mois', '', $durees_engags[$duree_engag] ),
						)
					);
				}
			}

			// Doit-on exclure un type d'orientation ?
			$value = Hash::get( $search, 'Orientstruct.not_typeorient_id' );
			if( !empty( $value ) ) {
				$query['conditions'][] = array(
					'OR' => array(
						array(
							'Typeorient.parentid IS NULL',
							'NOT' => array(
								'Typeorient.id' => $value
							)
						),
						array(
							'Typeorient.parentid IS NOT NULL',
							'NOT' => array(
								'Typeorient.parentid' => $value
							)
						)
					)
				);
			}

			// Type d'orientation, ou non orienté
			$typeorient_id = Hash::get( $search, 'Orientstruct.typeorient_id' );
			if( !empty( $typeorient_id ) ) {
				$query['conditions'][] = array(
					'Orientstruct.statut_orient' => 'Orienté',
					'Orientstruct.typeorient_id' => $typeorient_id
				);
			}
			else if( $typeorient_id != '' && $typeorient_id == 0 ) {
				$query['conditions'][] = 'Orientstruct.id IS NULL';
			}

			// Statut du contrat
			if( $departement === 93 ) {
				$positioncer = Hash::get( $search, 'Cer93.positioncer' );
				if( !empty( $positioncer ) ) {
					$query['conditions']['Cer93.positioncer'] = $positioncer;
				}
			}
			else {
				$decision_ci = Hash::get( $search, 'Contratinsertion.decision_ci' );
				if( !empty( $decision_ci ) ) {
					$query['conditions'][] = 'Contratinsertion.decision_ci = \''.Sanitize::clean( $decision_ci, array( 'encode' => false ) ).'\'';
				}

				// ...
				if( !empty( $positioncer ) ) {
					$query['conditions'][] = 'Contratinsertion.positioncer = \''.Sanitize::clean( $positioncer, array( 'encode' => false ) ).'\'';
				}
			}

			if( $departement === 93 ) {
				// 1. Filtre par expérience professionnelle significative: on veut les valeurs SSI elles ont été sélectionnées par le filtre
				$expprocer93 = Hash::filter( (array)Hash::get( $search, 'Expprocer93' ) );
				if( !empty( $expprocer93 ) ) {
					$query['joins'][] = $this->Contratinsertion->Cer93->join( 'Expprocer93', array( 'type' => 'LEFT OUTER' ) );

					// Partie filtre
					$conditions = array(
						'Expprocer93.cer93_id = Cer93.id'
					);
					foreach( $expprocer93 as $fieldName => $value ) {
						$value = suffix( $value );
						if( !empty( $value ) ) {
							if( in_array( $fieldName, array( 'metierexerce_id', 'secteuracti_id' ) ) ) {
								$conditions["Expprocer93.{$fieldName}"] = $value;
							}
							else {
								$conditions["Entreeromev3.{$fieldName}"] = $value;
							}
						}
					}

					// On veut éviter d'avoir des doublons de lignes de résultats
					$querySq = array(
						'alias' => 'Expprocer93',
						'fields' => array( 'Expprocer93.id' ),
						'contain' => false,
						'conditions' => $conditions,
						'joins' => array(
							$this->Contratinsertion->Cer93->Expprocer93->join( 'Entreeromev3', array( 'type' => 'LEFT OUTER' ) ),
						),
						'limit' => 1
					);
					$sql = $this->Contratinsertion->Cer93->Expprocer93->sq(
						array_words_replace(
							$querySq,
							array( 'Expprocer93' => 'expsproscers93', 'Entreeromev3'  => 'entreesromesv3' )
						)
					);
					$query['conditions'][] = "Expprocer93.id IN ( {$sql} )";

					// Ajout des champs et des jointures (aliasées) dans la requête principale
					$suffix = 'exppro';
					$aliases = array(
						// INSEE
						'Metierexerce' => "Metierexerce{$suffix}",
						'Secteuracti' => "Secteuracti{$suffix}",
						// ROME v.3
						'Entreeromev3' => "Entree{$suffix}",
						'Familleromev3' => "Famille{$suffix}",
						'Domaineromev3' => "Domaine{$suffix}",
						'Metierromev3' => "Metier{$suffix}",
						'Appellationromev3' => "Appellation{$suffix}"
					);
					$query['joins'][] = array_words_replace(
						$this->Contratinsertion->Cer93->Expprocer93->join( 'Entreeromev3', array( 'type' => 'LEFT OUTER' ) ),
						$aliases
					);
					$query = $this->Contratinsertion->Cer93->Expprocer93->Entreeromev3->getCompletedRomev3Joins( $query, 'LEFT OUTER', $aliases );

					// Ajout des champs et des jointures INSEE
					$query['fields']["Metierexerce{$suffix}.name"] = "Metierexerce{$suffix}.name";
					$query['joins'][] = array_words_replace(
						$this->Contratinsertion->Cer93->Expprocer93->join( 'Metierexerce', array( 'type' => 'LEFT OUTER' ) ),
						$aliases
					);

					$query['fields']["Secteuracti{$suffix}.name"] = "Secteuracti{$suffix}.name";
					$query['joins'][] = array_words_replace(
						$this->Contratinsertion->Cer93->Expprocer93->join( 'Secteuracti', array( 'type' => 'LEFT OUTER' ) ),
						$aliases
					);
				}

				// 2. Filtre par emploi trouvé
				// 2.1 Codes ROME v.3
				$query = $this->Contratinsertion->Cer93->getCompletedRomev3Joins( $query, 'emptrouv' );
				foreach( $this->Contratinsertion->Cer93->Emptrouvromev3->romev3Fields as $fieldName ) {
					$path = "Emptrouvromev3.{$fieldName}";
					$value = suffix( Hash::get( $search, $path ) );
					if( !empty( $value ) ) {
						$query['conditions'][$path] = $value;
					}
				}

				// 2.2 Codes INSEE -> TODO: aliaser Metiertrouve ?
				$query['fields']['Metierexerce.name'] = 'Metierexerce.name';
				$query['joins'][] = $this->Contratinsertion->Cer93->join( 'Metierexerce', array( 'type' => 'LEFT OUTER' ) );
				$query['fields']['Secteuracti.name'] = 'Secteuracti.name';
				$query['joins'][] = $this->Contratinsertion->Cer93->join( 'Secteuracti', array( 'type' => 'LEFT OUTER' ) );

				foreach( array( 'metierexerce_id', 'secteuracti_id' ) as $fieldName ) {
					$path = "Cer93.{$fieldName}";
					$value = suffix( Hash::get( $search, $path ) );
					if( !empty( $value ) ) {
						$query['conditions'][$path] = $value;
					}
				}

				// 3. Filtre par "Votre contrat porte sur"
				// On veut les valeurs SSI elles ont été sélectionnées par le filtre
				$sujetcer93_id = Hash::get( $search, 'Cer93Sujetcer93.sujetcer93_id' );
				$soussujetcer93_id = suffix( Hash::get( $search, 'Cer93Sujetcer93.soussujetcer93_id' ) );
				$valeurparsoussujetcer93_id = suffix( Hash::get( $search, 'Cer93Sujetcer93.valeurparsoussujetcer93_id' ) );

				if( !empty( $sujetcer93_id ) || !empty( $soussujetcer93_id ) || !empty( $valeurparsoussujetcer93_id ) ) {
					$query['joins'][] = $this->Contratinsertion->Cer93->join( 'Cer93Sujetcer93', array( 'type' => 'INNER' ) );
					$query['joins'][] = $this->Contratinsertion->Cer93->Cer93Sujetcer93->join( 'Sujetcer93', array( 'type' => 'LEFT OUTER' ) );
					$query['joins'][] = $this->Contratinsertion->Cer93->Cer93Sujetcer93->join( 'Soussujetcer93', array( 'type' => 'LEFT OUTER' ) );
					$query['joins'][] = $this->Contratinsertion->Cer93->Cer93Sujetcer93->join( 'Valeurparsoussujetcer93', array( 'type' => 'LEFT OUTER' ) );

					$fields = array(
						'Cer93Sujetcer93.commentaireautre',
						'Cer93Sujetcer93.autrevaleur',
						'Cer93Sujetcer93.autresoussujet',
						'Sujetcer93.name',
						'Soussujetcer93.name',
						'Valeurparsoussujetcer93.name',
					);
					$query['fields'] = array_merge( $query['fields'], array_combine( $fields, $fields ) );

					$conditions = array( 'cers93_sujetscers93.cer93_id = Cer93.id' );

					foreach( array( 'sujetcer93_id', 'soussujetcer93_id', 'valeurparsoussujetcer93_id' ) as $field ) {
						$value = $$field;
						if( !empty( $value ) ) {
							$conditions["cers93_sujetscers93.{$field}"] = $value;
						}
					}

					// On veut éviter d'avoir des doublons de lignes de résultats
					$sql = $this->Contratinsertion->Cer93->Cer93Sujetcer93->sq(
						array(
							'alias' => 'cers93_sujetscers93',
							'fields' => array( 'cers93_sujetscers93.id' ),
							'contain' => false,
							'conditions' => $conditions,
							'limit' => 1
						)
					);
					$query['conditions'][] = "Cer93Sujetcer93.id IN ( {$sql} )";
				}

				// Votre contrat porte sur l'emploi
				$query = $this->Contratinsertion->Cer93->getCompletedRomev3Joins( $query, 'sujet' );
				foreach( $this->Contratinsertion->Cer93->Sujetromev3->romev3Fields as $fieldName ) {
					$path = "Sujetromev3.{$fieldName}";
					$value = suffix( Hash::get( $search, $path ) );
					if( !empty( $value ) ) {
						$query['conditions'][$path] = $value;
					}
				}
			}

			// Condition sur le projet de ville territorial de la structure de l'orientation
			$query['conditions'] = $this->conditionCommunautesr(
				$query['conditions'],
				$search,
				array( 'Contratinsertion.communautesr_id' => 'Contratinsertion.structurereferente_id' )
			);

			return $query;
		}

		/**
		 * Surcharge de la méthode checkParametrage() permettant d'ajouter certains
		 * champs spéciaux aux champs disponibles.
		 *
		 * @deprecated since 3.0.00
		 *
		 * @param array $params Paramètres supplémentaires (clé 'query' possible)
		 * @return array
		 */
		public function checkParametrage( array $params = array() ) {
			$departement = (int)Configure::read( 'Cg.departement' );
			if( $departement === 93 ) {
				$search = array(
					'Expprocer93' => array(
						'cer93_id' => 1
					),
					'Cer93Sujetcer93' => array(
						'sujetcer93_id' => 1
					)
				);
			}
			else {
				$search = array();
			}

			$query = $this->search( $search );

			return parent::checkParametrage( compact( 'query' ) );
		}

		/**
		 * Retourne la liste des clés de configuration pour lesquelles il faut
		 * vérifier la syntaxe de l'intervalle PostgreSQL.
		 *
		 * @return array
		 */
		public function checkPostgresqlIntervals() {
			$keys = array( 'Criterecer.delaiavanteecheance' );
			return $this->_checkPostgresqlIntervals( $keys );
		}
	}
?>