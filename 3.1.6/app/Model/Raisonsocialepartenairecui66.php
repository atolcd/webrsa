<?php	
	/**
	 * Code source de la classe Raisonsocialepartenairecui66.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Raisonsocialepartenairecui66 ...
	 *
	 * @package app.Model
	 */
	class Raisonsocialepartenairecui66 extends AppModel
	{
		public $name = 'Raisonsocialepartenairecui66';

		public $displayField = 'name';
		
		public $recursive = -1;

		public $actsAs = array(
			'Formattable',
			'Pgsqlcake.PgsqlAutovalidate'
		);

		/*public $validate = array(
			'libstruc' => array(
				'isUnique' => array(
					'rule' => array( 'isUnique' ),
					'message' => 'Cette valeur est déjà utilisée'
				),
				'notEmpty' => array(
					'rule' => array( 'notEmpty' ),
					'message' => 'Champ obligatoire'
				)
			),
		);*/

		public $hasMany = array(
			'Partenaire' => array(
				'className' => 'Partenaire',
				'foreignKey' => 'raisonsocialepartenairecui66_id',
				'dependent' => false,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
		);
	}
?>