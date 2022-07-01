<?php
	/**
	 * Fichier source du modèle Soussujetcer.
	 *
	 * PHP 7.2
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * Classe Soussujetcer.
	 *
	 * @package app.Model
	 */
	class Soussujetcer extends AppModel
	{
		/**
		 * Nom.
		 *
		 * @var string
		 */
		public $name = 'Soussujetcer';

		public $useTable = 'soussujetscers';

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

		/**
		 * Liaisons "belongsTo" avec d'autres modèles.
		 *
		 * @var array
		 */
		public $belongsTo = array(
			'Sujetcer' => array(
				'className' => 'Sujetcer',
				'foreignKey' => 'sujetcer_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);

		/**
		 * Liaisons "hasMany" avec d'autres modèles.
		 *
		 * @var array
		 */
		public $hasMany = array(
			'Valeurparsoussujetcer' => array(
				'className' => 'Valeurparsoussujetcer',
				'foreignKey' => 'soussujetcer_id',
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

		/**
		 * Liaisons "hasAndBelongsToMany" avec d'autres modèles.
		 *
		 * @var array
		 */
		public $hasAndBelongsToMany = array(
            'Contratinsertion' => array(
				'className' => 'Contratinsertion',
				'joinTable' => 'contratsinsertion_sujetscers',
				'foreignKey' => 'soussujetcer_id',
				'associationForeignKey' => 'contratinsertion_id',
				'unique' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
			),
		);

		public function listOptions() {

			$results = $this->find(
				'list',
				array(
					'fields' => array(
						'Soussujetcer.id',
						'Soussujetcer.libelle',
						'Sujetcer.libelle'
					),
					'recursive' => -1,
					'joins' => array(
						$this->join( 'Sujetcer', array( 'type' => 'INNER' ) )
					),
					'order' => array(
						'Sujetcer.libelle ASC',
						'Soussujetcer.libelle'
					)
				)
			);
			return $results;
		}

	}