<?php	
	/**
	 * Code source de la classe Fraisdeplacement66.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Fraisdeplacement66 ...
	 *
	 * @package app.Model
	 */
	class Fraisdeplacement66 extends AppModel
	{
		public $name = 'Fraisdeplacement66';

		public $actsAs = array(
			'Autovalidate2',
			'Formattable',
			'Frenchfloat' => array(
				'fields' => array(
					'nbkmvoiture',
					'nbtrajetvoiture',
					'nbtrajettranspub',
					'prixbillettranspub',
					'nbtotalkm',
					'forfaitvehicule',
					'nbnuithebergt',
					'nbrepas',
					'totalvehicule',
					'totalhebergt',
					'totaltranspub',
					'totalrepas'
				)
			),
		);

		public $validate = array(
			'aideapre66_id' => array(
				'numeric' => array(
					'rule' => array('numeric'),
				),
				array(
					'rule' => 'notEmpty'
				)
			),
			'destination' => array(
				array(
					'rule' => 'notEmpty',
					'message' => 'Champ obligatoire'
				)
			)
		);

		public $belongsTo = array(
			'Aideapre66' => array(
				'className' => 'Aideapre66',
				'foreignKey' => 'aideapre66_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);
	}
?>