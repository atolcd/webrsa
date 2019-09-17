<?php
	/**
	 * Code source de la classe Recourgracieux.
	 *
	 * PHP 7.2
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );
	App::uses( 'WebrsaAccessRecoursgracieux', 'Utility' );

	/**
	 * La classe Recourgracieux ...
	 *
	 * @package app.Model
	 */
	class Recourgracieux extends AppModel
	{
		public $name = 'Recourgracieux';

		/**
		 * Ce model utilise cette table de la base de données
		 *
		 * @var string
		 */
		public $useTable = 'recoursgracieux';

		/**
		 * Les modèles qui seront utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array('WebrsaAccessRecoursgracieux', 'WebrsaRecourgracieux');

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		public $actsAs = array(
			'Gedooo.Gedooo',
			'Postgres.PostgresAutovalidate',
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
		);

		public $fakeInLists = array(
			'haspiecejointe' => array('0', '1'),
		);

		public $validate = array(
			'etat' => array(
				'inList' => array(
					'rule' => array('inList',
						array(
							'ATTAFECT', 'ATTINSTRUCTION','ATTVALIDATION','ATTIMPRESSION','ATTSIGNATURE','ATTENVOIE','TRAITER', 'ANNULER',
						)
					)
				)
			),
		);

		/**
		 * Associations "Belongs To".
		 * @var array
		 */
		public $belongsTo = array(
			'Foyer' => array(
				'className' => 'Foyer',
				'foreignKey' => 'foyer_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);

		/**
		 * Associations "Has many".
		 *
		 * @var array
		 */
		public $hasMany = array(
			'Typerecoursgracieux' => array(
				'className' => 'Typerecoursgracieux',
				'foreignKey' => 'typerecoursgracieux_id',
				'conditions' => null,
				'type' => 'LEFT OUTER',
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
			'Originerecoursgracieux' => array(
				'className' => 'Originerecoursgracieux',
				'foreignKey' => 'originerecoursgracieux_id',
				'conditions' => null,
				'type' => 'LEFT OUTER',
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
			'Fichiermodule' => array(
				'className' => 'Fichiermodule',
				'foreignKey' => false,
				'dependent' => false,
				'conditions' => array(
					'Fichiermodule.modele = \'Recourgracieux\'',
					'Fichiermodule.fk_value = {$__cakeID__$}'
				),
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			)
		);

		/**
		 * Retourne l'id d'un foyer à partir de l'id d'un RecoursGracieux.
		 *
		 * @param integer $recourgracieux_id
		 * @return integer
		 */
		public function foyerId( $recourgracieux_id ) {
			$qd_recourgracieux = "SELECT foyer_id FROM Recoursgracieux WHERE id = ".$recourgracieux_id." LIMIT 1";
			$recourgracieux_id = $this->query($qd_recourgracieux);
			if( !empty( $recourgracieux_id ) ) {
				return $recourgracieux_id[0][0]['foyer_id'];
			}
			else {
				return null;
			}
		}

		/**
		 * Retourne l'id d'un dossier à partir de l'id d'un RecoursGracieux.
		 *
		 * @param integer $recourgracieux_id
		 * @return integer
		 */
		public function dossierId( $recourgracieux_id ) {
			$qd_recourgracieux = "SELECT Foyers.dossier_id FROM Recoursgracieux INNER JOIN Foyers ON Foyers.id = Recoursgracieux.foyer_id WHERE Recoursgracieux.id = ".$recourgracieux_id." LIMIT 1";
			$recourgracieux = $this->query($qd_recourgracieux);
			if( !empty( $recourgracieux ) ) {
				return $recourgracieux[0][0]['dossier_id'];
			}
			else {
				return null;
			}

		}

		/**
		 * Renvoi le context permettant l'appel à WebrsaAccess
		 *
		 * @return array
		 */
		public function getContext() {
			return array(
				'controller' => ClassRegistry::init('RecoursgracieuxController'),
				'webrsaModelName' => $this->WebrsaRecourgracieux,
				'webrsaAccessName' => 'WebrsaAccessRecoursgracieux',
				'mainModelName' => $this
			);
		}

		/**
		 * Retourne les options nécessaires au formulaire de recherche, au formulaire,
		 * aux impressions, ...
		 *
		 * @return array
		 */
		public function options() {
			$options['Originerecoursgracieux']['origine'] = ClassRegistry::init( 'Originerecoursgracieux' )->find( 'list' );
			$options['Originerecoursgracieux']['origine_actif'] = ClassRegistry::init( 'Originerecoursgracieux' )->find( 'list', array( 'conditions' => array( 'actif' => true ) ) );
			$options['Typerecoursgracieux']['type'] = ClassRegistry::init( 'Typerecoursgracieux' )->find( 'list' );
			$options['Typerecoursgracieux']['type_actif'] = ClassRegistry::init( 'Typerecoursgracieux' )->find( 'list', array( 'conditions' => array( 'actif' => true ) ) );
			return $options;
		}

	}
