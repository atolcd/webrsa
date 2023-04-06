<?php
	/**
	 * Code source de la classe Exceptionimpressiontypeorient.
	 *
	 * PHP 7.2
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Exceptionimpressiontypeorient ...
	 *
	 * @package app.Model
	 */
	class Exceptionimpressiontypeorient extends AppModel
	{
		public $name = 'Exceptionimpressiontypeorient';

		/**
		 * Les modèles utilisés par ce modèle, en plus des modèles présents dans
		 * les relations.
		 *
		 * @var array
		 */
		public $uses = array( 'Orientstruct', 'ExceptionimpressiontypeorientOrigine', 'Structurereferente' );

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

		/**
		 * Associations "Has many".
		 *
		 * @var array
		 */
		public $hasMany = array(
			'ExceptionimpressiontypeorientOrigine' => array(
				'className' => 'ExceptionimpressiontypeorientOrigine',
				'foreignKey' => 'excepimprtypeorient_id',
				'conditions' => null,
				'order' => null,
				'limit' => null,
				'offset' => null,
				'dependent' => true,
				'exclusive' => null,
				'finderQuery' => null
			),
		);

		/**
		 * Associations "Has and belongs to many".
		 *
		 * @var array
		 */
		public $hasAndBelongsToMany = array(
			'Zonegeographique' => array(
				'className' => 'Zonegeographique',
				'joinTable' => 'excepimprtypesorients_zonesgeographiques',
				'foreignKey' => 'excepimprtypeorient_id',
				'associationForeignKey' => 'zonegeographique_id',
				'unique' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'finderQuery' => '',
				'deleteQuery' => '',
				'insertQuery' => '',
				'with' => 'ExcepimprtypeorientZonegeo'
			)
		);

		public function getPorteurprojet(){
			return [
				'1' => __m('Exceptionimpression.porteurprojet.1'),
				'0' => __m('Exceptionimpression.porteurprojet.0')
			];
		}

		public function getByTypeOrient($typeorient_id){
			$exceptions = $this->findAllByTypeorientId($typeorient_id, null, ['ordre' => 'asc']);

			foreach ($exceptions as $key => $exception) {
				$zones = [];
				foreach($exception['Zonegeographique'] as $zonegeo){
					array_push($zones, $zonegeo['libelle']);
				}
				$lib_struct = $this->Structurereferente->find('first', ['conditions' => ['Structurereferente.id' => $exception['Exceptionimpressiontypeorient']['structurereferente_id']], 'recursive' => -1, 'fields' => ['Structurereferente.lib_struc']]);
				$exceptions[$key]['Exceptionimpressiontypeorient']['origine'] = $this->ExceptionimpressiontypeorientOrigine->getOriginesParExceptions($exception['Exceptionimpressiontypeorient']['id']);
				$exceptions[$key]['Exceptionimpressiontypeorient']['structurereferente_libelle'] = empty($lib_struct) ? '' : $lib_struct['Structurereferente']['lib_struc'];
				$exceptions[$key]['Exceptionimpressiontypeorient']['act'] = $exception['Exceptionimpressiontypeorient']['act'] != '' ? __d('activite','ENUM::ACT::'.$exception['Exceptionimpressiontypeorient']['act']) : '';
				$exceptions[$key]['Exceptionimpressiontypeorient']['porteurprojet'] = $exception['Exceptionimpressiontypeorient']['porteurprojet'] !== null ? __m('Exceptionimpressiontypeorient.porteurprojet.'.$exception['Exceptionimpressiontypeorient']['porteurprojet']) : '';
				$exceptions[$key]['Exceptionimpressiontypeorient']['zonesgeo'] = implode(" <br> ", $zones);
			}
			return $exceptions;
		}

		public function getLePlusProche($id, $sens, $typeorient_id){
			$listeExceptions = $this->findAllByTypeorientId($typeorient_id, null, ['ordre' => 'asc']);
			$index = array_search($id, array_column(array_column($listeExceptions,'Exceptionimpressiontypeorient'), 'id'));

			if ($sens == 'monter'){
				return [
					'idAutre' => $listeExceptions[$index-1]['Exceptionimpressiontypeorient']['id'],
					'ordreAutre' => $listeExceptions[$index-1]['Exceptionimpressiontypeorient']['ordre'],
					'ordre' => $listeExceptions[$index]['Exceptionimpressiontypeorient']['ordre']
				];
			} else if ($sens == 'descendre') {
				return [
					'idAutre' => $listeExceptions[$index+1]['Exceptionimpressiontypeorient']['id'],
					'ordreAutre' => $listeExceptions[$index+1]['Exceptionimpressiontypeorient']['ordre'],
					'ordre' => $listeExceptions[$index]['Exceptionimpressiontypeorient']['ordre']
				];
			}
		}

		public function getPremierId($exceptions){
			if ($exceptions != null){
				return $exceptions[0]['Exceptionimpressiontypeorient']['id'];
			} else {
				return null;
			}
		}

		public function getDernierId($exceptions){
			if ($exceptions != null){
				return $exceptions[count($exceptions)-1]['Exceptionimpressiontypeorient']['id'];
			} else {
				return null;
			}
		}

		public function getOrigines($exception_id = null){
			$origines = $this->Orientstruct->enum('origine');
			$listeorigines = [];
			foreach($origines as $origine => $label){
				$excep = $this->ExceptionimpressiontypeorientOrigine->findByExcepimprtypeorientIdAndOrigine($exception_id, $origine);
				$listeorigines[$origine][0] = $label;
				if($exception_id != null && $this->ExceptionimpressiontypeorientOrigine->findByExcepimprtypeorientIdAndOrigine($exception_id, $origine)){
					$listeorigines[$origine][1] = 'checked';
				} else {
					$listeorigines[$origine][1] = '';
				}
			}

			return $listeorigines;
		}
	}