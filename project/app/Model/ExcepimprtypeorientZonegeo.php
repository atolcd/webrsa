<?php
	/**
	 * Code source de la classe ExcepimprtypeorientZonegeo.
	 *
	 * PHP 7.2
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe ExcepimprtypeorientZonegeo ...
	 *
	 * @package app.Model
	 */
	class ExcepimprtypeorientZonegeo extends AppModel
	{
		public $name = 'ExcepimprtypeorientZonegeo';

		/**
		 * Ce model utilise cette table de la base de données
		 *
		 * @var string
		 */
		public $useTable = 'excepimprtypesorients_zonesgeographiques';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		public $actsAs = array(
			'Postgres.PostgresAutovalidate',
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes'
		);

		/**
		 * Associations "Belongs to".
		 *
		 * @var array
		 */
		public $belongsTo = array(
			'Exceptionimpressiontypeorient' => array(
				'className' => 'Exceptionimpressiontypeorient',
				'foreignKey' => 'excepimprtypeorient_id'
			),
			'Zonegeographique' => array(
				'className' => 'Zonegeographique',
				'foreignKey' => 'zonegeographique_id'
			)
		);

		public function getZonesgeoParExceptionsTableau($id_exception){
			$zonesgeo = $this->findAllByExcepimprtypeorientId($id_exception);
			$liste_zonesgeo = [];
			if (!empty($zonesgeo)){
				foreach ($zonesgeo as $key => $value){
					$liste_zonesgeo[] = $value['Zonegeographique']['codeinsee'];
				}
			}

			return $liste_zonesgeo;
		}

	}