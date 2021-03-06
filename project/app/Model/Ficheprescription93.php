<?php
	/**
	 * Code source de la classe Ficheprescription93.
	 *
	 * PHP 7.2
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );
	App::uses( 'Sanitize', 'Utility' );

	/**
	 * La classe Ficheprescription93 ...
	 *
	 * @package app.Model
	 */
	class Ficheprescription93 extends AppModel
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'Ficheprescription93';

		/**
		 * Correspondance entre les champs virtuels de l'action et les champ réels.
		 *
		 * @var array
		 */
		public $correspondances = array(
			'Ficheprescription93.typethematiquefp93_id' => 'Thematiquefp93.type',
			'Ficheprescription93.yearthematiquefp93_id' => 'Thematiquefp93.yearthema',
			'Ficheprescription93.thematiquefp93_id' => 'Thematiquefp93.id',
			'Ficheprescription93.categoriefp93_id' => 'Categoriefp93.id',
			'Ficheprescription93.filierefp93_id' => 'Filierefp93.id',
			'Ficheprescription93.prestatairefp93_id' => 'Prestatairefp93.id',
			'Ficheprescription93.actionfp93_id' => 'Actionfp93.id',
			'Ficheprescription93.numconvention' => 'Ficheprescription93.numconvention',
			'Ficheprescription93.adresseprestatairefp93_id' => 'Ficheprescription93.adresseprestatairefp93_id'
		);

		/**
		 * Liste des champs Hors PDI
		 *
		 * @var array
		 */
		public $fieldsHorsPdi = array(
			'Ficheprescription93.actionfp93',
			'Ficheprescription93.selection_adresse_prestataire' => array(
				'type' => 'select',
				'options' => array()
			),
			'Prestatairehorspdifp93.name',
			'Prestatairehorspdifp93.adresse',
			'Prestatairehorspdifp93.codepos',
			'Prestatairehorspdifp93.localite',
			'Prestatairehorspdifp93.tel',
			'Prestatairehorspdifp93.fax',
			'Prestatairehorspdifp93.email'
		);

		/**
		 * Behaviors utilisés par le modèle.
		 *
		 * @var array
		 */
		public $actsAs = array(
			'Allocatairelie',
			'Conditionnable',
			'Gedooo.Gedooo',
			'ModelesodtConditionnables' => array(
				93 => array(
					'Ficheprescription93/ficheprescription.odt',
				)
			),
			'Validation2.Validation2Formattable' => array(
				'Validation2.Validation2DefaultFormatter' => array(
					'stripNotAlnum' => '/^(prestatairefp93_tel|prestatairefp93_fax)$/'
				)
			),
			'Validation2.Validation2RulesFieldtypes',
			'Validation2.Validation2RulesComparison',
			'Postgres.PostgresAutovalidate',
			'Ficheprescription93CSV'
		);

		/**
		 * Associations "Has one".
		 *
		 * @var array
		 */
		public $hasOne = array(
			'Instantanedonneesfp93' => array(
				'className' => 'Instantanedonneesfp93',
				'foreignKey' => 'ficheprescription93_id',
				'conditions' => null,
				'fields' => null,
				'order' => null,
				'dependent' => true
			),
		);

		/**
		 * Associations "Has many".
		 *
		 * @var array
		 */
		public $hasMany = array(
			'Populationb4b5pdv93' => array(
				'className' => 'Populationb4b5pdv93',
				'foreignKey' => 'ficheprescription93_id',
				'conditions' => null,
				'order' => null,
				'limit' => null,
				'offset' => null,
				'dependent' => true,
				'exclusive' => null,
				'finderQuery' => null
			)
		);

		/**
		 * Associations "Belongs to".
		 *
		 * @var array
		 */
		public $belongsTo = array(
			'Actionfp93' => array(
				'className' => 'Actionfp93',
				'foreignKey' => 'actionfp93_id',
				'conditions' => null,
				'type' => 'LEFT OUTER',
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
			'Adresseprestatairefp93' => array(
				'className' => 'Adresseprestatairefp93',
				'foreignKey' => 'adresseprestatairefp93_id',
				'conditions' => null,
				'type' => 'LEFT OUTER',
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
			'Filierefp93' => array(
				'className' => 'Filierefp93',
				'foreignKey' => 'filierefp93_id',
				'conditions' => null,
				'type' => 'INNER',
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
			'Motifnonintegrationfp93' => array(
				'className' => 'Motifnonintegrationfp93',
				'foreignKey' => 'motifnonintegrationfp93_id',
				'conditions' => null,
				'type' => 'LEFT OUTER',
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
			'Motifnonretenuefp93' => array(
				'className' => 'Motifnonretenuefp93',
				'foreignKey' => 'motifnonretenuefp93_id',
				'conditions' => null,
				'type' => 'LEFT OUTER',
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
			'Personne' => array(
				'className' => 'Personne',
				'foreignKey' => 'personne_id',
				'conditions' => null,
				'type' => 'INNER',
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
			'Prestatairehorspdifp93' => array(
				'className' => 'Prestatairehorspdifp93',
				'foreignKey' => 'prestatairehorspdifp93_id',
				'conditions' => null,
				'type' => 'LEFT OUTER',
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
			'Referent' => array(
				'className' => 'Referent',
				'foreignKey' => 'referent_id',
				'conditions' => null,
				'type' => 'INNER',
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
			'Motifcontactfp93' => array(
				'className' => 'Motifcontactfp93',
				'foreignKey' => 'motifcontactfp93_id',
				'conditions' => null,
				'type' => 'LEFT OUTER',
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
			'Motifactionachevefp93' => array(
				'className' => 'Motifactionachevefp93',
				'foreignKey' => 'motifactionachevefp93_id',
				'conditions' => null,
				'type' => 'LEFT OUTER',
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
			'Motifnonactionachevefp93' => array(
				'className' => 'Motifnonactionachevefp93',
				'foreignKey' => 'motifnonactionachevefp93_id',
				'conditions' => null,
				'type' => 'LEFT OUTER',
				'fields' => null,
				'order' => null,
				'counterCache' => null
			)
		);


		/**
		 * Associations "Has and belongs to many".
		 *
		 * @var array
		 */
		public $hasAndBelongsToMany = array(
			'Documentbeneffp93' => array(
				'className' => 'Documentbeneffp93',
				'joinTable' => 'documentsbenefsfps93_fichesprescriptions93',
				'foreignKey' => 'ficheprescription93_id',
				'associationForeignKey' => 'documentbeneffp93_id',
				'unique' => true,
				'conditions' => null,
				'fields' => null,
				'order' => null,
				'limit' => null,
				'offset' => null,
				'finderQuery' => null,
				'deleteQuery' => null,
				'insertQuery' => null,
				'with' => 'Documentbeneffp93Ficheprescription93'
			),
			'Modtransmfp93' => array(
				'className' => 'Modtransmfp93',
				'joinTable' => 'fichesprescriptions93_modstransmsfps93',
				'foreignKey' => 'ficheprescription93_id',
				'associationForeignKey' => 'modtransmfp93_id',
				'unique' => true,
				'conditions' => null,
				'fields' => null,
				'order' => null,
				'limit' => null,
				'offset' => null,
				'finderQuery' => null,
				'deleteQuery' => null,
				'insertQuery' => null,
				'with' => 'Ficheprescription93Modtransmfp93'
			)
		);

		/**
		 * Règles de validation.
		 *
		 * @var array
		 */
		public $validate = array(
			// Début champs virtuels pour le formulaire d'ajout / modification
			'structurereferente_id' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => null,
					'allowEmpty' => false
				)
			),
			'typethematiquefp93_id' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => null,
					'allowEmpty' => false
				)
			),
			'thematiquefp93_id' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => null,
					'allowEmpty' => false
				)
			),
			'categoriefp93_id' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => null,
					'allowEmpty' => false
				)
			),
			'filierefp93_id' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => null,
					'allowEmpty' => false
				)
			),
			'prestatairefp93_id' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( 'notEmptyIf', 'prestatairehorspdifp93_id', true, array( NULL, '' ) ),
					'message' => null
				)
			),
			'prestatairehorspdifp93_id' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( 'notEmptyIf', 'prestatairefp93_id', true, array( NULL, '' ) ),
					'message' => null
				)
			),
			'actionfp93_id' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( 'notEmptyIf', 'typethematiquefp93_id', true, array( 'pdi' ) ),
					'message' => null
				)
			),
			'actionfp93' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( 'notEmptyIf', 'typethematiquefp93_id', true, array( 'horspdi' ) ),
					'message' => null
				)
			),
			// Fin champs virtuels pour le formulaire d'ajout / modification
			/*'objet' => array(
				NOT_BLANK_RULE_NAME => array(
					'rule' => array( NOT_BLANK_RULE_NAME ),
					'message' => null,
					'allowEmpty' => false
				)
			),*/
			'dd_action' => array(
				'compareDates' => array(
					'rule' => array( 'compareDates', 'df_action', '<' ),
					'message' => 'La date de début d\'action doit être strictement inférieure à la date de fin d\'action',
					'allowEmpty' => true
				)
			),
			'df_action' => array(
				'compareDates' => array(
					'rule' => array( 'compareDates', 'dd_action', '>' ),
					'message' => 'La date de fin d\'action doit être strictement supérieure à la date de début d\'action',
					'allowEmpty' => true
				)
			),
		);

		/**
		 * Retourne les options nécessaires au formulaire de recherche, au formulaire,
		 * aux impressions, ...
		 *
		 * Les options pdf nécessitent les options de l'allocataire.
		 *
		 * @todo actif
		 *
		 * @param array $params <=> array( 'allocataire' => true, 'find' => false, 'autre' => false, 'pdf' => false, 'enums' => true )
		 * @return array
		 */
		public function options( array $params = array() ) {
			$options = array();
			$params = $params + array( 'allocataire' => true, 'find' => false, 'autre' => false, 'pdf' => false, 'enums' => true );

			// Les options pdf nécessitent les options de l'allocataire
			$params['allocataire'] = ( $params['allocataire'] || $params['pdf'] );

			$motifsNames = array(  'Motifnonretenuefp93', 'Motifnonintegrationfp93','Motifcontactfp93','Motifactionachevefp93','Motifnonactionachevefp93', 'Documentbeneffp93' );

			if( Hash::get( $params, 'allocataire' ) ) {
				$Allocataire = ClassRegistry::init( 'Allocataire' );

				$options = $Allocataire->options();
			}

			if( Hash::get( $params, 'enums' ) ) {
				$options = Hash::merge(
					$options,
					$this->enums(),
					array( 'Ficheprescription93' => array( 'exists' => array( '0' => 'Non', '1' => 'Oui' ) ) ),
					$this->Actionfp93->enums(),
					$this->Filierefp93->enums(),
					$this->Filierefp93->Categoriefp93->enums(),
					$this->Filierefp93->Categoriefp93->Thematiquefp93->enums(),
					$this->Instantanedonneesfp93->enums()
				);
			}

			if( Hash::get( $params, 'find' ) ) {
				$options = Hash::merge(
					$options,
					array( 'Ficheprescription93' => array( 'typethematiquefp93_id' => $this->Filierefp93->Categoriefp93->Thematiquefp93->enum( 'type' ) ) ),
					array( 'Modtransmfp93' => array( 'Modtransmfp93' => $this->Modtransmfp93->find( 'list' ) ) ),
					array( 'Documentbeneffp93' => array( 'Documentbeneffp93' => $this->Documentbeneffp93->find( 'list' ) ) )
				);
				foreach( $motifsNames as $motifName ) {
					$foreignKey = Inflector::underscore( $motifName ).'_id';

					$options[$this->alias][$foreignKey] = $this->{$motifName}->find( 'list', array( 'order' => 'autre ASC, name ASC' ) );
				}
			}

			// Valeurs "Autre" pour les motifs ...
			if( Hash::get( $params, 'autre' ) ) {
				foreach( $motifsNames as $motifName ) {
					$foreignKey = Inflector::underscore( $motifName ).'_id';

					$query = array(
						'fields' => array( "{$motifName}.id" ),
						'conditions' => array(
							"{$motifName}.autre" => '1'
						)
					);
					$options['Autre'][$this->alias][$foreignKey] = $this->{$motifName}->find( 'list', $query );
				}
			}

			if( Hash::get( $params, 'pdf' ) ) {
				$options = Hash::merge(
					$options,
					array(
						'Instantanedonnees93' => array(
							'benef_qual' => $options['Personne']['qual']
						),
						'Referent' => array(
							'qual' => $options['Personne']['qual']
						)
					)
				);
			}

			return $options;
		}

		/**
		 * Préparation des données du formulaire d'ajout / de modification.
		 *
		 * @param integer $personne_id
		 * @param integer $id
		 * @return array
		 * @throws InternalErrorException
		 */
		public function prepareFormDataAddEdit( $personne_id, $id = null ) {
			// A la création
			if( $id === null ) {
				$return = $this->Instantanedonneesfp93->getInstantane( $personne_id );

				$return[$this->alias]['personne_id'] = $personne_id;
				$return[$this->alias]['statut'] = '01renseignee';

				// Référent du parcours actuel
				$referentparcours = $this->Personne->PersonneReferent->referentParcoursActuel( $personne_id );
				if( !empty( $referentparcours ) ) {
					$return[$this->alias]['structurereferente_id'] = $referentparcours['Referent']['structurereferente_id'];
					$return[$this->alias]['referent_id'] = "{$referentparcours['Referent']['structurereferente_id']}_{$referentparcours['Referent']['id']}";
				}
				$return['Ficheprescription93']['frsa_datetransmi'] = NULL;
			}
			else {
				$query = array(
					'fields' => Hash::merge(
						$this->fields(),
						$this->Instantanedonneesfp93->fields(),
						$this->Prestatairehorspdifp93->fields(),
						array(
							$this->Instantanedonneesfp93->sqVirtualField( 'benef_natpf' ),
							'Referent.structurereferente_id',
							'Actionfp93.numconvention',
							'Actionfp93.filierefp93_id',
							'Actionfp93.adresseprestatairefp93_id',
							'Adresseprestatairefp93.prestatairefp93_id',
							'Filierefp93.categoriefp93_id',
							'Categoriefp93.thematiquefp93_id',
							'Thematiquefp93.type',
							'Thematiquefp93.yearthema',
						)
					),
					'contain' => false,
					'joins' => array(
						$this->join( 'Actionfp93', array( 'type' => 'LEFT OUTER' ) ),
						$this->join( 'Instantanedonneesfp93' ),
						$this->join( 'Prestatairehorspdifp93', array( 'type' => 'LEFT OUTER' ) ),
						$this->join( 'Referent' ),
						$this->join( 'Filierefp93' ),
						$this->Actionfp93->join( 'Adresseprestatairefp93', array( 'type' => 'LEFT OUTER' ) ),
						$this->Actionfp93->Adresseprestatairefp93->join( 'Prestatairefp93', array( 'type' => 'LEFT OUTER' ) ),
						$this->Filierefp93->join( 'Categoriefp93' ),
						$this->Filierefp93->Categoriefp93->join( 'Thematiquefp93' ),
					),
					'conditions' => array(
						"{$this->alias}.id" => $id
					)
				);
				$data = $this->find( 'first', $query );

				if( empty( $data ) || $data[$this->alias]['statut'] == '99annulee' ) {
					throw new InternalErrorException();
				}

				// Récupération des modes de transmissions
				$query = array(
					'fields' => array(
						'Ficheprescription93Modtransmfp93.id',
						'Ficheprescription93Modtransmfp93.modtransmfp93_id',
					),
					'conditions' => array(
						'Ficheprescription93Modtransmfp93.ficheprescription93_id' => $id
					)
				);
				$data['Modtransmfp93']['Modtransmfp93'] = (array)$this->Ficheprescription93Modtransmfp93->find( 'list', $query );

				// Récupération des documents dont le bénéficiaire est invité à se munir
				$query = array(
					'fields' => array(
						'Documentbeneffp93Ficheprescription93.id',
						'Documentbeneffp93Ficheprescription93.documentbeneffp93_id',
					),
					'conditions' => array(
						'Documentbeneffp93Ficheprescription93.ficheprescription93_id' => $id
					)
				);
				$data['Documentbeneffp93']['Documentbeneffp93'] = (array)$this->Documentbeneffp93Ficheprescription93->find( 'list', $query );

				// Fin de la Récupération des données
				$return = $data;

				$return[$this->alias]['structurereferente_id'] = $data['Referent']['structurereferente_id'];
				$return[$this->alias]['referent_id'] = "{$data['Referent']['structurereferente_id']}_{$data[$this->alias]['referent_id']}";

				$return[$this->alias]['numconvention'] = $data['Actionfp93']['numconvention'];

				$return[$this->alias]['actionfp93_id'] = $data[$this->alias]['actionfp93_id'];
				$return[$this->alias]['adresseprestatairefp93_id'] = $data['Actionfp93']['adresseprestatairefp93_id'];
				$return[$this->alias]['prestatairefp93_id'] = $data['Adresseprestatairefp93']['prestatairefp93_id'];
				$return[$this->alias]['categoriefp93_id'] = $data['Filierefp93']['categoriefp93_id'];
				$return[$this->alias]['thematiquefp93_id'] = $data['Categoriefp93']['thematiquefp93_id'];
				$return[$this->alias]['typethematiquefp93_id'] = $data['Thematiquefp93']['type'];
				$return[$this->alias]['yearthematiquefp93_id'] = $data['Thematiquefp93']['yearthema'];

				$return[$this->alias]['rdvprestataire_adresse_check'] = ( trim( (string)$data[$this->alias]['rdvprestataire_adresse'] ) !== '' );
			}

			// Formattage de l'adresse
			$return['Instantanedonneesfp93']['benef_adresse'] =
				$return['Instantanedonneesfp93']['benef_numvoie']
				.' '.$return['Instantanedonneesfp93']['benef_libtypevoie']
				.' '.$return['Instantanedonneesfp93']['benef_nomvoie']
				.( !empty( $return['Instantanedonneesfp93']['benef_complideadr'] ) ? "\n".$return['Instantanedonneesfp93']['benef_complideadr'] : '' )
				.( !empty( $return['Instantanedonneesfp93']['benef_compladr'] ) ? "\n".$return['Instantanedonneesfp93']['benef_compladr'] : '' );

			return $return;
		}

		/**
		 * Tentative de sauvegarde du formulaire d'ajout / de modification.
		 *
		 * @param array $data
		 * @return boolean
		 */
		public function saveAddEdit( array $data ) {
			// Pour l'état 01renseignee
			$unneeded = array( 'Validate', 'Thematiquefp93', 'Categoriefp93', 'Filierefp93', 'Actionfp93' );
			foreach( $unneeded as $modelName ) {
				unset( $data[$modelName] );
			}

			$id = Hash::get( $data, "{$this->alias}.{$this->primaryKey}" );
			$personne_id = Hash::get( $data, "{$this->alias}.personne_id" );

			$ficheprescription = array();
			// En cas de modification, on va rechercher les informations qui ne sont pas renvoyées par le formulaire
			if( !empty( $id ) ) {
				$ficheprescription = $this->find(
					'first',
					array(
						'fields' => Hash::merge(
							$this->fields(),
							$this->Instantanedonneesfp93->fields()
						),
						'contain' => false,
						'joins' => array(
							$this->join( 'Instantanedonneesfp93' )
						),
						'conditions' => array(
							"{$this->alias}.id" => $id
						)
					)
				);

				unset( $ficheprescription[$this->alias]['created'], $ficheprescription[$this->alias]['modified'] );
			}

			$data = Hash::merge( $ficheprescription, $data );

			// Case à cocher "Adresse du lieu de RDV"
			$rdvprestataire_adresse_check = Hash::get( $data, "{$this->alias}.rdvprestataire_adresse_check" );
			if( in_array( $rdvprestataire_adresse_check, array( '0', 0, null ), true ) ) {
				$data[$this->alias]['rdvprestataire_adresse'] = null;
			}

			// Certains champs sont désactivés via javascript et ne sont pas renvoyés
			$autres = Hash::get( $this->options( array( 'allocataire' => false, 'find' => false, 'autre' => true ) ), 'Autre' );
			//champs liée a personne retenu
			$value = Hash::get( $data, 'Ficheprescription93.personne_retenue' );
			if( $value !== '0' ) {
				$data['Ficheprescription93']['motifnonretenuefp93_id'] = null;
				$data['Ficheprescription93']['personne_nonretenue_autre'] = null;
			}
			else if( !in_array( $data['Ficheprescription93']['motifnonretenuefp93_id'], $autres['Ficheprescription93']['motifnonretenuefp93_id'] ) ) {
				$data['Ficheprescription93']['personne_nonretenue_autre'] = null;
			}
			// champs liée a personne intégré
			$value = Hash::get( $data, 'Ficheprescription93.personne_a_integre' );
			if( $value === '' ) {
				$data['Ficheprescription93']['personne_date_integration'] = null;
				$data['Ficheprescription93']['motifnonintegrationfp93_id'] = null;
				$data['Ficheprescription93']['personne_nonintegre_autre'] = null;
			}
			else if( $value === '0' ) {
				$data['Ficheprescription93']['personne_date_integration'] = null;
			}
			else if( $value === '1' ) {
				$data['Ficheprescription93']['motifnonintegrationfp93_id'] = null;
				$data['Ficheprescription93']['personne_nonintegre_autre'] = null;
			}
			else if( $value === '1' && !in_array( $data['Ficheprescription93']['motifnonintegrationfp93_id'], $autres['Ficheprescription93']['motifnonintegrationfp93_id'] ) ) {
				$data['Ficheprescription93']['personne_nonintegre_autre'] = null;
			}
			// Champs liée a personne acheve
			$value = Hash::get( $data, 'Ficheprescription93.personne_acheve' );
			if( $value === '' ) {
				$data['Ficheprescription93']['motifactionachevefp93_id'] = null;
				$data['Ficheprescription93']['motifnonactionachevefp93_id'] = null;
				$data['Ficheprescription93']['personne_acheve_autre'] = null;
			}
			else if( $value === '0' ) {
				$data['Ficheprescription93']['motifactionachevefp93_id'] = null;
				if (!in_array( $data['Ficheprescription93']['motifactionachevefp93_id'], $autres['Ficheprescription93']['motifactionachevefp93_id'])
				&& !in_array( $data['Ficheprescription93']['motifnonactionachevefp93_id'], $autres['Ficheprescription93']['motifnonactionachevefp93_id'])	) {
					$data['Ficheprescription93']['personne_acheve_autre'] = null;
				}
			}
			else if( $value === '1' ) {
				$data['Ficheprescription93']['motifnonactionachevefp93_id'] = null;
				if (!in_array( $data['Ficheprescription93']['motifactionachevefp93_id'], $autres['Ficheprescription93']['motifactionachevefp93_id'])
				&& !in_array( $data['Ficheprescription93']['motifnonactionachevefp93_id'], $autres['Ficheprescription93']['motifnonactionachevefp93_id'])	) {
					$data['Ficheprescription93']['personne_acheve_autre'] = null;
				}
			}

			// Cases à cocher "Le bénéficiaire est invité à se munir de"
			$values = (array)Hash::get( $data, 'Documentbeneffp93.Documentbeneffp93' );
			if( count( array_intersect( $values, $autres['Ficheprescription93']['documentbeneffp93_id'] ) ) == 0 ) {
				$data['Ficheprescription93']['documentbeneffp93_autre'] = null;
			}

			$data = $this->calculStatusFP($data);

			// Début Instantanedonnees93 ...
			$referent_id = suffix( Hash::get( $data, "{$this->alias}.referent_id" ) );
			$referent = $this->Referent->find(
				'first',
				array(
					'fields' => array(
						'Referent.fonction',
						'Referent.email',
						'Structurereferente.lib_struc',
						'Structurereferente.num_voie',
						'Structurereferente.type_voie',
						'Structurereferente.nom_voie',
						'Structurereferente.code_postal',
						'Structurereferente.ville',
						'Structurereferente.numtel',
						'Structurereferente.numfax',
					),
					'contain' => false,
					'joins' => array(
						$this->Referent->join( 'Structurereferente', array( 'type' => 'INNER' ) )
					),
					'conditions' => array(
						'Referent.id' => $referent_id
					),
				)
			);

			if( $data[$this->alias]['statut'] == '01renseignee' || empty( $id ) ) {
				$instantanedonneesfp93 = $this->Instantanedonneesfp93->getInstantane( $personne_id );
				$data = Hash::merge( $instantanedonneesfp93, $data );
			}

			if( !empty( $referent ) ) {
				$data = Hash::merge(
					$data,
						array(
						'Instantanedonneesfp93' => array(
							'referent_fonction' => $referent['Referent']['fonction'],
							'structure_name' => $referent['Structurereferente']['lib_struc'],
							'structure_num_voie' => $referent['Structurereferente']['num_voie'],
							'structure_type_voie' => $referent['Structurereferente']['type_voie'],
							'structure_nom_voie' => $referent['Structurereferente']['nom_voie'],
							'structure_code_postal' => $referent['Structurereferente']['code_postal'],
							'structure_ville' => $referent['Structurereferente']['ville'],
							'structure_tel' => $referent['Structurereferente']['numtel'],
							'structure_fax' => $referent['Structurereferente']['numfax'],
							'referent_email' => $referent['Referent']['email'],
						)
					)
				);
			}
			// Fin Instantanedonnees93

			$success = true;
			// Différenciation action PDI / Hors PDI
			$typethematiquefp93_id = Hash::get( $data, "{$this->alias}.typethematiquefp93_id" );
			$prestatairehorspdifp93_id = Hash::get( $data, 'Prestatairehorspdifp93.id' );

			if( $typethematiquefp93_id === 'pdi' ) {
				// Suppression des informations Hors PDI
				foreach( array_keys( Hash::normalize( $this->fieldsHorsPdi ) ) as $path ) {
					$data = Hash::insert( $data, $path, null );
				}

				$data = Hash::merge(
					$data,
					array(
						$this->alias => array(
							'actionfp93' => null,
							'prestatairehorspdifp93_id' => null,
						)
					)
				);
			}
			else {
				$data = Hash::merge(
					$data,
					array(
						$this->alias => array(
							'actionfp93_id' => null,
							'adresseprestatairefp93_id' => null,
						)
					)
				);
			}

			// Si la case "Adresse du lieu de rendez-vous..." n'est pas cochée, on supprime l'information de l'adresse
			if( !Hash::get( $data, 'Ficheprescription93.rdvprestataire_adresse_check' ) ) {
				$data = Hash::insert( $data, 'Ficheprescription93.rdvprestataire_adresse', null );
			}

			if( $typethematiquefp93_id === 'pdi' && $prestatairehorspdifp93_id !== null ) {
				$data[$this->alias]['prestatairehorspdifp93_id'] = null;
			}
			else if( $typethematiquefp93_id === 'horspdi' ) {
				$this->Prestatairehorspdifp93->create( $data );
				$success = ( $this->Prestatairehorspdifp93->save( null, array( 'atomic' => false ) ) !== false ) && $success;
				$data[$this->alias]['prestatairehorspdifp93_id'] = $this->Prestatairehorspdifp93->id;
			}

			// Sauvegarde de la fiche
			$this->create( $data );
			$success = ( $this->save( null, array( 'atomic' => false ) ) !== false ) && $success;

			// Si on est PDI mais qu'on était hors PDI avant, il faut supprimer l'enregistrement de la table Prestatairehorspdifp93
			if( $typethematiquefp93_id === 'pdi' && !in_array( $prestatairehorspdifp93_id, array( null, '' ), true ) ) {
				$success = $this->Prestatairehorspdifp93->delete( $prestatairehorspdifp93_id ) && $success;
			}

			$dspData = array(
				'Dsp' => array(
					'nivetu' => Hash::get( $data, 'Instantanedonneesfp93.benef_nivetu' )
				)
			);
			$success = $this->Personne->Dsp->WebrsaDsp->updateDerniereDsp( $personne_id, $dspData ) && $success;

			// Instantané données
			$data['Instantanedonneesfp93']['ficheprescription93_id'] = $this->id;
			$this->Instantanedonneesfp93->create( $data );
			$success = ( $this->Instantanedonneesfp93->save( null, array( 'atomic' => false ) ) !== false ) && $success;

			if( !$success && empty( $this->validationErrors ) ) {
				$hiddenErrors = array(
					'Instantanedonneesfp93' => $this->Instantanedonneesfp93->validationErrors
				);
				unset( $hiddenErrors['Instantanedonneesfp93']['ficheprescription93_id'] );

				if( !empty( $hiddenErrors ) ) {
					debug( $hiddenErrors );
				}
			}

			return $success;
		}

		/**
		 * Calcul du statu en fonction des états de la fiche.
		 *
		 * @param array $data
		 * @return array $data
		 */
		public function calculStatusFP(array $data) {
			// Modification de l'état suivant les données
				$statut = '01renseignee';
				if( dateComplete(  $data, 'Ficheprescription93.date_signature' ) ) {
					$statut = '02signee';
				}
				if( $statut == '02signee' && dateComplete(  $data, 'Ficheprescription93.date_transmission' ) ) {
					$statut = '03transmise_partenaire';
				}
				if( $statut == '03transmise_partenaire' && dateComplete(  $data, 'Ficheprescription93.date_retour' ) ) {
					$statut = '05suivi_renseigne'; // 04effectivite_renseignee
				}
				/*Retirer en Version 3.2.6 sur la simplification de la fiche prescription
				 * if( $statut == '04effectivite_renseignee' && Hash::get(  $data, 'Ficheprescription93.personne_recue' ) != '' ) {
					$statut = '05suivi_renseigne';
				}*/
				$data[$this->alias]['statut'] = $statut;
			return $data;
		}

		/**
		 * Permet de savoir si un ajout est possible à partir des messages
		 * renvoyés par la méthode messages.
		 *
		 * @param array $messages
		 * @return boolean
		 */
		public function addEnabled( array $messages ) {
			return !in_array( 'error', $messages );
		}

		/**
		 * Messages à envoyer à l'utilisateur.
		 *
		 * @param integer $personne_id
		 * @return array
		 */
		public function messages( $personne_id ) {
			$messages = array();

			$query = array(
				'fields' => array(
					'Calculdroitrsa.toppersdrodevorsa',
					'Situationdossierrsa.etatdosrsa',
				),
				'contain' => false,
				'joins' => array(
					$this->Personne->join( 'Calculdroitrsa', array( 'type' => 'LEFT OUTER' ) ),
					$this->Personne->join( 'Foyer', array( 'type' => 'INNER' ) ),
					$this->Personne->Foyer->join( 'Dossier', array( 'type' => 'INNER' ) ),
					$this->Personne->Foyer->Dossier->join( 'Situationdossierrsa', array( 'type' => 'LEFT OUTER' ) ),
				),
				'conditions' => array(
					'Personne.id' => $personne_id
				)
			);

			$result = $this->Personne->find( 'first', $query );

			$toppersdrodevorsa = Hash::get( $result, 'Calculdroitrsa.toppersdrodevorsa' );
			if( empty( $toppersdrodevorsa ) ) {
				$messages['Instantanedonneesfp93.benef_toppersdrodevorsa_notice'] = 'notice';
			}

			$etatdosrsa = Hash::get( $result, 'Situationdossierrsa.etatdosrsa' );
			if( !in_array( $etatdosrsa, (array)Configure::read( 'Situationdossierrsa.etatdosrsa.ouvert' ), true ) ) {
				$messages['Instantanedonneesfp93.benef_etatdosrsa_ouverts'] = 'notice';
			}

			return $messages;
		}

		/**
		 * Récupération des informations pour l'impression.
		 *
		 * @see Configure::write( 'Ficheprescription93.regexpNumconventionFictif', '...' );
		 *
		 * @param integer $ficheprescription93_id
		 * @param integer $user_id
		 * @return array
		 * @throws NotFoundException
		 */
		public function getDataForPdf( $ficheprescription93_id, $user_id = null ) {
			$data = $this->find(
				'first',
				array(
					'fields' => Hash::merge(
						$this->fields(),
						$this->Actionfp93->fields(),
						$this->Adresseprestatairefp93->fields(),
						$this->Filierefp93->fields(),
						$this->Instantanedonneesfp93->fields(),
						$this->Motifnonintegrationfp93->fields(),
						$this->Motifcontactfp93->fields(),
						$this->Motifnonretenuefp93->fields(),
						$this->Motifactionachevefp93->fields(),
						$this->Motifnonactionachevefp93->fields(),
						$this->Personne->fields(),
						$this->Referent->fields(),
						$this->Adresseprestatairefp93->Prestatairefp93->fields(),
						$this->Prestatairehorspdifp93->fields(),
						$this->Filierefp93->Categoriefp93->fields(),
						$this->Filierefp93->Categoriefp93->Thematiquefp93->fields(),
						$this->Referent->Structurereferente->fields()
					),
					'contain' => false,
					'joins' => array(
						$this->join( 'Actionfp93', array( 'type' => 'LEFT OUTER' ) ),
						$this->join( 'Adresseprestatairefp93', array( 'type' => 'LEFT OUTER' ) ),
						$this->join( 'Filierefp93', array( 'type' => 'INNER' ) ),
						$this->join( 'Instantanedonneesfp93', array( 'type' => 'INNER' ) ),
						$this->join( 'Motifnonintegrationfp93', array( 'type' => 'LEFT OUTER' ) ),
						$this->join( 'Motifnonretenuefp93', array( 'type' => 'LEFT OUTER' ) ),
						$this->join( 'Motifactionachevefp93', array( 'type' => 'LEFT OUTER' ) ),
						$this->join( 'Motifcontactfp93', array( 'type' => 'LEFT OUTER' ) ),
						$this->join( 'Motifnonactionachevefp93', array( 'type' => 'LEFT OUTER' ) ),
						$this->join( 'Personne', array( 'type' => 'INNER' ) ),
						$this->join( 'Prestatairehorspdifp93', array( 'type' => 'LEFT OUTER' ) ),
						$this->join( 'Referent', array( 'type' => 'INNER' ) ),
						$this->Adresseprestatairefp93->join( 'Prestatairefp93', array( 'type' => 'LEFT OUTER' ) ),
						$this->Filierefp93->join( 'Categoriefp93', array( 'type' => 'INNER' ) ),
						$this->Filierefp93->Categoriefp93->join( 'Thematiquefp93', array( 'type' => 'INNER' ) ),
						$this->Referent->join( 'Structurereferente', array( 'type' => 'INNER' ) ),
					),
					'conditions' => array(
						"{$this->alias}.id" => $ficheprescription93_id
					)
				)
			);

			if( empty( $data ) ) {
				throw new NotFoundException();
			}

			// Si le numéro de convention est fictif, ne pas le faire apparaître à l'impression
			$regexp = Configure::read( 'Ficheprescription93.regexpNumconventionFictif' );
			if( !empty( $regexp ) && isset( $data['Actionfp93']['numconvention'] ) && preg_test( $regexp ) && preg_match( $regexp, Hash::get( $data, 'Actionfp93.numconvention' ) ) ) {
				$data['Actionfp93']['numconvention'] = null;
			}

			if( !empty( $user_id ) ) {
				$User = ClassRegistry::init( 'User' );
				$user = $User->find(
					'first',
					array(
						'fields' => $User->fields(),
						'contain' => false,
						'conditions' => array(
							'User.id' => $user_id
						)
					)
				);
				unset( $user['User']['password'] );

				$data = Hash::merge( $data, $user );
			}

			$return = array(
				$data,
				'documentbeneffp93' => array(),
				'modtransmfp93' => array(),
				/*'motifcontactfp93' => array(),
				'motifactionachevefp93' => array(),
				'motifnonactionachevefp93' => array()*/
			);

			// Lecture des données HABTM
			//, 'Motifcontactfp93', 'Motifactionachevefp93', 'Motifnonactionachevefp93'
			foreach( array( 'Documentbeneffp93', 'Modtransmfp93' ) as $habtmModelName ) {
				$with = $this->hasAndBelongsToMany[$habtmModelName]['with'];

				$query = array(
					'fields' => array(
						"{$habtmModelName}.{$this->{$habtmModelName}->primaryKey}",
						"{$habtmModelName}.{$this->{$habtmModelName}->displayField}"
					),
					'joins' => array(
						$this->{$habtmModelName}->join( $with, array( 'type' => 'INNER' ) )
					),
					'conditions' => array(
						"{$with}.ficheprescription93_id" => Hash::get( $data, 'Ficheprescription93.id' )
					)
				);

				$key = Inflector::underscore( $habtmModelName );
				$return[$key] = $this->{$habtmModelName}->find( 'all', $query );
			}

			return $return;
		}

		/**
		 * Retourne le chemin relatif du modèle de document à utiliser pour
		 * l'enregistrement du PDF.
		 *
		 * @param array $data
		 * @return string
		 */
		public function modeleOdt( $data ) {
			return 'Ficheprescription93/ficheprescription.odt';
		}

		/**
		 * Retourne le PDF par défaut généré par les appels aux méthodes
		 * getDataForPdf, modeleOdt, options et à la méthode ged du behavior
		 * Gedooo,
		 *
		 * @param integer $id Id de la fiche de prescription
		 * @param integer $user_id Id de l'utilisateur connecté
		 * @return string
		 */
		public function getDefaultPdf( $id, $user_id = null ) {
			$data = $this->getDataForPdf( $id, $user_id );
			$modeleodt = $this->modeleOdt( $data );
			$options = $this->options( array( 'pdf' => true ) );

			return $this->ged( $data, $modeleodt, true, $options );
		}
	}
?>