<?php
	/**
	 * Code source de la classe DetaildifdispRev.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe DetaildifdispRev ...
	 *
	 * @package app.Model
	 */
	class DetaildifdispRev extends AppModel
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'DetaildifdispRev';

		/**
		 * Behaviors utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $actsAs = array(
			'Enumerable'
		);

		/**
		 * Associations "Belongs to".
		 *
		 * @var array
		 */
		public $belongsTo = array(
			'DspRev' => array(
				'className' => 'DspRev',
				'foreignKey' => 'dsp_rev_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
		);
	}
?>