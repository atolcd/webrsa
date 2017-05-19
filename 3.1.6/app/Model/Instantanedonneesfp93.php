<?php
	/**
	 * Code source de la classe Instantanedonneesfp93.
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Instantanedonneesfp93 ...
	 *
	 * @package app.Model
	 */
	class Instantanedonneesfp93 extends AppModel
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'Instantanedonneesfp93';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = -1;

		/**
		 * Behaviors utilisés par le modèle.
		 *
		 * @var array
		 */
		public $actsAs = array(
			'Formattable' => array(
				'null' => false,
				'trim' => false,
				'phone' => array( 'benef_tel_fixe', 'benef_tel_port' ),
				'suffix' => false,
				'amount' => false,
			),
			'Postgres.PostgresAutovalidate',
			'Validation2.Validation2Formattable',
		);

		/**
		 * Règles de validation
		 *
		 * @var array
		 */
		public $validate = array(
			'benef_tel_fixe' => array(
				'phoneFr' => array(
					'rule' => array( 'phoneFr' ),
					'allowEmpty' => true,
				)
			),
			'benef_tel_port' => array(
				'phoneFr' => array(
					'rule' => array( 'phoneFr' ),
					'allowEmpty' => true,
				)
			),
			'benef_email' => array(
				'email' => array(
					'rule' => array( 'email' ),
					'allowEmpty' => true
				)
			),
		);

		/**
		 * Les valeurs possibles et leurs conditions pour la nature de la prestation.
		 *
		 * @see __construct()
		 * @see getInstantane()
		 *
		 * @var array
		 */
		public $benef_natpf = array(
			'socle_majore_activite' => array(
				'benef_natpf_socle' => '1',
				'benef_natpf_activite' => '1',
				'benef_natpf_majore' => '1',
			),
			'socle_activite' => array(
				'benef_natpf_socle' => '1',
				'benef_natpf_activite' => '1',
				'benef_natpf_majore' => '0',
			),
			'socle_majore' => array(
				'benef_natpf_socle' => '1',
				'benef_natpf_activite' => '0',
				'benef_natpf_majore' => '1',
			),
			'socle' => array(
				'benef_natpf_socle' => '1',
				'benef_natpf_activite' => '0',
				'benef_natpf_majore' => '0',
			),
			'activite_majore' => array(
				'benef_natpf_socle' => '0',
				'benef_natpf_activite' => '1',
				'benef_natpf_majore' => '1',
			),
			'activite' => array(
				'benef_natpf_socle' => '0',
				'benef_natpf_activite' => '1',
				'benef_natpf_majore' => '0',
			),
			'NC',
		);

		/**
		 * Associations "Belongs to".
		 *
		 * @var array
		 */
		public $belongsTo = array(
			'Ficheprescription93' => array(
				'className' => 'Ficheprescription93',
				'foreignKey' => 'ficheprescription93_id',
				'conditions' => null,
				'type' => null,
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
		);

		/**
		 * Retourne la sous-requête du champ virtuel benef_natpf à partir des valeurs
		 * possibles et des conditions de l'attribut benef_natpf.
		 *
		 * @see $benef_natpf
		 *
		 * @return string
		 */
		public function getVirtualFieldBenefNatpf() {
			$cases = array();

			foreach( Hash::normalize( $this->benef_natpf ) as $value => $conditions ) {
				if( is_array( $conditions ) && !empty( $conditions ) ) {
					$when = array();
					foreach( $conditions as $field => $fieldValue ) {
						$when[] = "\"{$this->alias}\".\"{$field}\" = '{$fieldValue}'";
					}
					$cases[] = "WHEN ( ".implode( ' AND ', $when )." ) THEN '{$value}'";
				}
			}

			return '( CASE '.implode( "\n", $cases ).' ELSE \'NC\' END )';
		}

		/**
		 * Surcharge du constructeur de manière à créer le champ virtuel benef_natpf.
		 *
		 * @param integer|string|array $id
		 * @param string $table
		 * @param string $ds
		 */
		public function __construct( $id = false, $table = null, $ds = null ) {
			parent::__construct( $id, $table, $ds );

			$this->virtualFields['benef_natpf'] = $this->getVirtualFieldBenefNatpf();
		}

		/**
		 * Retourne la nature de prestation à partir des champs benef_natpf_socle,
		 * benef_natpf_activite, benef_natpf_majore ainsi que des valeurs possibles
		 * et des conditions de l'attribut benef_natpf.
		 *
		 * @param array $record
		 * @return string
		 */
		public function getBenefNatpf( array $record ) {
			$current = array(
				'benef_natpf_socle' => Hash::get( $record, "{$this->alias}.benef_natpf_socle" ),
				'benef_natpf_activite' => Hash::get( $record, "{$this->alias}.benef_natpf_activite" ),
				'benef_natpf_majore' => Hash::get( $record, "{$this->alias}.benef_natpf_majore" ),
			);

			foreach( $this->benef_natpf as $benef_natpf => $conditions ) {
				if( $conditions === $current ) {
					return $benef_natpf;
				}
			}

			return 'NC';
		}

		/**
		 * Récupère un instantané des données pour une personne donnée.
		 *
		 * @param integer $personne_id
		 * @return array
		 */
		public function getInstantane( $personne_id ) {
			$Informationpe = ClassRegistry::init( 'Informationpe' );

			$vfPositioncer = "( CASE
				WHEN \"Contratinsertion\".\"decision_ci\" = 'V' THEN 'valide'
				WHEN \"Cer93\".\"positioncer\" IN ( '00enregistre', '01signe', '02attdecisioncpdv' ) THEN 'validationpdv'
				WHEN \"Cer93\".\"positioncer\" IN ( '03attdecisioncg', '04premierelecture', '05secondelecture', '07attavisep' ) THEN 'validationcg'
				ELSE 'aucun'
			END )  AS \"Cer93__positioncer\"";

			$sqContratinsertion = $this->Ficheprescription93->Personne->Contratinsertion->sq(
				array(
					'fields' => array(
						'contratsinsertion.id'
					),
					'alias' => 'contratsinsertion',
					'conditions' => array(
						'contratsinsertion.personne_id = Personne.id',
						'contratsinsertion.decision_ci' => array( 'E', 'V' ),
					),
					'order' => array( 'contratsinsertion.dd_ci DESC' ),
					'limit' => 1
				)
			);

			$querydata = array(
				'fields' => array_merge(
					array(
						'Personne.qual',
						'Personne.nom',
						'Personne.prenom',
						'Personne.dtnai',
						'"Personne"."numfixe" AS "Personne__tel_fixe"',
						'"Personne"."numport" AS "Personne__tel_port"',
						'Personne.email',
						'Dsp.nivetu',
						'DspRev.nivetu',
						'Historiqueetatpe.identifiantpe',
						'Historiqueetatpe.etat',
						'Adresse.numvoie',
						'Adresse.libtypevoie',
						'Adresse.nomvoie',
						'Adresse.complideadr',
						'Adresse.compladr',
						'Adresse.numcom',
						'Adresse.codepos',
						'Adresse.nomcom',
						'Dossier.matricule',
						'Situationdossierrsa.etatdosrsa',
						'Calculdroitrsa.toppersdrodevorsa',
						'Contratinsertion.dd_ci',
						'Contratinsertion.df_ci',
						$vfPositioncer
					),
					$this->Ficheprescription93->Personne->Foyer->Dossier->Detaildroitrsa->Detailcalculdroitrsa->vfsSummary()
				),
				'contain' => false,
				'joins' => array(
					$this->Ficheprescription93->Personne->join( 'Prestation', array( 'type' => 'INNER' ) ),
					$this->Ficheprescription93->Personne->join( 'Calculdroitrsa', array( 'type' => 'LEFT OUTER' ) ),
					$this->Ficheprescription93->Personne->join(
						'Contratinsertion',
						array(
							'type' => 'LEFT OUTER',
							'conditions' => array(
								"Contratinsertion.id IN ( {$sqContratinsertion} )"
							)
						)
					),
					$this->Ficheprescription93->Personne->join(
						'Dsp',
						array(
							'type' => 'LEFT OUTER',
							'conditions' => array(
								'Dsp.id IN ( '.$this->Ficheprescription93->Personne->Dsp->WebrsaDsp->sqDerniereDsp( 'Personne.id' ).' )'
							)
						)
					),
					$this->Ficheprescription93->Personne->join(
						'DspRev',
						array(
							'type' => 'LEFT OUTER',
							'conditions' => array(
								'DspRev.id IN ( '.$this->Ficheprescription93->Personne->DspRev->sqDerniere( 'Personne.id' ).' )'
							)
						)
					),
					$this->Ficheprescription93->Personne->join( 'Foyer', array( 'type' => 'INNER' ) ),
					$this->Ficheprescription93->Personne->Contratinsertion->join( 'Cer93', array( 'type' => 'LEFT OUTER' ) ),
					$this->Ficheprescription93->Personne->Foyer->join( 'Adressefoyer', array( 'type' => 'LEFT OUTER' ) ),
					$this->Ficheprescription93->Personne->Foyer->Adressefoyer->join( 'Adresse', array( 'type' => 'LEFT OUTER' ) ),
					$this->Ficheprescription93->Personne->Foyer->join( 'Dossier', array( 'type' => 'INNER' ) ),
					$this->Ficheprescription93->Personne->Foyer->Dossier->join( 'Detaildroitrsa', array( 'type' => 'LEFT OUTER' ) ),
					$this->Ficheprescription93->Personne->Foyer->Dossier->join( 'Situationdossierrsa', array( 'type' => 'LEFT OUTER' ) ),
					$Informationpe->joinPersonneInformationpe( 'Personne', 'Informationpe', 'LEFT OUTER' ),
					$Informationpe->join( 'Historiqueetatpe', array( 'type' => 'LEFT OUTER' ) ),
				),
				'conditions' => array(
					'Personne.id' => $personne_id,
					array(
						'OR' => array(
							'Adressefoyer.id IS NULL',
							'Adressefoyer.id IN ( '.$this->Ficheprescription93->Personne->Foyer->Adressefoyer->sqDerniereRgadr01( 'Foyer.id' ).' )'
						)
					),
					array(
						'OR' => array(
							'Informationpe.id IS NULL',
							'Informationpe.id IN( '.$Informationpe->sqDerniere( 'Personne' ).' )'
						)
					),
					array(
						'OR' => array(
							'Historiqueetatpe.id IS NULL',
							'Historiqueetatpe.id IN( '.$Informationpe->Historiqueetatpe->sqDernier( 'Informationpe' ).' )'
						)
					)
				)
			);

			$result = $this->Ficheprescription93->Personne->find( 'first', $querydata );

			$return = array();
			if( !empty( $result ) ) {
				foreach( array( 'Personne', 'Adresse', 'Dossier', 'Detailcalculdroitrsa', 'Situationdossierrsa', 'Calculdroitrsa', 'Contratinsertion', 'Cer93' ) as $modelName ) {
					foreach( $result[$modelName] as $field => $value ) {
						$return[$this->alias]["benef_{$field}"] = $value;
					}
				}

				$return[$this->alias]['benef_identifiantpe'] = $result['Historiqueetatpe']['identifiantpe'];
				$return[$this->alias]['benef_inscritpe'] = ( $result['Historiqueetatpe']['etat'] === 'inscription' ? '1' : '0' );

				// Niveau d'étude
				$nivetu = Hash::get( $result, 'DspRev.nivetu' );
				if( $nivetu === null ) {
					$nivetu = Hash::get( $result, 'Dsp.nivetu' );
				}
				$return[$this->alias]['benef_nivetu'] = $nivetu;

				$return[$this->alias]['benef_positioncer'] = Hash::get( $result, 'Cer93.positioncer' );

				foreach( array( 'benef_natpf_activite', 'benef_natpf_majore', 'benef_natpf_socle' ) as $field ) {
					$return[$this->alias][$field] = ( $return[$this->alias][$field] ? '1' : '0' );
				}

				$return[$this->alias]['benef_natpf'] = $this->getBenefNatpf( $return );
			}

			return $return;
		}

		/**
		 * Complète les enums avec le champ virtuel benef_natpf.
		 *
		 * @return array
		 */
		public function enums() {
			$enums = parent::enums();

			$enums[$this->alias]['benef_natpf'] = array();
			foreach( array_keys( Hash::normalize( $this->benef_natpf ) ) as $natpf ) {
				$enums[$this->alias]['benef_natpf'][$natpf] = __d( 'instantanedonneesfp93', "ENUM::BENEF_NATPF::{$natpf}" );
			}

			return $enums;
		}
	}
?>