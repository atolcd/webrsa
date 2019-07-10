<?php
	/**
	 * Code source de la classe Historiqueetat.
	 *
	 * PHP 7.2
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Historiqueetat ...
	 *
	 * @package app.Model
	 */
	class Historiqueetat extends AppModel
	{
		public $name = 'Historiqueetat';

		/**
		 * Ce model utilise cette table de la base de données
		 *
		 * @var string
		 */
		public $useTable = 'historiqueetats';

		/**
		 * Les modèles qui seront utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array();

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		public $actsAs = array(
			'Postgres.PostgresAutovalidate',
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
		);

		/**
		 * Associations "Belongs To".
		 * @var array
		 */
		public $belongsTo = array(
			'User' => array(
				'className' => 'User',
				'foreignKey' => 'user_id',
				'conditions' => null,
				'fields' => '',
				'order' => ''
			),
			'Foyer' => array(
				'className' => 'Foyer',
				'foreignKey' => 'foyer_id',
				'conditions' => null,
				'fields' => null,
				'order' => null,
				'counterCache' => null
			)
		);

       /**
		 * Associations "Has many".
		 *
		 * @var array
		 */
		public $hasMany = array();

        /**
         * Ajoute une ligne dans l'historique selon le modèle utilisé
         * @param string $modelName : nom de modèle qui appelle l'historisation
         * @param int $idModel: ID du modèle
		 * @param int $idModelParent: ID du modele parent (possible à null)
		 * @param string $action: action réalisée (add, edit, delete, cance, etc.)
		 * @param string $etat: nouvel état de l'id du modèle
		 * @param int $idFoyer: ID du foyer concerné
         *
         */
        public function setHisto($modelName, $idModel, $idModelParent, $action, $etat, $idFoyer) {
            $data = array();

            // Initialisation des données à enregistrer selon les paramètres passés
            $data['modele'] = $modelName;
            $data['modele_id'] = $idModel;
            $data['modeleparent_id'] = $idModelParent;
            $data['evenement'] = $action;
            $data['etat'] = $etat;
            $data['foyer_id'] = $idFoyer;

            // Initialisation du user à enregistrer
            $User = ClassRegistry::init( 'User' );
			$user = $User->find(
				'first',
				array(
					'conditions' => array(
						'User.id' => AuthComponent::user('id')
					),
					'contain' => false
				)
            );

            $data['user_id'] = $user['User']['id'];
            $data['nom'] = $user['User']['nom'];
            $data['prenom'] = $user['User']['prenom'];

            $this->begin();
            $success = $this->save( $data, array( 'validate' => 'first', 'atomic' => false ) );
            if($success) {
				$return = true;
                $this->commit();
            } else {
				$return = false;
                $this->rollback();
			}

			return $return;
        }

		/**
		 * Récupère l'historique d'un modele selon le nom du modele, de son id et l'action si besoin
		 * @param string $modelName
		 * @param int $idModel
		 * @param string $action
		 * @param int $idModelParent
		 *
		 * @return array
		 */
		public function getHisto($modelName, $idModel, $action = null,  $idModelParent = null) {
			$conditions = array(
				'modele' => $modelName,
				'modele_id' => $idModel,
				'modeleparent_id' => $idModelParent
			);

			if($action) {
				$conditions = $conditions + array('evenement' => $action);
			}

			return $this->find('all', array(
					'conditions' => $conditions
					)
				);
		}
    }
