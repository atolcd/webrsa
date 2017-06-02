<?php
	/**
	 * Code source de la classe WebrsaDossiercov58.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'ConfigurableQueryFields', 'ConfigurableQuery.Utility' );

	/**
	 * La classe WebrsaDossiercov58 contient la logique métier concernant les
	 * dossiers d'EP.
	 *
	 * @package app.Model
	 */
	class WebrsaDossiercov58 extends AppModel
	{

		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaDossiercov58';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $useTable = false;

		/**
		 * Behaviors utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $actsAs = array();

		/**
		 * Modèles utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array(
			'Dossiercov58'
		);

		/**
		 * "Live" cache pour les options et getQuery (clé query).
		 *
		 * @var array
		 */
		protected $_cache = array(
			'options' => array(),
			'query' => array()
		);

		/**
		 * Retourne la liste des dossiers de COV en cours ne débouchant pas sur
		 * une orientation pour un allocataire donné.
		 *
		 * @param integer $personne_id
		 * @return array
		 */
		public function getNonReorientationsEnCours( $personne_id ) {
			// 1. Récupération des conditions concernant les dossiers d'EP ouverts pour le bénéficiaire
			$query = $this->Dossiercov58->qdDossiersepsOuverts( $personne_id );
			$conditions = $query['conditions'];

			// 2. Récupération du query permettant de récupérer les dossiers d'EP
			// liés à leur dernier passage en commission
			$query = $this->Dossiercov58->getDossiersQuery();
			$query['fields'] = array(
				'Personne.id',
				'Personne.qual',
				'Personne.nom',
				'Personne.prenom',
				'Dossiercov58.id',
				'Dossiercov58.created',
				'Dossiercov58.themecov58',
				'Passagecov58.id',
				'Passagecov58.etatdossiercov',
				'Cov58.id',
				'Cov58.datecommission',
				'Cov58.etatcov'
			);

			$query['conditions'][] = $conditions;

			// et qui ne conduisent pas à une réorientation (ils se trouvent déjà dans $reorientationscovs)
			$query['conditions'][] = array(
				'NOT' => array(
					'Dossiercov58.themecov58' => $this->Dossiercov58->getThematiquesReorientations()
				)
			);

			// Pour la personne
			$query['conditions'][] = array( 'Dossiercov58.personne_id' => $personne_id );


			return $this->Dossiercov58->find( 'all', $query );
		}

		/**
		 * Retourne la clé de cache du modèle courant, pour le nom de méthode et
		 * la liste de paramètres spécifiés.
		 *
		 * @param string $method Le nom de la méthode appelée
		 * @param array $params Les paramètres
		 * @return string
		 */
		public function cacheKey( $method, array $params = array() ) {
			return Inflector::underscore( $this->useDbConfig )
				.'_'.Inflector::underscore( $this->alias )
				.'_'.Inflector::underscore( $method )
				.'_'.sha1( serialize( $params ) );
		}

		/**
		 * Retourne un querydata permettant d'avoir tout à partir du modèle
		 * Dossiercov58.
		 *
		 * @return array
		 */
		public function getQuery( array $params = array() ) {
			$params += array( 'themes' => true, 'decisions' => true );
			$cacheKey = $this->cacheKey( __FUNCTION__, $params );

			if( false === isset( $this->_cache['query'][$cacheKey] ) ) {
				$this->_cache['query'][$cacheKey] = Cache::read( $cacheKey );

				if( $this->_cache['query'][$cacheKey] === false ) {
					$result = array(
							'fields' => array_merge(
							array(
								'Dossiercov58.themecov58',
								'Passagecov58.id',
								'Cov58.id',
								'Cov58.etatcov'
							),
							ConfigurableQueryFields::getModelsFields(
								array(
									$this->Dossiercov58,
									$this->Dossiercov58->Passagecov58,
									$this->Dossiercov58->Passagecov58->Cov58,
									$this->Dossiercov58->Passagecov58->Cov58->Sitecov58
								)
							)
						),
						'conditions' => array(),
						'joins' => array(
							'Passagecov58' => array(
								$this->Dossiercov58->join( 'Passagecov58', array( 'type' => 'LEFT OUTER' ) )
							),
							'Cov58' => array(
								$this->Dossiercov58->Passagecov58->join( 'Cov58', array( 'type' => 'LEFT OUTER' ) )
							),
							'Sitecov58' => array(
								$this->Dossiercov58->Passagecov58->Cov58->join( 'Sitecov58', array( 'type' => 'LEFT OUTER' ) )
							)
						)
					);

					$themes = array_keys( (array)Hash::get( $this->Dossiercov58->enums(), 'Dossiercov58.themecov58' ) );

					$list = array();
					// FIXME: à factoriser
					foreach( $themes as $theme ) {
						// 1. Modèle de thématique
						if( true === Hash::get( $params, 'themes' ) ) {
							$modelName = Inflector::classify( $theme );
							$query = array(
								'fields' => array(),
								'joins' => array(
									$this->Dossiercov58->join( $modelName, array( 'type' => 'LEFT OUTER' ) )
								),
								'conditions' => array()
							);
							$webrsaModelName = 'Webrsa'.$modelName;
							$this->loadModel( $webrsaModelName );
							$query = $this->{$webrsaModelName}->completeQuery( $query );

							$result['fields'] = array_merge(
								$result['fields'],
								$query['fields']
							);

							foreach( $query['joins'] as $join ) {
								if( !isset( $result['joins'][$join['alias']] ) ) {
									$result['joins'][$join['alias']] = array();
								}
								unset($list[$join['alias']]);
								$list[$join['alias']] = null;
								$result['joins'][$join['alias']][] = $join;
							}
						}

						// 2. Modèle de décision
						if( true === Hash::get( $params, 'decisions' ) ) {
							$modelDecisionName = Inflector::classify( "decisions{$theme}" );
							$query = array(
								'fields' => array(),
								'joins' => array(
									$this->Dossiercov58->Passagecov58->join( $modelDecisionName, array( 'type' => 'LEFT OUTER' ) )
								),
								'conditions' => array()
							);
							$webrsaModelDecisionName = 'Webrsa'.$modelDecisionName;
							$this->loadModel( $webrsaModelDecisionName );
							$query = $this->{$webrsaModelDecisionName}->completeQuery( $query );

							$result['fields'] = array_merge(
								$result['fields'],
								$query['fields']
							);

							foreach( $query['joins'] as $join ) {
								if( !isset( $result['joins'][$join['alias']] ) ) {
									$result['joins'][$join['alias']] = array();
								}
								unset($list[$join['alias']]);
								$list[$join['alias']] = null;
								$result['joins'][$join['alias']][] = $join;
							}
						}
					}

					// -----------------------------------------------------------------

					$end = array();
					foreach( $result['joins'] as $alias => $joins ) {
						unset( $result['joins'][$alias] );
						$count = count( $joins );
						if( $count === 1 ) {
							$join = $joins[0];
							$join['type'] = 'LEFT OUTER';
							$result['joins'][] = $join;
						}
						else if( $count > 1 ) {
							$conditions = array( 'OR' => array() );
							foreach( $joins as $join ) {
								$conditions['OR'][] = $join['conditions'];
							}

							$join['type'] = 'LEFT OUTER';
							$join['conditions'] = $conditions;
							$end[$join['alias']] = $join;
						}
					}

					foreach( array_keys( $list ) as $alias ) {
						if( isset( $end[$alias] ) ) {
							$result['joins'][] = $end[$alias];
						}
					}

					$result['contain'] = false;

					Cache::write( $cacheKey, $result );
					$this->_cache['query'][$cacheKey] = $result;
				}
			}

			return $this->_cache['query'][$cacheKey];
		}

		/**
		 * Retourne les options associées à la méthode getQuery.
		 *
		 * @param array $params
		 * @return array
		 */
		public function options( array $params = array() ) {
			$params += array( 'themes' => true, 'decisions' => true );
			$cacheKey = $this->cacheKey( __FUNCTION__, $params );

			if( false === isset( $this->_cache['options'][$cacheKey] ) ) {
				$this->_cache['options'][$cacheKey] = Cache::read( $cacheKey );

				if( $this->_cache['options'][$cacheKey] === false ) {
					$options = array_merge(
						$this->Dossiercov58->enums(),
						$this->Dossiercov58->Passagecov58->enums(),
						$this->Dossiercov58->Passagecov58->Cov58->enums()
					);
					$options['Dossiercov58']['anciennes_thematiques'] = $this->Dossiercov58->anciennesThematiques;

					$themes = array_keys( (array)Hash::get( $this->Dossiercov58->enums(), 'Dossiercov58.themecov58' ) );

					foreach( $themes as $theme ) {
						if( true === Hash::get( $params, 'themes' ) ) {
							$modelName = Inflector::classify( $theme );
							$options = array_merge( $options, $this->Dossiercov58->{$modelName}->enums() );
						}
						if( true === Hash::get( $params, 'decisions' ) ) {
							$modelDecisionName = Inflector::classify( "decisions{$theme}" );

							$options = array_merge(
								$options,
								$this->Dossiercov58->Passagecov58->{$modelDecisionName}->enums()
							);
						}
					}

					Cache::write( $cacheKey, $options );
					$this->_cache['options'][$cacheKey] = $options;
				}
			}

			return $this->_cache['options'][$cacheKey];
		}

		/**
		 * Exécute les différentes méthods du modèle permettant la mise en cache.
		 * Utilisé au préchargement de l'application (/prechargements/index).
		 *
		 * @return boolean true en cas de succès, false en cas d'erreur,
		 * 	null pour les méthodes qui ne font rien.
		 */
		public function prechargement() {
			$success = true;
			$configs = array(
				array(),
				array( 'themes' => false, 'decisions' => false )
			);
			foreach( $configs as $config ) {
				$query = $this->getQuery( $config );
				$options = $this->options( $config );
				$success = $success && !empty( $query ) && !empty( $options );
			}
			return $success;
		}
	}
?>