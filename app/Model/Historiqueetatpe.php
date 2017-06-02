<?php
	/**
	 * Code source de la classe Historiqueetatpe.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Historiqueetatpe ...
	 *
	 * @package app.Model
	 */
	class Historiqueetatpe extends AppModel
	{
		public $name = 'Historiqueetatpe';

		public $actsAs = array(
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Postgres.PostgresAutovalidate'
		);

		/**
		 * Liste de champs et de valeurs possibles qui ne peuvent pas être mis en
		 * règle de validation inList ou en contrainte dans la base de données en
		 * raison des valeurs actuellement en base, mais pour lequels un ensemble
		 * fini de valeurs existe.
		 *
		 * @see AppModel::enums
		 *
		 * @var array
		 */
		public $fakeInLists = array(
			'code' => array('1', '2', '3', '4', '5', '6', '7', '8'),
		);

		public $belongsTo = array(
			'Informationpe' => array(
				'className' => 'Informationpe',
				'foreignKey' => 'informationpe_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);

		public $hasMany = array(
			'Defautinsertionep66' => array(
				'className' => 'Defautinsertionep66',
				'foreignKey' => 'historiqueetatpe_id',
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
			'Nonrespectsanctionep93' => array(
				'className' => 'Nonrespectsanctionep93',
				'foreignKey' => 'historiqueetatpe_id',
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
			'Nonoriente66' => array(
				'className' => 'Nonoriente66',
				'foreignKey' => 'historiqueetatpe_id',
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
			'Sanctionep58' => array(
				'className' => 'Sanctionep58',
				'foreignKey' => 'historiqueetatpe_id',
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

		/**
		* Retourne une array à utiliser comme jointure entre la table personnes
		* et la table informationspe.
		*
		* @param boolean $dernier Permet de rechercher uniquement l'information la plus récente
		* @param string $aliasInformationpe Alias pour la table informationspe
		* @param string $aliasHistoriqueetatpe Alias pour la table historiqueetatspe
		* @param string $type Type de jointure à effectuer
		* @return array
		*/

		public function joinInformationpeHistoriqueetatpe( $dernier = true, $aliasInformationpe = 'Informationpe', $aliasHistoriqueetatpe = 'Historiqueetatpe', $type = 'LEFT OUTER' ) {
			$join = array(
				'table'      => 'historiqueetatspe',
				'alias'      => $aliasHistoriqueetatpe,
				'type'       => $type,
				'foreignKey' => false,
				'conditions' => array(
					"{$aliasHistoriqueetatpe}.informationpe_id = {$aliasInformationpe}.id",
				)
			);

			if( $dernier ) {
				$join['conditions'][] = "{$aliasHistoriqueetatpe}.id IN (
					SELECT
							historiqueetatspe.id
						FROM historiqueetatspe
						WHERE historiqueetatspe.informationpe_id = {$aliasInformationpe}.id
						ORDER BY historiqueetatspe.date DESC,  historiqueetatspe.id DESC
						LIMIT 1
				)";
			}

			return $join;
		}

		/**
		* Retourne une condition sur l'identifiant Pôle Emploi de la table historiqueetatspe
		* Si Recherche.identifiantpecourt est configuré à true, on ne compare que sur
		* les 8 derniers caractères, sinon on fait une comparaison normale.
		* La comparaison se fait sur la mise en majuscule dans tous les cas.
		*
		* @param string $identifiantpe L'identifiant Pôle Emploi qui est recherché
		* @param string $aliasHistoriqueetatpe Alias pour la table historiqueetatspe
		* @return string
		*/

		public function conditionIdentifiantpe( $identifiantpe, $aliasHistoriqueetatpe = 'Historiqueetatpe' ) {
			if( !empty( $identifiantpe ) ) {
				if( Configure::read( 'Recherche.identifiantpecourt' ) ) {
					return "SUBSTRING(UPPER({$aliasHistoriqueetatpe}.identifiantpe) FROM 4 FOR 8) = '".strtoupper( Sanitize::clean( $identifiantpe, array( 'encode' => false ) ) )."'";
				}
				else {
					return "UPPER({$aliasHistoriqueetatpe}.identifiantpe) = '".strtoupper( Sanitize::clean( $identifiantpe, array( 'encode' => false ) ) )."'";
				}
			}
		}

		public function sqDernier( $informationpeAlias = 'Informationpe' ) {
			return "SELECT h.id
						FROM historiqueetatspe AS h
						WHERE h.informationpe_id = {$informationpeAlias}.id
						ORDER BY h.date DESC
						LIMIT 1";
		}
	}
?>