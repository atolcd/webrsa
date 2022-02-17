<?php
	/**
	 * Code source de la classe Categorieutilisateur.
	 *
	 * PHP 7.2
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Categorieutilisateur ...
	 *
	 * @package app.Model
	 */
	class Exceptionsimpression extends AppModel
	{
		public $name = 'Exceptionsimpression';

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

		/**
		 * Associations "Belongs to".
		 *
		 * @var array
		 */
		public $belongsTo = array(
			'Typeorient' => array(
				'className' => 'Typeorient',
				'foreignKey' => 'typeorient_id',
				'conditions' => null,
				'type' => null,
				'fields' => null,
				'order' => null,
				'counterCache' => null
			)
		);

		public function getOrigines(){
			return [
				'C' => __m('Exceptionsimpression.origine.C'),
				'HC' => __m('Exceptionsimpression.origine.HC')
			];
		}

		public function getPorteurprojet(){
			return [
				'' => '',
				'1' => __m('Exceptionsimpression.porteurprojet.1'),
				'0' => __m('Exceptionsimpression.porteurprojet.0')
			];
		}

		public function getByTypeOrient($typeorient_id){
			$exceptions = $this->findAllByTypeorientId($typeorient_id, null, ['ordre' => 'asc']);

			foreach ($exceptions as $key => $exception) {
				$exceptions[$key]['Exceptionsimpression']['origine'] = __m('Exceptionsimpression.origine.'.$exception['Exceptionsimpression']['origine']);
				$exceptions[$key]['Exceptionsimpression']['act'] = $exception['Exceptionsimpression']['act'] != '' ? __m('Exceptionsimpression.act.'.$exception['Exceptionsimpression']['act']) : '';
				$exceptions[$key]['Exceptionsimpression']['porteurprojet'] = $exception['Exceptionsimpression']['porteurprojet'] !== null ? __m('Exceptionsimpression.porteurprojet.'.$exception['Exceptionsimpression']['porteurprojet']) : '';
			}

			return $exceptions;
		}

		public function getLePlusProche($id, $sens, $typeorient_id){
			$listeExceptions = $this->findAllByTypeorientId($typeorient_id, null, ['ordre' => 'asc']);
			$index = array_search($id, array_column(array_column($listeExceptions,'Exceptionsimpression'), 'id'));

			if ($sens == 'monter'){
				return [
					'idAutre' => $listeExceptions[$index-1]['Exceptionsimpression']['id'],
					'ordreAutre' => $listeExceptions[$index-1]['Exceptionsimpression']['ordre'],
					'ordre' => $listeExceptions[$index]['Exceptionsimpression']['ordre']
				];
			} else if ($sens == 'descendre') {
				return [
					'idAutre' => $listeExceptions[$index+1]['Exceptionsimpression']['id'],
					'ordreAutre' => $listeExceptions[$index+1]['Exceptionsimpression']['ordre'],
					'ordre' => $listeExceptions[$index]['Exceptionsimpression']['ordre']
				];
			}

		}

		public function getPremierId($exceptions){
			if ($exceptions != null){
				return $exceptions[0]['Exceptionsimpression']['id'];
			} else {
				return null;
			}
		}

		public function getDernierId($exceptions){
			if ($exceptions != null){
				return $exceptions[count($exceptions)-1]['Exceptionsimpression']['id'];
			} else {
				return null;
			}
		}


	}