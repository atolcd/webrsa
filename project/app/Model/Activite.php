<?php
	/**
	 * Code source de la classe Activite.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Activite ...
	 *
	 * @package app.Model
	 */
	class Activite extends AppModel
	{
		public $name = 'Activite';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		protected $_modules = array( 'caf' );

		/**
		 * Modèles utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array( 'Option' );

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
			'paysact' => array(
				'FRA', 'LUX', 'CEE', 'ACE', 'CNV', 'AUT'
			),
			'reg' => array(
				'AA', 'AD', 'AG', 'AL', 'AM', 'CL', 'EF', 'EN', 'FP',
				'FT', 'GE', 'MC', 'MI', 'MO', 'NI', 'PM', 'PT', 'RE',
				'RL', 'RP', 'SN', 'TG'
			),
			'act' => array(
				'ABS', 'AAP', 'CAT', 'SIN', 'ANI', 'AFA', 'APP', 'AMA',
				'AEN', 'SUR', 'CAC', 'CHR', 'CHA', 'CDA', 'ADN', 'ADA',
				'CAP', 'FDA', 'FDN', 'CDN', 'CNI', 'CPL', 'CHO', 'ABA',
				'AFD', 'AFC', 'AIN', 'ASS', 'ASP', 'CIS', 'CCV', 'MAT',
				'CGP', 'SAB', 'CSS', 'CJT', 'CIA', 'DSF', 'DEG', 'CAR',
				'CDD', 'CDI', 'CUI', 'MMA', 'CBS', 'CLD', 'ETU', 'EBO',
				'ETS', 'EXS', 'GSA', 'INP', 'NAS', 'HAN', 'ANP', 'IAD',
				'INT', 'INV', 'JNF', 'MMC', 'MMI', 'MAL', 'INF', 'SCI',
				'MLD', 'MAR', 'MOA', 'MOC', 'AMT', 'MNE', 'NOB', 'NCH',
				'PRE', 'RAC', 'EXP', 'ETI', 'RAT', 'RET', 'RSA', 'DNL',
				'SAL', 'SAC', 'SAV', 'SCO', 'SSA', 'SNA', 'AUT', 'SFP',
				'SNR', 'PIL', 'RMA', 'DET', 'TSA', 'VDI', 'VRP',
			),
			'natcontrtra' => array(
				'CA', 'CDD', 'CDI', 'CUI', 'CU1', 'CU2', 'CU3', 'CU4',
				'INT', 'AUT', 'AEN', 'VDI'
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
		 * Retourne une sous-requète permettant d'obtenir l'identifiant de la dernière action pour une
		 * personne donnée.
		 *
		 * @param string $personneId
		 * @return string
		 */
		public function sqDerniere( $personneId = 'Personne.id' ) {
			return $this->sq(
				array(
					'alias' => 'activites',
					'fields' => array(
						'activites.id'
					),
					'conditions' => array(
						"activites.personne_id = {$personneId}"
					),
					'order' => array( 'activites.ddact DESC' ),
					'limit' => 1
				)
			);
		}
	}
?>