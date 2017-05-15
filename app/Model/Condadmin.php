<?php	
	/**
	 * Code source de la classe Condadmin.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Condadmin ...
	 *
	 * @package app.Model
	 */
	class Condadmin extends AppModel
	{
		public $name = 'Condadmin';

		public $validate = array(
			'avispcgdroitrsa_id' => array(
				'numeric' => array(
					'rule' => array('numeric')
				),
			),
		);
		
		/**
		 * Liste de champs et de valeurs possibles qui ne peuvent pas être mis en
		 * règle de validation inList ou en contrainte dans la base de données en
		 * raison des valeurs actuellement en base, mais pour lequels un ensemble
		 * fini de valeurs existe.
		 * 
		 * @see AppModel::enums
		 *
		 * @var array
		 */
		public $fakeInLists = array(
			'aviscondadmrsa' => array('D', 'A', 'R', 'S'),
            'moticondadmrsa' => array('NR', 'NS', 'EU', 'AU'),
		);

		public $belongsTo = array(
			'Avispcgdroitrsa' => array(
				'className' => 'Avispcgdroitrsa',
				'foreignKey' => 'avispcgdroitrsa_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);
	}
?>