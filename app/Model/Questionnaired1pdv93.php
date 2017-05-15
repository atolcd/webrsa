<?php
	/**
	 * Code source de la classe Questionnaired1pdv93.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Questionnaired1pdv93 ...
	 *
	 * @package app.Model
	 */
	class Questionnaired1pdv93 extends AppModel
	{
		/**
		 * Nom.
		 *
		 * @var string
		 */
		public $name = 'Questionnaired1pdv93';

		/**
		 * Behaviors utilisés.
		 *
		 * @var array
		 */
		public $actsAs = array(
			'Allocatairelie',
			'Formattable',
			'Pgsqlcake.PgsqlAutovalidate',
			'Questionnairepdv93',
		);

		/**
		 * Par défaut, on met la récursivité au minimum.
		 *
		 * @var integer
		 */
		public $recursive = -1;

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
			'Rendezvous' => array(
				'className' => 'Rendezvous',
				'foreignKey' => 'rendezvous_id',
				'conditions' => null,
				'type' => null,
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
			'Situationallocataire' => array(
				'className' => 'Situationallocataire',
				'foreignKey' => 'situationallocataire_id',
				'conditions' => null,
				'type' => null,
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
		);

		/**
		 * Associations "Has one".
		 *
		 * @var array
		 */
		public $hasOne = array(
			'Populationd1d2pdv93' => array(
				'className' => 'Populationd1d2pdv93',
				'foreignKey' => 'questionnaired2pdv93_id',
				'conditions' => null,
				'fields' => null,
				'order' => null,
				'dependent' => true
			),
			'Questionnaired2pdv93' => array(
				'className' => 'Questionnaired2pdv93',
				'foreignKey' => 'questionnaired1pdv93_id',
				'conditions' => null,
				'fields' => null,
				'order' => null,
				'dependent' => true
			),
		);

		/**
		 * Règles de validation en plus de celles en base.
		 *
		 * @var array
		 */
		public $validate = array(
			'inscritpe' => array(
				'notEmpty' => array(
					'rule' => array( 'notEmpty' )
				)
			),
			'marche_travail' => array(
				'notEmpty' => array(
					'rule' => array( 'notEmpty' )
				)
			),
			'vulnerable' => array(
				'notEmpty' => array(
					'rule' => array( 'notEmpty' )
				)
			),
			'diplomes_etrangers' => array(
				'notEmpty' => array(
					'rule' => array( 'notEmpty' )
				)
			),
			'categorie_sociopro' => array(
				'notEmpty' => array(
					'rule' => array( 'notEmpty' )
				)
			),
			'nivetu' => array(
				'notEmpty' => array(
					'rule' => array( 'notEmpty' )
				)
			),
			'autre_caracteristique' => array(
				'notEmpty' => array(
					'rule' => array( 'notEmpty' )
				)
			),
			'autre_caracteristique_autre' => array(
				'notNullIf' => array(
					'rule' => array( 'notNullIf', 'autre_caracteristique', true, array( 'autres' ) )
				)
			),
			'conditions_logement' => array(
				'notNullIf' => array(
					'rule' => array( 'notEmpty' )
				)
			),
			'conditions_logement_autre' => array(
				'notNullIf' => array(
					'rule' => array( 'notNullIf', 'conditions_logement', true, array( 'autre' ) )
				)
			),
			'date_validation' => array(
				'notEmpty' => array(
					'rule' => array( 'notEmpty' )
				),
				'checkDateOnceAYear' => array(
					'rule' => array( 'checkDateOnceAYear', 'personne_id', 'rendezvous_id' )
				),
			),
		);

		/**
		 * FIXME: sur la date de RDV + traduction
		 *
		 * @param array $check
		 * @return boolean
		 */
		public function checkDateOnceAYear( $check, $group_column1, $group_column2 ) {
			if( !is_array( $check ) ) {
				return false;
			}

			$result = true;
			foreach( Hash::normalize( $check ) as $key => $value ) {
				list( $year, ) = explode( '-', $value );

				if( !empty( $year ) ) {
					// Pas encore de questionnaire D1 pour l'année en question
					$querydata = array( 'contain' => false );

					$personne_id = Hash::get( $this->data, "{$this->alias}.{$group_column1}" );
					$rendezvous_id = Hash::get( $this->data, "{$this->alias}.{$group_column2}" );

					$querydata['conditions'] = array(
						"{$this->alias}.{$group_column1}" => $personne_id,
						"{$this->alias}.{$key} BETWEEN '{$year}-01-01' AND '{$year}-12-31'",
						"{$this->alias}.{$group_column2}" => $rendezvous_id
					);

					$id = Hash::get( $this->data, "{$this->alias}.{$this->primaryKey}" );
					if( !empty( $id ) ) {
						$querydata['conditions']["{$this->alias}.{$this->primaryKey} <>"] = $id;
					}

					$count = $this->find( 'count', $querydata );

					if( $count == 0 ) {
						$result = ( $count == 0 ) && $result;
					}
					else {
						// Tous les D1 ont déjà un D2 correspondant ?
						$sq = $this->Personne->Questionnaired2pdv93->sq(
							array(
								'alias' => 'questionnairesd2pdvs93',
								'fields' => array( 'questionnairesd2pdvs93.questionnaired1pdv93_id' ),
								'conditions' => array(
									'questionnairesd2pdvs93.questionnaired1pdv93_id = Questionnaired1pdv93.id',
									'questionnairesd2pdvs93.personne_id = Questionnaired1pdv93.personne_id',
								),
								'contain' => false
							)
						);

						$querydata = array(
							'contain' => false,
							'conditions' => array(
								"Questionnaired1pdv93.id NOT IN ( {$sq} )",
								'Questionnaired1pdv93.personne_id' => $personne_id,
							),
						);
						$count = $this->find( 'count', $querydata );
						$result = ( $count == 0 ) && $result;
					}
				}
			}
			return $result;
		}

		/**
		 *
		 * @param array $data
		 * @return array
		 */
		public function completeDataForView( $data ) {
			// Calcul de la situation familiale suivant les catégories du tableau D1
			$sitfam_view = null;
			$isole = in_array( $data['Situationallocataire']['sitfam'], array( 'CEL', 'DIV', 'ISO', 'SEF', 'SEL', 'VEU' ) );
			if( $isole ) {
				$sitfam_view = ( empty( $data['Situationallocataire']['nbenfants'] ) ? 'isole_sans_enfant' : 'isole_avec_enfant' );
			}
			else {
				$sitfam_view = ( empty( $data['Situationallocataire']['nbenfants'] ) ? 'en_couple_sans_enfant' : 'en_couple_avec_enfant' );
			}
			$data['Situationallocataire']['sitfam_view'] = $sitfam_view;

			$date_validation = Hash::get( $data, 'Questionnaired1pdv93.date_validation' );

			// Calcul des tranches d'âge suivant les catégories du tableau D1
			$Tableausuivipdv93 = ClassRegistry::init( 'Tableausuivipdv93' );
			$tranches = array_keys( $Tableausuivipdv93->WebrsaTableausuivipdv93->tranches_ages );
			$tranche_age_view = null;
			$age = age( $data['Situationallocataire']['dtnai'], $date_validation );
			foreach( $tranches as $tranche ) {
				list( $min, $max ) = explode( '_', $tranche );
				if( $min <= $age && $age <= $max ) {
					$tranche_age_view = $tranche;
				}
			}
			$data['Situationallocataire']['tranche_age_view'] = $tranche_age_view;

			// Calcul de l'ancienneté dans le dispositif suivant les catégories du tableau D1
			$Tableausuivipdv93 = ClassRegistry::init( 'Tableausuivipdv93' );
			$tranches = array_keys( $Tableausuivipdv93->WebrsaTableausuivipdv93->anciennetes_dispositif );
			$anciennete_dispositif_view = null;
			$age = age( $data['Situationallocataire']['dtdemrsa'], $date_validation );
			foreach( $tranches as $tranche ) {
				list( $min, $max ) = explode( '_', $tranche );
				if( $min <= $age && $age <= $max ) {
					$anciennete_dispositif_view = $tranche;
				}
			}
			$data['Situationallocataire']['anciennete_dispositif_view'] = $anciennete_dispositif_view;

			return $data;
		}

		/**
		 *
		 *
		 * @param integer $personne_id
		 * @return array
		 * @throws NotFoundException
		 */
		public function prepareFormData( $personne_id ) {
			$formData = array();

			$data = $this->Situationallocataire->getSituation( $personne_id );
			if( empty( $data ) ) {
				throw new NotFoundException();
			}

			// On complète les données du formulaire
			$formData[$this->alias]['personne_id'] = $personne_id;

			$formData['Situationallocataire']['personne_id'] = $personne_id;
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
					if( $field !== 'id' ) {
						$formData['Situationallocataire'][$field] = $value;
					}
				}
			}
			$formData['Situationallocataire']['identifiantpe'] = $data['Historiqueetatpe']['identifiantpe'];
			$formData['Situationallocataire']['datepe'] = $data['Historiqueetatpe']['date'];
			$formData['Situationallocataire']['etatpe'] = $data['Historiqueetatpe']['etat'];
			$formData['Situationallocataire']['codepe'] = $data['Historiqueetatpe']['code'];
			$formData['Situationallocataire']['motifpe'] = $data['Historiqueetatpe']['motif'];

			foreach( array( 'socle', 'majore', 'activite' ) as $type ) {
				$formData['Situationallocataire']["natpf_{$type}"] = ( $formData['Situationallocataire']["natpf_{$type}"] ? '1' : '0' );
			}

			$formData['Situationallocataire']['natpf_d1'] = $this->Situationallocataire->natpfD1( $formData, true );

			// Inscrit à Pôle Emploi
			$inscritpe = Hash::get( $data, 'Historiqueetatpe.etat' );
			if( !is_null( $inscritpe ) ) {
				$inscritpe = ( ( $inscritpe == 'inscription' ) ? '1' : '0' );
			}
			$formData[$this->alias]['inscritpe'] = $inscritpe;

			$formData[$this->alias]['nivetu'] = $this->nivetu( $personne_id );
			$formData[$this->alias]['autre_caracteristique'] = 'beneficiaire_minimas';
			$formData[$this->alias]['rendezvous_id'] = $this->rendezvous( $personne_id );
			$formData[$this->alias]['date_validation'] = $this->_dateValidation( $formData[$this->alias]['rendezvous_id'] );

			// Champs en visualisation uniquement
			$formData = $this->completeDataForView( $formData );

			return $formData;
		}

		/**
		 * Sauvegarde du questionnaire D1 d'un allocataire.
		 *
		 * Si les informations enregistrées dans Situationallocataire diffèrent
		 * des dernières informations enregistrées dans Historiquedroit pour la
		 * personne, on met à jour l'ancienne entrée et on en crée une nouvelle,
		 * sinon on met uniquement à jour la valeur du modified de l'ancienne
		 * entrée.
		 *
		 * @param integer $personne_id L'id de la personne traitée.
		 * @param array $data Les données renvoyées par le formulaire D1
		 *	(Questionnaired1pdv93 et Situationallocataire)
		 * @return boolean
		 */
		public function saveFormData( $personne_id, array $data ) {
			// Sauvegarde des données du formulaire
			debug($data);
			try {
			$result = $this->saveAssociated(
				$data,
				array(
					'validate' => 'first',
					'atomic' => false
				)
			);
			} catch (Exception $e) {
				debug($e);
			}

			$success = $this->saveResultAsBool( $result );

			// Ajout ou mise à jour dans Historiquedroit
			if( $success ) {
				// Recherche du dernier historique
				$query = array(
					'contain' => false,
					'conditions' => array(
						'Historiquedroit.personne_id' => $personne_id
					),
					'order' => array( 'Historiquedroit.created DESC' )
				);
				$historiquedroit = $this->Personne->Historiquedroit->find( 'first', $query );

				$update = (
					!empty( $historiquedroit )
					&& (string)$data['Situationallocataire']['toppersdrodevorsa'] === (string)$historiquedroit['Historiquedroit']['toppersdrodevorsa']
					&& (string)$data['Situationallocataire']['etatdosrsa'] === (string)$historiquedroit['Historiquedroit']['etatdosrsa']
				);

				if( $update ) {
					$success = $this->Personne->Historiquedroit->updateAllUnbound(
						array( 'Historiquedroit.modified' => 'NOW()' ),
						array( 'Historiquedroit.id' => $historiquedroit['Historiquedroit']['id'] )
					) && $success;
				}
				else {
					if( !empty( $historiquedroit ) ) {
						$now = strtotime( 'now' );
						$yesterday = strtotime( '-1 day' );
						$modified = strtotime( $historiquedroit['Historiquedroit']['modified'] );

						// On met à jour l'historique si besoin (modified trop ancien ou dans le présent / futur)
						if( $modified < $yesterday || $modified >= $now ) {
							$success = $this->Personne->Historiquedroit->updateAllUnbound(
								array( 'Historiquedroit.modified' => 'NOW() - INTERVAL \'1 day\'' ),
								array( 'Historiquedroit.id' => $historiquedroit['Historiquedroit']['id'] )
							) && $success;
						}
					}

					$historiquedroit = array(
						'Historiquedroit' => array(
							'personne_id' => $personne_id,
							'toppersdrodevorsa' => (string)$data['Situationallocataire']['toppersdrodevorsa'],
							'etatdosrsa' => (string)$data['Situationallocataire']['etatdosrsa']
						)
					);

					$this->Personne->Historiquedroit->create( $historiquedroit );
					$success = $success && $this->Personne->Historiquedroit->save();
				}
			}

			return $success;
		}

		/**
		 * Filtrage des options pour le formulaire: pour les groupes vulnérables,
		 * on ne garde que "Personnes handicapées (reconnues par la MDPH)" et
		 * "Autres personnes défavorisées"
		 *
		 * @param array $options
		 * @return array
		 */
		public function filterOptions( array $options ) {
			$options = Hash::remove( $options, "{$this->alias}.vulnerable.migrant" );
			$options = Hash::remove( $options, "{$this->alias}.vulnerable.minorite" );

			return $options;
		}

		/**
		 * Retourne le niveau d'étude d'un allocataire donné.
		 *
		 * @param integer $personne_id
		 * @return string
		 */
		public function nivetu( $personne_id ) {
			$querydata = array(
				'fields' => array( 'nivetu' ),
				'contain' => false,
				'conditions' => array( 'personne_id' => $personne_id ),
				'order' => array( 'id DESC' ),
			);

			$nivetu = $this->Personne->Dsp->DspRev->find( 'first', $querydata );
			$nivetu = Hash::get( $nivetu, 'DspRev.nivetu' );

			if( empty( $nivetu ) ) {
				$nivetu = $this->Personne->Dsp->find( 'first', $querydata );
				$nivetu = Hash::get( $nivetu, 'Dsp.nivetu' );
			}

			return $nivetu;
		}

		/**
		 * Retourne l'id du RDV à utiliser dans le questionnaire.
		 * Il s'agit d'un "premier RDV" à l'état "Prévu"
		 *
		 * @param integer $personne_id
		 * @return integer
		 */
		public function rendezvous( $personne_id ) {
			$querydata = array(
				'conditions' => array(
					'Thematiquerdv.linkedmodel' => $this->alias
				)
			);
			$thematiquesrdvs = $this->Rendezvous->Thematiquerdv->find( 'all', $querydata );

			$with = $this->Rendezvous->hasAndBelongsToMany['Thematiquerdv']['with'];

			// RDV qui n'est pas déjà utilisé pour le remplissage d'un questionnaire D1
			$sq = $this->sq(
				array(
					'alias' => 'questionnairesd1pdvs93',
					'fields' => array( 'questionnairesd1pdvs93.rendezvous_id' ),
					'contain' => false,
					'conditions' => array(
						'questionnairesd1pdvs93.rendezvous_id = Rendezvous.id'
					)
				)
			);

			$querydata = array(
				'fields' => array(
					'Rendezvous.id'
				),
				'contain' => false,
				'conditions' => array(
					'Rendezvous.personne_id' => $personne_id,
					'Rendezvous.typerdv_id' => Hash::extract( $thematiquesrdvs, '{n}.Thematiquerdv.typerdv_id' ),
					'Thematiquerdv.linkedmodel' => $this->alias,
					"Rendezvous.id NOT IN ( {$sq} )",
					'Rendezvous.statutrdv_id' => (array)Configure::read( 'Questionnaired1pdv93.rendezvous.statutrdv_id' ),
				),
				'joins' => array(
					$this->Rendezvous->join( $with, array( 'type' => 'INNER' ) ),
					$this->Rendezvous->{$with}->join( 'Thematiquerdv', array( 'type' => 'INNER' ) ),
				),
				'order' => array( 'Rendezvous.daterdv ASC' )
			);
			$rendezvous = $this->Rendezvous->find( 'first', $querydata );

			return Hash::get( $rendezvous, 'Rendezvous.id' );
		}

		/**
		 * Si la date de rendez-vous est sur une année différente, on prend la
		 * date de RDV comme date de validation, sinon la date du jour.
		 *
		 * @param integer $rendezvous_id
		 * @return string
		 */
		protected function _dateValidation( $rendezvous_id ) {
			$date_validation = date( 'Y-m-d' );

			if( !empty( $rendezvous_id ) ) {
				$querydata = array(
					'fields' => array(
						'Rendezvous.daterdv'
					),
					'conditions' => array(
						'Rendezvous.id' => $rendezvous_id
					),
					'contain' => false,
					'order' => array(
						'Rendezvous.daterdv ASC'
					),
				);

				$rendezvous = $this->Rendezvous->find( 'first', $querydata );
				if( !empty( $rendezvous ) ) {
					if( date( 'Y', strtotime( $rendezvous['Rendezvous']['daterdv'] ) ) != date( 'Y' ) ) {
						$date_validation = $rendezvous['Rendezvous']['daterdv'];
					}
				}
			}

			return $date_validation;
		}

		/**
		 * Messages à envoyer à l'utilisateur.
		 *
		 * @param integer $personne_id
		 * @return array
		 */
		public function messages( $personne_id ) {
			$messages = array();

			// Qui possède un RDV ...
			$rendezvous = $this->rendezvous( $personne_id );
			if( empty( $rendezvous ) ) {
				$year = date( 'Y' );
				$querydata = array(
					'contain' => false,
					'conditions' => array(
						"{$this->alias}.personne_id" => $personne_id,
						"{$this->alias}.date_validation BETWEEN '{$year}-01-01' AND '{$year}-12-31'"
					)
				);
				$count = $this->find( 'count', $querydata );

				if( $count == 0 ) {
					$messages['Rendezvous.premierrdv'] = 'error';
				}
				else {
					$messages['Rendezvous.premierrdv_utilisable'] = 'error';
				}
			}

			$nivetu = $this->nivetu( $personne_id );
			if( empty( $nivetu ) ) {
				$messages['Dsp.nivetu_obligatoire'] = 'error';
			}

			$droitsouverts = $this->droitsouverts( $personne_id );
			if( empty( $droitsouverts ) ) {
				$messages['Situationdossierrsa.etatdosrsa_ouverts'] = 'notice';
			}

			$toppersdrodevorsa = $this->toppersdrodevorsa( $personne_id );
			if( empty( $toppersdrodevorsa ) ) {
				$messages['Calculdroitrsa.toppersdrodevorsa_notice'] = 'notice';
			}

			$date_validation = $this->_dateValidation( $rendezvous );

			$this->create( array( 'personne_id' => $personne_id, 'rendezvous_id' => $rendezvous ) );
			$exists = !$this->checkDateOnceAYear( array( 'date_validation' => $date_validation ), 'personne_id', 'rendezvous_id' );
			if( $exists ) {
				$messages['Questionnaired1pdv93.exists'] = 'notice';
			}

			return $messages;
		}

		/**
		 * Permet de savoir si un ajout est possible à partir des messages
		 * renvoyés par la méthode messages.
		 *
		 * @param array $messages
		 * @return boolean
		 */
		public function addEnabled( array $messages ) {
			return !in_array( 'error', $messages ) && !array_key_exists( 'Questionnaired1pdv93.exists', $messages );
		}
	}
?>