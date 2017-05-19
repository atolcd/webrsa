<?php
	/**
	 * Code source de la classe Detaildroitrsa.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Detaildroitrsa ...
	 *
	 * @package app.Model
	 */
	class Detaildroitrsa extends AppModel
	{
		public $name = 'Detaildroitrsa';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		/**
		 * Behaviors utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $actsAs = array(
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Postgres.PostgresAutovalidate'
		);

		public $validate = array(
			'topsansdomfixe' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire'
				)
			),
			'dtoridemrsa' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire'
				)
			),

			'topfoydrodevorsa' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire'
				)
			),
			'nbenfautcha' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire'
				)
			),
			'oridemrsa' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire'
				)
			),
			'ddelecal' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire'
				)
			),
			'dfelecal' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire'
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
			'topsansdomfixe' => array('0', '1'),
			'topfoydrodevorsa' => array('1', '0'),
			'oridemrsa' => array('DEM', 'RMI', 'API'),
		);

		public $belongsTo = array(
			'Dossier' => array(
				'className' => 'Dossier',
				'foreignKey' => 'dossier_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);

		public $hasMany = array(
			'Detailcalculdroitrsa' => array(
				'className' => 'Detailcalculdroitrsa',
				'foreignKey' => 'detaildroitrsa_id',
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
			'Dossier' => array(
				'className' => 'Dossier',
				'foreignKey' => 'detaildroitrsa_id',
				'dependent' => false,
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

		/**
		*	Vérfication et envoi d'un booleen si le dossier est un RSA majoré ou non
		*	On passe en paramètre l'alias du modèle et du champ
		*/

		public function vfRsaMajore( $aliasDossierId = '"Dossier"."id"' ){
			return 'EXISTS(
				SELECT * FROM detailsdroitsrsa
					INNER JOIN detailscalculsdroitsrsa ON ( detailscalculsdroitsrsa.detaildroitrsa_id = detailsdroitsrsa.id )
					WHERE
						detailsdroitsrsa.dossier_id = '.$aliasDossierId.'
						AND detailscalculsdroitsrsa.natpf IN ( \'RCI\', \'RSI\' )
			)';
		}


		/**
		*	Vérfication et envoi d'un booleen si le dossier est un RSA socle ou non
		*	On passe en paramètre l'alias du modèle et du champ
		*/

		public function vfRsaSocle( $aliasDossierId = '"Dossier"."id"' ){
			return 'EXISTS(
				SELECT * FROM detailsdroitsrsa
					INNER JOIN detailscalculsdroitsrsa ON ( detailscalculsdroitsrsa.detaildroitrsa_id = detailsdroitsrsa.id )
					WHERE
						detailsdroitsrsa.dossier_id = '.$aliasDossierId.'
						AND detailscalculsdroitsrsa.natpf IN ( \'RSB\', \'RSD\', \'RSI\', \'RSU\' )
			)';
		}

		/**
		 * Retourne une sous-requête permettant d'obtenir en une seule chaîne de
		 * caractères la liste des différentes natures de prestations.
		 *
		 * @see Detailcalculdroitrsa::$natspfs
		 *
		 * @return string
		 */
		public function vfNatpf() {
			$return = array();

			foreach( $this->Detailcalculdroitrsa->natspfs as $categorie => $natspfs ) {
				$query = array(
					'fields' => array(
						"{$this->Detailcalculdroitrsa->alias}.{$this->Detailcalculdroitrsa->primaryKey}"
					),
					'conditions' => array(
						'Detailcalculdroitrsa.detaildroitrsa_id = Detaildroitrsa.id',
						"Detailcalculdroitrsa.natpf" => $natspfs
					)
				);
				$sql = $this->sq( $query );
				$return[] = "( CASE WHEN EXISTS( {$sql} ) THEN '{$categorie}' ELSE NULL END )";
			}

			return "ARRAY_TO_STRING( ARRAY[".implode( ', ', $return )."], ', ' )";

		}
	}
?>
