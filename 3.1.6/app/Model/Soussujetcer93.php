<?php
	/**
	 * Fichier source du modèle Soussujetcer93.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * Classe Soussujetcer93.
	 *
	 * @package app.Model
	 */
	class Soussujetcer93 extends AppModel
	{
		/**
		 * Nom.
		 *
		 * @var string
		 */
		public $name = 'Soussujetcer93';

		/**
		 * Tri par défaut
		 *
		 * @var array
		 */
		public $order = array( 'Soussujetcer93.name ASC' );


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
			'Formattable',
		);

		/**
		 * Liaisons "belongsTo" avec d'autres modèles.
		 *
		 * @var array
		 */
		public $belongsTo = array(
			'Sujetcer93' => array(
				'className' => 'Sujetcer93',
				'foreignKey' => 'sujetcer93_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);

		/**
		 * Liaisons "hasMany" avec d'autres modèles.
		 *
		 * @var array
		 */
		public $hasMany = array(
			'Cer93Sujetcer93' => array(
				'className' => 'Cer93Sujetcer93',
				'foreignKey' => 'soussujetcer93_id',
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
			'Valeurparsoussujetcer93' => array(
				'className' => 'Valeurparsoussujetcer93',
				'foreignKey' => 'soussujetcer93_id',
				'dependent' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			)
		);

	}
?>