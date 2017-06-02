<?php
	/**
	 * Code source de la classe Tauxcgcui66.
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Tauxcgcui66 ...
	 *
	 * @package app.Model
	 */
	class Tauxcgcui66 extends AppModel
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'Tauxcgcui66';

		/**
		 * Behaviors utilisés par le modèle.
		 *
		 * @var array
		 */
		public $actsAs = array(
			'Postgres.PostgresAutovalidate',
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes'
		);

		/**
		 * Relations belongsTo.
		 *
		 * @var array
		 */
		public $belongsTo = array(
			'Typecontratcui66' => array(
				'className' => 'Typecontratcui66',
				'foreignKey' => 'typecontrat',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);

		/**
		 * Retourne les options du CUI pour le CD 66.
		 *
		 * @return array
		 */
		public function options(){
			$optionsCui66 = ClassRegistry::init( 'Cui66' )->options();

			$options['Tauxcgcui66'] = array_merge( $optionsCui66['Cui'], $optionsCui66['Cui66'] );

			return $options;
		}
	}
?>