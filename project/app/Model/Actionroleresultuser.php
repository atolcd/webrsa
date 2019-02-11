<?php
	/**
	 * Code source de la classe Actionroleresultuser.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Actionroleresultuser ...
	 *
	 * @package app.Model
	 */
	class Actionroleresultuser extends AppModel
	{

		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'Actionroleresultuser';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = -1;

		/**
		 * Behaviors utilisés par le modèle.
		 *
		 * @var array
		 */
		public $actsAs = array(
			'Postgres.PostgresAutovalidate',
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
		);

		/**
		 * Associations "Belongs to".
		 *
		 * @var array
		 */
		public $belongsTo = array(
			'Actionrole' => array(
				'className' => 'Actionrole',
				'foreignKey' => 'actionrole_id',
				'conditions' => null,
				'type' => null,
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
			'User' => array(
				'className' => 'User',
				'foreignKey' => 'user_id',
				'conditions' => null,
				'type' => null,
				'fields' => null,
				'order' => null,
				'counterCache' => null
			)
		);

		public function refresh( $actionrole_id, $user_id ) {
			$query = array(
				'fields' => array_merge(
					$this->Actionrole->fields(),
					$this->fields()
				),
				'contain' => false,
				'joins' => array(
					$this->Actionrole->join(
						'Actionroleresultuser',
						array(
							'type' => 'LEFT OUTER',
							'conditions' => array(
								'Actionroleresultuser.user_id' => $user_id
							)
						)
					)
				),
				'conditions' => array(
					'Actionrole.id' => $actionrole_id
				)
			);
			$actionrole = $this->Actionrole->find('first', $query);

			if( true === empty( $actionrole ) ) {
				$message = sprintf(
					'Impossible de charger l\'ActionRole d\'id %d',
					$actionrole_id
				);
				throw new RuntimeException( $message, 500 );
			}

			$record = array(
				$this->alias => array(
					'id' => Hash::get( $actionrole, 'Actionroleresultuser.id' ),
					'actionrole_id' => $actionrole_id,
					'user_id' => $user_id,
					'results' => $this->Actionrole->count(
						Hash::get( $actionrole, 'Actionrole.url' )
					)
				)
			);

			$this->create( $record );
			return $this->save( null, array( 'atomic' => false ) );
		}
	}
?>