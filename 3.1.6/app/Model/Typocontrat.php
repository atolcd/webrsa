<?php	
	/**
	 * Code source de la classe Typocontrat.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Typocontrat ...
	 *
	 * @package app.Model
	 */
	class Typocontrat extends AppModel
	{
		public $name = 'Typocontrat';

		public $displayField = 'lib_typo';

		public $order = 'Typocontrat.id ASC';

		public $validate = array(
			'lib_typo' => array(
					'rule' => 'notEmpty',
					'message' => 'Champ obligatoire'
			)
		);

		public $hasMany = array(
			'Contratinsertion' => array(
				'className' => 'Contratinsertion',
				'foreignKey' => 'typocontrat_id',
				'dependent' => false,
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