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
			'Emploiromev3' => array(
				'className' => 'Entreeromev3',
				'foreignKey' => 'emploiromev3_id',
				'conditions' => null,
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
		);


		/** 
		 * Retourne l'état de la D2 de l'utilisateur
		 * @param integer $personne_id
		 * @return array
		 * 		$status[button]  ( 0 : no button, 1 : activate button)
		 * 		$status[messageExist] (0 : pas de message, 1 : aucun D1, 2 : possede un D2),
		 * 		$status[messageMissing] (0 : pas de message, 1 : aucun D1, 2 : possede un D2),
		 */
		public function statusQuestionnaireD2( $personne_id ) {
			$status['button']=false;
			$status['messageExist']=false;
			$status['messageNotExist']=false;
			$status['messageMissing']=false;

			// Rechercher le D1 pour obtenir une date de validation logique

			// identifant d'une D1 auquel il manque une D2
			$questionnaired1pdv93_id = $this->questionnairesd1pdv93Id( $personne_id );
			if( !empty( $questionnaired1pdv93_id ) ) {
				$date_validation = $this->_getDateValidation( $questionnaired1pdv93_id );
				$status['button']=true;
				$year_validation = date( 'Y', strtotime( $date_validation ) );
				if( $year_validation != date( 'Y' ) ) {
					$status['messageNotExist']=true;
				}
			}else{
				$status['messageMissing']=true;
			}

			// identifiant d'une D2 pour l'année en cours 
			$questionnaired1avecd2pdv93_id = $this->questionnairesd1avecd2pdv93Id( $personne_id );
			if( !empty( $questionnaired1avecd2pdv93_id ) ) {
				$date_validationD2 = $this->_getDateValidation( $questionnaired1avecd2pdv93_id );
				$year_validationD2 = date( 'Y', strtotime( $date_validationD2 ) );
				if( $year_validationD2 == date( 'Y' ) ) {
					$status['messageMissing']=false;
					$status['messageExist']=true;
				}
			}

			return $status ;
		}
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
		 * Retourne l'id du questionnaire D1 pour lequel l'allocataire posséde
		 *  un questionnaire D2 (pour l'année en cours).
		 *
		 * @param integer $personne_id
		 * @return boolean
		 */
		public function questionnairesd1avecd2pdv93Id( $personne_id ) {
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
					"Questionnaired1pdv93.id IN ( {$sq} )",
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
		 * @param array $status
		 * @return array
		 */
		public function messages( $personne_id ) {
			$messages = array();

			$status = $this->statusQuestionnaireD2( $personne_id );

			// Rechercher le D1 pour obtenir une date de validation logique
			if( $status['messageMissing'] ) {
					$messages['Questionnaired1pdv93.missing'] = 'error';
			}
			if( $status['messageExist']  ) {
				$messages['Questionnaired2pdv93.exists'] = 'notice';
			}
			if( $status['messageNotExist']  ) {
				$messages['Questionnaired2pdv93.notexists'] = 'notice';
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
		 * Récupère la date de validation du questionnaire PDV
		 *
		 * @param integer $rendezvous_id
		 * @return string
		 */
		protected function _getDateValidation( $questionnaired1pdv93_id ) {
			$querydata = array(
				'fields' => array( 'Questionnaired1pdv93.date_validation' ),
				'contain' => false,
				'conditions' => array(
					'Questionnaired1pdv93.id' => $questionnaired1pdv93_id
				)
			);
			$questionnaired1pdv93 = $this->Personne->Questionnaired1pdv93->find( 'first', $querydata );

			$date_validation = Hash::get( $questionnaired1pdv93, 'Questionnaired1pdv93.date_validation' );

			return $date_validation;
		}

		/**
		 * Lorsque l'allocataire possède un D1 sur l'année N-1, on force la
		 * date_validation au 31/12/N-1, sinon on prend la date du jour.
		 *
		 * @param integer $rendezvous_id
		 * @return string
		 */
		protected function _createDateValidation( $questionnaired1pdv93_id ) {
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
					'contain' => array(
						'Emploiromev3'
					),
				);

				$formData = $this->find( 'first', $querydata );

				// Rome V3
				$data = array ();
				$data['familleromev3_id'] = $formData['Emploiromev3']['familleromev3_id'];
				$data['domaineromev3_id'] = $formData['Emploiromev3']['familleromev3_id'].'_'.$formData['Emploiromev3']['domaineromev3_id'];
				$data['metierromev3_id'] = $formData['Emploiromev3']['domaineromev3_id'].'_'.$formData['Emploiromev3']['metierromev3_id'];
				$data['appellationromev3_id'] = $formData['Emploiromev3']['metierromev3_id'].'_'.$formData['Emploiromev3']['appellationromev3_id'];
				$formData['Emploiromev3'] = $data;
			}
			else {
				$formData[$this->alias]['personne_id'] = $personne_id;
				$formData[$this->alias]['questionnaired1pdv93_id'] = $this->questionnairesd1pdv93Id( $personne_id );
				$formData[$this->alias]['date_validation'] = $this->_createDateValidation( $formData[$this->alias]['questionnaired1pdv93_id'] );

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
						'date_validation' => $this->_createDateValidation( $questionnaired1pdv93_id ),
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

		/**
		 * Sauvegarde du questionnaire B7 d'un allocataire.
		 *
		 * @param integer $personne_id L'id de la personne traitée.
		 * @param array $data Les données renvoyées par le formulaire B7
		 *	(Questionnaireb7pdv93)
		 * @return boolean
		 */
		public function getEmploiromev3Id( array $data ) {
			if (!empty ($data['Emploiromev3']['appellationromev3_id'])) {
				$domaineromev3_id = explode("_", $data['Emploiromev3']['domaineromev3_id']);
				$metierromev3_id = explode("_", $data['Emploiromev3']['metierromev3_id']);
				$appellationromev3_id = explode("_", $data['Emploiromev3']['appellationromev3_id']);

				$this->loadModel('Entreeromev3');
				$entreeromev3 = $this-> Entreeromev3->find (
					'first',
					array (
						'conditions' => array (
							'familleromev3_id' => $data['Emploiromev3']['familleromev3_id'],
							'domaineromev3_id' => $domaineromev3_id[1],
							'metierromev3_id' => $metierromev3_id[1],
							'appellationromev3_id' => $appellationromev3_id[1],
						),
					)
				);

				if (isset ($entreeromev3['Entreeromev3'])) {
					return $entreeromev3['Entreeromev3']['id'];
				}
			}

			return null;
		}
	}
?>