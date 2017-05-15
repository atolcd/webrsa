<?php	
	/**
	 * Code source de la classe EntiteTag.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe EntiteTag ...
	 *
	 * @package app.Model
	 */
	class EntiteTag extends AppModel
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'EntiteTag';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = -1;

		/**
		 * Associations "Belongs to".
		 *
		 * @var array
		 */
		public $belongsTo = array(
			'Tag' => array(
				'className' => 'Tag',
				'foreignKey' => 'tag_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Foyer' => array(
				'className' => 'Foyer',
				'foreignKey' => 'fk_value',
				'conditions' => array(
					'EntiteTag.modele' => 'Foyer'
				),
				'fields' => '',
				'order' => ''
			),
			'Personne' => array(
				'className' => 'Personne',
				'foreignKey' => 'fk_value',
				'conditions' => array(
					'EntiteTag.modele' => 'Personne'
				),
				'fields' => '',
				'order' => ''
			),
		);
	}
?>