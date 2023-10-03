<?php
	/**
	 * Code source de la classe StructurereferenteZonegeographique.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe StructurereferenteZonegeographique ...
	 *
	 * @package app.Model
	 */
	class StructurereferenteZonegeographique extends AppModel
	{
		public $name = 'StructurereferenteZonegeographique';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		public $validate = array(
			'structurereferente_id' => array(
				'numeric' => array(
					'rule' => array('numeric'),
				),
			),
			'zonegeographique_id' => array(
				'numeric' => array(
					'rule' => array('numeric'),
				),
			),
		);

		public $belongsTo = array(
			'Structurereferente' => array(
				'className' => 'Structurereferente',
				'foreignKey' => 'structurereferente_id',
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

		public function getStructuresOkParZonesGeos($zonesgeo){
			$structuresOK = $this->query(
				"
				select array_agg(distinct s.id)
				from structuresreferentes s
				left join structuresreferentes_zonesgeographiques sz  on s.id = sz.structurereferente_id
				where sz.zonegeographique_id in {$zonesgeo}
				or s.filtre_zone_geo = false
				"
			);

			return json_decode(str_replace(['{', '}'], ['[', ']'], $structuresOK[0][0]['array_agg']));

		}
	}
?>