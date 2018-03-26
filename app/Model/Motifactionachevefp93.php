<?php
	/**
	 * Code source de la classe Motifactionachevefp93.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractElementCataloguefp93', 'Model/Abstractclass' );

	/**
	 * La classe Motifactionachevefp93 contient les moifs de fin de l'action de la fiche prescription
	 * de prescription du CG 93.
	 *
	 * @package app.Model
	 */
	class Motifactionachevefp93 extends AbstractElementCataloguefp93
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'Motifactionachevefp93';

		/**
		 * Behaviors utilisés par le modèle.
		 *
		 * @var array
		 */
		public $actsAs = array(
			'Postgres.PostgresAutovalidate',
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
		);

		/**
		 * Associations "Has and belongs to many".
		 *
		 * @var array
		 */
		public $hasAndBelongsToMany = array(
			'Ficheprescription93' => array(
				'className' => 'Motifactionachevefp93',
				'joinTable' => 'fichesprescriptions93_motifsactionachevesfps93',
				'foreignKey' => 'motifactionachevefp93_id',
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
				'with' => 'Ficheprescription93Motifactionachevefp93'
			)
		);
	}
?>