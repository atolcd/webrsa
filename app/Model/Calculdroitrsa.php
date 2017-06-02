<?php
	/**
	 * Code source de la classe Calculdroitrsa.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Calculdroitrsa ...
	 *
	 * @package app.Model
	 */
	class Calculdroitrsa extends AppModel
	{
		public $name = 'Calculdroitrsa';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		protected $_modules = array( 'caf' );

		public $actsAs = array(
			'Allocatairelie',
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Postgres.PostgresAutovalidate'
		);

		public $validate = array(
			'mtpersressmenrsa' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire'
				),
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
			'toppersdrodevorsa' => array('1', '0'),
		);

		public $belongsTo = array(
			'Personne' => array(
				'className' => 'Personne',
				'foreignKey' => 'personne_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);

		/**
		*	Fonction retournant un booléen précisant si la personne est soumise à drit et devoir ou non
		*/

		public function isSoumisAdroitEtDevoir( $personne_id ) {
			return (
				$this->find(
					'count',
					array(
						'conditions' => array(
							'Calculdroitrsa.personne_id' => $personne_id,
							'Calculdroitrsa.toppersdrodevorsa' => '1'
						),
						'contain' => false
					)
				) > 0
			);
		}
	}
?>
