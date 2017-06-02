<?php
	/**
	 * Fichier source de la classe Personnecui.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Personnecui est la classe contenant les allocataires du CUI.
	 *
	 * @package app.Model
	 */
	class Personnecui extends AppModel
	{
		public $name = 'Personnecui';

		public $hasOne = array(
			'Cui' => array(
				'className' => 'Cui',
				'foreignKey' => 'personnecui_id',
				'dependent' => true,
			)
		);

		/**
		 * Behaviors utilisés par le modèle.
		 *
		 * @var array
		 */
		public $actsAs = array(
			'Postgres.PostgresAutovalidate',
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
		);
	}
?>