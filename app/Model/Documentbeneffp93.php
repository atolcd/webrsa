<?php
	/**
	 * Code source de la classe Documentbeneffp93.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractElementCataloguefp93', 'Model/Abstractclass' );

	/**
	 * La classe Documentbeneffp93 contient les intitulés des documents dont le
	 * bénéficiaire est invité à se munir pour la fiche de prescription du CG 93.
	 *
	 * @package app.Model
	 */
	class Documentbeneffp93 extends AbstractElementCataloguefp93
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'Documentbeneffp93';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = -1;

		/**
		 * Behaviors utilisés par le modèle.
		 *
		 * @var array
		 */
		public $actsAs = array(
			'Postgres.PostgresAutovalidate',
			'Validation2.Validation2Formattable',
		);

		/**
		 * Associations "Has and belongs to many".
		 *
		 * @var array
		 */
		public $hasAndBelongsToMany = array(
			'Ficheprescription93' => array(
				'className' => 'Ficheprescription93',
				'joinTable' => 'documentsbenefsfps93_fichesprescriptions93',
				'foreignKey' => 'documentbeneffp93_id',
				'associationForeignKey' => 'ficheprescription93_id',
				'unique' => true,
				'conditions' => null,
				'fields' => null,
				'order' => null,
				'limit' => null,
				'offset' => null,
				'finderQuery' => null,
				'deleteQuery' => null,
				'insertQuery' => null,
				'with' => 'Documentbeneffp93Ficheprescription93'
			),
		);
	}
?>