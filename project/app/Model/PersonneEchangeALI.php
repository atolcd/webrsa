<?php
	/**
	 * Code source de la classe PersonneEchangeALI.
	 *
	 * PHP 7.2
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe PersonneEchangeALI ...
	 *
	 * @package app.Model
	 */
	class PersonneEchangeALI extends AppModel
	{

		/**
		 * Nom.
		 *
		 * @var string
		*/
		public $name = 'PersonneEchangeALI';

		public $useTable = 'personnesechangesali';

		public $useDbConfig = 'log';

		public $recursive = 1;


		public $belongsTo = array(
			'RapportEchangeALI' => array(
				'className' => 'RapportEchangeALI',
				'foreignKey' => 'rapport_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Personne' => array(
				'className' => 'Personne',
				'foreignKey' => 'personne_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);



	}