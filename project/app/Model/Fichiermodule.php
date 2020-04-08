<?php
	/**
	 * Code source de la classe Fichiermodule.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );
	require_once  APPLIBS.'cmis.php' ;

	/**
	 * La classe Fichiermodule ...
	 *
	 * @package app.Model
	 */
	class Fichiermodule extends AppModel
	{
		/**
		 *
		 */
		public $name = 'Fichiermodule';

		public $actsAs = array(
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Postgres.PostgresAutovalidate'
		);

		public $validate = array(
			'modeledoc' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
				)
			)
		);

		public $belongsTo = array(
			'Traitementpdo' => array(
				'className' => 'Traitementpdo',
				'foreignKey' => false,
				'conditions' => array(
					'Fichiermodule.modele = \'Traitementpdo\'',
					'Fichiermodule.fk_value = {$__cakeID__$}'
				),
				'fields' => '',
				'order' => ''
			),
			'Propopdo' => array(
				'className' => 'Propopdo',
				'foreignKey' => false,
				'conditions' => array(
					'Fichiermodule.modele = \'Propopdo\'',
					'Fichiermodule.fk_value = {$__cakeID__$}'
				),
				'fields' => '',
				'order' => ''
			),
			'Orientstruct' => array(
				'className' => 'Orientstruct',
				'foreignKey' => false,
				'conditions' => array(
					'Fichiermodule.modele = \'Orientstruct\'',
					'Fichiermodule.fk_value = {$__cakeID__$}'
				),
				'fields' => '',
				'order' => ''
			),
			'Rendezvous' => array(
				'className' => 'Rendezvous',
				'foreignKey' => false,
				'conditions' => array(
					'Fichiermodule.modele = \'Rendezvous\'',
					'Fichiermodule.fk_value = {$__cakeID__$}'
				),
				'fields' => '',
				'order' => ''
			),
			'Bilanparcours66' => array(
				'className' => 'Bilanparcours66',
				'foreignKey' => false,
				'conditions' => array(
					'Fichiermodule.modele = \'Bilanparcours66\'',
					'Fichiermodule.fk_value = {$__cakeID__$}'
				),
				'fields' => '',
				'order' => ''
			),
			'Contratinsertion' => array(
				'className' => 'Contratinsertion',
				'foreignKey' => false,
				'conditions' => array(
					'Fichiermodule.modele = \'Contratinsertion\'',
					'Fichiermodule.fk_value = {$__cakeID__$}'
				),
				'fields' => '',
				'order' => ''
			),
			'Foyer' => array(
				'className' => 'Foyer',
				'foreignKey' => false,
				'conditions' => array(
					'Fichiermodule.modele = \'Foyer\'',
					'Fichiermodule.fk_value = {$__cakeID__$}'
				),
				'fields' => '',
				'order' => ''
			),
			'DspRev' => array(
				'className' => 'DspRev',
				'foreignKey' => false,
				'conditions' => array(
					'Fichiermodule.modele = \'Dsp\'',
					'Fichiermodule.fk_value = {$__cakeID__$}'
				),
				'fields' => '',
				'order' => ''
			),
			'PersonneReferent' => array(
				'className' => 'PersonneReferent',
				'foreignKey' => false,
				'conditions' => array(
					'Fichiermodule.modele = \'PersonneReferent\'',
					'Fichiermodule.fk_value = {$__cakeID__$}'
				),
				'fields' => '',
				'order' => ''
			),
			'Entretien' => array(
				'className' => 'Entretien',
				'foreignKey' => false,
				'conditions' => array(
					'Fichiermodule.modele = \'Entretien\'',
					'Fichiermodule.fk_value = {$__cakeID__$}'
				),
				'fields' => '',
				'order' => ''
			),
			'Apre' => array(
				'className' => 'Apre',
				'foreignKey' => false,
				'conditions' => array(
					'Fichiermodule.modele = \'Apre\'',
					'Fichiermodule.fk_value = {$__cakeID__$}'
				),
				'fields' => '',
				'order' => ''
			),
			'Apre66' => array(
				'className' => 'Apre66',
				'foreignKey' => false,
				'conditions' => array(
					'Fichiermodule.modele = \'Apre66\'',
					'Fichiermodule.fk_value = {$__cakeID__$}'
				),
				'fields' => '',
				'order' => ''
			),
			'Personne' => array(
				'className' => 'Personne',
				'foreignKey' => false,
				'conditions' => array(
					'Fichiermodule.modele = \'Personne\'',
					'Fichiermodule.fk_value = {$__cakeID__$}'
				),
				'fields' => '',
				'order' => ''
			),
			'ActioncandidatPersonne' => array(
				'className' => 'ActioncandidatPersonne',
				'foreignKey' => false,
				'conditions' => array(
					'Fichiermodule.modele = \'ActioncandidatPersonne\'',
					'Fichiermodule.fk_value = {$__cakeID__$}'
				),
				'fields' => '',
				'order' => ''
			),
			'Dossierpcg66' => array(
				'className' => 'Dossierpcg66',
				'foreignKey' => false,
				'conditions' => array(
					'Fichiermodule.modele = \'Dossierpcg66\'',
					'Fichiermodule.fk_value = {$__cakeID__$}'
				),
				'fields' => '',
				'order' => ''
			),
			'Traitementpcg66' => array(
				'className' => 'Traitementpcg66',
				'foreignKey' => false,
				'conditions' => array(
					'Fichiermodule.modele = \'Traitementpcg66\'',
					'Fichiermodule.fk_value = {$__cakeID__$}'
				),
				'fields' => '',
				'order' => ''
			),
			'Actioncandidat' => array(
				'className' => 'Actioncandidat',
				'foreignKey' => false,
				'conditions' => array(
					'Fichiermodule.modele = \'Actioncandidat\'',
					'Fichiermodule.fk_value = {$__cakeID__$}'
				),
				'fields' => '',
				'order' => ''
			),
			'Nonoriente66' => array(
				'className' => 'Nonoriente66',
				'foreignKey' => false,
				'conditions' => array(
					'Fichiermodule.modele = \'Nonoriente66\'',
					'Fichiermodule.fk_value = {$__cakeID__$}'
				),
				'fields' => '',
				'order' => ''
			),
			'Cui' => array(
				'className' => 'Cui',
				'foreignKey' => false,
				'conditions' => array(
					'Fichiermodule.modele = \'Cui\'',
					'Fichiermodule.fk_value = {$__cakeID__$}'
				),
				'fields' => '',
				'order' => ''
			),
			'Decisiondossierpcg66' => array(
				'className' => 'Decisiondossierpcg66',
				'foreignKey' => false,
				'conditions' => array(
					'Fichiermodule.modele = \'Decisiondossierpcg66\'',
					'Fichiermodule.fk_value = {$__cakeID__$}'
				),
				'fields' => '',
				'order' => ''
			),
			'Manifestationbilanparcours66' => array(
				'className' => 'Manifestationbilanparcours66',
				'foreignKey' => false,
				'conditions' => array(
					'Fichiermodule.modele = \'Manifestationbilanparcours66\'',
					'Fichiermodule.fk_value = {$__cakeID__$}'
				),
				'fields' => '',
				'order' => ''
			),
			'Memo' => array(
				'className' => 'Memo',
				'foreignKey' => false,
				'conditions' => array(
					'Fichiermodule.modele = \'Memo\'',
					'Fichiermodule.fk_value = {$__cakeID__$}'
				),
				'fields' => '',
				'order' => ''
			),
			'Propodecisioncui66' => array(
				'className' => 'Propodecisioncui66',
				'foreignKey' => false,
				'conditions' => array(
					'Fichiermodule.modele = \'Propodecisioncui66\'',
					'Fichiermodule.fk_value = {$__cakeID__$}'
				),
				'fields' => '',
				'order' => ''
			),
			'Decisioncui66' => array(
				'className' => 'Decisioncui66',
				'foreignKey' => false,
				'conditions' => array(
					'Fichiermodule.modele = \'Decisioncui66\'',
					'Fichiermodule.fk_value = {$__cakeID__$}'
				),
				'fields' => '',
				'order' => ''
			),
			'Accompagnementcui66' => array(
				'className' => 'Accompagnementcui66',
				'foreignKey' => false,
				'conditions' => array(
					'Fichiermodule.modele = \'Accompagnementcui66\'',
					'Fichiermodule.fk_value = {$__cakeID__$}'
				),
				'fields' => '',
				'order' => ''
			),
            'Rupturecui66' => array(
                'className' => 'Rupturecui66',
                'foreignKey' => false,
                'conditions' => array(
                    'Fichiermodule.modele = \'Rupturecui66\'',
                    'Fichiermodule.fk_value = {$__cakeID__$}'
                ),
                'fields' => '',
                'order' => ''
            ),
            'Suspensioncui66' => array(
                'className' => 'Suspensioncui66',
                'foreignKey' => false,
                'conditions' => array(
                    'Fichiermodule.modele = \'Suspensioncui66\'',
                    'Fichiermodule.fk_value = {$__cakeID__$}'
                ),
                'fields' => '',
                'order' => ''
            ),
            'Piecemailcui66' => array(
                'className' => 'Piecemailcui66',
                'foreignKey' => false,
                'conditions' => array(
                    'Fichiermodule.modele = \'Piecemailcui66\'',
                    'Fichiermodule.fk_value = {$__cakeID__$}'
                ),
                'fields' => '',
                'order' => ''
            ),
			'Fichedeliaison' => array(
				'className' => 'Fichedeliaison',
				'foreignKey' => 'fk_value',
				'conditions' => array(
					'Fichiermodule.modele' => 'Fichedeliaison'
				),
				'fields' => '',
				'order' => ''
			)
		);

		public $hasOne = array(
			'Foyerpiecejointe' => array(
				'className' => 'Foyerpiecejointe',
				'foreignKey' => 'fichiermodule_id',
				'conditions' => array(
					'Fichiermodule.modele = \'Foyerpiecejointe\'',
					'Fichiermodule.fk_value = {$__cakeID__$}'
				),
				'fields' => '',
				'order' => ''
			)
		);

		/**
		 * Surcharge de la fonction de sauvegarde pour essayer d'enregistrer le PDF sur Alfresco.
		 */
		public function save( $data = null, $validate = true, $fieldList = array( ) ) {
			$cmsPath = "/{$this->data[$this->alias]['modele']}/{$this->data[$this->alias]['fk_value']}/{$this->data[$this->alias]['name']}";
			$cmsSuccess = Cmis::write( $cmsPath, $this->data[$this->alias]['document'], $this->data[$this->alias]['mime'], true );

			if( $cmsSuccess ) {
				$this->data[$this->alias]['cmspath'] = $cmsPath;
				$this->data[$this->alias]['document'] = null;
			}

			$success = parent::save( $data, $validate, $fieldList );
			if( !$success && $cmsSuccess ) {
				$success = Cmis::delete( $cmsPath, true ) && $success;
			}

			return $success;
		}

		/**
		 * @see FichiersmodulesShell::_repairOrphans
		 */
		public function delete( $id = NULL, $cascade = true ) {
			$conditions = array( );
			if( empty( $id ) && !empty( $this->id ) ) {
				$conditions["{$this->alias}.{$this->primaryKey}"] = $this->id;
			}
			else {
				$conditions["{$this->alias}.{$this->primaryKey}"] = $id;
			}

			$records = $this->find(
					'all', array(
				'fields' => array( 'id', 'modele', 'fk_value', 'cmspath' ),
				'conditions' => $conditions
					)
			);

			$success = parent::delete( $id, $cascade );
			$cmspaths = Hash::filter( (array)Set::extract( $records, "/{$this->alias}/cmspath" ) );

			if( $success && !empty( $cmspaths ) ) {
				foreach( $cmspaths as $cmspath ) {
					$success = Cmis::delete( $cmspath, true ) && $success;
				}
			}

			return $success;
		}

		/**
		 * @see FichiersmodulesShell::_repairOrphans
		 */
		public function deleteAll( $conditions, $cascade = true, $callbacks = false ) {
			$records = $this->find(
					'all', array(
				'fields' => array( 'id', 'modele', 'fk_value', 'cmspath' ),
				'conditions' => $conditions
					)
			);

			$success = parent::deleteAll( $conditions, $cascade, $callbacks );
			$cmspaths = Hash::filter( (array)Set::extract( $records, "/{$this->alias}/cmspath" ) );

			if( $success && !empty( $cmspaths ) ) {
				foreach( $cmspaths as $cmspath ) {
					$success = Cmis::delete( $cmspath, true ) && $success;
				}
			}

			return $success;
		}

		/**
		 * Sous-requête permettant de savoir si une entrée existe dans la table fichiersmodules pour une entrée d'une
		 * table d'un autre modèle.
		 *
		 * @param Model $Model
		 * @param string $fieldName Si null, renvoit uniquement la sous-reqête,
		 * 	sinon renvoit la sous-requête aliasée pour un champ (avec l'alias du
		 * 	modèle).
		 * @param string $modelAlias Si null, utilise l'alias de la class Fichiermodule, sinon la valeur donnée.
		 * @return string
		 */
		public function sqNbFichiersLies( Model $Model, $fieldName = null, $modelAlias = null ) {
			$alias = Inflector::underscore( $this->alias );

			$modelAlias = ( is_null( $modelAlias ) ? $this->alias : $modelAlias );

			$sq = $this->sq(
					array(
						'fields' => array(
							"COUNT( {$alias}.id )"
						),
						'alias' => $alias,
						'conditions' => array(
							"{$alias}.modele" => $Model->alias,
							"{$alias}.fk_value = {$Model->alias}.{$Model->primaryKey}"
						)
					)
			);

			if( !is_null( $fieldName ) ) {
				$sq = "( {$sq} ) AS \"{$modelAlias}__{$fieldName}\"";
			}

			return $sq;
		}

		/**
		 * Fonction de recuperer la liste des entrées dans la table fichiersmodules pour une entrée d'une
		 * table d'un autre modèle.
		 *
		 * @param Model $Model
		 * @param string $fieldName Si null, renvoit uniquement la sous-reqête,
		 * 	sinon renvoit la sous-requête aliasée pour un champ (avec l'alias du
		 * 	modèle).
		 * @return array
		 */
		public function sqListeFichiersLies( $Model, $model_id ) {

			$query = array(
				'fields' => array(
					'id',
					'name',
					'cmspath'
				),
				'conditions' => array(
					'modele' => $Model,
					'fk_value' => $model_id
				)
			);

			$return = $this->find('all', $query);

			return $return;
		}

	}
?>