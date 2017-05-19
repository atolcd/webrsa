<?php
	/**
	 * Code source de la classe Typeorient.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Typeorient s'occupe de la gestion des types d'orientation.
	 *
	 * @package app.Model
	 */
	class Typeorient extends AppModel
	{
		public $name = 'Typeorient';

		public $displayField = 'lib_type_orient';

		public $actsAs = array(
			'Enumerable' => array(
				'fields' => array(
					'actif' => array( 'type' => 'no', 'domain' => 'default' ),
				)
			)
		);

		public $hasMany = array(
			'Orientstruct' => array(
				'className' => 'Orientstruct',
				'foreignKey' => 'typeorient_id',
				'dependent' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
			'Structurereferente' => array(
				'className' => 'Structurereferente',
				'foreignKey' => 'typeorient_id',
				'dependent' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
			'Decisionnonorientationproep58' => array(
				'className' => 'Decisionnonorientationproep58',
				'foreignKey' => 'typeorient_id',
				'dependent' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
			'Decisionpropoorientsocialecov58' => array(
				'className' => 'Decisionpropoorientsocialecov58',
				'foreignKey' => 'typeorient_id',
				'dependent' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
			'Decisionnonorientationproep93' => array(
				'className' => 'Decisionnonorientationproep93',
				'foreignKey' => 'typeorient_id',
				'dependent' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
			'Decisionnonorientationprocov58' => array(
				'className' => 'Decisionnonorientationprocov58',
				'foreignKey' => 'typeorient_id',
				'dependent' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
			'Regressionorientationcov58' => array(
				'className' => 'Regressionorientationcov58',
				'foreignKey' => 'typeorient_id',
				'dependent' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
			'Decisionregressionorientationcov58' => array(
				'className' => 'Decisionregressionorientationcov58',
				'foreignKey' => 'typeorient_id',
				'dependent' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
		);

		public $validate = array(
			'lib_type_orient' => array(
				array(
					'rule' => 'notEmpty',
					'message' => 'Champ obligatoire'
				),
				array(
					'rule' => 'isUnique',
					'message' => 'Valeur déjà utilisée'
				),
				array(
					'rule' => array( 'maxLength', 75 ),
					'message' => 'Maximum 75 caractères'
				),
			),
			'modele_notif' => array(
				array(
					'rule' => 'notEmpty',
					'message' => 'Champ obligatoire'
				)
			),
			'modele_notif_cohorte' => array(
				array(
					'rule' => 'notEmpty',
					'message' => 'Champ obligatoire'
				)
			),
		);

		/**
		*
		*/

		public function listOptions( $conditions = array() ) {
			$options = $this->find(
				'list',
				array (
					'fields' => array(
						'Typeorient.id',
						'Typeorient.lib_type_orient'
					),
					'conditions' => array(
						'Typeorient.parentid' => NULL,
						'Typeorient.actif' => 'O'
					),
					'order'  => array( 'Typeorient.lib_type_orient ASC' )
				)
			);

			if( $this->find( 'count', array( 'conditions' => array( 'Typeorient.parentid NOT' => NULL ) ) ) > 0 ) {
				$list = array();
				foreach( $options as $key => $option ) {
					$innerConditions = Set::merge(
						array(
							'AND' => array(
								'Typeorient.parentid' => $key,
								'Typeorient.actif' => 'O'
							)
						),
						$conditions
					);

					$innerOptions = $this->find(
						'list',
						array (
							'fields' => array(
								'Typeorient.id',
								'Typeorient.lib_type_orient'/*,
								'Typeorient.parentid'*/
							),
							'conditions' => $innerConditions,
							'order'  => array( 'Typeorient.lib_type_orient ASC' )
						)
					);

					if( !empty( $innerOptions ) ) {
						$list[$option] = $innerOptions ;
					}
				}
				return $list;
			}
			else {
				return $options;
			}
		}

		/**
		*
		*/
		public function list1Options() {
			$tmp = $this->find(
				'all',
				array(
					'conditions' => array( 'Typeorient.parentid IS NOT NULL' ),
					'fields' => array(
						'Typeorient.id',
						'Typeorient.parentid',
						'Typeorient.lib_type_orient'
					),
					'order'  => array( 'Typeorient.lib_type_orient ASC' ),
					'recursive' => -1
				)
			);

			$results = array();
			foreach( $tmp as $key => $value ) {
				$results[$value['Typeorient']['parentid'].'_'.$value['Typeorient']['id']] = $value['Typeorient']['lib_type_orient'];
			}

			return $results;
		}

		/**
		*
		*/

		/*public function occurences() {
			// Orientstruct
			$queryData = array(
				'fields' => array(
					'"Typeorient"."id"',
					'COUNT("Structurereferente"."id") + COUNT("Orientstruct"."id") AS "Typeorient__occurences"',
				),
				'joins' => array(
					array(
						'table'      => 'structuresreferentes',
						'alias'      => 'Structurereferente',
						'type'       => 'LEFT OUTER',
						'foreignKey' => false,
						'conditions' => array( 'Structurereferente.typeorient_id = Typeorient.id' )
					),
					array(
						'table'      => 'orientsstructs',
						'alias'      => 'Orientstruct',
						'type'       => 'LEFT OUTER',
						'foreignKey' => false,
						'conditions' => array( 'Orientstruct.typeorient_id = Typeorient.id' )
					),
				),
				'recursive' => -1,
				'group' => array( '"Typeorient"."id"' )
			);
			$results = $this->find( 'all', $queryData );

			return Set::combine( $results, '{n}.Typeorient.id', '{n}.Typeorient.occurences' );
		}*/

		/**
		*   Recherche du type d'orientation qui n'a plus de parent
		*/

		public function getIdLevel0( $typeorient_id ) {
			$tmpTypeorient = $this->find(
				'first',
				array(
					'fields' => array( 'Typeorient.id', 'Typeorient.parentid' ),
					'recursive' => -1,
					'conditions' => array(
						'Typeorient.id' => $typeorient_id
					)
				)
			);
			if( !empty( $tmpTypeorient ) ) {
				while( $parentid = Set::classicExtract( $tmpTypeorient, 'Typeorient.parentid' ) ) {
					$tmpTypeorient = $this->find(
						'first',
						array(
							'fields' => array( 'Typeorient.id', 'Typeorient.parentid' ),
							'recursive' => -1,
							'conditions' => array(
								'Typeorient.id' => $parentid
							)
						)
					);
				}
			}
			if( !empty( $tmpTypeorient ) ) {
				$typeorient_niv1_id = Set::classicExtract( $tmpTypeorient, 'Typeorient.id' );
				if( !empty( $typeorient_niv1_id ) ) {
					return $typeorient_niv1_id;
				}
			}
			return null;
		}

		/**
		* Vérifie si pour un id de type d'orientation donné il s'agit ou non d'une orientation vers le professionnel
		*/

		public function isProOrientation( $typeorient_id ) {

			if( Configure::read( 'Cg.departement' ) == 58 ) {
				$typeOrientEmploiId = Configure::read( 'Typeorient.emploi_id' );
				$return =  ( !empty( $typeOrientEmploiId ) && is_int( $typeOrientEmploiId ) && $typeorient_id == $typeOrientEmploiId );
			}
			else{
				$typeorient = $this->find(
					'first',
					array(
						'conditions' => array(
							'Typeorient.id' => $typeorient_id,
							'Typeorient.lib_type_orient LIKE' => 'Emploi%'
						),
						'contain' => false
					)
				);
				$return = ( !empty( $typeorient ) );
			}
			return $return;
		}

		/**
		*
		*/

		public function listRadiosOptionsPrincipales( $listeIds ) {
			$options = $this->find(
				'list',
				array (
					'fields' => array(
						'Typeorient.id',
						'Typeorient.lib_type_orient'
					),
					'conditions' => array(
						'Typeorient.parentid' => NULL,
						'Typeorient.actif' => 'O',
						'Typeorient.id' => $listeIds
					),
					'contain' => false,
					'order'  => array( 'Typeorient.lib_type_orient ASC' )
				)
			);
			return $options;
		}

		/**
		*
		*/

		public function listOptionsUnderParent() {
			$typesorients = $this->find(
				'all',
				array (
					'fields' => array(
						'Typeorient.id',
						'Typeorient.lib_type_orient',
						'Typeorient.parentid'
					),
					'conditions' => array(
						'Typeorient.parentid NOT' => NULL,
						'Typeorient.actif' => 'O'
					),
					'contain' => false,
					'order'  => array( 'Typeorient.lib_type_orient ASC' )
				)
			);
			$options = array();
			foreach( $typesorients as $typeorient ) {
				$options[$typeorient['Typeorient']['parentid']][$typeorient['Typeorient']['id']] = $typeorient['Typeorient']['lib_type_orient'];
			}
			return $options;
		}

		/**
		 * Retourne la liste des modèles odt paramétrés pour le impressions de
		 * cette classe.
		 *
		 * @return array
		 */
		public function modelesOdt() {
			$prefix = 'Orientation'.DS;

			$items = $this->find(
				'all',
				array(
					'fields' => array(
						'( \''.$prefix.'\' || "'.$this->alias.'"."modele_notif" || \'.odt\' ) AS "'.$this->alias.'__modele"',
					),
					'recursive' => -1
				)
			);
			return Set::extract( $items, '/'.$this->alias.'/modele' );
		}

		/**
		 * Retourne la liste des types d'orientations à utiliser dans les cohortes d'orientation du CG 93.
		 * Cette liste est mise en cache sous la clé 'typeorient_list_options_cohortes93'.
		 *
		 * @return array
		 */
		public function listOptionsCohortes93() {
			$cacheKey = 'typeorient_list_options_cohortes93';
			$results = Cache::read( $cacheKey );

			if( $results === false ) {
				$results = $this->find(
					'list',
					array(
						'fields' => array(
							'Typeorient.id',
							'Typeorient.lib_type_orient'
						),
						'conditions' => array( 'Typeorient.actif' => 'O' ),
						'order' => 'Typeorient.lib_type_orient ASC'
					)
				);

				Cache::write( $cacheKey, $results );
				ModelCache::write( $cacheKey, array( 'Typeorient' ) );
			}

			return $results;
		}

		/**
		 * Retourne la liste des types d'orientations à utiliser dans les cohortes d'orientation du CG 93.
		 * Cette liste est mise en cache sous la clé 'typeorient_list_options_preorientation_cohortes93'.
		 *
		 * @return array
		 */
		public function listOptionsPreorientationCohortes93() {
			$cacheKey = 'typeorient_list_options_preorientation_cohortes93';
			$results = Cache::read( $cacheKey );

			if( $results === false ) {
				$results = $this->find(
					'list',
					array(
						'fields' => array( 'lib_type_orient' ),
						'conditions' => array(
							'Typeorient.parentid IS NULL',
							'Typeorient.actif' => 'O'
						)
					)
				);

				Cache::write( $cacheKey, $results );
				ModelCache::write( $cacheKey, array( 'Typeorient' ) );
			}

			return $results;
		}

		/**
		 * Suppression et regénération du cache.
		 *
		 * @return boolean
		 */
		protected function _regenerateCache() {
			$this->_clearModelCache();

			// Regénération des éléments du cache.
			$success = true;

			if( $this->alias == 'Typeorient' ) {
				$success = ( $this->listOptionsCohortes93() !== false )
					&& ( $this->listOptionsPreorientationCohortes93() !== false );
			}

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