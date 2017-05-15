<?php	
	/**
	 * Code source de la classe EpMembreep.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe EpMembreep ...
	 *
	 * @package app.Model
	 */
	class EpMembreep extends AppModel
	{
		public $name = 'EpMembreep';

		public $actsAs = array(
			'Autovalidate2',
			'Formattable',
			'ValidateTranslate'
		);

		public $belongsTo = array(
			'Ep' => array(
				'className' => 'Ep',
				'foreignKey' => 'ep_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Membreep' => array(
				'className' => 'Membreep',
				'foreignKey' => 'membreep_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);
	}
?>
