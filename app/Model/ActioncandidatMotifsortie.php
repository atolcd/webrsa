<?php
	/**
	 * Code source de la classe ActioncandidatMotifsortie.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe ActioncandidatMotifsortie ...
	 *
	 * @package app.Model
	 */
	class ActioncandidatMotifsortie extends AppModel
	{
		public $name = 'ActioncandidatMotifsortie';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

        public $actsAs = array(
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Postgres.PostgresAutovalidate'
        );

		public $belongsTo = array(
			'Actioncandidat' => array(
				'className' => 'Actioncandidat',
				'foreignKey' => 'actioncandidat_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Motifsortie' => array(
				'className' => 'Motifsortie',
				'foreignKey' => 'motifsortie_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);
	}
?>