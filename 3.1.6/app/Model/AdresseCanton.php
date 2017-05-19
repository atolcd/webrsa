<?php
	/**
	 * Code source de la classe AdresseCanton.
	 *
	 * @package app.Model
	 * @license Expression license is undefined on line 11, column 23 in Templates/CakePHP/CakePHP Model.php.
	 */

	/**
	 * La classe AdresseCanton ...
	 *
	 * @package app.Model
	 */
	class AdresseCanton extends AppModel
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'AdresseCanton';

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
		public $actsAs = array();
		
		/**
		 * Associations "Belongs to".
		 *
		 * @var array
		 */
		public $belongsTo = array(
			'Adresse' => array(
				'className' => 'Adresse',
				'foreignKey' => 'adresse_id',
				'conditions' => null,
				'type' => null,
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
			'Canton' => array(
				'className' => 'Canton',
				'foreignKey' => 'canton_id',
				'conditions' => null,
				'type' => null,
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
		);
		
		/**
		 * Met à jour la table de liaison AdresseCanton selon les conditions indiqué
		 * 
		 * @param mixed $conditions
		 * @param boolean $transaction vrai par defaut, effectue un begin et un commit dans ce cas
		 * @return boolean
		 */
		public function updateByConditions( $conditions, $transaction = true ) {
			$departement = Configure::read('Cg.departement');
			
			$query = array(
				'fields' => array(
					'Adresse.id',
					'Canton.id',
				),
				'joins' => array(
					$this->Canton->joinAdresse()
				),
				'conditions' => $conditions,
				'contain' => false
			);
			$results = $this->Adresse->find('all', $query);
			
			if ( $transaction ) {
				$this->begin();
			}
			
			$data = array();
			$success = true;
			foreach ( $results as $value ) {
				$success = $this->deleteAllUnBound(
					array(
						'adresse_id' => Hash::get($value, 'Adresse.id')
					), false
				);
				
				if ( !$success ) {
					break;
				}
				
				if ( Hash::get($value, 'Canton.id') ) {
					$data[] = array(
						'adresse_id' => Hash::get($value, 'Adresse.id'),
						'canton_id' => Hash::get($value, 'Canton.id'),
					);
				}
			}
			
			if ( $success && !empty($data) ) {
				$success = $this->saveMany($data);
			}
			
			if ( $transaction ) {
				if ( $success ) {
					$this->commit();
				}
				else {
					$this->rollback();
				}
			}
			
			return $success;
		}
	}
?>