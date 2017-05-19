<?php
	/**
	 * Fichier source du modèle Expprocer93.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * Classe Expprocer93.
	 *
	 * @package app.Model
	 */
	class Expprocer93 extends AppModel
	{
		/**
		 * Nom.
		 *
		 * @var string
		 */
		public $name = 'Expprocer93';

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
			'Formattable' => array(
				'amount' => array( 'nbduree' )
			),
			'Pgsqlcake.PgsqlAutovalidate',
		);

		/**
		 * Liaisons "belongsTo" avec d'autres modèles.
		 *
		 * @var array
		 */
		public $belongsTo = array(
			'Cer93' => array(
				'className' => 'Cer93',
				'foreignKey' => 'cer93_id',
				'conditions' => null,
				'type' => null,
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
			'Entreeromev3' => array(
				'className' => 'Entreeromev3',
				'foreignKey' => 'entreeromev3_id',
				'conditions' => null,
				'type' => null,
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
			'Metierexerce' => array(
				'className' => 'Metierexerce',
				'foreignKey' => 'metierexerce_id',
				'conditions' => null,
				'type' => null,
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
			'Naturecontrat' => array(
				'className' => 'Naturecontrat',
				'foreignKey' => 'naturecontrat_id',
				'conditions' => null,
				'type' => 'LEFT OUTER',
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
			'Secteuracti' => array(
				'className' => 'Secteuracti',
				'foreignKey' => 'secteuracti_id',
				'conditions' => null,
				'type' => null,
				'fields' => null,
				'order' => null,
				'counterCache' => null
			)
		);

		/**
		 * Règles de validation non déduites.
		 *
		 * @var array
		 */
		public $validate = array(
			'nbduree' => array(
				'inclusiveRange' => array(
					'rule' => array( 'inclusiveRange', 0, 1000 ),
					'message' => 'Veuillez entrer une valeur comprise entre 0 et 1000',
				)
			),
			'naturecontrat_id' => array(
				'notEmpty' => array(
					'rule' => array( 'notEmpty' ),
					'message' => 'Champ obligatoire'
				)
			)
		);
	}
?>