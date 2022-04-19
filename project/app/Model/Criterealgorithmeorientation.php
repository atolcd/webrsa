<?php
	/**
	 * Code source de la classe Criterealgorithmeorientation.
	 *
	 * PHP 7.2
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Criterealgorithmeorientation ...
	 *
	 * @package app.Model
	 */
	class Criterealgorithmeorientation extends AppModel
	{
		public $name = 'Criterealgorithmeorientation';
		public $useTable = 'criteresalgorithmeorientation';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		public $actsAs = array(
			'Postgres.PostgresAutovalidate',
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes'
		);

		public $belongsTo = array(
			'Typeorientparent' => array(
				'className' => 'Typeorient',
				'foreignKey' => 'type_orient_parent_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Typeorientenfant' => array(
				'className' => 'Typeorient',
				'foreignKey' => 'type_orient_enfant_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Valeurtag' => array(
				'className' => 'Valeurtag',
				'foreignKey' => 'valeurtag_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);

		/**
		 * Récupère le critere le plus proche de celui passé en paramètre selon le sens en paramètre
		 * @param integer $id id du critère
		 * @param string $sens 'monter' ou 'descendre'
		 */
		public function getLePlusProche($id, $sens){
			if ($sens != 'monter' && $sens != 'descendre'){
				$sens = 'monter';
			}
			$liste = $this->find('all', ['conditions' => ['Criterealgorithmeorientation.actif' => true],'order' => 'ordre ASC']);
			$index = array_search($id, array_column(array_column($liste,'Criterealgorithmeorientation'), 'id'));
			//On récupère l'id du critère juste au dessus
			if ($sens == 'monter'){
				return [
					'idAutre' => $liste[$index-1]['Criterealgorithmeorientation']['id'],
					'ordreAutre' => $liste[$index-1]['Criterealgorithmeorientation']['ordre'],
					'ordre' => $liste[$index]['Criterealgorithmeorientation']['ordre']
				];
			//On récupère l'id du critère juste en dessous
			} else if ($sens == 'descendre') {
				return [
					'idAutre' => $liste[$index+1]['Criterealgorithmeorientation']['id'],
					'ordreAutre' => $liste[$index+1]['Criterealgorithmeorientation']['ordre'],
					'ordre' => $liste[$index]['Criterealgorithmeorientation']['ordre']
				];
			}
		}


	}