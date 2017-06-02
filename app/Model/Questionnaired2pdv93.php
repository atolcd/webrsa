<?php
	/**
	 * Code source de la classe Questionnaired2pdv93.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Questionnaired2pdv93 ...
	 *
	 * @package app.Model
	 */
	class Questionnaired2pdv93 extends AppModel
	{
		/**
		 * Nom.
		 *
		 * @var string
		 */
		public $name = 'Questionnaired2pdv93';

		/**
		 * Behaviors utilisés.
		 *
		 * @var array
		 */
		public $actsAs = array(
			'Allocatairelie',
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Validation2.Validation2RulesComparison',
			'Postgres.PostgresAutovalidate',
			'Questionnairepdv93',
		);

		/**
		 * Les règles de validation qui seront ajoutées aux règles de validation
		 * déduites de la base de données.
		 *
		 * @var array
		 */
		public $validate = array(
			'sortieaccompagnementd2pdv93_id' => array(
				'notEmptyIf' => array(
					'rule' => array( 'notEmptyIf', 'situationaccompagnement', true, array( 'sortie_obligation' ) ),
					'message' => 'Champ obligatoire',
				),
			),
			'chgmentsituationadmin' => array(
				'notEmptyIf' => array(
					'rule' => array( 'notEmptyIf', 'situationaccompagnement', true, array( 'changement_situation' ) ),
					'message' => 'Champ obligatoire',
				),
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
			'Questionnaired1pdv93' => array(
				'className' => 'Questionnaired1pdv93',
				'foreignKey' => 'questionnaired1pdv93_id',
				'conditions' => null,
				'type' => null,
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
			'Sortieaccompagnementd2pdv93' => array(
				'className' => 'Sortieaccompagnementd2pdv93',
				'foreignKey' => 'sortieaccompagnementd2pdv93_id',
				'conditions' => null,
				'type' => null,
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
		);

		/**
		 * Retourne l'id du questionnaire D1 pour lequel l'allocataire doit encore
		 * remplir un questionnaire D2 (pour l'année en cours).
		 *
		 * @param integer $personne_id
		 * @return boolean
		 */
		public function questionnairesd1pdv93Id( $personne_id ) {
			$sq = $this->sq(
				array(
					'alias' => 'questionnairesd2pdvs93',
					'fields' => 'questionnairesd2pdvs93.questionnaired1pdv93_id',
					'contain' => false,
					'conditions' => array(
						'questionnairesd2pdvs93.personne_id' => $personne_id,
						'EXTRACT( \'YEAR\' FROM questionnairesd2pdvs93.date_validation ) = EXTRACT( \'YEAR\' FROM Rendezvous.daterdv )',
						'questionnairesd2pdvs93.questionnaired1pdv93_id = Questionnaired1pdv93.id'
					)
				)
			);

			$querydata = array(
				'fields' => array( 'Questionnaired1pdv93.id' ),
				'contain' => false,
				'joins' => array(
					$this->Personne->Questionnaired1pdv93->join( 'Rendezvous', array( 'type' => 'INNER' ) )
				),
				'conditions' => array(
					'Questionnaired1pdv93.personne_id' => $personne_id,
					'EXTRACT( \'YEAR\' FROM Questionnaired1pdv93.date_validation ) = EXTRACT( \'YEAR\' FROM Rendezvous.daterdv )',
					"Questionnaired1pdv93.id NOT IN ( {$sq} )",
				),
				'order' => array(
					'Questionnaired1pdv93.date_validation DESC'
				)
			);
			$questionnaired1pdv93 = $this->Personne->Questionnaired1pdv93->find( 'first', $querydata );

			return Hash::get( $questionnaired1pdv93, 'Questionnaired1pdv93.id' );
		}

		/**
		 * Retourne une sous-requête permettant de trouver les clés primaires des
		 * allocataires ayant un questionnaire D1 pour l'année en cours qui n'a
		 * pas encore de questionnaire D2 associé.
		 *
		 * @param string $personneIdAlias
		 * @param integer $year
		 * @return string
		 */
		public function sqQuestionnaired2Necessaire( $personneIdAlias = 'Personne.id', $year = null ) {
			$sqQ2Q1Id = $this->sq(
				array(
					'alias' => 'questionnairesd2pdvs93',
					'fields' => 'questionnairesd2pdvs93.questionnaired1pdv93_id',
					'contain' => false,
					'conditions' => array(
						"questionnairesd2pdvs93.personne_id = questionnairesd1pdvs93.personne_id",
						'EXTRACT( \'YEAR\' FROM questionnairesd2pdvs93.date_validation ) = EXTRACT( \'YEAR\' FROM rendezvous.daterdv )',
					)
				)
			);

			$querydata = array(
				'alias' => 'questionnairesd1pdvs93',
				'fields' => 'questionnairesd1pdvs93.personne_id',
				'contain' => false,
				'joins' => array(
					$this->Questionnaired1pdv93->join( 'Rendezvous', array( 'type' => 'INNER' ) )
				),
				'conditions' => array(
					"questionnairesd1pdvs93.personne_id = {$personneIdAlias}",
					"questionnairesd1pdvs93.id NOT IN ( {$sqQ2Q1Id} )"
				)
			);

			if( !is_null( $year ) ) {
				$querydata['conditions']['EXTRACT( \'YEAR\' FROM rendezvous.daterdv )'] = $year;
			}

			$querydata = array_words_replace(
				$querydata,
				array(
					'Rendezvous' => 'rendezvous',
					'Questionnaired1pdv93' => 'questionnairesd1pdvs93',
					'Questionnaired2pdv93' => 'questionnairesd2pdvs93',
				)
			);

			return $this->Questionnaired1pdv93->sq( $querydata );
		}

		/**
		 * Messages à envoyer à l'utilisateur.
		 *
		 * @param integer $personne_id
		 * @return array
		 */
		public function messages( $personne_id ) {
			$messages = array();

			// Rechercher le D1 pour obtenir une date de validation logique
			$date_validation = date( 'Y-m-d' );
			$questionnaired1pdv93_id = $this->questionnairesd1pdv93Id( $personne_id );
			if( !empty( $questionnaired1pdv93_id ) ) {
				$date_validation = $this->_dateValidation( $questionnaired1pdv93_id );
			}
			else {
				 $messages['Questionnaired1pdv93.missing'] = 'error';
			}

			$this->create( array( 'personne_id' => $personne_id ) );
			$exists = !$this->checkDateOnceAYear( array( 'date_validation' => $date_validation ), 'personne_id' );
			if( $exists ) {
				$messages['Questionnaired2pdv93.exists'] = 'error';
			}

			$droitsouverts = $this->droitsouverts( $personne_id );
			if( empty( $droitsouverts ) ) {
				$messages['Situationdossierrsa.etatdosrsa_ouverts'] = 'notice';
			}

			$toppersdrodevorsa = $this->toppersdrodevorsa( $personne_id );
			if( empty( $toppersdrodevorsa ) ) {
				$messages['Calculdroitrsa.toppersdrodevorsa_notice'] = 'notice';
			}

			return $messages;
		}

		/**
		 * @param array $check
		 * @return boolean
		 */
		public function checkDateOnceAYear( $check, $group_column ) {
			if( !is_array( $check ) ) {
				return false;
			}

			$result = true;
			foreach( Hash::normalize( $check ) as $key => $value ) {
				list( $year, ) = explode( '-', $value );

				if( !empty( $year ) ) {
					// Pas encore de questionnaire D1 pour l'année en question
					$querydata = array( 'contain' => false );

					$personne_id = Hash::get( $this->data, "{$this->alias}.{$group_column}" );
					$querydata['conditions'] = array(
						"{$this->alias}.{$group_column}" => $personne_id,
						"{$this->alias}.{$key} BETWEEN '{$year}-01-01' AND '{$year}-12-31'"
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
						$sq = $this->sq(
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

						$count = $this->Personne->Questionnaired1pdv93->find( 'count', $querydata );

						$result = ( $count > 0 ) && $result;
					}
				}
			}

			return $result;
		}

		/**
		 * Permet de savoir si un ajout est possible à partir des messages
		 * renvoyés par la méthode messages.
		 *
		 * @param array $messages
		 * @return boolean
		 */
		public function addEnabled( array $messages ) {
			return !in_array( 'error', $messages ) && !array_key_exists( 'Questionnaired2pdv93.exists', $messages );
		}

		/**
		 * Lorsque l'allocataire possède un D1 sur l'année N-1, on force la
		 * date_validation au 31/12/N-1, sinon on prend la date du jour.
		 *
		 * @param integer $rendezvous_id
		 * @return string
		 */
		protected function _dateValidation( $questionnaired1pdv93_id ) {
			$querydata = array(
				'fields' => array( 'Questionnaired1pdv93.date_validation' ),
				'contain' => false,
				'conditions' => array(
					'Questionnaired1pdv93.id' => $questionnaired1pdv93_id
				)
			);
			$questionnaired1pdv93 = $this->Personne->Questionnaired1pdv93->find( 'first', $querydata );

			$date_validation = Hash::get( $questionnaired1pdv93, 'Questionnaired1pdv93.date_validation' );
			$year_validation = date( 'Y', strtotime( $date_validation ) );
			if( $year_validation < date( 'Y' ) ) {
				$date_validation = "{$year_validation}-12-31";
			}
			else {
				$date_validation = date( 'Y-m-d' );
			}

			return $date_validation;
		}

		/**
		 *
		 *
		 * @param integer $personne_id
		 * @param integer $id
		 * @return array
		 * @throws NotFoundException
		 */
		public function prepareFormData( $personne_id, $id = null ) {
			$formData = array();

			if( !empty( $id ) ) {
				$querydata = array(
					'conditions' => array(
						"{$this->alias}.id" => $id
					),
					'contain' => false
				);

				$formData = $this->find( 'first', $querydata );
			}
			else {
				$formData[$this->alias]['personne_id'] = $personne_id;
				$formData[$this->alias]['questionnaired1pdv93_id'] = $this->questionnairesd1pdv93Id( $personne_id );
				$formData[$this->alias]['date_validation'] = $this->_dateValidation( $formData[$this->alias]['questionnaired1pdv93_id'] );

				// Lorsque l'allocataire ne possède pas encore de D2 et est soumis à droits et devoirs, on préremplit en maintien
				$querydata = array(
					'fields' => array( 'Calculdroitrsa.toppersdrodevorsa' ),
					'contain' => false,
					'conditions' => array(
						'Calculdroitrsa.personne_id' => $personne_id
					)
				);
				$calculdroitrsa = $this->Personne->Calculdroitrsa->find( 'first', $querydata );
				if( Hash::get( $calculdroitrsa, 'Calculdroitrsa.toppersdrodevorsa' ) ) {
					$formData[$this->alias]['situationaccompagnement'] = 'maintien';
				}
			}

			return $formData;
		}

		/**
		 * Enregistrement d'un questionnaire D2 pour une situation d'accompagnement
		 * et un allocataire donné.
		 *
		 * @param integer $personne_id
		 * @param string $situationaccompagnement
		 * @param string $chgmentsituationadmin
		 * @return boolean
		 */
		public function saveAuto( $personne_id, $situationaccompagnement, $chgmentsituationadmin = null ) {
			$success = true;

			$questionnaired1pdv93_id = $this->questionnairesd1pdv93Id( $personne_id );

			if( !empty( $questionnaired1pdv93_id ) ) {
				$questionnaired2pdv93 = array(
					'Questionnaired2pdv93' => array(
						'personne_id' => $personne_id,
						'questionnaired1pdv93_id' => $questionnaired1pdv93_id,
						'situationaccompagnement' => $situationaccompagnement,
						'sortieaccompagnementd2pdv93_id' => null,
						'chgmentsituationadmin' => $chgmentsituationadmin,
						'date_validation' => $this->_dateValidation( $questionnaired1pdv93_id ),
					)
				);

				$this->create( $questionnaired2pdv93 );
				$success = $this->save( null, array( 'atomic' => false ) ) && $success;
			}

			return $success;
		}

		/**
		 * Retourne les options nécessaires au formulaire de recherche de la
		 * cohorte et au formulaire d'ajout / modification,
		 *
		 * @param array $params <=> array( 'find' => false )
		 * @return array
		 */
		public function options( array $params = array() ) {
			$options = array();
			$params = $params + array( 'find' => false );

			$options = $this->enums();
			if( Hash::get( $params, 'find' ) ) {
				$options['Questionnaired2pdv93']['sortieaccompagnementd2pdv93_id'] = $this->Sortieaccompagnementd2pdv93->find(
					'list',
					array(
						'fields' => array(
							'Sortieaccompagnementd2pdv93.id',
							'Sortieaccompagnementd2pdv93.name',
							'Parent.name',
						),
						'joins' => array(
							$this->Sortieaccompagnementd2pdv93->join( 'Parent', array( 'type' => 'INNER' ) )
						)
					)
				);
			}

			return $options;
		}
	}
?>