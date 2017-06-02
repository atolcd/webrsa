<?php
	/**
	 * Code source de la classe Correspondanceromev2v3.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Correspondanceromev2v3 ...
	 *
	 * @package app.Model
	 */
	class Correspondanceromev2v3 extends AppModel
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'Correspondanceromev2v3';

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
// TODO: ces associations dans l'autre sens
		/**
		 * Associations "Belongs to".
		 *
		 * @var array
		 */
		public $belongsTo = array(
			'Appellationv3' => array(
				'className' => 'Appellationv3',
				'foreignKey' => 'appellationromev3_id',
				'conditions' => null,
				'type' => null,
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
			'Coderomemetierdsp66' => array(
				'className' => 'Coderomemetierdsp66',
				'foreignKey' => 'coderomemetierdsp66_id',
				'conditions' => null,
				'type' => null,
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
			'Metierromev3' => array(
				'className' => 'Metierromev3',
				'foreignKey' => 'metierromev3_id',
				'conditions' => null,
				'type' => null,
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
		);
	}
?>