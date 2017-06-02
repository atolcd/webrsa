<?php
	/**
	 * Code source de la classe Modecontact.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );
	App::uses( 'FrValidation', 'Validation' );

	/**
	 * La classe Modecontact ...
	 *
	 * @package app.Model
	 */
	class Modecontact extends AppModel
	{

		public $name = 'Modecontact';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		public $actsAs = array(
			'Validation2.Validation2Formattable' => array(
				'Validation2.Validation2DefaultFormatter' => array(
					'stripNotAlnum' => '/^numtel$/'
				)
			),
			'Validation2.Validation2RulesFieldtypes',
			'Postgres.PostgresAutovalidate'
		);

		public $validate = array(
			'numtel' => array(
				'phone' => array(
					'rule' => array( 'phone', null, 'fr' ),
					'allowEmpty' => true
				)
			),
			'numposte' => array(
				'alphaNumeric' => array(
					'rule' => array( 'alphaNumeric' ),
					'allowEmpty' => true
				),
				'between' => array(
					'rule' => array( 'between', 4, 4 ),
					'message' => 'Le numéro de poste est composé de 4 chiffres'
				)
			)
		);

		/**
		 * Liste de champs et de valeurs possibles qui ne peuvent pas être mis en
		 * règle de validation inList ou en contrainte dans la base de données en
		 * raison des valeurs actuellement en base, mais pour lequels un ensemble
		 * fini de valeurs existe.
		 *
		 * @see AppModel::enums
		 *
		 * @var array
		 */
		public $fakeInLists = array(
			'nattel' => array('D', 'T'),
            'matetel' => array('FAX', 'TEL', 'TFA'),
            'autorutitel' => array('A', 'I', 'R'),
            'autorutiadrelec' => array('A', 'I', 'R'),
		);

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
		 *
		 */
		public function dossierId( $modecontact_id ) {
			$qd_modecontact = array(
				'conditions'=> array(
					'Modecontact.id' => $modecontact_id
				),
				'fields' => array( 'Foyer.dossier_id' ),
				'joins' => array(
					$this->join( 'Foyer', array( 'type' => 'INNER' ) )
				),
				'recursive' => -1
			);
			$modecontact = $this->find('first', $qd_modecontact);

			if( !empty( $modecontact ) ) {
				return $modecontact['Foyer']['dossier_id'];
			}
			else {
				return null;
			}
		}

		/**
		 * Retourne une sous-requête permettant d'obtenir le "dernier" mode de
		 * contact du foyer (le dernier par-rapport à la valeur de la colonne id).
		 *
		 * @param string $field Le champ de la requête principale représentant l'id du foyer
		 * @param array $conditions Conditions supplémentaires pour la jointure
		 *	(l'alias du modèle sera aliasé avec le nom de la table qui est l'alias
		 *	de la sous-requête).
		 * @return type
		 */
		public function sqDerniere( $field, array $conditions = array() ) {
			$dbo = $this->getDataSource( $this->useDbConfig );
			$tableName = $dbo->fullTableName( $this, false, false );

			$conditions = Hash::merge(
				array( "{$tableName}.foyer_id = {$field}" ),
				array_words_replace( $conditions, array( $this->alias => $tableName ) )
			);

			$sql = $this->sq(
				array(
					'alias' => $tableName,
					'fields' => array( "{$tableName}.id" ),
					'conditions' => $conditions,
					'contain' => false,
					'order' => array( "{$tableName}.id DESC" ),
					'limit' => 1
				)
			);

			return $sql;
		}

	}
?>
