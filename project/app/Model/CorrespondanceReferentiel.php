<?php
	/**
	 * Code source de la classe CorrespondanceReferentiel.
	 *
	 * PHP 7.2
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe CorrespondanceReferentiel ...
	 *
	 * @package app.Model
	 */
	class CorrespondanceReferentiel extends AppModel
	{

		/**
		 * Nom.
		 *
		 * @var string
		*/
		public $name = 'CorrespondanceReferentiel';

		public $useTable = 'correspondancesreferentiels';

		public $useDbConfig = 'log';

		public $belongsTo = array(
			'SujetReferentiel' => array(
				'className' => 'SujetReferentiel',
				'foreignKey' => 'sujetsreferentiels_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);

		public function getIdReferentielFromIdTable($code_sujet, $id_webrsa){

			$id =  $this->find(
				'first',
				[
					'fields' => ['id'],
					'conditions' => [
						'SujetReferentiel.code' => $code_sujet,
						'CorrespondanceReferentiel.id_dans_table' => $id_webrsa
					],
					'recursive' => 1
				]
			);

			if(!empty($id)){
				return $id['CorrespondanceReferentiel']['id'];
			} else {
				return null;
			}
		}


		public function getIdReferentielFromCode($code_sujet, $code){

			if($code_sujet == 'etatdos' && $code == null){
				$code = 'NULL';
			}

			$id = $this->find(
				'first',
				[
					'fields' => ['id'],
					'conditions' => [
						'SujetReferentiel.code' => $code_sujet,
						'CorrespondanceReferentiel.code' => $code
					],
					'recursive' => 1
				]
			);

			if(!empty($id)){
				return $id['CorrespondanceReferentiel']['id'];
			} else {
				return null;
			}
		}

		public function getZonesGeoALI($structure_id){
			$zonesgeo_ali = $this->find(
				'first',
				[
					'recursive' => 0,
					'conditions' => [
						'CorrespondanceReferentiel.id_dans_table' => $structure_id,
						'SujetReferentiel.code' => 'structuresreferentes'
					],
					'fields' => [
						'CorrespondanceReferentiel.structuresreferentes_zonesgeo'
					]
				]
			);

			return str_replace(['{', '}'], ['(', ')'], $zonesgeo_ali['CorrespondanceReferentiel']['structuresreferentes_zonesgeo']);

		}

		public function getIdTableFromIdReferentiel($code_sujet, $id_referentiel){

			$id = $this->find(
				'first',
				[
					'fields' => ['id_dans_table'],
					'conditions' => [
						'SujetReferentiel.code' => $code_sujet,
						'CorrespondanceReferentiel.id' => $id_referentiel
					],
					'recursive' => 1
				]
			);

			if(!empty($id)){
				return $id['CorrespondanceReferentiel']['id_dans_table'];
			} else {
				return null;
			}

		}


		public function getCodeFromIdReferentiel($code_sujet, $id_referentiel){

			$id = $this->find(
				'first',
				[
					'fields' => ['code'],
					'conditions' => [
						'SujetReferentiel.code' => $code_sujet,
						'CorrespondanceReferentiel.id' => $id_referentiel
					],
					'recursive' => 1
				]
			);

			if(!empty($id)){
				return $id['CorrespondanceReferentiel']['code'];
			} else {
				return null;
			}

		}

	}