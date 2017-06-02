<?php	
	/**
	 * Code source de la classe ActioncandidatPartenaire.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe ActioncandidatPartenaire ...
	 *
	 * @package app.Model
	 */
	class ActioncandidatPartenaire extends AppModel
	{
		public $name = 'ActioncandidatPartenaire';

		public $displayField = 'libstruc';

		public $actsAs = array (
			'Formattable',
			'ValidateTranslate'
		);

		public $validate = array(
			'actioncandidat_id' => array(
				array(
					'rule' => array('numeric'),
				),
				array(
					'rule' => array('notEmpty'),
				),
			),
			'partenaire_id' => array(
				array(
					'rule' => array('numeric'),
				),
				array(
					'rule' => array('notEmpty'),
				),
			),
		);

		public $belongsTo = array(
			'Actioncandidat' => array(
				'className' => 'Actioncandidat',
				'foreignKey' => 'actioncandidat_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Partenaire' => array(
				'className' => 'Partenaire',
				'foreignKey' => 'partenaire_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);
	}
?>