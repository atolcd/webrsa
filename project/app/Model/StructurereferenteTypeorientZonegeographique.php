<?php
	/**
	 * Code source de la classe StructurereferenteTypeorientZonegeographique.
	 *
	 * PHP 7.2
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe StructurereferenteTypeorientZonegeographique ...
	 *
	 * @package app.Model
	 */
	class StructurereferenteTypeorientZonegeographique extends AppModel
	{
		public $name = 'StructurereferenteTypeorientZonegeographique';

		/**
		 * Ce model utilise cette table de la base de données
		 *
		 * @var string
		 */
		public $useTable = 'structuresreferentes_typesorients_zonesgeographiques';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		/**
		 * Les modèles utilisés par ce modèle, en plus des modèles présents dans
		 * les relations.
		 *
		 * @var array
		 */
		public $uses = array( 'Criterealgorithmeorientation', 'Zonegeographique' );

		public $belongsTo = array(
			'Structurereferente' => array(
				'className' => 'Structurereferente',
				'foreignKey' => 'structurereferente_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
            'Typeorient' => array(
				'className' => 'Typeorient',
				'foreignKey' => 'typeorient_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
			'Zonegeographique' => array(
				'className' => 'Zonegeographique',
				'foreignKey' => 'zonegeographique_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);


		/**
		 * Retourne un tableau comprenant la valeur de la table pour chaque croisement entre les villes et les structures référentes en paramètre
		 */
		public function tableauIndex($villes, $typesorients){
			$tableau = [];
			foreach ($villes as $keyville => $ville){
					foreach ($typesorients as $keytypeorient => $typeorient){
						$struct = $this->findByZonegeographiqueIdAndTypeorientId($keyville, $keytypeorient, 'Structurereferente.lib_struc');
						$tableau[$keyville][$keytypeorient] = $struct != [] ? $struct['Structurereferente']['lib_struc'] : '' ;
					}
			}

			return $tableau;
		}

		/**
		 * Retourne un tableau comprenant la valeur de la table pour chacun des types d'orientation passés en paramètre pour la ville passée en paramètre
		 */
		public function tableauIndexVille($ville, $typesorients){
			$tableau = [];
				foreach ($typesorients as $keytypeorient => $typeorient){
					$struct = $this->findByZonegeographiqueIdAndTypeorientId($ville, $keytypeorient);
					$tableau[$keytypeorient] = $struct != [] ? $struct['Structurereferente']['id'] : '' ;
				}

			return $tableau;
		}

		/**
		 * Vérifie s'il existe bien un lien pour chaque zone géographique avec le type d'orientation du critère final
		 * @return array
		 */
		public function checkBlocageAlgo(){
			$manques = [];
			$typefinal = $this->Criterealgorithmeorientation->find('first', ['conditions' => ['code' => 'FINAL']])['Typeorientenfant'];
			$villes = $this->Zonegeographique->find('list', ['fields' => 'libelle', 'conditions' => ['codeinsee ilike' => '93%']]);
			$id_type_orient_pe = Configure::read('Typeorient.emploi_id');
			$id_type_orient_ss = Configure::read('Typeorient.service_social_id');
			$lib_type_orient_pe = $this->Typeorient->find('first', ['conditions' => ['Typeorient.id' => $id_type_orient_pe]])['Typeorient']['lib_type_orient'];
			$lib_type_orient_ss = $this->Typeorient->find('first', ['conditions' => ['Typeorient.id' => $id_type_orient_ss]])['Typeorient']['lib_type_orient'];
			$total_villes = count($villes);

			if($typefinal['id'] != $id_type_orient_pe && $typefinal['id'] != $id_type_orient_ss){
				if(count($this->find('all', ['conditions' => ['StructurereferenteTypeorientZonegeographique.typeorient_id' => $typefinal['id']]])) != $total_villes){
					$manques[] = $typefinal['lib_type_orient'];
				}
			}
			if(count($this->find('all', ['conditions' => ['StructurereferenteTypeorientZonegeographique.typeorient_id' => $id_type_orient_pe]])) != $total_villes){
				$manques[] = $lib_type_orient_pe;
			}
			if(count($this->find('all', ['conditions' => ['StructurereferenteTypeorientZonegeographique.typeorient_id' => $id_type_orient_ss]])) != $total_villes){
				$manques[] = $lib_type_orient_ss;
			}

			return implode (",", $manques);
		}
	}
?>