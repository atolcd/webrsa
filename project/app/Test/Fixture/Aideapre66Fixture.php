<?php
	/**
	 * Code source de la classe Aideapre66Fixture.
	 *
	 * @package app.Test.Fixture
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	require_once( dirname( __FILE__ ).DS.'pgsql_constraints_fixture.php' );

	/**
	 * La classe Aideapre66Fixture ...
	 *
	 * @package app.Test.Fixture
	 */
	class Aideapre66Fixture extends PgsqlConstraintsFixture
	{
		/**
		 * On importe la définition de la table, pas les enregistrements.
		 *
		 * @var array
		 */
		public $import = array(
			'model' => 'Aideapre66',
			'records' => false
		);

		/**
		 * Définition des enregistrements.
		 *
		 * @var array
		 */
		public $records = array(
			array(
				'apre_id' => 1,
				'themeapre66_id' => 1,
				'typeaideapre66_id' => 1,
				'montantaide' => 1000,
				'motivdem' => null,
				'virement' => null,
				'versement' => null,
				'autorisationvers' => null,
				'datedemande' => null,
				'motifrejet' => null,
				'montantpropose' => 1000,
				'datemontantpropose' => null,
				'decisionapre' => 'ACC',
				'montantaccorde' => 1000,
				'datemontantaccorde' => null,
				'creancier' => null,
				'motifrejetequipe' => null,
			),
			array(
				'apre_id' => 2,
				'themeapre66_id' => 1,
				'typeaideapre66_id' => 1,
				'montantaide' => 1000,
				'motivdem' => null,
				'virement' => null,
				'versement' => null,
				'autorisationvers' => null,
				'datedemande' => null,
				'motifrejet' => null,
				'montantpropose' => 1000,
				'datemontantpropose' => null,
				'decisionapre' => 'ACC',
				'montantaccorde' => 1000,
				'datemontantaccorde' => null,
				'creancier' => null,
				'motifrejetequipe' => null,
			)
		);

		/**
		 * Création de la table et affectation de datemontantpropose à l'année actuelle
		 * @param Object $Db
		 * @return boolean
		 */
		public function create( $Db ) {
			$annee = date('Y');
			foreach ($this->records as $key => $value){
				$this->records[$key]['datemontantpropose'] = "{$annee}-06-06";
			}
			
			$return = parent::create( $Db );

			return $return;
		}
	}
?>