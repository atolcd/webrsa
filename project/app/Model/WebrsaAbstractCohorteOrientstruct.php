<?php
	/**
	 * Code source de la classe WebrsaAbstractCohorteOrientstruct.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaCohorte', 'Model/Abstractclass' );
	App::uses( 'ConfigurableQueryFields', 'ConfigurableQuery.Utility' );

	/**
	 * La classe WebrsaAbstractCohorteOrientstruct ...
	 *
	 * @package app.Model
	 */
	abstract class WebrsaAbstractCohorteOrientstruct extends AbstractWebrsaCohorte
	{
		/**
		 * Modèles utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array( 'Allocataire', 'Personne' );

		/**
		 * Liste des champs de formulaire à inserer dans le tableau de résultats
		 *
		 * @var array
		 */
		public $cohorteFields = array(
			'Dossier.id' => array( 'type' => 'hidden', 'label' => '', 'hidden' => true ),
			'Orientstruct.id' => array( 'type' => 'hidden', 'label' => '', 'hidden' => true ),
			'Orientstruct.propo_algo' => array( 'type' => 'hidden', 'label' => '', 'hidden' => true ),
			'Orientstruct.origine' => array( 'type' => 'hidden', 'label' => '', 'hidden' => true ),
			'Adresse.numcom' => array( 'type' => 'hidden', 'label' => '', 'hidden' => true ),
			'Orientstruct.personne_id' => array( 'type' => 'hidden', 'label' => '', 'hidden' => true ),
			'Orientstruct.typeorient_id' => array( 'type' => 'select', 'label' => '', 'empty' => true, 'required' => false ),
			'Orientstruct.structurereferente_id' => array( 'type' => 'select', 'label' => '', 'empty' => true, 'required' => false ),
			'Orientstruct.statut_orient' => array( 'type' => 'radio', 'fieldset' => false, 'legend' => false, 'div' => false ),
		);

		/**
		 * Valeurs par défaut pour le préremplissage des champs du formulaire de cohorte
		 * array(
		 *		'Mymodel' => array( 'Myfield' => 'MyValue' ) )
		 * )
		 *
		 * @var array
		 */
		public $defaultValues = array();

		/**
		 * Retourne le querydata de base, en fonction du département, à utiliser
		 * dans le moteur de recherche.
		 *
		 * @param array $types Les types de jointure alias => type
		 * @return array
		 */
		public function searchQuery( array $types = array(), $baseModelName = 'Personne', $forceBeneficiaire = true ) {
			$types += array(
				'Prestation' => 'INNER',
				'Calculdroitrsa' => 'INNER',
				'Dsp' => 'LEFT OUTER',
				'Suiviinstruction' => 'LEFT OUTER',
				'Adressefoyer' => 'INNER',
				'Adresse' => 'INNER',
				'Orientstruct' => 'INNER',
				'Typeorient' => 'LEFT OUTER',
				'Structurereferente' => 'LEFT OUTER',
				'Detaildroitrsa' => 'INNER',
				'Situationdossierrsa' => 'INNER'
			);

			$cacheKey = Inflector::underscore( $this->useDbConfig ).'_'.Inflector::underscore( $this->alias ).'_'.Inflector::underscore( __FUNCTION__ ).'_'.sha1( serialize( $types ) );
			$query = Cache::read( $cacheKey );

			if( $query === false ) {
				$query = $this->Allocataire->searchQuery( $types, $baseModelName, $forceBeneficiaire );

				// 1. Ajout des champs supplémentaires
				$query['fields'] = array_merge(
					$query['fields'],
					ConfigurableQueryFields::getModelsFields(
						array(
							$this->Personne->Dsp,
							$this->Personne->Foyer->Dossier->Suiviinstruction,
							$this->Personne->Orientstruct,
							$this->Personne->Orientstruct->Typeorient,
							$this->Personne->Orientstruct->Structurereferente,
						)
					),
					array(
						'Dossier.id',
						'Orientstruct.id',
						'Orientstruct.personne_id',
						'Orientstruct.propo_algo',
						'Adresse.numcom',
						'Personne.has_dsp' => '( "Dsp"."id" IS NOT NULL ) AS "Personne__has_dsp"',
					)
				);

				// 2. Jointures
				$query['joins'] = array_merge(
					$query['joins'],
					array(
						$this->Personne->join(
							'Dsp',
							array(
								'type' => $types['Dsp'],
								'conditions' => array(
									'Dsp.id IN ( '.$this->Personne->Dsp->WebrsaDsp->sqDerniereDsp().' )'
								)
							)
						),
						$this->Personne->join( 'Orientstruct', array( 'type' => $types['Orientstruct'] ) ),
						$this->Personne->Foyer->Dossier->join(
							'Suiviinstruction',
							array(
								'type' => $types['Suiviinstruction'],
								'conditions' => array(
									'Suiviinstruction.id IN ( '.$this->Personne->Foyer->Dossier->Suiviinstruction->sqDernier2().' )'
								)
							)
						),
						$this->Personne->Orientstruct->join( 'Typeorient', array( 'type' => $types['Typeorient'] ) ),
						$this->Personne->Orientstruct->join( 'Structurereferente', array( 'type' => $types['Structurereferente'] ) ),
					)
				);

				// 3. Conditions
				$query['conditions'][] = array( 'Orientstruct.statut_orient' => $this->statut_orient );

				// 4. Tri par défaut
				$query['order'] = array( 'Dossier.dtdemrsa' => 'ASC' );

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
			$query = $this->Allocataire->searchConditions( $query, $search );

			$hasDsp = Hash::get( $search, 'Personne.has_dsp' );
			if( in_array( $hasDsp, array( '0', '1' ) ) ) {
				if( $hasDsp ) {
					$query['conditions'][] = 'Dsp.id IS NOT NULL';
				}
				else {
					$query['conditions'][] = 'Dsp.id IS NULL';
				}
			}

			$propo_algo = Hash::get( $search, 'Orientstruct.propo_algo' );
			if( !empty( $propo_algo ) ) {
				if( $propo_algo === 'NULL' ) {
					$query['conditions'][] = array( 'Orientstruct.propo_algo IS NULL' );
				}
				else if( $propo_algo === 'NOTNULL' ) {
					$query['conditions'][] = array( 'Orientstruct.propo_algo IS NOT NULL' );
				}
				else {
					$query['conditions'][] = array( 'Orientstruct.propo_algo' => $propo_algo );
				}
			}


			return $query;
		}

		/**
		 * Préremplissage des champs du formulaire de cohorte.
		 *
		 * @param array $results
		 * @param array $params
		 * @param array $options
		 * @return array
		 */
		public function prepareFormDataCohorte( array $results, array $params = array(), array &$options = array() ) {
			$data = array();

			foreach( $results as $key => $result ) {
				$data[$key]['Orientstruct']['typeorient_id'] = $result['Orientstruct']['propo_algo'];
				$data[$key]['Orientstruct']['statut_orient'] = 'Orienté';
			}

			return $data;
		}

		/**
		 * Enregistrement du formulaire de cohorte: si on a choisi "A valider",
		 * l'orientation sera effective, sinon, l'orientation sera transférée
		 * dans la liste des "En attente de validation d'orientation".
		 *
		 * @param array $data
		 * @param array $params
		 * @param integer $user_id
		 * @return boolean
		 */
		public function saveCohorte( array $data, array $params = array(), $user_id = null ) {
			$success = true;

			foreach( array_keys( $data ) as $key ) {
				if( $data[$key]['Orientstruct']['statut_orient'] === 'Orienté' ) {
					$data[$key]['Orientstruct']['structurereferente_id'] = suffix( $data[$key]['Orientstruct']['structurereferente_id'] );
					$data[$key]['Orientstruct']['origine'] = 'cohorte';
					$data[$key]['Orientstruct']['user_id'] = $user_id;
					$data[$key]['Orientstruct']['date_valid'] = date( 'Y-m-d' );
				}
				else {
					$data[$key]['Orientstruct']['origine'] = null;
					$data[$key]['Orientstruct']['user_id'] = null;

					if( $data[$key]['Orientstruct']['statut_orient'] === 'Non orienté' ) {
						$data[$key]['Orientstruct']['date_propo'] = date( 'Y-m-d' );
					}
				}
			}

			return $this->saveResultAsBool(
				$this->Personne->Orientstruct->saveAll(
					Hash::extract( $data, '{n}.Orientstruct' ),
					array( 'validate' => 'first', 'atomic' => false )
				)
			);
		}

		/**
		 * Retourne un array à deux niveaux de clés permettant de connaître une structure référente à partir
		 * d'un type d'orientation et d'une zone géographique, afin de permettre de désigner automatiquement
		 * une structure référente à un allocataire.
		 *
		 * Le résultat est mis en cache.
		 *
		 * @fixme: afterSave, afterDelete pour: structures, types d'orientation, zones géographiques
		 *
		 * @return array
		 */
		public function structuresAutomatiques() {
			$cacheKey = Inflector::underscore( $this->useDbConfig ).'_'.Inflector::underscore( $this->alias ).'_'.Inflector::underscore( __FUNCTION__ );
			$results = Cache::read( $cacheKey );

			if( $results === false ) {
				$typesPermis = $this->Personne->Orientstruct->Typeorient->find(
					'list',
					array(
						'conditions' => array(
							'Typeorient.lib_type_orient' => $this->Personne->Orientstruct->Typeorient->listTypeParent()
						),
						'recursive' => -1
					)
				);
				$typesPermis = array_keys( $typesPermis );

				$structures = $this->Personne->Orientstruct->Structurereferente->find(
					'all',
					array(
						'conditions' => array(
							'Structurereferente.typeorient_id' => $typesPermis,
							'Structurereferente.orientation' => 'O',
							'Structurereferente.actif' => 'O'
						),
						'contain' => array(
							'Zonegeographique'
						)
					)
				);


				$results = array();
				foreach( $structures as $structure ) {
					if( !empty( $structure['Zonegeographique'] ) ) {
						foreach( $structure['Zonegeographique'] as $zonegeographique ) {
							$results[$structure['Structurereferente']['typeorient_id']][$zonegeographique['codeinsee']] = $structure['Structurereferente']['typeorient_id'].'_'.$structure['Structurereferente']['id'];
						}
					}
				}

				Cache::write( $cacheKey, $results );
				ModelCache::write( $cacheKey, array( 'Structurereferente', 'Typeorient', 'Zonegeographique' ) );
			}
			return $results;
		}

		/**
		 * Suppression et regénération du cache.
		 *
		 * @return boolean
		 */
		protected function _regenerateCache() {
			// Suppression des éléments du cache.
			$this->_clearModelCache();

			// Regénération des éléments du cache.
			$success = ( $this->structuresAutomatiques() !== false );

			return $success;
		}

		/**
		 * Exécute les différentes méthods du modèle permettant la mise en cache.
		 * Utilisé au préchargement de l'application (/prechargements/index).
		 *
		 * @return boolean true en cas de succès, false en cas d'erreur,
		 * 	null pour les fonctions vides.
		 */
		public function prechargement() {
			$success = $this->_regenerateCache();
			return $success;
		}
	}
?>