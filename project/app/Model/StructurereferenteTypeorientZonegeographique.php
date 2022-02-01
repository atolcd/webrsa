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
	}
?>