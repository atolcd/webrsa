<?php
	/**
	 * Code source de la classe Participantcomite.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );
	App::uses( 'FrValidation', 'Validation' );

	/**
	 * La classe Participantcomite ...
	 *
	 * @package app.Model
	 */
	class Participantcomite extends AppModel
	{
		public $name = 'Participantcomite';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		public $order = 'Participantcomite.id ASC';

		public $actsAs = array(
			'Occurences',
			'Validation2.Validation2Formattable' => array(
				'Validation2.Validation2DefaultFormatter' => array(
					'stripNotAlnum' => '/^numtel$/'
				)
			),
			'Validation2.Validation2RulesFieldtypes',
			'Postgres.PostgresAutovalidate'
		);

		/**
		 * Modèles utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array( 'Option' );

		public $hasAndBelongsToMany = array(
			'Comiteapre' => array(
				'className' => 'Comiteapre',
				'joinTable' => 'comitesapres_participantscomites',
				'foreignKey' => 'participantcomite_id',
				'associationForeignKey' => 'comiteapre_id',
				'unique' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'finderQuery' => '',
				'deleteQuery' => '',
				'insertQuery' => '',
				'with' => 'ComiteapreParticipantcomite'
			)
		);

		public $validate = array(
			'nom' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire'
				)
			),
			'qual' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire'
				)
			),
			'prenom' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire'
				)
			),
			'organisme' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire'
				)
			),
			'fonction' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire'
				)
			),
			'numtel' => array(
				'phone' => array(
					'rule' => array( 'phone', null, 'fr' ),
					'allowEmpty' => true
				)
			),
			'mail' => array(
				'email' => array(
					'rule' => array( 'email' ),
					'allowEmpty' => true,
					'message' => 'Le mail n\'est pas valide'
				)
			)
		);

		/**
		 * Champ virtuel "Nom complêt" (nomcomplet)
		 *
		 * @var array
		 */
		public $virtualFields = array(
			'nomcomplet' => array(
				'type'      => 'string',
				'postgres'  => 'COALESCE( "%s"."qual", \'\' ) || \' \' || COALESCE( "%s"."nom", \'\' ) || \' \' || COALESCE( "%s"."prenom", \'\' )'
			)
		);

		/**
		 * Surcharge de la méthode enums pour ajouter la civilité.
		 *
		 * @return array
		 */
		public function enums() {
			$results = parent::enums();

			$results[$this->alias]['qual'] = $this->Option->qual();

			return $results;
		}
	}
?>
