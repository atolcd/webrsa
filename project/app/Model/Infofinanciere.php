<?php
	/**
	 * Code source de la classe Infofinanciere.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Infofinanciere ...
	 *
	 * @package app.Model
	 */
	class Infofinanciere extends AppModel
	{
		public $name = 'Infofinanciere';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		public $actsAs = array(
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Postgres.PostgresAutovalidate',
		);

		public $validate = array(
			'type_allocation' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire'
				)
			),
			'natpfcre' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire'
				)
			),
			'typeopecompta' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire'
				)
			),
			'sensopecompta' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire'
				)
			),
			'dttraimoucompta' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire'
				)
			),
			'mtmoucompta' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => 'Champ obligatoire'
				),
				'numeric' => array(
					'rule' => array( 'numeric' ),
					'message' => 'Veuillez n\'utiliser que des lettres et des chiffres'
				),
			),
			'mtmoucompta' => array(
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
			'type_allocation' => array(
				'AllocationsComptabilisees', 'IndusConstates', 'IndusTransferesCG',
				'RemisesIndus', 'AnnulationsFaibleMontant', 'AutresAnnulations'
			),
			'natpfcre' => array(
				'ACB', 'ASB', 'ASD', 'ASI', 'INK', 'INL', 'INM', 'INN',
				'INP', 'INS', 'INT', 'ISK', 'ISL', 'ISM', 'ISS', 'ITK',
				'ITL', 'ITM', 'ITN', 'ITP', 'ITS', 'ITT', 'RCB', 'RSB',
				'RSD', 'RSI', 'VCB', 'VSB', 'VSD', 'VSI', 'ACD'
			),
			'typeopecompta' => array(
				'PME', 'PRA', 'RAI', 'RMU', 'RTR', 'CIC', 'CAI', 'CDC',
				'CCP', 'CRC', 'CRG', 'CAF', 'CFC', 'CEX', 'CES', 'CRN'
			),
			'sensopecompta' => array('AJ', 'DE'),
		);



		public $belongsTo = array(
			'Dossier' => array(
				'className' => 'Dossier',
				'foreignKey' => 'dossier_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);

		/**
		 * Associations "Has many".
		 *
		 * @var array
		 */
		public $hasMany = array(
			'Indurecoursgracieux' => array(
				'className' => 'Indurecoursgracieux',
				'foreignKey' => 'indus_id',
				'conditions' => null,
				'fields' => null,
				'order' => null,
				'dependent' => true
			)
		);

		/**
		*
		*/
		public function search( $mesCodesInsee, $filtre_zone_geo, $criteres ) {
			/// Conditions de base
			$conditions = array();

			/// Critères
			$mois = Set::extract( $criteres, 'Filtre.moismoucompta' );
			$types = Set::extract( $criteres, 'Filtre.type_allocation' );
			$nomcom = Set::extract( $criteres, 'Filtre.nomcom' );
			$numcom = Set::extract( $criteres, 'Filtre.numcom' );

			/// Mois du mouvement comptable
			if( !empty( $mois ) && dateComplete( $criteres, 'Filtre.moismoucompta' ) ) {
				$month = $mois['month'];
				$year = $mois['year'];
				$conditions[] = 'EXTRACT(MONTH FROM Infofinanciere.moismoucompta) = '.$month;
				$conditions[] = 'EXTRACT(YEAR FROM Infofinanciere.moismoucompta) = '.$year;
			}

			/// Id du Dossier
			if( !empty( $criteres ) && isset( $criteres['Dossier.id'] ) ) {
				$conditions['Dossier.id'] = $criteres['Dossier.id'];
			}

			/// Type d'allocation
			if( !empty( $types ) ) {
				$conditions[] = 'Infofinanciere.type_allocation ILIKE \'%'.Sanitize::clean( $types, array( 'encode' => false ) ).'%\'';
			}

			/// Par adresse
			if( !empty( $nomcom ) ) {
				$conditions[] = 'Adresse.nomcom ILIKE \'%'.Sanitize::clean( $nomcom, array( 'encode' => false ) ).'%\'';
			}

			/// Par code postal
			if( !empty( $numcom ) ) {
				$conditions[] = 'Adresse.numcom ILIKE \'%'.Sanitize::clean( $numcom, array( 'encode' => false ) ).'%\'';
			}

			$conditions[] = $this->conditionsZonesGeographiques( $filtre_zone_geo, $mesCodesInsee );

			/// Requête
			$this->Dossier = ClassRegistry::init( 'Dossier' );

			$query = array(
				'fields' => array(
					'"Infofinanciere"."id"',
					'"Infofinanciere"."dossier_id"',
					'"Infofinanciere"."moismoucompta"',
					'"Infofinanciere"."type_allocation"',
					'"Infofinanciere"."natpfcre"',
					'"Infofinanciere"."rgcre"',
					'"Infofinanciere"."numintmoucompta"',
					'"Infofinanciere"."typeopecompta"',
					'"Infofinanciere"."sensopecompta"',
					'"Infofinanciere"."mtmoucompta"',
					'"Infofinanciere"."ddregu"',
					'"Infofinanciere"."dttraimoucompta"',
					'"Infofinanciere"."heutraimoucompta"',
					'"Dossier"."id"',
					'"Dossier"."numdemrsa"',
					'"Dossier"."matricule"',
					'"Dossier"."typeparte"',
					'"Personne"."id"',
					'"Personne"."nom"',
					'"Personne"."prenom"',
					'"Personne"."nir"',
					'"Personne"."dtnai"',
					'"Personne"."qual"',
					'"Personne"."nomcomnai"',
					'"Situationdossierrsa"."etatdosrsa"',
					'"Adresse"."nomcom"',
					'"Adresse"."numcom"',
				),
				'recursive' => -1,
				'joins' => array(
					array(
						'table'      => 'dossiers',
						'alias'      => 'Dossier',
						'type'       => 'INNER',
						'foreignKey' => false,
						'conditions' => array( 'Infofinanciere.dossier_id = Dossier.id' )
					),
					array(
						'table'      => 'situationsdossiersrsa',
						'alias'      => 'Situationdossierrsa',
						'type'       => 'INNER',
						'foreignKey' => false,
						'conditions' => array( 'Situationdossierrsa.dossier_id = Dossier.id' )
					),
					array(
						'table'      => 'foyers',
						'alias'      => 'Foyer',
						'type'       => 'INNER',
						'foreignKey' => false,
						'conditions' => array( 'Foyer.dossier_id = Dossier.id' )
					),
					array(
						'table'      => 'personnes',
						'alias'      => 'Personne',
						'type'       => 'INNER',
						'foreignKey' => false,
						'conditions' => array( 'Personne.foyer_id = Foyer.id' )
					),
					array(
						'table'      => 'prestations',
						'alias'      => 'Prestation',
						'type'       => 'INNER',
						'foreignKey' => false,
						'conditions' => array(
							'Personne.id = Prestation.personne_id',
							'Prestation.natprest = \'RSA\'',
							'( Prestation.rolepers = \'DEM\' )'
						)
					),
					array(
						'table'      => 'adressesfoyers',
						'alias'      => 'Adressefoyer',
						'type'       => 'INNER',
						'foreignKey' => false,
						'conditions' => array( 'Foyer.id = Adressefoyer.foyer_id', 'Adressefoyer.rgadr = \'01\'' )
					),
					array(
						'table'      => 'adresses',
						'alias'      => 'Adresse',
						'type'       => 'INNER',
						'foreignKey' => false,
						'conditions' => array( 'Adresse.id = Adressefoyer.adresse_id' )
					),
				),
				'limit' => 10,
				'order' => array( '"Dossier"."numdemrsa"' ),
				'conditions' => $conditions
			);

			$typesAllocation = array( 'AllocationsComptabilisees', 'IndusConstates', 'IndusTransferesCG', 'RemisesIndus', 'AnnulationsFaibleMontant', 'AutresAnnulations' );

			$query['conditions'] = Set::merge( $query['conditions'], $conditions );
			return $query;
		}

		/**
		*
		* @return array contenant les clés minYear et maxYear
		* @access public
		*/

		public function range() {
			$first = $this->find( 'first', array( 'order' => 'moismoucompta ASC', 'recursive' => -1 ) );
			$last = $this->find( 'first', array( 'order' => 'moismoucompta DESC', 'recursive' => -1 ) );

			if( !empty( $first ) && !empty( $last ) ) {
				list( $yearFirst, ,  ) = explode( '-', $first[$this->name]['moismoucompta'] );
				list( $yearLast, ,  ) = explode( '-', $last[$this->name]['moismoucompta'] );

				return array( 'minYear' => $yearFirst, 'maxYear' => $yearLast );
			}
			else {
				return array( 'minYear' => date( 'Y' ), 'maxYear' => date( 'Y' ) );
			}
		}

		/**
		 * Retourne l'id du dossier à partir de l'id d'un enregistrement.
		 *
		 * @param integer $id
		 * @return integer
		 */
		public function dossierId( $id ) {
			$querydata = array(
				'fields' => array( "{$this->alias}.dossier_id" ),
				'conditions' => array(
					"{$this->alias}.id" => $id
				),
				'order' => null,
				'recursive' => -1
			);

			$result = $this->find( 'first', $querydata );

			if( !empty( $result ) ) {
				return $result[$this->alias]['dossier_id'];
			}
			else {
				return null;
			}
		}

		public function enum($field, array $params = array()) {
			if ($field === 'natpfcre') {
				$params += array('type' => null);
				switch ($params['type']) {
					case 'totsocl':
						$filter = array('RSD', 'INK', 'ITK', 'ISK', 'ACD', 'ASD');
						break;
					case 'soclmaj':
						$filter = array('RSI', 'INL', 'ITL');
						break;
					case 'localrsa':
						$filter = array('RSB', 'RCB', 'INM', 'ITM');
						break;
					case 'indutotsocl':
						$filter = array('INK', 'ITK', 'ISK');
						break;
					case 'alloccompta':
						$filter = array('RSD', 'RSI', 'RSB', 'RCB', 'ASD', 'VSD', 'INK', 'INL', 'INM', 'ITK', 'ITL', 'ITM', 'ISK');
						break;
					case 'indutransferecg':
						$filter = array('INK', 'INL', 'INM', 'ITK', 'ITL', 'ITM');
						break;
					case 'annulationfaible':
						$filter = array('INK', 'INL', 'INM', 'ITK', 'ITL', 'ITM', 'ISK', 'INN', 'ITN', 'INP', 'ITP');
						break;
					case 'autreannulation':
						$filter = array('INK', 'INL', 'INM', 'ITK', 'ITL', 'ITM', 'ISK');
						break;
					case 'none':
					case 'all':
						$filter = array();
						break;
					default:
						$filter = array('RSD', 'RSI', 'RSB', 'RCB', 'ASD', 'VSD', 'INK',
							'INL', 'INM', 'ITK', 'ITL', 'ITM', 'INN', 'ITN', 'INP', 'ITP');
				}

				$result = parent::enum($field, compact('filter') + $params);
			} else {
				$result = parent::enum($field, $params);
			}

			return $result;
		}
	}
?>