<?php
	/**
	 * Code source de la classe Zonegeographique.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Zonegeographique ...
	 *
	 * @package app.Model
	 */
	class Zonegeographique extends AppModel
	{
		public $name = 'Zonegeographique';

		public $displayField = 'libelle';

		public $order = array( 'libelle ASC' );

		public $validate = array(
			'libelle' => array(
				array(
					'rule' => 'notEmpty',
					'message' => 'Champ obligatoire'
				),
				array(
					'rule' => 'isUnique',
					'message' => 'Valeur déjà présente'
				)
			),
			'codeinsee' => array(
				array(
					'rule' => 'notEmpty',
					'message' => 'Champ obligatoire'
				),
				array(
					'rule' => 'isUnique',
					'message' => 'Valeur déjà présente'
				)
			)
		);

		public $hasMany = array(
			'Contratinsertion' => array(
				'className' => 'Contratinsertion',
				'foreignKey' => 'zonegeographique_id',
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
			'Canton' => array(
				'className' => 'Canton',
				'foreignKey' => 'zonegeographique_id',
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
		);

		public $hasAndBelongsToMany = array(
			'Actioncandidat' => array(
				'className' => 'Actioncandidat',
				'joinTable' => 'actionscandidats_zonesgeographiques',
				'foreignKey' => 'zonegeographique_id',
				'associationForeignKey' => 'actioncandidat_id',
				'unique' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'finderQuery' => '',
				'deleteQuery' => '',
				'insertQuery' => '',
				'with' => 'ActioncandidatZonegeographique'
			),
			'Ep' => array(
				'className' => 'Ep',
				'joinTable' => 'eps_zonesgeographiques',
				'foreignKey' => 'zonegeographique_id',
				'associationForeignKey' => 'ep_id',
				'unique' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'finderQuery' => '',
				'deleteQuery' => '',
				'insertQuery' => '',
				'with' => 'EpZonegeographique'
			),
			'Structurereferente' => array(
				'className' => 'Structurereferente',
				'joinTable' => 'structuresreferentes_zonesgeographiques',
				'foreignKey' => 'zonegeographique_id',
				'associationForeignKey' => 'structurereferente_id',
				'unique' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'finderQuery' => '',
				'deleteQuery' => '',
				'insertQuery' => '',
				'with' => 'StructurereferenteZonegeographique'
			),
			'User' => array(
				'className' => 'User',
				'joinTable' => 'users_zonesgeographiques',
				'foreignKey' => 'zonegeographique_id',
				'associationForeignKey' => 'user_id',
				'unique' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'finderQuery' => '',
				'deleteQuery' => '',
				'insertQuery' => '',
				'with' => 'UserZonegeographique'
			),
			'Regroupementzonegeo' => array(
				'className' => 'Regroupementzonegeo',
				'joinTable' => 'regroupementszonesgeo_zonesgeographiques',
				'foreignKey' => 'zonegeographique_id',
				'associationForeignKey' => 'regroupementzonegeo_id',
				'unique' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'finderQuery' => '',
				'deleteQuery' => '',
				'insertQuery' => '',
				'with' => 'RegroupementzonegeoZonegeographique'
			),
			'Sitecov58' => array(
				'className' => 'Sitecov58',
				'joinTable' => 'sitescovs58_zonesgeographiques',
				'foreignKey' => 'zonegeographique_id',
				'associationForeignKey' => 'sitecov58_id',
				'unique' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'finderQuery' => '',
				'deleteQuery' => '',
				'insertQuery' => '',
				'with' => 'Sitecov58Zonegeographique'
			),
		);

		/**
		*
		*/

		public function listeCodesInseeLocalites( $codesFiltres = array(), $filtre_zone_geo = true ){
			$conditions = array();

			if( $filtre_zone_geo == true ) {
				if( !empty( $codesFiltres ) ) {
					$conditions['Zonegeographique.codeinsee'] = $codesFiltres;
				}
				else {
					$conditions['Zonegeographique.codeinsee'] = null;
				}
			}

			$codes = $this->find(
				'all',
				array(
					'fields' => array( 'DISTINCT Zonegeographique.codeinsee', 'Zonegeographique.libelle' ),
					'conditions' => $conditions,
					'recursive' => -1,
					'order' => 'Zonegeographique.codeinsee'
				)
			);

			return Hash::combine( $codes, '{n}.Zonegeographique.codeinsee', array( '%s %s', '{n}.Zonegeographique.codeinsee', '{n}.Zonegeographique.libelle' ) );
		}
	}
?>