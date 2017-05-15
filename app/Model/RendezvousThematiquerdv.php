<?php
	/**
	 * Code source de la classe RendezvousThematiquerdv.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe RendezvousThematiquerdv ...
	 *
	 * @package app.Model
	 */
	class RendezvousThematiquerdv extends AppModel
	{
		/**
		 * Nom.
		 *
		 * @var string
		 */
		public $name = 'RendezvousThematiquerdv';

		/**
		 * Associations "Belongs to".
		 *
		 * @var array
		 */
		public $belongsTo = array(
			'Rendezvous' => array(
				'className' => 'Rendezvous',
				'foreignKey' => 'rendezvous_id',
				'conditions' => null,
				'type' => null,
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
			'Thematiquerdv' => array(
				'className' => 'Thematiquerdv',
				'foreignKey' => 'thematiquerdv_id',
				'conditions' => null,
				'type' => null,
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
		);
	}
?>