<?php
	/**
	 * Code source de la classe Tiersprestataireapre.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );
	App::uses( 'FrValidation', 'Validation' );

	/**
	 * La classe Tiersprestataireapre ...
	 *
	 * @package app.Model
	 */
	class Tiersprestataireapre extends AppModel
	{
		public $name = 'Tiersprestataireapre';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		public $displayField = 'nomtiers';

		public $order = 'Tiersprestataireapre.id ASC';

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

		public $hasMany = array(
			'Actprof' => array(
				'className' => 'Actprof',
				'foreignKey' => 'tiersprestataireapre_id',
				'dependent' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
			'Formqualif' => array(
				'className' => 'Formqualif',
				'foreignKey' => 'tiersprestataireapre_id',
				'dependent' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
			'Formpermfimo' => array(
				'className' => 'Formpermfimo',
				'foreignKey' => 'tiersprestataireapre_id',
				'dependent' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
			'Permisb' => array(
				'className' => 'Permisb',
				'foreignKey' => 'tiersprestataireapre_id',
				'dependent' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			)
		);

		public $modelsFormation = array( 'Formqualif', 'Formpermfimo', 'Permisb', 'Actprof' );

		public $validate = array(
			'siret' => array(
				'isUnique' => array(
					'rule' => array( 'isUnique' ),
					'message' => 'Ce numéro SIRET existe déjà'
				),
				'numeric' => array(
					'rule' => array( 'numeric' ),
					'message' => 'Le numéro SIRET est composé de 14 chiffres',
					'allowEmpty' => true
				)
			),
			'numtel' => array(
				'phone' => array(
					'rule' => array( 'phone', null, 'fr' ),
					'allowEmpty' => true
				)
			),
			'adrelec' => array(
				'email' => array(
					'rule' => array( 'email' ),
					'message' => 'Email non valide',
					'allowEmpty' => true
				)
			),
			'aidesliees' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire'
				)
			)
		);

		/**
		 * Champs virtuels.
		 *
		 * @var array
		 */
		public $virtualFields = array(
			'adresse' => array(
				'type'      => 'string',
				'postgres'  => '( COALESCE( "%s"."numvoie", \'\' ) || \' \' || COALESCE( "%s"."typevoie", \'\' ) || \' \' || COALESCE( "%s"."nomvoie", \'\' ) || \' \' || COALESCE( "%s"."codepos", \'\' ) || \' \' || COALESCE( "%s"."ville", \'\' ) )'
			)
		);

		/**
		*   Fonction permettant de vérifier que le RIB est correct
		 *
		 * @deprecated since 3.2
		*/

		public function check_rib( $cbanque = null, $cguichet = null, $nocompte = null, $clerib = null ) {
			$cbanque = $this->data['Tiersprestataireapre']['etaban'];
			$cguichet = $this->data['Tiersprestataireapre']['guiban'];
			$nocompte = $this->data['Tiersprestataireapre']['numcomptban'];
			$clerib = $this->data['Tiersprestataireapre']['clerib'];

			$tabcompte = "";
			$len = strlen($nocompte);

			if ($len != 11) {
				return false;
			}

			for ($i = 0; $i < $len; $i++) {
				$car = substr($nocompte, $i, 1);
				if (!is_numeric($car)) {
					$c = ord($car) - (ord('A') - 1);
					$b = ($c + pow(2, ($c - 10)/9)) % 10;
					$tabcompte .= $b;
				}
				else {
					$tabcompte .= $car;
				}
			}
			$int = $cbanque . $cguichet . $tabcompte . $clerib;
			$return = (strlen($int) >= 21 && bcmod($int, 97) == 0);

			return $return;
		}

		/**
		 * Surcharge de la méthode enums pour ajouter le type de voie ainsi que
		 * des traductions distinctes pour le CG 58.
		 *
		 * @return array
		 */
		public function enums() {
			$results = parent::enums();

			$results[$this->alias]['typevoie'] = $this->Option->libtypevoie();
			$results[$this->alias]['aidesliees'] = $this->Option->natureAidesApres();

			return $results;
		}
	}
?>