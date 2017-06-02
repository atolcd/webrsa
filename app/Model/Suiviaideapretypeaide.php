<?php	
	/**
	 * Code source de la classe Suiviaideapretypeaide.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Suiviaideapretypeaide ...
	 *
	 * @package app.Model
	 */
	class Suiviaideapretypeaide extends AppModel
	{
		public $name = 'Suiviaideapretypeaide';

		public $belongsTo = array(
			'Suiviaideapre' => array(
				'className' => 'Suiviaideapre',
				'foreignKey' => 'suiviaideapre_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);
	}
?>