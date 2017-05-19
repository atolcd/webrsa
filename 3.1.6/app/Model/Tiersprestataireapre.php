<?php
	/**
	 * Code source de la classe Tiersprestataireapre.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Tiersprestataireapre ...
	 *
	 * @package app.Model
	 */
	class Tiersprestataireapre extends AppModel
	{
		public $name = 'Tiersprestataireapre';

		public $displayField = 'nomtiers';

		public $order = 'Tiersprestataireapre.id ASC';

		public $actsAs = array(
			'Formattable' => array(
				'phone' => array( 'numtel' )
			),
			'ValidateTranslate',
			'Validation.ExtraValidationRules',
		);

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
			'nomtiers' => array(
				'notEmpty' => array(
					'rule' => 'notEmpty',
					'message' => 'Champ obligatoire'
				),
				'isUnique' => array(
					'rule' => 'isUnique',
					'message' => 'Cette valeur est déjà utilisée'
				),
			),
			'siret' => array(
				'isUnique' => array(
					'rule' => 'isUnique',
					'message' => 'Ce numéro SIRET existe déjà'
				),
				'numeric' => array(
					'rule' => 'numeric',
					'message' => 'Le numéro SIRET est composé de 14 chiffres',
					'allowEmpty' => true
				)
			),
			'numtel' => array(
				'phoneFr' => array(
					'rule' => array( 'phoneFr' ),
					'allowEmpty' => true,
				)
			),
			'adrelec' => array(
				'email' => array(
					'rule' => 'email',
					'message' => 'Email non valide',
					'allowEmpty' => true
				)
			),
			'aidesliees' => array(
				'notEmpty' => array(
					'rule' => 'notEmpty',
					'message' => 'Champ obligatoire'
				)
			),
		);

		/**
		*   Fonction permettant de récupérer la liste des tiers prestataires ainsi
		*   qu'un champ virtuel 'deletable' qui indique si le tiers est lié à une aide de l'APRE
		*/

		public function adminList() {
			$tiersprestatairesapres = $this->find( 'all', array( 'recursive' => -1 ) );

			foreach( $tiersprestatairesapres as $key => $tiersprestataireapre ) {
				$subQueries = array();
				foreach( $this->modelsFormation as $model ) {
					$tableName = Inflector::tableize( $model );
					$subQueries[] = "( SELECT COUNT(*) FROM {$tableName} WHERE tiersprestataireapre_id = {$tiersprestatairesapres[$key]['Tiersprestataireapre']['id']} )";
				}
				$result = $this->query( 'SELECT ( '.implode( '+', $subQueries ).' ) AS count' );
				$result = Set::classicExtract( $result, '0.0.count' );

				$tiersprestatairesapres[$key]['Tiersprestataireapre']['deletable'] = empty( $result );
			}

			return $tiersprestatairesapres;
		}

		/**
		*   Fonction permettant de vérifier que le RIB est correct
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
	}
?>