<?php
	/**
	 * Fichier source du modèle Secteuracti.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * Classe Secteuracti.
	 *
	 * @package app.Model
	 */
	class Secteuracti extends AppModel
	{
		/**
		 * Nom.
		 *
		 * @var string
		 */
		public $name = 'Secteuracti';

		/**
		 * Tri par défaut
		 *
		 * @var array
		 */
		public $order = array( 'Secteuracti.name ASC' );

		/**
		 * Behaviors utilisés.
		 *
		 * @var array
		 */
		public $actsAs = array(
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Postgres.PostgresAutovalidate'
		);

		/**
		 * Liaisons "hasMany" avec d'autres modèles.
		 *
		 * @var array
		 */
		public $hasMany = array(
			'Cer93' => array(
				'className' => 'Cer93',
				'foreignKey' => 'secteuracti_id',
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
			'Expprocer93' => array(
				'className' => 'Expprocer93',
				'foreignKey' => 'secteuracti_id',
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