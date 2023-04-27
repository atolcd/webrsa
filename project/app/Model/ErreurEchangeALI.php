<?php
	/**
	 * Code source de la classe ErreurEchangeALI.
	 *
	 * PHP 7.2
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe ErreurEchangeALI ...
	 *
	 * @package app.Model
	 */
	class ErreurEchangeALI extends AppModel
	{

		/**
		 * Nom.
		 *
		 * @var string
		*/
		public $name = 'ErreurEchangeALI';

		public $useTable = 'erreursechangesali';

		public $useDbConfig = 'log';

		public $belongsTo = array(
			'RapportEchangeALI' => array(
				'className' => 'RapportEchangeALI',
				'foreignKey' => 'rapport_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);

	}