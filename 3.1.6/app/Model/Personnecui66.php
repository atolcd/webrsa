<?php
	/**
	 * Fichier source de la classe Personnecui66.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Personnecui66 est la classe contenant les allocataires du CUI.
	 *
	 * @package app.Model
	 */
	class Personnecui66 extends AppModel
	{
		public $name = 'Personnecui66';

		public $recursive = -1;

		public $hasOne = array(
			'Cui66' => array(
				'className' => 'Cui66',
				'foreignKey' => 'personnecui66_id',
				'dependent' => true,
			)
		);

		/**
		 * Behaviors utilisés par le modèle.
		 *
		 * @var array
		 */
		public $actsAs = array(
			'Formattable',
			'Postgres.PostgresAutovalidate',
			'Validation2.Validation2Formattable',
		);
	}
?>