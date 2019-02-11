<?php
	/**
	 * Code source de la classe Aideagricole.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Aideagricole ...
	 *
	 * @package app.Model
	 */
	class Aideagricole extends AppModel
	{
		public $name = 'Aideagricole';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		public $validate = array(
			'infoagricole_id' => array(
				'numeric' => array(
					'rule' => array('numeric')
				),
			),
		);

		public $belongsTo = array(
			'Infoagricole' => array(
				'className' => 'Infoagricole',
				'foreignKey' => 'infoagricole_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);
	}
?>