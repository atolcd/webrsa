<?php
	/**
	 * Code source de la classe RapportEchangeALI.
	 *
	 * PHP 7.2
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe RapportEchangeALI ...
	 *
	 * @package app.Model
	 */
	class RapportEchangeALI extends AppModel
	{

		/**
		 * Nom.
		 *
		 * @var string
		*/
		public $name = 'RapportEchangeALI';

		public $useTable = 'rapportsechangesali';

		public $useDbConfig = 'log';

		public $virtualFields = array(
			'duree' => 'RapportEchangeALI.created - RapportEchangeALI.debut',
		);

		public $hasMany = array(
			'ErreurEchangeALI' => array(
				'className' => 'ErreurEchangeALI',
				'foreignKey' => 'rapport_id',
				'dependent' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
			'PersonneEchangeALI' => array(
				'className' => 'PersonneEchangeALI',
				'foreignKey' => 'rapport_id',
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

		public $belongsTo = array(
			'Structurereferente' => array(
				'className' => 'Structurereferente',
				'foreignKey' => 'ali_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);

		public function dejaImporte($id_ali, $date_generation){
			$rapport = $this->find(
				'first',
				[
					'conditions' => [
						'type' => 'import',
						'ali_id' => $id_ali,
						'date_fichier' => $date_generation
					]
				]
			);

			return (!empty($rapport));

		}

	}