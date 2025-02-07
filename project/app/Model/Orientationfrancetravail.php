<?php
	/**
	 * Code source de la classe Orientationfrancetravail.
	 *
	 * PHP 7.2
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Orientationfrancetravail ...
	 *
	 * @package app.Model
	 */
	class Orientationfrancetravail extends AppModel
	{

		/**
		 * Nom.
		 *
		 * @var string
		*/
		public $name = 'Orientationfrancetravail';

		public $useTable = 'orientations_francetravail';

		public $belongsTo = array(
			'Personne' => array(
				'className' => 'Personne',
				'foreignKey' => 'personne_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);
	}