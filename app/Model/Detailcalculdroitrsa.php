<?php
	/**
	 * Code source de la classe Detailcalculdroitrsa.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Detailcalculdroitrsa ...
	 *
	 * @package app.Model
	 */
	class Detailcalculdroitrsa extends AppModel
	{
		public $name = 'Detailcalculdroitrsa';

		public $validate = array(
			'detaildroitrsa_id' => array(
				'numeric' => array(
					'rule' => array('numeric')
				),
			),
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
			'sousnatpf' => array(
				'RSDN1', 'RSDN2', 'RSIN1', 'RSUN1', 'RSUN2', 'RSUN3',
				'RSUN4', 'RSBN1', 'RSBN2', 'RSBN3', 'RSJN1', 'RCDN1',
				'RCDN2', 'RCIN1', 'RCUN1', 'RCUN2', 'RCUN3', 'RCUN4',
				'RCBN1', 'RCBN2', 'RCBN3', 'RCJN1', 'RSID1', 'RCID1',
				'RSDD1', 'RSDD2', 'RCDD1', 'RCDD2', 'RSUD1', 'RSUD2',
				'RSUD3', 'RSUD4', 'RCUD1', 'RCUD2', 'RCUD3', 'RCUD4',
				'RSBD1', 'RSBD2', 'RSBD3', 'RCBD1', 'RCBD2', 'RCBD3',
				'RSJD1', 'RCJD2'
			),
			'natpf' => array(
				'RSD', 'RSI', 'RSU', 'RSB', 'RCD', 'RCI', 'RCU', 'RCB',
				'RSJ', 'RCJ', 'RSD,RCD', 'RSD-RCD', 'RCD-RSD'
			),
		);

		public $belongsTo = array(
			'Detaildroitrsa' => array(
				'className' => 'Detaildroitrsa',
				'foreignKey' => 'detaildroitrsa_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);

		/**
		 * Les catégories de natures de prestations.
		 *
		 * Servira à remplir les $virtualFields dans le constructeur.
		 *
		 * @todo RSA jeune
		 *
		 * @var array
		 */
		public $categoriesNatspfs = array(
			'activite' => array( 'RCD', 'RCI', 'RCU', 'RCB', 'RCJ' ),
			//'jeune' => array( 'RCJ', 'RSJ' ),
			'majore' => array( 'RSI', 'RCI' ),
			'socle' => array( 'RSD', 'RSI', 'RSU', 'RSB', 'RSJ' ),
		);

		/**
		 * Liste complète des catégories de natures de prestations.
		 *
		 * @see Detaildroitrsa::vfNatpf()
		 *
		 * @var array
		 */
		public $natspfs = array(
			'socle' => array( 'RSD', 'RSI', 'RSU', 'RSB', 'RSJ' ),
			'activité' => array( 'RCD', 'RCI', 'RCU', 'RCB', 'RCJ' ),
			'majoré' => array( 'RSI', 'RCI' ),
			'jeune' => array( 'RCJ', 'RSJ' ),
		);

		/**
		 * Surcharge du constructeur permettant de remplir les champs virtuels
		 * natpf_{$categorie} qui sont des catégories de natures de prestations.
		 *
		 * @param integer|string|array $id Set this ID for this model on startup, can also be an array of options, see above.
		 * @param string $table Name of database table to use.
		 * @param string $ds DataSource connection name.
		 */
		public function __construct( $id = false, $table = null, $ds = null ) {
			parent::__construct( $id, $table, $ds );

			foreach( $this->categoriesNatspfs as $categorie => $natpfs ) {
				$this->virtualFields["natpf_{$categorie}"] = "\"{$this->alias}\".\"natpf\" IN ( '".implode( "', '", $natpfs )."' )";
			}
		}

		/**
		 * Retourne le dernier détail du droit rsa d'un dossier RSA
		 *
		 * @param string $detaildroitrsaIdField
		 * @return string
		 */
		public function sqDernier( $detaildroitrsaIdField = 'Detaildroitrsa.id' ) {
			return $this->sq(
				array(
					'fields' => array(
						'detailscalculsdroitsrsa.id'
					),
					'alias' => 'detailscalculsdroitsrsa',
					'conditions' => array(
						"detailscalculsdroitsrsa.detaildroitrsa_id = {$detaildroitrsaIdField}"
					),
					'order' => array( 'detailscalculsdroitsrsa.ddnatdro DESC' ),
					'limit' => 1
				)
			);
		}

		/**
		 * Champs virtuels pour connaître la nature de la prestation en une fois.
		 *
		 * @param string $alias
		 * @param array $conditions
		 * @return array
		 */
		public function vfsSummary( $alias = null, $conditions = array( 'Detailcalculdroitrsa.detaildroitrsa_id = Detaildroitrsa.id' ) ) {
			$alias = ( is_null( $alias ) ? $this->alias : $alias );

			$return = array();
			foreach( $this->categoriesNatspfs as $categorie => $natspfs ) {
				$return[$categorie] = $this->sq(
					array(
						'fields' => array(
							"{$this->alias}.{$this->primaryKey}"
						),
						'conditions' => array_merge(
							$conditions,
							array(
								"{$this->alias}.natpf" => $natspfs
							)
						)
					)
				);
				$return[$categorie] = "( EXISTS( {$return[$categorie]} ) ) AS \"{$alias}__natpf_{$categorie}\"";
			}

			return $return;
		}
	}
?>