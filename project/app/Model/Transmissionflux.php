<?php
	/**
	 * Code source de la classe Transmissionflux.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Transmissionflux ...
	 *
	 * @package app.Model
	 */
	class Transmissionflux extends AppModel
	{
		public $name = 'Transmissionflux';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		public $validate = array(
			'identificationflux_id' => array(
				'numeric' => array(
					'rule' => array('numeric'),
				),
			),
		);

		public $belongsTo = array(
			'Identificationflux' => array(
				'className' => 'Identificationflux',
				'foreignKey' => 'identificationflux_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);
	}
?>