<?php
	/**
	 * Code source de la classe Thematiquerdv.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Thematiquerdv ...
	 *
	 * @package app.Model
	 */
	class Thematiquerdv extends AppModel
	{
		/**
		 * Nom.
		 *
		 * @var string
		 */
		public $name = 'Thematiquerdv';

		/**
		 * Tri par défaut.
		 *
		 * @var integer
		 */
		public $order = array( 'Thematiquerdv.name' );

		/**
		 * Behaviors utilisés.
		 *
		 * @var array
		 */
		public $actsAs = array(
			'Postgres.PostgresAutovalidate',
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesComparison',
			'Validation2.Validation2RulesFieldtypes',
		);

		/**
		 * Associations "Belongs to".
		 *
		 * @var array
		 */
		public $belongsTo = array(
			'Statutrdv' => array(
				'className' => 'Statutrdv',
				'foreignKey' => 'statutrdv_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Typerdv' => array(
				'className' => 'Typerdv',
				'foreignKey' => 'typerdv_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
		);

		/**
		 * Associations "Has and belongs to many".
		 *
		 * @var array
		 */
		public $hasAndBelongsToMany = array(
			'Rendezvous' => array(
				'className' => 'Rendezvous',
				'joinTable' => 'rendezvous_thematiquesrdvs',
				'foreignKey' => 'thematiquerdv_id',
				'associationForeignKey' => 'rendezvous_id',
				'unique' => true,
				'conditions' => null,
				'fields' => null,
				'order' => null,
				'limit' => null,
				'offset' => null,
				'finderQuery' => null,
				'deleteQuery' => null,
				'insertQuery' => null,
				'with' => 'RendezvousThematiquerdv'
			),
		);

		/**
		 * Règles de validation
		 *
		 * @var array
		 */
		public $validate = array(
			'statutrdv_id' => array(
				'notEmptyIf' => array(
					'rule' => array( 'notEmptyIf', 'linkedmodel', false, array( null ) ),
					'message' => 'Champ obligatoire',
				),
			),
			'linkedmodel' => array(
				'notEmptyIf' => array(
					'rule' => array( 'notEmptyIf', 'statutrdv_id', false, array( null ) ),
					'message' => 'Champ obligatoire',
				),
			),
		);

		/**
		 * Retourne la liste des modèles liés avec lesquels on peut utiliser la
		 * fonctionnalité de blocage.
		 *
		 * @return array
		 */
		public function linkedModels() {
			if( false === $this->Statutrdv->Rendezvous->Behaviors->attached( 'Postgres.PostgresTable' ) ) {
				$this->Statutrdv->Rendezvous->Behaviors->attach( 'Postgres.PostgresTable' );
			}

			$return = array();
			$domain = Inflector::underscore( $this->alias );
			$foreignKeys = $this->Statutrdv->Rendezvous->getPostgresForeignKeysTo();

			if( !empty( $foreignKeys ) ) {
				foreach( $foreignKeys as $foreignKey ) {
					$linkedModel = Inflector::classify( $foreignKey['From']['table'] );
					// TODO: quand ce sera adapté ailleurs
					$allowed = ( $linkedModel == 'Questionnaired1pdv93' );
					if( $allowed && !isset( $this->Statutrdv->Rendezvous->hasAndBelongsToMany[$linkedModel] ) ) {
						$return[$linkedModel] = __d( $domain, "LINKED::{$linkedModel}" );
					}
				}
			}

			return $return;
		}

		/**
		 * Permet de savoir si on utilise les thématiques de RDV.
		 *
		 * @return boolean
		 */
		public function used() {
			$querydata = array(
				'fields' => array( "{$this->alias}.{$this->primaryKey}" ),
				'contain' => false,
				'recursive' => -1
			);
			$element = $this->find( 'first', $querydata );

			return ( !empty( $element ) );
		}
	}
?>