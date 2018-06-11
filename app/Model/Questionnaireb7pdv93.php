<?php
	/**
	 * Code source de la classe Questionnaireb7pdv93.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Questionnaireb7pdv93 ...
	 *
	 * @package app.Model
	 */
	class Questionnaireb7pdv93 extends AppModel
	{
		/**
		 * Ce model utilise cette table de la base de données
		 *
		 * @var string
		 */
		public $useTable = 'questionnairesb7pdvs93';

		/**
		 * Nom.
		 *
		 * @var string
		 */
		public $name = 'Questionnaireb7pdv93';

		/**
		 * Behaviors utilisés.
		 *
		 * @var array
		 */
		public $actsAs = array(
			'Allocatairelie',
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Validation2.Validation2RulesComparison',
			'Postgres.PostgresAutovalidate',
			'Questionnairepdv93',
		);

		/**
		 * Les règles de validation qui seront ajoutées aux règles de validation
		 * déduites de la base de données.
		 *
		 * @var array
		 */
		public $validate = array();

		/**
		 * Associations "Has one".
		 *
		 * @var array
		 */
		public $hasOne = array();

		/**
		 * Associations "Belongs to".
		 *
		 * @var array
		 */
		public $belongsTo = array(
			'Personne' => array(
				'className' => 'Personne',
				'foreignKey' => 'personne_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Expproromev3' => array(
				'className' => 'Entreeromev3',
				'foreignKey' => 'expproromev3_id',
				'conditions' => null,
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
			'Typeemploi' => array(
				'className' => 'Typeemploi',
				'foreignKey' => 'typeemploi_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Dureeemploi' => array(
				'className' => 'Dureeemploi',
				'foreignKey' => 'dureeemploi_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
		);

		/**
		 * Constructeur
		 *
		 * Sert à utiliser les traductions pour la validation de formulaire
		 */
		public function __construct($id = false, $table = null, $ds = null) {
			parent::__construct($id, $table, $ds);

			$this->validate = array(
				'typeemploi' => array(
					NOT_BLANK_RULE_NAME => array(
						'rule' => array( NOT_BLANK_RULE_NAME ),
						'message' => __d ('default', 'Validate::notEmpty')
					)
				),
				'dureeemploi' => array(
					NOT_BLANK_RULE_NAME => array(
						'rule' => array( NOT_BLANK_RULE_NAME ),
						'message' => __d ('default', 'Validate::notEmpty')
					)
				),
			);
		}

		/**
		 * Informations sur une personne
		 *
		 * @param integer $personne_id L'id de la personne traitée.
		 * @return array
		 */
		public function getPersonne ($personne_id) {
			$query = array(
				'conditions' => array(
					'Personne.id' => $personne_id
				),
				'contain' => false
			);

			return $this->Personne->find ('first', $query);
		}

		/**
		 * Liste des B7 pour une personne
		 *
		 * @param integer $personne_id L'id de la personne traitée.
		 * @return array
		 */
		public function getByPersonne ($personne_id) {
			$conditions = array(
				'personne_id' => $personne_id,
			);

			$query = $this->queryQuestionnaireb7pdv93ByCondition($conditions);
			$query['order'] = array('dateemploi ASC');

			return $this->find ('all', $query);
		}

		/**
		 * B7
		 *
		 * @param integer $id L'id du questionnaire.
		 * @return array
		 */
		public function getById ($id) {
			$conditions = array(
				'Questionnaireb7pdv93.id' => $id,
			);

			$query = $this->queryQuestionnaireb7pdv93ByCondition($conditions);
			$query['order'] = array('dateemploi ASC');

			return $this->find ('first', $query);
		}

		/**
		 * Renvoi la query de base pour les questionnaires b7
		 *
		 * @param array $conditions
		 * @return array
		 */
		public function queryQuestionnaireb7pdv93ByCondition( $conditions ) {
			return array (
				'contain' => array(
					'Personne',
					'Expproromev3' => array (
						'Appellationromev3',
					),
					'Typeemploi',
					'Dureeemploi',
				),
				'conditions' => $conditions,
			);
		}

		/**
		 * Sauvegarde du questionnaire B7 d'un allocataire.
		 *
		 * @param integer $personne_id L'id de la personne traitée.
		 * @param array $data Les données renvoyées par le formulaire B7
		 *	(Questionnaireb7pdv93)
		 * @return boolean
		 */
		public function saveFormData( $personne_id, array $data ) {
			$success = false;

			if (!isset ($data['Expproromev3']['appellationromev3_id'])) {
				return $success;
			}

			$domaineromev3_id = explode("_", $data['Expproromev3']['domaineromev3_id']);
			$metierromev3_id = explode("_", $data['Expproromev3']['metierromev3_id']);
			$appellationromev3_id = explode("_", $data['Expproromev3']['appellationromev3_id']);

			$this->loadModel('Entreeromev3');
			$entreeromev3 = $this-> Entreeromev3->find (
				'first',
				array (
					'conditions' => array (
						'familleromev3_id' => $data['Expproromev3']['familleromev3_id'],
						'domaineromev3_id' => $domaineromev3_id[1],
						'metierromev3_id' => $metierromev3_id[1],
						'appellationromev3_id' => $appellationromev3_id[1],
					),
				)
			);

			if (!isset ($entreeromev3['Entreeromev3'])) {
				return $success;
			}

			$dateemploi = $data['Questionnaireb7pdv93']['dateemploi']['year'].
				'-'.$data['Questionnaireb7pdv93']['dateemploi']['month'].
				'-1';

			$query = array ('Questionnaireb7pdv93' => array (
				'personne_id' => $personne_id,
				'typeemploi_id' => $data['Questionnaireb7pdv93']['typeemploi'],
				'dureeemploi_id' => $data['Questionnaireb7pdv93']['dureeemploi'],
				'expproromev3_id' => $entreeromev3['Entreeromev3']['id'],
				'dateemploi' => $dateemploi,
			));

			// Si modification
			if (isset ($data['Questionnaireb7pdv93']['id'])) {
				$query['Questionnaireb7pdv93']['id'] = $data['Questionnaireb7pdv93']['id'];
			}

			$questionnaireb7pdv93 = $this->save($query);

			if (is_numeric($questionnaireb7pdv93['Questionnaireb7pdv93']['id'])) {
				$success = true;
			}

			return $success;
		}
	}
?>