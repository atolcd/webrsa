<?php
	/**
	 * Code source de la classe Dossiermodifie.
	 *
	 * PHP 7.2
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Dossiermodifie ...
	 *
	 * @package app.Model
	 */
	class Dossiermodifie extends AppModel
	{
		public $name = 'Dossiermodifie';

		/**
		 * Ce model utilise cette table de la base de données
		 *
		 * @var string
		 */
		public $useTable = 'dossiersmodifies';

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
			'Dossier' => array(
				'className' => 'Dossier',
				'foreignKey' => 'dossier_id',
				'conditions' => null,
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
			'User' => array(
				'className' => 'User',
				'foreignKey' => 'user_id',
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
		 * Ajoute ou met a jour une ligne dans la table des modifications du Dossier
		 * @param int $idFoyer: ID du foyer concerné
		 *
		 */
           public function setModified( $idDossier) {
			$data = array();
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
			foreach ( $idDossier['dossier_id'] AS $key => $dossier_id	) {
				// Initialisation des données à enregistrer
				$data[$key]['dossier_id'] = $dossier_id;
				$data[$key]['user_id'] = $user['User']['id'];
				// Initialisation du user à enregistrer
				$dossierModifie = $this->find(
					'first',
					array(
						'conditions' => array(
							'Dossiermodifie.dossier_id' => $dossier_id
						),
						'contain' => false
					)
	            );
				if ( ! empty ( $dossierModifie ) ){
					$data[$key]['id'] = $dossierModifie['Dossiermodifie']['id'];	
				}
			}
			$this->begin();
			$success = $this->saveall( $data, array( 'validate' => 'first', 'atomic' => false ) );
			if($success) {
				$return = true;
				$this->commit();
			} else {
				$return = false;
				$this->rollback();
			}
			return $return;
        }
    }
