<?php
	/**
	 * Code source de la classe Titresejour.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Titresejour ...
	 *
	 * @package app.Model
	 */
	class Titresejour extends AppModel
	{
		public $name = 'Titresejour';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		protected $_modules = array( 'caf' );

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
			'nattitsej' => array(
				'AND', 'APF', 'APS', 'APT', 'AUT', 'CRA', 'CRC', 'CRE',
				'CST', 'CTS', 'DIS', 'DCE', 'FRO', 'MON', 'OFP', 'OMI',
				'PDC', 'RAF', 'RDA', 'REF', 'RCS', 'RPI', 'RRA', 'RRE',
				'RSA', 'RSR', 'RTS', 'RUN', 'RVA', 'VAC', 'VLS', 'CVC'
            ),
            'menttitsej' => array(
				'AC', 'AD', 'AM', 'AO', 'AP', 'A5', 'AS', 'AT', 'CA',
				'CN', 'CR', 'CS', 'DO', 'DR', 'DT', 'DS', 'EO', 'ET',
				'IO', 'JT', 'MF', 'PE', 'PF', 'PR', 'PS', 'PT', 'RE',
				'RF', 'RM', 'RR', 'RS', 'RT', 'R5', 'SA', 'SC', 'S5',
				'SO', 'TO', 'TT', 'VF', 'VI', 'V5', 'VO'
            ),
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
		 * Permet de récupérer le dernier titre de séjour d'une personne, en
		 * fonction de la date de début de titre de séjour.
		 *
		 * @param string $personneIdFied
		 * @return string
		 */
		public function sqDernier( $personneIdFied = 'Personne.id' ) {
			$query = array(
				'alias' => 'titressejour',
				'fields' => array( 'titressejour.id' ),
				'conditions' => array(
					"titressejour.personne_id = {$personneIdFied}"
				),
				'contain' => false,
				'order' => array( 'titressejour.ddtitsej DESC' ),
				'limit' => 1
			);

			return $this->sq( $query );
		}
	}
?>