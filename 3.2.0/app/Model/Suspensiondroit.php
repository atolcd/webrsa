<?php
	/**
	 * Code source de la classe Suspensiondroit.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Suspensiondroit ...
	 *
	 * @package app.Model
	 */
	class Suspensiondroit extends AppModel
	{
		public $name = 'Suspensiondroit';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

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

		public $belongsTo = array(
			'Situationdossierrsa' => array(
				'className' => 'Situationdossierrsa',
				'foreignKey' => 'situationdossierrsa_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
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
			'motisusdrorsa' => array(
				'DA', 'DB', 'DC', 'DD', 'DE', 'DF', 'DG', 'DH', 'DI',
				'DJ', 'DK', 'DL', 'DM', 'DN', 'DO', 'DP', 'DQ', 'DR',
				'GF', 'GR', 'GA', 'GS', 'GC', 'GI', 'GX', 'GE', 'GJ',
				'GK', 'GL'
			),
            'natgroupfsus' => array('RSA', 'RSX', 'RCX', 'DIF', 'HOS', 'ISO'),
		);

		/**
		 * Retourne une sous-requête permettant d'avoir la dernière entrée de la table (avec un tri
		 * descendant sur ddsusdrorsa), pour une entrée de situationsdossiersrsa.
		 * Fonctionne si une entrée existe pour Situationdossierrsa ou pas.
		 *
		 * @param string $situationdossierrsaId
		 * @return string
		 */
		public function sqDerniere( $situationdossierrsaId = 'Situationdossierrsa.id' ) {
			return $this->sq(
				array(
					'alias' => 'suspensionsdroits',
					'fields' => array( "suspensionsdroits.id" ),
					'contain' => false,
					'conditions' => array(
						"suspensionsdroits.situationdossierrsa_id = {$situationdossierrsaId}"
					),
					'order' => array( "suspensionsdroits.ddsusdrorsa DESC" ),
					'limit' => 1
				)
			);
		}
	}
?>
