<?php
	/**
	 * Code source de la classe Situationallocataire.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Situationallocataire ...
	 *
	 * @package app.Model
	 */
	class Situationallocataire extends AppModel
	{
		/**
		 * Nom.
		 *
		 * @var string
		 */
		public $name = 'Situationallocataire';

		/**
		 * Behaviors utilisés.
		 *
		 * @var array
		 */
		public $actsAs = array(
			'Postgres.PostgresAutovalidate',
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
		);

		/**
		 *
		 * @see Situationallocataire::natpfD1()
		 *
		 * @var array
		 */
		public $virtualFields = array(
			'natpf_d1' => array(
				'type'      => 'string',
				'postgres'  => '( CASE
					WHEN ( "%s"."natpf_socle" = \'1\' AND "%s"."natpf_activite" = \'1\' AND "%s"."natpf_majore" = \'1\' ) THEN \'ENUM::NATPF_D1::majore\'
					WHEN ( "%s"."natpf_socle" = \'1\' AND "%s"."natpf_activite" = \'1\' AND "%s"."natpf_majore" = \'0\' ) THEN \'ENUM::NATPF_D1::socle_activite\'
					WHEN ( "%s"."natpf_socle" = \'1\' AND "%s"."natpf_activite" = \'0\' AND "%s"."natpf_majore" = \'1\' ) THEN \'ENUM::NATPF_D1::majore\'
					WHEN ( "%s"."natpf_socle" = \'1\' AND "%s"."natpf_activite" = \'0\' AND "%s"."natpf_majore" = \'0\' ) THEN \'ENUM::NATPF_D1::socle\'
					WHEN ( "%s"."natpf_socle" = \'0\' AND "%s"."natpf_activite" = \'1\' AND "%s"."natpf_majore" = \'1\' ) THEN \'ENUM::NATPF_D1::majore\'
					WHEN ( "%s"."natpf_socle" = \'0\' AND "%s"."natpf_activite" = \'1\' AND "%s"."natpf_majore" = \'0\' ) THEN \'ENUM::NATPF_D1::NC\'
					WHEN ( "%s"."natpf_socle" = \'0\' AND "%s"."natpf_activite" = \'0\' AND "%s"."natpf_majore" = \'1\' ) THEN \'ENUM::NATPF_D1::NC\'
					WHEN ( "%s"."natpf_socle" = \'0\' AND "%s"."natpf_activite" = \'0\' AND "%s"."natpf_majore" = \'0\' ) THEN \'ENUM::NATPF_D1::NC\'
					ELSE \'ENUM::NATPF_D1::NC\'
				END )'
			),
		);

		/**
		 * Les valeurs possibles pour la nature de la prestation (voir le champ
		 * virtuel natpf_d1.
		 *
		 * @var array
		 */
		public $natpf_d1 = array(
			'ENUM::NATPF_D1::majore',
			'ENUM::NATPF_D1::socle_activite',
			'ENUM::NATPF_D1::socle',
			'ENUM::NATPF_D1::activite',
			'ENUM::NATPF_D1::NC',
		);

		/**
		 * Les valeurs possibles pour la nature de la prestation (voir le champ
		 * virtuel natpf_fp.
		 *
		 * @var array
		 */
		public $natpf_fp = array(
			'ENUM::NATPF_FP::socle_majore_activite',
			'ENUM::NATPF_FP::socle_activite',
			'ENUM::NATPF_FP::socle_majore',
			'ENUM::NATPF_FP::socle',
			'ENUM::NATPF_FP::NC',
		);

		/**
		 * Associations "Has one".
		 *
		 * @var array
		 */
		public $hasOne = array(
			'Questionnaired1pdv93' => array(
				'className' => 'Questionnaired1pdv93',
				'foreignKey' => 'situationallocataire_id',
				'conditions' => null,
				'fields' => null,
				'order' => null,
				'dependent' => true
			),
		);

		/**
		 * Associations "Belongs to".
		 *
		 * @var array
		 */
		public $belongsTo = array(
			'Personne' => array(
				'className' => 'Personne',
				'foreignKey' => 'personne_id',
				'conditions' => null,
				'type' => null,
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
		);

		/**
		 * Règles de validation en plus de celles en base.
		 *
		 * @var array
		 */
		public $validate = array(
			'nati' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME )
				)
			),
		);

		/**
		 *
		 * @param integer $personne_id
		 * @return array
		 */
		public function getSituation( $personne_id ) {
				$Informationpe = ClassRegistry::init( 'Informationpe' );

				$querydata = array(
					'fields' => array_merge(
						array(
							'Personne.id',
							'Personne.qual',
							'Personne.nom',
							'Personne.prenom',
							'Personne.nomnai',
							'Personne.nir',
							'Personne.sexe',
							'Personne.dtnai',
							'Personne.nati',
							'Prestation.rolepers',
							'Calculdroitrsa.toppersdrodevorsa',
							'Historiqueetatpe.identifiantpe',
							'Historiqueetatpe.date',
							'Historiqueetatpe.etat',
							'Historiqueetatpe.code',
							'Historiqueetatpe.motif',
							'Adresse.numvoie',
							'Adresse.libtypevoie',
							'Adresse.nomvoie',
							'Adresse.complideadr',
							'Adresse.compladr',
							'Adresse.numcom',
							'Adresse.codepos',
							'Adresse.nomcom',
							'Dossier.numdemrsa',
							'Dossier.matricule',
							'Dossier.fonorg',
							'Dossier.dtdemrsa',
							'Dossier.dtdemrmi',
							'Dossier.statudemrsa',
							'Situationdossierrsa.etatdosrsa',
							'Foyer.sitfam',
							'( '.$this->Personne->Foyer->vfNbEnfants().' ) AS "Foyer__nbenfants"',
							'Suiviinstruction.numdepins',
							'Suiviinstruction.typeserins',
							'Suiviinstruction.numcomins',
							'Suiviinstruction.numagrins',
						),
						$this->Personne->Foyer->Dossier->Detaildroitrsa->Detailcalculdroitrsa->vfsSummary()
					),
					'contain' => false,
					'joins' => array(
						$this->Personne->join( 'Prestation', array( 'type' => 'INNER' ) ),
						$this->Personne->join( 'Calculdroitrsa', array( 'type' => 'LEFT OUTER' ) ),
						$this->Personne->join( 'Foyer', array( 'type' => 'INNER' ) ),
						$this->Personne->join( 'Dsp', array( 'type' => 'LEFT OUTER' )),
						$this->Personne->join( 'DspRev', array( 'type' => 'LEFT OUTER' )),
						$this->Personne->Foyer->join( 'Adressefoyer', array( 'type' => 'LEFT OUTER' ) ),
						$this->Personne->Foyer->Adressefoyer->join( 'Adresse', array( 'type' => 'LEFT OUTER' ) ),
						$this->Personne->Foyer->join( 'Dossier', array( 'type' => 'INNER' ) ),
						$this->Personne->Foyer->Dossier->join( 'Situationdossierrsa', array( 'type' => 'LEFT OUTER' ) ),
						$this->Personne->Foyer->Dossier->join( 'Suiviinstruction', array( 'type' => 'LEFT OUTER' ) ),
						$this->Personne->Foyer->Dossier->join( 'Detaildroitrsa', array( 'type' => 'LEFT OUTER' ) ),
						$Informationpe->joinPersonneInformationpe( 'Personne', 'Informationpe', 'LEFT OUTER' ),
						$Informationpe->join( 'Historiqueetatpe', array( 'type' => 'LEFT OUTER' ) ),
					),
					'conditions' => array(
						'Personne.id' => $personne_id,
						array(
							'OR' => array(
								'Adressefoyer.id IS NULL',
								'Adressefoyer.id IN ( '.$this->Personne->Foyer->Adressefoyer->sqDerniereRgadr01( 'Foyer.id' ).' )'
							)
						),
						array(
							'OR' => array(
								'Dsp.id IS NULL',
								'Dsp.id IN ( '.$this->Personne->Dsp->WebrsaDsp->sqDerniereDsp( 'Personne.id' ).' )'
							)
						),
						array(
							'OR' => array(
								'DspRev.id IS NULL',
								'DspRev.id IN ( '.$this->Personne->DspRev->sqDerniere( 'Personne.id' ).' )'
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

				return $this->Personne->find( 'first', $querydata );
		}

		// FIXME: nom de fonction... l'utiliser ailleurs ?
		// @see Questionnaired1pdv93::prepareFormData()
		public function foo( array $data ) {
			$return = array();

			$schema = array_keys( $this->schema() );

			$return['Situationallocataire']['personne_id'] = $data['Personne']['id'];

			$modelNames = array(
				'Personne',
				'Prestation',
				'Calculdroitrsa',
				'Historiqueetatpe',
				'Adresse',
				'Dossier',
				'Situationdossierrsa',
				'Foyer',
				'Suiviinstruction',
				'Detailcalculdroitrsa',
			);

			foreach( $modelNames as $modelName ) {
				foreach( $data[$modelName] as $field => $value ) {
					if( !in_array( $field, array( 'id', 'personne_id', 'created', 'modified' ) ) && in_array( $field, $schema ) ) {
						$return['Situationallocataire'][$field] = $value;
					}
				}
			}
			$return['Situationallocataire']['identifiantpe'] = $data['Historiqueetatpe']['identifiantpe'];
			$return['Situationallocataire']['datepe'] = $data['Historiqueetatpe']['date'];
			$return['Situationallocataire']['etatpe'] = $data['Historiqueetatpe']['etat'];
			$return['Situationallocataire']['codepe'] = $data['Historiqueetatpe']['code'];
			$return['Situationallocataire']['motifpe'] = $data['Historiqueetatpe']['motif'];

			foreach( array( 'socle', 'majore', 'activite' ) as $type ) {
				$return['Situationallocataire']["natpf_{$type}"] = ( $return['Situationallocataire']["natpf_{$type}"] ? '1' : '0' );
			}

			$return['Situationallocataire']['natpf_d1'] = $this->natpfD1( $return, true );

			$return['Situationallocataire']['inscritpe'] = ( $return['Situationallocataire']['etatpe'] == 'inscription' ? '1' : '0' );

			return $return;
		}

		/**
		 * Complète les enums avec certains "enums" de Tableausuivipdv93.
		 *
		 * @return array
		 */
		public function enums() {
			$enums = parent::enums();
			$Tableausuivipdv93 = ClassRegistry::init( 'Tableausuivipdv93' );

			$enums[$this->alias]['sitfam_view'] = $Tableausuivipdv93->WebrsaTableausuivipdv93->sitfam;
			$enums[$this->alias]['anciennete_dispositif'] = $Tableausuivipdv93->WebrsaTableausuivipdv93->anciennetes_dispositif;
			$enums[$this->alias]['natpf_view'] = $Tableausuivipdv93->WebrsaTableausuivipdv93->natpf;
			$enums[$this->alias]['tranche_age_view'] = $Tableausuivipdv93->WebrsaTableausuivipdv93->tranches_ages;
			$enums[$this->alias]['anciennete_dispositif_view'] = $Tableausuivipdv93->WebrsaTableausuivipdv93->anciennetes_dispositif;

			$enums[$this->alias]['natpf_d1'] = array();
			foreach( $this->natpf_d1 as $natpf_d1 ) {
				$enums[$this->alias]['natpf_d1'][$natpf_d1] = __d( 'situationallocataire', $natpf_d1 );
			}

			$enums[$this->alias]['natpf_fp'] = array();
			foreach( $this->natpf_fp as $natpf_fp ) {
				$value = str_replace( 'ENUM::NATPF_FP::', '', $natpf_fp );
				$enums[$this->alias]['natpf_fp'][$value] = __d( 'situationallocataire', $natpf_fp );
			}

			$enums[$this->alias]['inscritpe'] = array(
				'0' => __d( 'situationallocataire', 'ENUM::INSCRITPE::0' ),
				'1' => __d( 'situationallocataire', 'ENUM::INSCRITPE::1' ),
			);

			return $enums;
		}

		/**
		 * Retourne la nature de la prestation sous la forme d'une chaîne de
		 * caractères à partir
		 *
		 * @see Situationallocataire::natpf_d1
		 *
		 * @param array $data
		 * @param boolean $translate
		 * @return string
		 */
		public function natpfD1( $data, $translate = false ) {
			$natpf_d1 = 'ENUM::NATPF_D1::NC';

			$socle = Hash::get( $data, 'Situationallocataire.natpf_socle' );
			$activite = Hash::get( $data, 'Situationallocataire.natpf_activite' );
			$majore = Hash::get( $data, 'Situationallocataire.natpf_majore' );

			if( ( $socle == '1' ) && ( $activite == '1' ) && ( $majore == '1' ) ) {
				$natpf_d1 = 'ENUM::NATPF_D1::majore';
			}
			else if( ( $socle == '1' ) && ( $activite == '1' ) && ( $majore == '0' ) ) {
				$natpf_d1 = 'ENUM::NATPF_D1::socle_activite';
			}
			else if( ( $socle == '1' ) && ( $activite == '0' ) && ( $majore == '1' ) ) {
				$natpf_d1 = 'ENUM::NATPF_D1::majore';
			}
			else if( ( $socle == '1' ) && ( $activite == '0' ) && ( $majore == '0' ) ) {
				$natpf_d1 = 'ENUM::NATPF_D1::socle';
			}
			else if( ( $socle == '0' ) && ( $activite == '1' ) && ( $majore == '1' ) ) {
				$natpf_d1 = 'ENUM::NATPF_D1::majore';
			}
			else if( ( $socle == '0' ) && ( $activite == '1' ) && ( $majore == '0' ) ) {
				$natpf_d1 = 'ENUM::NATPF_D1::NC';
			}
			else if( ( $socle == '0' ) && ( $activite == '0' ) && ( $majore == '1' ) ) {
				$natpf_d1 = 'ENUM::NATPF_D1::NC';
			}
			else if( ( $socle == '0' ) && ( $activite == '0' ) && ( $majore == '0' ) ) {
				$natpf_d1 = 'ENUM::NATPF_D1::NC';
			}

			if( $translate ) {
				$natpf_d1 = __d( 'situationallocataire', $natpf_d1 );
			}

			return $natpf_d1;

		}
	}
?>