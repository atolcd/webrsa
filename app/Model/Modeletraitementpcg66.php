<?php	
	/**
	 * Code source de la classe Modeletraitementpcg66.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Modeletraitementpcg66 ...
	 *
	 * @package app.Model
	 */
	class Modeletraitementpcg66 extends AppModel
	{
		public $name = 'Modeletraitementpcg66';

		public $actsAs = array(
			'Autovalidate2',
			'ValidateTranslate',
			'Formattable'
		);

		
		public $belongsTo = array(
			'Traitementpcg66' => array(
				'className' => 'Traitementpcg66',
				'foreignKey' => 'traitementpcg66_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Modeletypecourrierpcg66' => array(
				'className' => 'Modeletypecourrierpcg66',
				'foreignKey' => 'modeletypecourrierpcg66_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);
		
		public $hasAndBelongsToMany = array(
			'Piecemodeletypecourrierpcg66' => array(
				'className' => 'Piecemodeletypecourrierpcg66',
				'joinTable' => 'mtpcgs66_pmtcpcgs66',
				'foreignKey' => 'modeletraitementpcg66_id',
				'associationForeignKey' => 'piecemodeletypecourrierpcg66_id',
				'unique' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'finderQuery' => '',
				'deleteQuery' => '',
				'insertQuery' => '',
				'with' => 'Mtpcg66Pmtcpcg66'
			)
		);
	}
?>