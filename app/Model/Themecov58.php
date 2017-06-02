<?php
	/**
	 * Code source de la classe Themecov58.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Themecov58 ...
	 *
	 * @package app.Model
	 */
	class Themecov58 extends AppModel
	{
		public $name = 'Themecov58';

		public $actsAs = array(
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Postgres.PostgresAutovalidate',
		);

		public $hasMany = array(
			'Dossiercov58' => array(
				'className' => 'Dossiercov58',
				'foreignKey' => 'themecov58_id',
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
		 * Retourne la liste des thèmes traités par le regroupement.
		 *
		 * @return array
		 */
		public function themes() {
			$enums = $this->enums();
			foreach( array_keys( $enums[$this->alias] ) as $key ) {
 				if( substr( $key, -2 ) != Configure::read( 'Cg.departement' ) ) {
					unset( $enums[$this->alias][$key] );
				}
			}
			return array_keys( $enums[$this->alias] );
		}

	}
?>
