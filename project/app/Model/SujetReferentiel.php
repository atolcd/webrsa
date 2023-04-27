<?php
	/**
	 * Code source de la classe SujetReferentiel.
	 *
	 * PHP 7.2
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe SujetReferentiel ...
	 *
	 * @package app.Model
	 */
	class SujetReferentiel extends AppModel
	{

		/**
		 * Nom.
		 *
		 * @var string
		*/
		public $name = 'SujetReferentiel';

		public $useTable = 'sujetsreferentiels';

        public $useDbConfig = 'log';

		public $hasMany = array(
			'CorrespondanceReferentiel' => array(
				'className' => 'CorrespondanceReferentiel',
				'foreignKey' => 'sujetsreferentiels_id',
				'dependent' => true,
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