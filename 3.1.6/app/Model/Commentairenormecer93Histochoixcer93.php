<?php
	/**
	 * Fichier source du modèle Commentairenormecer93Histochoixcer93.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * Classe Commentairenormecer93Histochoixcer93.
	 *
	 * @package app.Model
	 */
	class Commentairenormecer93Histochoixcer93 extends AppModel
	{

		/**
		 * Nom.
		 *
		 * @var string
		 */
		public $name = 'Commentairenormecer93Histochoixcer93';

		/**
		 * Récursivité.
		 *
		 * @var integer
		 */
		public $recursive = -1;

		/**
		 * Behaviors utilisés.
		 *
		 * @var array
		 */
		public $actsAs = array(
			'Validation.Autovalidate',
			'Formattable'
		);

		/**
		 * Règles de validation
		 *
		 * @var array
		 */
		public $validate = array(
			'histochoixcer93_id' => array(
				'numeric' => array(
					'rule' => array('numeric'),
				),
			),
			'commentairenormecer93_id' => array(
				'numeric' => array(
					'rule' => array('numeric'),
				),
			)
		);

		/**
		 * Liaisons "belongsTo" avec d'autres modèles.
		 *
		 * @var array
		 */
		public $belongsTo = array(
			'Commentairenormecer93' => array(
				'className' => 'Commentairenormecer93',
				'foreignKey' => 'commentairenormecer93_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Histochoixcer93' => array(
				'className' => 'Histochoixcer93',
				'foreignKey' => 'histochoixcer93_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);
	}
?>