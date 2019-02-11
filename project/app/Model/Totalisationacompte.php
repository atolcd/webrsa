<?php
	/**
	 * Code source de la classe Totalisationacompte.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Totalisationacompte ...
	 *
	 * @package app.Model
	 */
	class Totalisationacompte extends AppModel
	{
		public $name = 'Totalisationacompte';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		/**
		 * Behaviors utilisés par le modèle.
		 *
		 * @var array
		 */
		public $actsAs = array(
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Postgres.PostgresAutovalidate'
		);

		public $validate = array(
			'type_totalisation' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire'
				)
			),
			'mttotsoclrsa' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire'
				)
			),
			'mttotsoclmajorsa' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire'
				)
			),
			'mttotlocalrsa' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire'
				)
			),
			'mttotrsa' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire'
				)
			)
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
			'type_totalisation' => array(
				'TotalAllocationsComptabilisees', 'TotalIndusConstates', 'TotalIndusTransferesCG',
				'TotalRemisesIndus', 'TotalAnnulationsIndus', 'MontantTotalAcompte'
			),
		);

		public $belongsTo = array(
			'Identificationflux' => array(
				'className' => 'Identificationflux',
				'foreignKey' => 'identificationflux_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);

		/**
		*
		*/

		public function search( $criteres ) {
			/// Conditions de base
			$conditions = array();

			/// Critères
			$date = Set::extract( $criteres, 'Filtre.dtcreaflux' );

			/// Date du flux financier
			if( !empty( $date ) && dateComplete( $criteres, 'Filtre.dtcreaflux' ) ) {
				$mois = $date['month'];
				$conditions[] = 'EXTRACT(MONTH FROM Identificationflux.dtcreaflux) = '.$mois;
				$annee = $date['year'];
				$conditions[] = 'EXTRACT(YEAR FROM Identificationflux.dtcreaflux) = '.$annee;
			}


			/// Requête
			$this->Dossier = ClassRegistry::init( 'Dossier' );

			$query = array(
				'fields' => array(
					'"Totalisationacompte"."type_totalisation"',
					'SUM("Totalisationacompte"."mttotsoclrsa") AS "Totalisationacompte__mttotsoclrsa"',
					'SUM("Totalisationacompte"."mttotsoclmajorsa") AS "Totalisationacompte__mttotsoclmajorsa"',
					'SUM("Totalisationacompte"."mttotlocalrsa") AS "Totalisationacompte__mttotlocalrsa"',
					'SUM("Totalisationacompte"."mttotrsa") AS "Totalisationacompte__mttotrsa"'
				),
				'recursive' => -1,
				'joins' => array(
					array(
						'table'      => 'identificationsflux',
						'alias'      => 'Identificationflux',
						'type'       => 'INNER',
						'foreignKey' => false,
						'conditions' => array( 'Totalisationacompte.identificationflux_id = Identificationflux.id' ),
					)
				),
				'group' => array(
					'Totalisationacompte.type_totalisation',
					'Totalisationacompte.id'
				),
				'order' => array( '"Totalisationacompte"."id" ASC' ),
				'conditions' => $conditions
			);

			return $query;
		}
	}
?>