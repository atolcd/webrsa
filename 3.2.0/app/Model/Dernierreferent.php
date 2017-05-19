<?php
	/**
	 * Code source de la classe Dernierreferent.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Dernierreferent ...
	 *
	 * @package app.Model
	 */
	class Dernierreferent extends AppModel
	{
		public $name = 'Dernierreferent';

		/**
		 * Behaviors utilisés par le modèle.
		 *
		 * @var array
		 */
		public $actsAs = array(
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Postgres.PostgresAutovalidate'
		);

		public $belongsTo = array(
			'Referent' => array(
				'className' => 'Referent',
				'foreignKey' => 'referent_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'ReferentPrecedent' => array(
				'className' => 'Referent',
				'foreignKey' => 'prevreferent_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'DernierReferent' => array(
				'className' => 'Referent',
				'foreignKey' => 'dernierreferent_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
		);

		public function listOptions() {
			return $this->Referent->find('list',
				array(
					'joins' => array($this->Referent->join('Dernierreferent')),
					'conditions' => array('Dernierreferent.referent_id = Dernierreferent.dernierreferent_id'),
					'order' => array('Referent.nom', 'Referent.prenom')
				)
			);
		}
	}
?>