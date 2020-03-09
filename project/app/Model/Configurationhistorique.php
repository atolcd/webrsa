<?php
	/**
	 * Code source de la classe Configurationhistorique.
	 *
	 * PHP 7.2
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Configurationhistorique ...
	 *
	 * @package app.Model
	 */
	class Configurationhistorique extends AppModel
	{
		public $name = 'Configurationhistorique';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		/**
		 * Ce model utilise cette table de la base de données
		 *
		 * @var string
		 */
		public $useTable = 'configurationshistoriques';

		public $uses = array( 'User');

		/**
		 * Associations "Belongs to".
		 *
		 * @var array
		 */
		public $belongsTo = array(
			'Configuration' => array(
				'className' => 'Configuration',
				'foreignKey' => 'configurations_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);

		/**
		 * Behaviors utilisés par le modèle.
		 *
		 * @var array
		 */
		public $actsAs = array(
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Postgres.PostgresAutovalidate'
		);

		/**
		 * Récupère l'historique d'une variable de configuration
		 *
		 * @param int $id
		 */
		public function getHisto($id) {
			return $this->find('all',
				array(
					'fields' => array(
						'Configuration.created',
						'Configurationhistorique.created',
						'Configurationhistorique.username'
					),
					'conditions' => array(
						'Configurationhistorique.configurations_id' => $id
					),
					'order' => array(
						'Configurationhistorique.created DESC'
					)
				)
			);
		}

		/**
		 * Sauvegarde de l'ancienne configuration et de la nouvelle pour avoir un historique
		 * Ne fonctionne que si il y a modification d'un utilisateur
		 *
		 * @param array $datas
		 */
		public function saveHisto($datas) {
			$oldConfig = $this->Configuration->find('first', array(
				'fields' => array('Configuration.value_variable'),
				'conditions' => array(
					'Configuration.id' => $datas['id']
				)
			));
			$dataToSave = array();
			$dataToSave['configurations_id'] = $datas['id'];
			$dataToSave['value_variable_old'] = $oldConfig['Configuration']['value_variable'];
			$dataToSave['value_variable_new'] = $datas['value_variable'];
			if($dataToSave['value_variable_old'] === $dataToSave['value_variable_new']) {
				return;
			}
			$user = $this->User->find(
				'first',
				array(
					'conditions' => array(
						'User.id' => AuthComponent::user('id')
					),
					'contain' => false
				)
			);
			$dataToSave['user_id'] = $user['User']['id'];
			$dataToSave['username'] = $user['User']['nom'] . ' ' . $user['User']['prenom'];
			$this->save($dataToSave);
		}
	}