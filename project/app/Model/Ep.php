<?php
	/**
	 * Code source de la classe Ep.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Ep ...
	 *
	 * @package app.Model
	 */
	class Ep extends AppModel
	{
		public $name = 'Ep';

		public $actsAs = array(
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Postgres.PostgresAutovalidate'
		);

		public $belongsTo = array(
			'Regroupementep' => array(
				'className' => 'Regroupementep',
				'foreignKey' => 'regroupementep_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			),
		);

		public $hasMany = array(
			'Commissionep' => array(
				'className' => 'Commissionep',
				'foreignKey' => 'ep_id',
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
			'Membreep' => array(
				'className' => 'Membreep',
				'joinTable' => 'eps_membreseps',
				'foreignKey' => 'ep_id',
				'associationForeignKey' => 'membreep_id',
				'unique' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'finderQuery' => '',
				'deleteQuery' => '',
				'insertQuery' => '',
				'with' => 'EpMembreep'
			),
			'Zonegeographique' => array(
				'className' => 'Zonegeographique',
				'joinTable' => 'eps_zonesgeographiques',
				'foreignKey' => 'ep_id',
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
				'with' => 'EpZonegeographique'
			),
		);

		public function listOptions( $filtre_zone_geo, $zonesgeographiques ) {
			$results = $this->find(
				'list',
				array(
					'fields' => array(
						'Ep.id',
						'Ep.name'
					),
					'contain' => array(
						'Regroupementep'=>array(
							'fields'=>array(
								'name'
							),
							'order'=>array(
								'Regroupementep.name ASC'
							)
						)
					),
					'conditions' => array(
							$this->sqRestrictionsZonesGeographiques(
									'Ep.id',
									$filtre_zone_geo,
									$zonesgeographiques
									),
							'Ep.actif' => 1
					),
					'order' => array(
						'Ep.name'
					)
				)
			);
			return $results;
		}

		/**
		* Retourne la liste des thèmes traités par les EPs
		*/

		public function themes() {
			return $this->Regroupementep->themes();
		}

		/**
		* Retourne une chaîne de 12 caractères formattée comme suit:
		* EP, année sur 4 chiffres, mois sur 2 chiffres, nombre de commissions.
		*/

		public function identifiant() {
			return 'EP'.date( 'Ym' ).sprintf( "%010s",  $this->find( 'count' ) + 1 );
		}

		/**
		* Ajout de l'identifiant de la séance lors de la sauvegarde.
		*/

		public function beforeValidate( $options = array() ) {
			$return = parent::beforeValidate( $options );

			$primaryKey = Set::classicExtract( $this->data, "{$this->alias}.{$this->primaryKey}" );
			$identifiant = Set::classicExtract( $this->data, "{$this->alias}.identifiant" );

			if( empty( $primaryKey ) && empty( $identifiant ) ) {
				$this->data[$this->alias]['identifiant'] = $this->identifiant();
			}

			return $return;
		}

		/**
		* Si l'utilisateur est restreint au niveau des zones géographiques qu'il peut voir,
		* on va s'assurer qu'il puisse au moins voir une des zones que l'EP traite
		*
		* @param string $champ Le nom du champ contenant la valeur de la clé primaire de l'EP
		* @param boolean $filtre_zone_geo Doit-on restreindre par zone géographique ?
		* @param array $zonesgeographiques Un tableau associatif (id => numcom) des zones géographiques accessibles
		* @return array Des conditions pour un queryData CakePHP
		*/

		public function sqRestrictionsZonesGeographiques( $champ, $filtre_zone_geo, $zonesgeographiques ) {
			$conditions = array();

			if( $filtre_zone_geo ) {
				if( !empty( $zonesgeographiques ) ) {
					$conditions[] = "{$champ} IN ( ".$this->EpZonegeographique->sq(
						array(
							'fields' => array( 'eps_zonesgeographiques.ep_id' ),
							'alias' => 'eps_zonesgeographiques',
							'conditions' => array(
								'eps_zonesgeographiques.zonegeographique_id' => array_keys( $zonesgeographiques ),
								"eps_zonesgeographiques.ep_id = {$champ}"
							)
						)
					).' )';
				}
				else {
					$conditions[] = "{$champ} IS NULL";
				}
			}

			return $conditions;
		}
	}
?>