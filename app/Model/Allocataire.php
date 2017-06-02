<?php
	/**
	 * Code source de la classe Allocataire.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractSearch', 'Model/Abstractclass' );
	App::uses( 'ConfigurableQueryFields', 'ConfigurableQuery.Utility' );

	/**
	 * La classe Allocataire comporte des méthodes de base pour les recherches,
	 * les formaulaires, les exportcsv, .. liées à des allocataires du RSA.
	 *
	 * @package app.Model
	 */
	class Allocataire extends AbstractSearch
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'Allocataire';

		/**
		 * Ce modèle n'est pas lié à une table.
		 *
		 * @var boolean
		 */
		public $useTable = false;

		/**
		 * Modèles utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array( 'Personne' );

		/**
		 * Behaviors utilisés par le modèle.
		 *
		 * @var array
		 */
		public $actsAs = array(
			'Conditionnable'
		);

		/**
		 * Retourne le querydata de base à utiliser dans le moteur de recherche.
		 *
		 * @todo à partir de Personne ou de Dossier, sachant que par défaut ça doit être Dossier
		 *
		 * @param array $types Les types de jointure alias => type. Concernant la
		 *	clé Prestation (natprest RSA), en cas d'INNER (join), les conditions
		 *	sur rolepers DEM ou CJT seront appliquées dans les conditions, sinon
		 *  (LEFT OUTER) dans la jointure (voir aussi le paramètre $forceBeneficiaire).
		 * @param string $baseModelName Le modèle de base de la requête (Personne,
		 *	Dossier ou un modèle lié à Personne)
		 * @param boolean $forceBeneficiaire Si vrai, alors le rôle de la personne
		 *	sera limité à DEM ou CJT (voir aussi la clé Prestation dans le paramètre
		 *	$types).
		 *	@fixme tests unitaire
		 * @return array
		 */
		public function searchQuery( array $types = array(), $baseModelName = 'Personne', $forceBeneficiaire = true ) {
			$types += array(
				'Calculdroitrsa' => 'LEFT OUTER',
				'Foyer' => 'INNER',
				'Personne' => 'INNER',
				'Prestation' => 'INNER',
				'Adressefoyer' => 'INNER',
				'Dossier' => 'INNER',
				'Adresse' => 'INNER',
				'Situationdossierrsa' => 'INNER',
				'Detaildroitrsa' => 'INNER',
			);

			$cacheKey = Inflector::underscore( $this->Personne->useDbConfig ).'_'.Inflector::underscore( $this->alias ).'_'.Inflector::underscore( __FUNCTION__ ).'_'.sha1( serialize( $types ).'_'. ( $forceBeneficiaire ? '1' : '0' ) ).'_'.$baseModelName;
			$query = Cache::read( $cacheKey );

			if( $query === false ) {
				if( $baseModelName === 'Personne' ) {
					$joins = array(
						$this->Personne->join( 'Foyer', array( 'type' => $types['Foyer'] ) ),
						$this->Personne->Foyer->join( 'Dossier', array( 'type' => $types['Dossier'] ) )
					);
				}
				else if( $baseModelName === 'Dossier' ) {
					$joins = array(
						$this->Personne->Foyer->Dossier->join( 'Foyer', array( 'type' => $types['Foyer'] ) ),
						$this->Personne->Foyer->join( 'Personne', array( 'type' => $types['Personne'] ) )
					);
				}
				else {
					$joins = $this->_findBaseModelJoin($types, $baseModelName);
				}

				$query = array(
					'fields' => ConfigurableQueryFields::getModelsFields(
						array(
							$this->Personne,
							$this->Personne->Calculdroitrsa,
							$this->Personne->Foyer,
							$this->Personne->Prestation,
							$this->Personne->Foyer->Adressefoyer,
							$this->Personne->Foyer->Adressefoyer->Adresse,
							$this->Personne->Foyer->Dossier,
							$this->Personne->Foyer->Dossier->Situationdossierrsa,
							$this->Personne->Foyer->Dossier->Detaildroitrsa
						)
					),
					'joins' => array_merge(
						$joins,
						array(
							$this->Personne->join( 'Calculdroitrsa', array( 'type' => $types['Calculdroitrsa'] ) ),
							$this->Personne->join(
								'Prestation',
								array(
									'type' => $types['Prestation'],
									'conditions' => ( $types['Prestation'] === 'INNER' && $forceBeneficiaire )
										? array( 'Prestation.rolepers' => array( 'DEM', 'CJT' ) )
										: array()
								)
							),
							$this->Personne->Foyer->join(
								'Adressefoyer',
								array(
									'type' => $types['Adressefoyer'],
									'conditions' => array(
										'Adressefoyer.id IN( '.$this->Personne->Foyer->Adressefoyer->sqDerniereRgadr01( 'Foyer.id' ).' )'
									)
								)
							),
							$this->Personne->Foyer->Adressefoyer->join( 'Adresse', array( 'type' => $types['Adresse'] ) ),
							$this->Personne->Foyer->Dossier->join( 'Situationdossierrsa', array( 'type' => $types['Situationdossierrsa'] ) ),
							$this->Personne->Foyer->Dossier->join( 'Detaildroitrsa', array( 'type' => $types['Detaildroitrsa'] ) ),
						)
					),
					'contain' => false,
					'conditions' => ( $types['Prestation'] !== 'INNER' && $forceBeneficiaire )
						? array( 'OR' => array(
							'Prestation.rolepers' => array( 'DEM', 'CJT' ),
							'Prestation.id IS NULL'
						) )
						: array()
				);

				// Ajout des cantons
				$Adresse =& $this->Personne->Foyer->Adressefoyer->Adresse;
				if (Configure::read( 'CG.cantons' )) {
					$query['fields']['Canton.canton'] = 'Canton.canton';

					if (Configure::read('Canton.useAdresseCanton')) {
						$query['joins'][] = $Adresse->join('AdresseCanton', array('type' => 'LEFT OUTER'));
						$query['joins'][] = $Adresse->AdresseCanton->join('Canton', array('type' => 'LEFT OUTER'));
					} else {
						$query['joins'][] = $Adresse->AdresseCanton->Canton->joinAdresse();
					}
				}

				$query = $this->Personne->PersonneReferent->completeSearchQueryReferentParcours( $query );

				// Ajout de champs virtuels spécifiques pour les départements
				$departement = (int)Configure::read( 'Cg.departement' );
				if( $departement === 58 ) {
					$sql = $this->Personne->WebrsaPersonne->vfEtapeDossierOrientation58();
					$query['fields']['Personne.etat_dossier_orientation'] = "{$sql} AS \"Personne__etat_dossier_orientation\"";
				}

				// Enregistrement dans le cache
				Cache::write( $cacheKey, $query );
			}

			return $query;
		}

		protected function _findBaseModelJoin($types, $baseModelName) {
			// Modele lié à Personne
			if (isset($this->Personne->{$baseModelName})) {
				$joins = array(
					$this->Personne->{$baseModelName}->join( 'Personne', array( 'type' => $types['Personne'] ) ),
					$this->Personne->join( 'Foyer', array( 'type' => $types['Foyer'] ) ),
					$this->Personne->Foyer->join( 'Dossier', array( 'type' => $types['Dossier'] ) )
				);
			}

			// Modele lié à Dossier
			elseif (isset($this->Personne->Foyer->Dossier->{$baseModelName})) {
				$joins = array(
					$this->Personne->Foyer->Dossier->{$baseModelName}->join( 'Dossier', array( 'type' => $types['Dossier'] ) ),
					$this->Personne->Foyer->Dossier->join( 'Foyer', array( 'type' => $types['Foyer'] ) ),
					$this->Personne->Foyer->join( 'Personne', array( 'type' => $types['Personne'] ) )
				);
			}

			// Modele lié à Foyer
			elseif (isset($this->Personne->Foyer->{$baseModelName})) {
				$joins = array(
					$this->Personne->Foyer->{$baseModelName}->join( 'Foyer', array( 'type' => $types['Foyer'] ) ),
					$this->Personne->Foyer->join( 'Dossier', array( 'type' => $types['Dossier'] ) ),
					$this->Personne->Foyer->join( 'Personne', array( 'type' => $types['Personne'] ) )
				);
			}

			else {
				debug("Aucuns liens depuis Personne/Foyer/Dossier vers {$baseModelName} !");
				throw new Exception("Aucuns liens depuis Personne/Foyer/Dossier vers {$baseModelName} !", 500);
			}


			return $joins;
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
			$query['conditions'] = $this->conditionsAdresse( $query['conditions'], $search );
			$query['conditions'] = $this->conditionsPersonneFoyerDossier( $query['conditions'], $search );
			$query['conditions'] = $this->conditionsDernierDossierAllocataire( $query['conditions'], $search );

			$query = $this->Personne->PersonneReferent->completeSearchConditionsReferentParcours( $query, $search );

			// Ajout de conditions spécifiques au département connecté
			$departement = (int)Configure::read( 'Cg.departement' );
			if( $departement === 58 ) {
				$query = $this->Personne->WebrsaPersonne->completeQueryVfEtapeDossierOrientation58( $query, $search );
			} elseif ($departement === 66) {
				if (hash::get($search, 'Personne.dtnai_month')) {
					$query['conditions'][] = array('Personne.dtnai_month' => hash::get($search, 'Personne.dtnai_month'));
				}
				if (hash::get($search, 'Personne.dtnai_year')) {
					$query['conditions'][] = array('Personne.dtnai_year' => hash::get($search, 'Personne.dtnai_year'));
				}
			}

			return $query;
		}

		/**
		 * Retourne les options nécessaires au formulaire de recherche, aux
		 * impressions, ...
		 *
		 * @param array $params
		 * @return array
		 */
		public function options( array $params = array() ) {
			$Option = ClassRegistry::init( 'Option' );

			$options = Hash::merge(
				$this->Personne->Foyer->Dossier->enums(),
				$this->Personne->Foyer->enums(),
				array(
					'Adresse' => array(
						'pays' => ClassRegistry::init('Adresse')->enum('pays'),
						'typeres' => ClassRegistry::init('Adresse')->enum('typeres')
					),
					'Adressefoyer' => array(
						'rgadr' => ClassRegistry::init('Adresse')->enum('rgadr'),
						'typeadr' => ClassRegistry::init('Adressefoyer')->enum('typeadr'),
					),
					'Calculdroitrsa' => array(
						'toppersdrodevorsa' => $Option->toppersdrodevorsa(true),
					),
					'Detailcalculdroitrsa' => array(
						'natpf' => $this->Personne->Foyer->Dossier->Detaildroitrsa->Detailcalculdroitrsa->enum('natpf'),
					),
					'Detaildroitrsa' => array(
						'oridemrsa' => ClassRegistry::init('Detaildroitrsa')->enum('oridemrsa'),
						'topfoydrodevorsa' => ClassRegistry::init('Detaildroitrsa')->enum('topfoydrodevorsa'),
						'topsansdomfixe' => ClassRegistry::init('Detaildroitrsa')->enum('topsansdomfixe'),
					),
					// FIXME: dans les enums du dossier
					'Dossier' => array(
						'numorg' => $this->Personne->Foyer->Dossier->enum( 'numorg' ),
						'typeparte' => ClassRegistry::init('Dossier')->enum('typeparte'),
					),
					'Personne' => array(
						'pieecpres' => ClassRegistry::init('Personne')->enum('pieecpres'),
						'qual' => $Option->qual(),
						'sexe' => $Option->sexe(),
						'typedtnai' => ClassRegistry::init('Personne')->enum('typedtnai'),
					),
					'Prestation' => array(
						'rolepers' => ClassRegistry::init('Prestation')->enum('rolepers'),
					),
					'Referentparcours' => array(
						'qual' => $Option->qual(),
					),
					'Situationdossierrsa' => array(
						'etatdosrsa' => $this->Personne->Foyer->Dossier->Situationdossierrsa->enum( 'etatdosrsa' ),
						'moticlorsa' => $this->Personne->Foyer->Dossier->Situationdossierrsa->enum( 'moticlorsa' ),
					),
				)
			);

			return $options;
		}

		/**
		 * Permet de test l'ajout de conditions supplémentaires à la requête de
		 * base.
		 * On part du principe que l'attribut forceVirtualFields du modèle Personne
		 * (sur lequel on fait le find) est (passé) à true.
		 *
		 * @param string|array $conditions
		 * @return array
		 */
		public function testSearchConditions( $conditions = null ) {
			$query = $this->searchQuery();
			$query['conditions'][] = $conditions;

			$this->Personne->forceVirtualFields = true;

			try {
				$this->Personne->find( 'first', $query );
				$return = array(
					'success' => true,
					'message' => null,
					'sql' => $this->Personne->sq( $query )
				);
			} catch( PDOException $Exception ) {
				$return = array(
					'success' => false,
					'message' => $Exception->getMessage(),
					'sql' => $this->Personne->sq( $query )
				);
			}

			return $return;
		}
	}
?>