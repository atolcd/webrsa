<?php	
	/**
	 * Code source de la classe Mtpcg66Pmtcpcg66.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Mtpcg66Pmtcpcg66 ...
	 *
	 * @package app.Model
	 */
	class Mtpcg66Pmtcpcg66 extends AppModel
	{
		public $name = 'Mtpcg66Pmtcpcg66';

		public $belongsTo = array(
			'Modeletraitementpcg66' => array(
				'className' => 'Modeletraitementpcg66',
				'foreignKey' => 'modeletraitementpcg66_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Piecemodeletypecourrierpcg66' => array(
				'className' => 'Piecemodeletypecourrierpcg66',
				'foreignKey' => 'piecemodeletypecourrierpcg66_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);
	}
?>