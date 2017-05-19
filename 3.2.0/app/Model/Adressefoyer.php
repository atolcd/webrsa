<?php
	/**
	 * Code source de la classe Adressefoyer.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Adressefoyer ...
	 *
	 * @package app.Model
	 */
	class Adressefoyer extends AppModel
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'Adressefoyer';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		/**
		 * Tri par défaut des enregistrements.
		 *
		 * @var array
		 */
		public $order = array( '%s.rgadr ASC' );

		/**
		 * Behaviors utilisés par le modèle.
		 *
		 * @var array
		 */
		public $actsAs = array(
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Postgres.PostgresAutovalidate'
		);

		/**
		 * Relations belongsTo
		 *
		 * @var array
		 */
		public $belongsTo = array(
			'Adresse' => array(
				'className'     => 'Adresse',
				'foreignKey'    => 'adresse_id'
			),
			'Foyer' => array(
				'className'     => 'Foyer',
				'foreignKey'    => 'foyer_id'
			)
		);

		/**
		 * Relations hasOne
		 *
		 * @var array
		 */
		public $hasOne = array(
			'VxTransfertpdv93' => array(
				'className' => 'Transfertpdv93',
				'foreignKey' => 'vx_adressefoyer_id',
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
			'NvTransfertpdv93' => array(
				'className' => 'Transfertpdv93',
				'foreignKey' => 'nv_adressefoyer_id',
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
		);

		/**
		 * Règles de validation.
		 *
		 * @var array
		 */
		public $validate = array(
			'rgadr' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire'
				)
			),
			'typeadr' => array(
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
			'rgadr' => array('01', '02', '03'),
			'typeadr' => array('D', 'P', 'R'),
			'etatadr' => array('CO', 'VO', 'VC', 'NC', 'AU'),
		);

		/**
		 * Retourne l'id d'un Dossier à partir de l'id d'une Adressefoyer.
		 *
		 * @param integer $adressefoyer_id
		 * @return integer
		 */
		public function dossierId( $adressefoyer_id ) {
			$qd_adressefoyer = array(
				'fields' => array( 'Foyer.dossier_id' ),
				'joins' => array(
					$this->join( 'Foyer', array( 'type' => 'INNER' ) )
				),
				'conditions' => array(
					'Adressefoyer.id' => $adressefoyer_id
				),
				'recursive' => -1
			);
			$adressefoyer = $this->find('first', $qd_adressefoyer);

			if( !empty( $adressefoyer ) ) {
				return $adressefoyer['Foyer']['dossier_id'];
			}
			else {
				return null;
			}
		}

		/**
		 * Foyers avec plusieurs adressesfoyers.rgadr = 01
		 * donc on s'assure de n'en prendre qu'un seul dont la dtemm est la plus récente
		 *
		 * @param string $field (ex.: Foyer.id)
		 * @return string
		 */
		public function sqDerniereRgadr01( $field ) {
			$alias = $this->getDataSource()->fullTableName( $this, false, false );

			$query = array(
				'alias' => $alias,
				'fields' => array( "{$alias}.{$this->primaryKey}" ),
				'contain' => false,
				'conditions' => array(
					"{$alias}.foyer_id = {$field}",
					"{$alias}.rgadr" => '01',
				),
				'order' => "{$alias}.dtemm DESC",
				'limit' => 1,

			);

			return $this->sq( $query );
		}

		/**
		 *   Fonction permettant de modifier le rang des adresses d'un foyer:
		 *       - Les adresses de rang 01 passent en rang 02
		 *       - Les adresses de rang 02 passent en rang 03
		 *       - Les adresses de rang 03 sont supprimées
		 *       - Les nouvelles adresses sont insérées avec un rang 01
		 *
		 * @param array $datas
		 * @return boolean
		 */
		public function saveNouvelleAdresse( $datas ) {
			$foyer_id = $datas['Adressefoyer']['foyer_id'];

			$success = $this->deleteAll(
				array(
					"\"{$this->alias}\".\"foyer_id\"" => $foyer_id,
					"\"{$this->alias}\".\"rgadr\"" => '03'
				)
			);

			foreach( array( '02' => '03', '01' => '02' ) as $oldRg => $newRg ) {
				$adrtmp = $this->find(
					'first',
					array(
						'conditions' => array(
							"{$this->alias}.foyer_id" => $foyer_id,
							"{$this->alias}.rgadr" => $oldRg
						),
						'contain' => false
					)
				);

				if( !empty( $adrtmp ) ) {
					$adrtmp[$this->alias]['rgadr'] = $newRg;
					$this->create( $adrtmp );
					$success = $this->save( null, array( 'atomic' => false ) ) && $success;
				}
			}

			$datas[$this->alias]['rgadr'] = '01';

			return $this->saveAll( $datas, array( 'atomic' => false ) ) && $success;
		}
	}
?>