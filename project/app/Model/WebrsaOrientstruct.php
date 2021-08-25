<?php
	/**
	 * Code source de la classe WebrsaOrientstruct.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'WebrsaAbstractLogic', 'Model' );
	App::uses( 'WebrsaLogicAccessInterface', 'Model/Interface' );
	App::uses( 'DepartementUtility', 'Utility' );

	/**
	 * La classe WebrsaOrientstruct possède la logique métier web-rsa pour les
	 * orientations stockées dans Orientstruct.
	 *
	 * @package app.Model
	 */
	class WebrsaOrientstruct extends WebrsaAbstractLogic implements WebrsaLogicAccessInterface
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaOrientstruct';

		/**
		 * Les modèles qui seront utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array( 'Orientstruct', 'Informationpe', 'Dsp' );

		/**
		 * Permet d'obtenir les données du formulaire d'ajout / de modification,
		 * en fonction du bénéficiaire, parfois de l'orientation.
		 *
		 * @param integer $personne_id
		 * @param integer $id
		 * @param integer $user_id
		 * @return array
		 * @throws NotFoundException
		 */
		public function getAddEditFormData( $personne_id, $id = null, $user_id = null ) {
			$departement = Configure::read( 'Cg.departement' );
			$data = array();

			// Modification
			if( $id !== null ) {
				$data = $this->Orientstruct->find(
					'first',
					array(
						'fields' => array_merge(
							$this->Orientstruct->fields(),
							$this->Orientstruct->Personne->Calculdroitrsa->fields()
						),
						'joins' => array(
							$this->Orientstruct->join( 'Personne' ),
							$this->Orientstruct->Personne->join( 'Calculdroitrsa' ),
						),
						'conditions' => array(
							"{$this->Orientstruct->alias}.{$this->Orientstruct->primaryKey}" => $id
						),
						'contain' => false
					)
				);

				if( empty( $data  ) ) {
					throw new NotFoundException();
				}

				// Listes dépendantes
				if( !empty( $data[$this->Orientstruct->alias]['structurereferente_id'] ) ) {
					if( !empty( $data[$this->Orientstruct->alias]['referent_id'] ) ) {
						$data[$this->Orientstruct->alias]['referent_id'] = "{$data[$this->Orientstruct->alias]['structurereferente_id']}_{$data[$this->Orientstruct->alias]['referent_id']}";
					}
					if( !empty( $data[$this->Orientstruct->alias]['typeorient_id'] ) ) {
						$data[$this->Orientstruct->alias]['structurereferente_id'] = "{$data[$this->Orientstruct->alias]['typeorient_id']}_{$data[$this->Orientstruct->alias]['structurereferente_id']}";
					}
				}

				if( $departement == 66 ) {
					if( !empty( $data[$this->Orientstruct->alias]['structureorientante_id'] ) && !empty( $data[$this->Orientstruct->alias]['referentorientant_id'] ) ) {
						$data[$this->Orientstruct->alias]['referentorientant_id'] = "{$data[$this->Orientstruct->alias]['structureorientante_id']}_{$data[$this->Orientstruct->alias]['referentorientant_id']}";
					}
				}
			}
			// Ajout
			else {
				$data = array(
					$this->Orientstruct->alias => array(
						'personne_id' => $personne_id,
						'user_id' => $user_id,
						'origine' => 'manuelle'
					)
				);

				// On propose la date de demande RSA comme date de demande par défaut
				$dossier = $this->Orientstruct->Personne->find(
					'first',
					array(
						'fields' => array( 'Dossier.dtdemrsa' ),
						'joins' => array(
							$this->Orientstruct->Personne->join( 'Foyer' ),
							$this->Orientstruct->Personne->Foyer->join( 'Dossier' ),
						),
						'conditions' => array(
							'Personne.id' => $personne_id
						),
						'contain' => false
					)
				);
				$data['Orientstruct']['date_propo'] = $dossier['Dossier']['dtdemrsa'];
				$data['Orientstruct']['date_valid'] = date( 'Y-m-d' );
			}

			// Soumission à droits et devoirs
			$query = array(
				'fields' => array(
					'Calculdroitrsa.id',
					'Calculdroitrsa.toppersdrodevorsa'
				),
				'conditions' => array(
					'Calculdroitrsa.personne_id' => $personne_id
				),
				'contain' => false
			);
			$calculdroitrsa = $this->Orientstruct->Personne->Calculdroitrsa->find( 'first', $query );

			$data['Calculdroitrsa'] = array(
				'id' => Hash::get( $calculdroitrsa, 'Calculdroitrsa.id' ),
				'toppersdrodevorsa' => Hash::get( $calculdroitrsa, 'Calculdroitrsa.toppersdrodevorsa' ),
				'personne_id' => $personne_id
			);

			return $data;
		}

		/**
		 * Sauvegarde du formulaire d'ajout / de modification de l'orientation
		 * d'un bénéficiaire.
		 *
		 * @param array $data
		 * @return boolean
		 */
		public function saveAddEditFormData( array $data, $user_id = null ) {
			$success = true;
			$departement = Configure::read( 'Cg.departement' );

			if( !empty( $user_id ) ) {
				$data[$this->Orientstruct->alias]['user_id'] = $user_id;
			}

			$primaryKey = Hash::get( $data, "{$this->Orientstruct->alias}.id" );
			$personne_id = Hash::get( $data, "{$this->Orientstruct->alias}.personne_id" );
			$typeorient_id = Hash::get( $data, "{$this->Orientstruct->alias}.typeorient_id" );
			$referent_id = suffix( Hash::get( $data, "{$this->Orientstruct->alias}.referent_id" ) );

			$origine = Hash::get( $data, "{$this->Orientstruct->alias}.origine" );
			if( empty( $origine ) ) {
				$data[$this->Orientstruct->alias]['origine'] = 'manuelle';
			}

			if( $departement == 58 && empty( $primaryKey ) && $this->isRegression( $personne_id, $typeorient_id ) ) {
				$theme = 'Regressionorientationep58';

				$dossierep = array(
					'Dossierep' => array(
						'personne_id' => $personne_id,
						'themeep' => Inflector::tableize( $theme )
					)
				);

				$success = $this->Orientstruct->Personne->Dossierep->save( $dossierep , array( 'atomic' => false ) ) && $success;

				$regressionorientationep = array(
					$theme => Hash::merge(
						(array)Hash::get( $data, $this->Orientstruct->alias ),
						array(
							'personne_id' => $personne_id,
							'dossierep_id' => $this->Orientstruct->Personne->Dossierep->id,
							'datedemande' => Hash::get( $data, "{$this->Orientstruct->alias}.date_propo" )
						)
					)
				);

				$success = $this->Orientstruct->Personne->Dossierep->{$theme}->save( $regressionorientationep , array( 'atomic' => false ) ) && $success;
			}
			else {
				// Orientstruct
				$orientstruct = array( $this->Orientstruct->alias => (array)Hash::get( $data, $this->Orientstruct->alias ) );
				$orientstruct[$this->Orientstruct->alias]['personne_id'] = $personne_id;
				$orientstruct[$this->Orientstruct->alias]['valid_cg'] = true;

				if( $departement == 976 ) {
					$statut_orient = Hash::get( $orientstruct, "{$this->Orientstruct->alias}.statut_orient" );

					if( $statut_orient != 'Orienté' ) {
						$orientstruct[$this->Orientstruct->alias]['origine'] = null;
						$orientstruct[$this->Orientstruct->alias]['date_valid'] = null;
					}
				}
				else if( empty( $primaryKey ) ) {
					$orientstruct[$this->Orientstruct->alias]['statut_orient'] = 'Orienté';
				}

				$statut_orient = Hash::get( $orientstruct, "{$this->Orientstruct->alias}.statut_orient" );
				$this->Orientstruct->create( $orientstruct );
				$success = $this->Orientstruct->save( null, array( 'atomic' => false ) ) && $success;

				// Calculdroitrsa
				$calculdroitsrsa = array( 'Calculdroitrsa' => (array)Hash::get( $data, 'Calculdroitrsa' ) );
				$this->Orientstruct->Personne->Calculdroitrsa->create( $calculdroitsrsa );
				$success = $this->Orientstruct->Personne->Calculdroitrsa->save( null, array( 'atomic' => false ) ) && $success;

				// Tentative d'ajout d'un référent de parcours
				if( $success && !empty( $referent_id ) && ( $statut_orient == 'Orienté' ) ) {
					$success = $this->Orientstruct->Referent->PersonneReferent->referentParModele(
						$data,
						$this->Orientstruct->alias,
						'date_valid'
					);
					if( empty( $success ) ) {
						$msgstr = 'Impossible d\'ajouter un nouveau référent du parcours. Il est peut-être nécessaire de clôturer l\'actuel.';
						$this->Orientstruct->validationErrors['typeorient_id'][] = $msgstr;
					}
				}
			}

			return $success;
		}

		/**
		 * Retourne un querydata permettant de connaître la liste des orientations
		 * d'un allocataire, en fonction du département.
		 *
		 * @see Configure::read( 'Cg.departement' )
		 *
		 * @param integer $personne_id
		 * @return array
		 */
		public function getIndexQuery( $personne_id ) {
			$cacheKey = implode( '_', array( $this->Orientstruct->useDbConfig, $this->Orientstruct->alias, __FUNCTION__ ) );
			$query = Cache::read( $cacheKey );

			if( $query === false ) {
				// Il n'est possible d'imprimer une orientation que suivant certaines conditions
				$sqPrintable = $this->getPrintableSq( 'printable' );

				// Il n'est possible de supprimer une orientation que si elle n'est pas liée à d'autres enregistrements
				$sqLinkedRecords = $this->Orientstruct->getSqLinkedModelsDepartement( 'linked_records' );

				// La requête
				$query = array(
					'fields' => array_merge(
						$this->Orientstruct->fields(),
						$this->Orientstruct->Personne->fields(),
						$this->Orientstruct->Typeorient->fields(),
						$this->Orientstruct->Structurereferente->fields(),
						$this->Orientstruct->Referent->fields(),
						array(
							$this->Orientstruct->Fichiermodule->sqNbFichiersLies( $this->Orientstruct, 'nombre' ),
							$sqPrintable,
							$sqLinkedRecords
						)
					),
					'conditions' => array(),
					'joins' => array(
						$this->Orientstruct->join( 'Personne' ),
						$this->Orientstruct->join( 'Typeorient' ),
						$this->Orientstruct->join( 'Structurereferente' ),
						$this->Orientstruct->join( 'Referent' ),
					),
					'contain' => false,
					'order' => array(
						'COALESCE( "Orientstruct"."rgorient", \'0\') DESC',
						'"Orientstruct"."date_valid" DESC',
						'"Orientstruct"."id" DESC'
					)
				);

				// On complète le querydata suivant le CG:
				// 1. Au CG 58, on veut savoir quelle COV a réalisé l'orientation
				if(  Configure::read( 'Cg.departement' ) == 58 ) {
					$query = $this->Orientstruct->Personne->Dossiercov58->getCompletedQueryOrientstruct( $query );
				}
				// 2. Au CG 66, on ne peut cliquer sur certains liens que sous certaines conditions
				else if( Configure::read( 'Cg.departement' ) == 66 ) {
					$Dbo = $this->Orientstruct->getDataSource();
					$sql = $Dbo->conditions( array( 'Typeorient.parentid' => (array)Configure::read( 'Orientstruct.typeorientprincipale.SOCIAL' ) ), true, false );
					$query['fields'][] = "( {$sql} ) AS \"{$this->Orientstruct->alias}__notifbenefcliquable\"";
				}

				// Sauvegarde dans le cache
				Cache::write( $cacheKey, $query );
			}

			$query['conditions']['Orientstruct.personne_id'] = $personne_id;

			return $query;
		}

		/**
		 * Exécute les différentes méthods du modèle permettant la mise en cache.
		 * Utilisé au préchargement de l'application (/prechargements/index).
		 *
		 * @return boolean true en cas de succès, false en cas d'erreur,
		 * 	null pour les méthodes qui ne font rien.
		 */
		public function prechargement() {
			$query = $this->getIndexQuery( null );
			return !empty( $query );
		}

		/**
		 * Construit les conditions pour ajout possible à partir de la configuration,
		 * du webrsa.inc, en prenant en compte le traitement spécial à appliquer
		 * pour la valeur NULL.
		 * ATTENTION: in_array confond null et 0
		 * @see http://fr.php.net/manual/en/function.in-array.php#99676
		 *
		 * @param type $key
		 * @param type $values
		 * @return array
		 */
		protected function _conditionsAjoutOrientationPossible( $key, $values ) {
			$hasNull = false;

			if( !is_array( $values ) ) {
				$values = array( $values );
			}

			foreach( $values as $value ) {
				if( $value === null ) {
					$hasNull = true;
				}
			}

			$conditions = array( $key => array_diff( $values, array( null ) ) );

			if( $hasNull ) {
				$conditions = array(
					'OR' => array(
						$conditions,
						"{$key} IS NULL"
					)
				);
			}
			return $conditions;
		}

		/**
		 * FIXME -> aucun dossier en cours, pour certains thèmes:
		 * 		- CG 93
		 * 			* Nonrespectsanctionep93 -> ne débouche pas sur une orientation: '1reduction', '1maintien', '1sursis', '2suspensiontotale', '2suspensionpartielle', '2maintien'
		 * 			* Reorientationep93 -> peut déboucher sur une réorientation
		 * 			* Nonorientationproep93 -> peut déboucher sur une orientation
		 * 		- CG 66
		 * 			* Defautinsertionep66 -> peut déboucher sur une orientation: 'suspensionnonrespect', 'suspensiondefaut', 'maintien', 'reorientationprofverssoc', 'reorientationsocversprof'
		 * 			* Saisinebilanparcoursep66 -> peut déboucher sur une réorientation
		 * 			* Saisinepdoep66 -> 'CAN', 'RSP' -> ne débouche pas sur une orientation
		 * 		- CG 58
		 * 			* Nonorientationproep58 -> peut déboucher sur une orientation
		 * FIXME -> CG 93: s'il existe une procédure de relance, on veut faire signer un contrat,
		  mais on veut peut-être aussi demander une réorientation.
		 * FIXME -> doit-on vérifier si:
		 * 			- la personne est soumise à droits et devoirs (oui)
		 * 			- la personne est demandeur ou conjoint RSA (oui) ?
		 * 			- le dossier est dans un état ouvert (non) ?
		 */
		public function ajoutPossible( $personne_id ) {
			$nbDossiersep = $this->Orientstruct->Personne->Dossierep->find(
				'count',
				$this->Orientstruct->Personne->Dossierep->qdDossiersepsOuverts( $personne_id )
			);

			// Quelles sont les valeurs de Calculdroitrsa.toppersdrodevorsa pour lesquelles on peut ajouter une orientation ?
			// Si la valeur null est dans l'array, il faut un traitement un peu spécial
			$conditionsToppersdrodevorsa = array( 'Calculdroitrsa.toppersdrodevorsa' => '1' );
			if( Configure::read( 'AjoutOrientationPossible.toppersdrodevorsa' ) != NULL ) {
				$conditionsToppersdrodevorsa = $this->_conditionsAjoutOrientationPossible(
					'Calculdroitrsa.toppersdrodevorsa',
					Configure::read( 'AjoutOrientationPossible.toppersdrodevorsa' )
				);
			}

			//Ancienne valeur par défaut
			//$conditionsSituationetatdosrsa = array( 'Situationdossierrsa.etatdosrsa' => array( 'Z', '2', '3', '4' ) );
			$conditionsSituationetatdosrsa = null;
			if( !is_null (Configure::read( 'AjoutOrientationPossible.situationetatdosrsa' )) ) {
				$conditionsSituationetatdosrsa = $this->_conditionsAjoutOrientationPossible(
					'Situationdossierrsa.etatdosrsa',
					Configure::read( 'AjoutOrientationPossible.situationetatdosrsa' )
				);
			}

			$query = array(
				'conditions' => array(
					'Personne.id' => $personne_id,
				),
				'joins' => array(
					array(
						'table'      => 'prestations',
						'alias'      => 'Prestation',
						'type'       => 'INNER',
						'foreignKey' => false,
						'conditions' => array(
							'Personne.id = Prestation.personne_id',
							'Prestation.natprest = \'RSA\'',
							'Prestation.rolepers' => array( 'DEM', 'CJT' )
						)
					),
					array(
						'table'      => 'calculsdroitsrsa',
						'alias'      => 'Calculdroitrsa',
						'type'       => 'LEFT OUTER',
						'foreignKey' => false,
						'conditions' => Set::merge(
							array( 'Personne.id = Calculdroitrsa.personne_id' ),
							$conditionsToppersdrodevorsa
						)
					),
					array(
						'table'      => 'foyers',
						'alias'      => 'Foyer',
						'type'       => 'INNER',
						'foreignKey' => false,
						'conditions' => array( 'Foyer.id = Personne.foyer_id' )
					),
					array(
						'table'      => 'dossiers',
						'alias'      => 'Dossier',
						'type'       => 'INNER',
						'foreignKey' => false,
						'conditions' => array( 'Foyer.dossier_id = Dossier.id' )
					),
				),
				'recursive' => -1
			);
			if ( !is_null($conditionsSituationetatdosrsa) ) {
				$query['joins'][] = array(
					'table'      => 'situationsdossiersrsa',
					'alias'      => 'Situationdossierrsa',
					'type'       => 'INNER',
					'foreignKey' => false,
					'conditions' => Set::merge(
						array( 'Situationdossierrsa.dossier_id = Dossier.id' ),
						$conditionsSituationetatdosrsa
					)
				);
			}
			$nbPersonnes = $this->Orientstruct->Personne->find ('count', $query);

			$success = (( $nbDossiersep == 0 ) && ( $nbPersonnes == 1 ));

			if ( $success === false && (integer)Configure::read('Cg.departement') === 66 ) {
				$joinBilan = $this->Orientstruct->Personne->join('Bilanparcours66', array( 'type' => 'INNER' ));
				$sqDernierBilan = $this->Orientstruct->Personne->Bilanparcours66->sq(
					array(
						'alias' => 'bilansparcours',
						'fields' => 'bilansparcours.id',
						'conditions' => array(
							'bilansparcours.personne_id = Personne.id'
						),
						'order' => array(
							'bilansparcours.id' => 'DESC'
						),
						'limit' => 1
					)
				);
				$joinBilan['conditions'] = array( "Bilanparcours66.id IN ({$sqDernierBilan})" );

				$query = array(
					'fields' => array(
						'Bilanparcours66.id'
					),
					'joins' => array(
						$this->Orientstruct->Personne->join('Orientstruct'),
						$joinBilan,
						$this->Orientstruct->Personne->Bilanparcours66->join('Manifestationbilanparcours66', array( 'type' => 'INNER' )),
					),
					'contain' => false,
					'conditions' => array(
						'Personne.id' => $personne_id,
						'Orientstruct.id IS NULL'
					)
				);

				if ( count((array)$this->Orientstruct->Personne->find('first', $query)) > 0 ) {
					$success = true;
				}
			}

			return $success;
		}

		/**
		 * Vérifie si pour une personne donnée la nouvelle orientation est une régression ou nonrespectssanctionseps93
		 * Orientation du pro vers le social
		 *
		 * @param integer $personne_id
		 * @param integer $newtypeorient_id
		 * @return boolean
		 */
		public function isRegression( $personne_id, $newtypeorient_id ) {
			$return = false;

			if( !$this->Orientstruct->Typeorient->isProOrientation( $newtypeorient_id ) ) {
				$lastOrient = $this->Orientstruct->find(
					'first',
					array(
						'conditions' => array(
							'Orientstruct.personne_id' => $personne_id
						),
						'contain' => array(
							'Typeorient'
						),
						'order' => array(
							'date_valid DESC'
						)
					)
				);

				if( !empty($lastOrient) && ( Configure::read( 'Typeorient.emploi_id' ) == $lastOrient['Typeorient']['id'] ) ) {
					$return = true;
				}
			}

			return $return;
		}


		/**
		 * Permet de savoir si un allocataire est en cours de procédure de
		 * relance pour une de ses orientations, en fonction du CG.
		 *
		 * @param integer $personne_id
		 * @return boolean
		 */
		public function enProcedureRelance( $personne_id ) {
			return (
				Configure::read( 'Cg.departement' ) == 93
				&& $this->Orientstruct->Nonrespectsanctionep93->enProcedureRelance( $personne_id )
			);
		}

		/**
		 * Lorsqu'on crée une nouvelle orientation via les EP (CG 93) et qu'il
		 * s'agit d'une réelle réorientation (changement de structure référente
		 * et/ou de type d'orientaion) et que l'allocataire est suivi par un PDV,
		 * sans questionnaire D2 lié, il faut en créer un de manière automatique
		 * pour cette réorientation.
		 *
		 * @param array $dossierep
		 * @param string $modeleDecision
		 * @param integer $nvorientstruct_id
		 * @return boolean
		 */
		public function reorientationEpQuestionnaired2pdv93Auto( $dossierep, $modeleDecision, $nvorientstruct_id ) {
			$success = true;

			$orientstructPcd = $this->Orientstruct->find(
				'first',
				array(
					'contain' => false,
					'conditions' => array(
						'Orientstruct.personne_id' => $dossierep['Dossierep']['personne_id'],
						'Orientstruct.statut_orient' => 'Orienté',
						'NOT' => array(
							'Orientstruct.id' => $nvorientstruct_id,
						)
					),
					'order' => array( 'Orientstruct.date_valid DESC' )
				)
			);

			$reorientation = (
				empty( $orientstructPcd )
				|| $orientstructPcd['Orientstruct']['typeorient_id'] != $dossierep[$modeleDecision]['typeorient_id']
				|| $orientstructPcd['Orientstruct']['structurereferente_id'] != $dossierep[$modeleDecision]['structurereferente_id']
			);

			if( $reorientation ) {
				$success = $this->Orientstruct->Personne->Questionnaired2pdv93->saveAuto( $dossierep['Dossierep']['personne_id'], 'reorientation' ) && $success;
			}

			return $success;
		}


		/**
		 * Retourne une sous-requête, aliasée si le paramètre $fieldName n'est
		 * pas vide, permettant de savoir si un enregistrement est imprimable,
		 * suivant l'état de l'orientation et le CG connecté.
		 *
		 * @see Configure Cg.departement
		 *
		 * @param string $fieldName
		 * @return string
		 */
		public function getPrintableSq( $fieldName = 'printable' ) {
			$departement = Configure::read( 'Cg.departement' );

			if( in_array( $departement, array( 976, 58) ) ) {
				$sqPrintable = "\"{$this->Orientstruct->alias}\".\"statut_orient\" IN ( 'En attente', 'Orienté' )";
			}
			else if( $departement == 66 ) {
				$sqPrintable = "\"{$this->Orientstruct->alias}\".\"statut_orient\" = 'Orienté'";
			}
			else {
				// Implique que le document a été imprimé en PDF en amont
				$Pdf = ClassRegistry::init( 'Pdf' );
				$sqPrintable = $Pdf->sqImprime( $this->Orientstruct, null );
			}

			if( !empty( $fieldName ) ) {
				$sqPrintable = "( {$sqPrintable} ) AS \"{$this->Orientstruct->alias}__{$fieldName}\"";
			}

			return $sqPrintable;
		}

		/**
		 *
		 * @param integer $orientstruct_id
		 * @param integer $user_id
		 * @return boolean
		 */
		public function getChangementReferentOrientation( $orientstruct_id, $user_id ) {
			$orientation = $this->Orientstruct->find(
				'first',
				array(
					'fields' => array_merge(
						$this->Orientstruct->fields(),
						$this->Orientstruct->Personne->fields(),
						$this->Orientstruct->Typeorient->fields(),
						$this->Orientstruct->Structurereferente->fields(),
						$this->Orientstruct->Referent->fields(),
						$this->Orientstruct->Personne->Foyer->Adressefoyer->Adresse->fields(),
						$this->Orientstruct->Personne->Foyer->fields(),
						$this->Orientstruct->Personne->Foyer->Dossier->fields()
					),
					'joins' => array(
						$this->Orientstruct->join( 'Personne', array( 'type' => 'INNER' ) ),
						$this->Orientstruct->join( 'Typeorient', array( 'type' => 'INNER' ) ),
						$this->Orientstruct->join( 'Structurereferente', array( 'type' => 'INNER' ) ),
						$this->Orientstruct->join( 'Referent', array( 'type' => 'LEFT OUTER' ) ),
						$this->Orientstruct->Personne->join( 'Foyer', array( 'type' => 'INNER' ) ),
						$this->Orientstruct->Personne->Foyer->join( 'Adressefoyer', array( 'type' => 'LEFT OUTER' ) ),
						$this->Orientstruct->Personne->Foyer->join( 'Dossier', array( 'type' => 'INNER' ) ),
						$this->Orientstruct->Personne->Foyer->Adressefoyer->join( 'Adresse', array( 'type' => 'LEFT OUTER' ) ),
					),
					'conditions' => array(
						'Orientstruct.id' => $orientstruct_id,
                        'Adressefoyer.id IN ( '.$this->Orientstruct->Personne->Foyer->Adressefoyer->sqDerniereRgadr01( 'Adressefoyer.foyer_id' ).' )'
					),
					'contain' => false
				)
			);

			if( empty( $orientation ) ) {
				return false;
			}

			$structurereferentePrecedente = $this->Orientstruct->find(
				'first',
				array(
					'fields' => array(
						'Structurereferente.typestructure'
					),
					'conditions' => array(
						'Orientstruct.personne_id' => $orientation['Orientstruct']['personne_id'],
						'Orientstruct.date_valid <' => $orientation['Orientstruct']['date_valid'],
						'Orientstruct.statut_orient' => 'Orienté',
						'Orientstruct.id <>' => $orientation['Orientstruct']['id']
					),
					'joins' => array(
						$this->Orientstruct->join( 'Structurereferente', array( 'type' => 'INNER') )
					),
					'order' => array( 'Orientstruct.date_valid DESC' ),
					'contain' => false
				)
			);

			// Options pour les traductions
			$Option = ClassRegistry::init( 'Option' );
			$options = array(
				'Personne' => array(
					'qual' => $Option->qual()
				),
				'Referent' => array(
					'qual' => $Option->qual()
				)
			);

			$user = $this->Orientstruct->User->find(
				'first',
				array(
					'conditions' => array(
						'User.id' => $user_id
					),
					'contain' => array(
						'Serviceinstructeur'
					)
				)
			);
			$orientation = Set::merge( $orientation, $user );
			// Choix du modèle de document
			$typestructure = Set::classicExtract( $orientation, 'Structurereferente.typestructure' );
			$typestructurepassee = Set::classicExtract( $structurereferentePrecedente, 'Structurereferente.typestructure' );

			if( $typestructure == $typestructurepassee ) {
				if( $typestructure == 'oa' ) {
					// INFO: Réponse du CG66 : d'expérience cela se fait à la marge donc pour le moment
					// aucun traitement particulier
					$modeleodt = "Orientation/changement_referent_cgcg.odt"; // FIXME: devrait être paoa
				}
				else {
					$modeleodt = "Orientation/changement_referent_cgcg.odt";
				}
			}
			else {
				if( $typestructure == 'oa' ) {
					$modeleodt = "Orientation/changement_referent_cgoa.odt";
				}
				else {
					$modeleodt = "Orientation/changement_referent_oacg.odt";
				}
			}

			// Génération du PDF
			return $this->Orientstruct->ged( $orientation, $modeleodt, false, $options );
		}

		/**
		 * Ajoute les virtuals fields pour permettre le controle de l'accès à une action
		 *
		 * @param array $query
		 * @return type
		 */
		public function completeVirtualFieldsForAccess(array $query = array(), array $params = array()) {
			$fields = array(
				'dernier' => $this->Orientstruct->sqVirtualField('dernier'),
				'date_valid' => 'Orientstruct.date_valid',
				'statut_orient' => 'Orientstruct.statut_orient',
				'dernier_oriente' => $this->Orientstruct->sqVirtualField('dernier_oriente'),
				'printable' => $this->getPrintableSq('printable'),
				'linked_records' => $this->Orientstruct->getSqLinkedModelsDepartement('linked_records'),
				'premier_oriente' => $this->Orientstruct->sqVirtualField('premier_oriente'),
			);

			if (Configure::read('Cg.departement') == 66) {
				$sql = $this->Orientstruct->getDataSource()->conditions(
					array('Typeorient.parentid' => (array)Configure::read('Orientstruct.typeorientprincipale.SOCIAL')), true, false
				);
				$fields['notifbenefcliquable'] = "({$sql}) AS \"Orientstruct__notifbenefcliquable\"";
			}

			return Hash::merge($query, array('fields' => array_values( $fields )));
		}

		/**
		 * Permet d'obtenir le nécéssaire pour calculer les droits d'accès métier à une action sur les orientations
		 *
		 * @param array $conditions
		 * @return array
		 */
		public function getDataForAccess(array $conditions, array $params = array()) {
			$query = array(
				'joins' => array(),
				'conditions' => $conditions,
				'contain' => false
			);

			if (Configure::read('Cg.departement') == 66) {
				$query['joins'][] = $this->Orientstruct->join('Typeorient');
			}

			$query = $this->completeVirtualFieldsForAccess($query);
			return $this->Orientstruct->find('all', $query);
		}

		/**
		 * Donne les options du rang d'orientation selon le département et le contenu de $records
		 *
		 * @param array $records
		 * @return array
		 */
		public function rangOrientationIndexOptions(array $records) {
			$departement = Configure::read('Cg.departement');

			foreach (array_keys($records) as $key) {
				$rgorient =& $records[$key]['Orientstruct']['rgorient'];

				if (!empty($rgorient)) {
					if ($departement == 58) {
						if (Hash::get($records, "{$key}.Orientstruct.premier_oriente")) {
							$rgorient = 'Première orientation';
						} elseif ($records[$key]['Orientstruct']['typeorient_id'] != $records[$key+1]['Orientstruct']['typeorient_id']) {
							$rgorient = 'Réorientation';
						} elseif ($records[$key]['Orientstruct']['typeorient_id'] == Configure::read('Typeorient.emploi_id')) {
							$rgorient = 'Maintien en emploi';
						} else {
							$rgorient = 'Maintien en social';
						}
					}
					elseif ($departement == 66) {
						$rgorient = DepartementUtility::getTypeorientName($records, $key);
					}
					else {
						$rgorient = $rgorient == 1 ? 'Première orientation' : 'Réorientation';
					}
				}
			}

			return $records;
		}

		/**
		 * Permet d'obtenir les paramètres à envoyer à WebrsaAccess pour une personne en particulier
		 *
		 * @see WebrsaAccess::getParamsList
		 * @param integer $personne_id
		 * @param array $params - Liste des paramètres actifs
		 */
		public function getParamsForAccess($personne_id, array $params = array()) {
			$results = array();

			if (in_array('ajout_possible', $params)) {
				$results['ajout_possible'] = $this->ajoutPossible($personne_id);
			}
			if (in_array('reorientationseps', $params)) {
				$results['reorientationseps'] = $this->Orientstruct->Personne->Dossierep->getReorientationsEnCours($personne_id);
			}

			return $results;
		}

		/**
		 * Retourne le chemin relatif du modèle de document à utiliser pour l'enregistrement du PDF.
		 *
		 * @param array $data Les données envoyées au modèle pour construire le PDF
		 * @return string
		 */
		public function modeleOdt( $data ) {
			$departement = Configure::read( 'Cg.departement' );

			if( $departement == 66 ) {
				$typenotification = $data['Orientstruct']['typenotification'];

				if( !empty( $typenotification ) && $typenotification == 'systematique' ) {
					return "Orientation/orientationsystematiquepe.odt";
				}
				else if( !empty( $typenotification ) && $typenotification == 'dejainscritpe' ) {
					return "Orientation/orientationpedefait.odt";
				}
				else {
					return "Orientation/{$data['Typeorient']['modele_notif']}.odt";
				}
			}
			// Au CG 93, lorsqu'une orientation fait suite à un déménagement, il
			// faut imprimer le courrier de transfert PDV
			else if( $departement == 93 && Hash::get( $data, 'NvOrientstruct.origine' ) === 'demenagement' ) {
				return $this->Orientstruct->Transfertpdv93->modeleOdt( $data );
			}
			// Au CD 93, si l'orientation est une orientation externe faite par un prestataire,
			// if faut tenir compte de l'origine de l'orientation pour définir le fichier odt.
			else if( $departement == 93 && preg_match('|^presta|', $data['Orientstruct']['origine'])) {
				return "Orientation/{$data['Typeorient']['modele_notif']}_{$data['Orientstruct']['origine']}.odt";
			}

			return "Orientation/{$data['Typeorient']['modele_notif']}.odt";
		}

		/**
		 * Récupère les données pour le PDF.
		 *
		 * @param integer $id L'id technique de l'orientation
		 * @return array
		 */
		public function getDataForPdf( $id, $user_id = null ) {
			$departement = Configure::read( 'Cg.departement' );

			// Au CG 93, lorsqu'une orientation fait suite à un déménagement, il
			// faut imprimer le courrier de transfert PDV
			$isDemenagement = false;

			if( $departement == 93 ) {
				$demenagement = $this->Orientstruct->find(
					'first',
					array(
						'fields' => array(
							"{$this->Orientstruct->alias}.{$this->Orientstruct->primaryKey}"
						),
						'contain' => false,
						'conditions' => array(
							"{$this->Orientstruct->alias}.{$this->Orientstruct->primaryKey}" => $id,
							"{$this->Orientstruct->alias}.origine" => 'demenagement'
						)
					)
				);

				$isDemenagement = !empty( $demenagement );
			}

			if( $isDemenagement ) {
				$orientstruct = $this->Orientstruct->Transfertpdv93->getDataForPdf( $id, $user_id );

				// Traduction car elles sont faites directement dans les données pour les orientsstructs
				$options = $this->Orientstruct->Transfertpdv93->getPdfOptions();
				foreach( $options as $modelAlias => $modelOptions ) {
					foreach( $modelOptions as $fieldName => $fieldOptions ) {
						if( isset( $orientstruct[$modelAlias][$fieldName] ) ) {
							$orientstruct[$modelAlias][$fieldName] = Set::enum(
								$orientstruct[$modelAlias][$fieldName],
								$options[$modelAlias][$fieldName]
							);
						}
					}
				}
			}
			else {
				// TODO: error404/error500 si on ne trouve pas les données
				$qual = $this->Orientstruct->Option->qual();

				$orientstruct = $this->Orientstruct->find(
					'first',
					array(
						'conditions' => array(
							'Orientstruct.id' => $id
						),
						'contain' => array(
							'Personne' => array(
								'Foyer' => array(
									'Adressefoyer' => array(
										'conditions' => array(
											'rgadr' => '01'
										),
										'Adresse'
									),
									'Dossier'
								),
							),
							'Typeorient',
							'Structurereferente',
							'Referent',
							'User',
						)
					)
				);

				if( !is_null( $user_id ) ) {
					$user = $this->Orientstruct->User->find(
						'first',
						array(
							'conditions' => array(
								'User.id' => $user_id
							),
							'contain' => array(
								'Serviceinstructeur'
							)
						)
					);
					$orientstruct = Set::merge( $orientstruct, $user );
				}

				$statut_orient = Hash::get( $orientstruct, "{$this->Orientstruct->alias}.statut_orient" );

				$printable = (
					( $departement == 976 && ( $statut_orient == 'En attente' ) )
					|| ( $statut_orient == 'Orienté' )
				);

				if( !$printable ) {
					return false;
				}

				$orientstruct['Dossier'] = $orientstruct['Personne']['Foyer']['Dossier'];
				if( isset( $orientstruct['Personne']['Foyer']['Adressefoyer'][0]['Adresse'] ) ){
					$orientstruct['Adresse'] = $orientstruct['Personne']['Foyer']['Adressefoyer'][0]['Adresse'];
					unset( $orientstruct['Personne']['Foyer'] );
				}

				if( $departement != 66 ) {
					$orientstruct['Personne']['qual'] = Set::classicExtract( $qual, Set::classicExtract( $orientstruct, 'Personne.qual' ) );
				}


				/// Recherche référent à tout prix ....
				// Premère étape: référent du parcours.
				$referent = Hash::filter( (array)$orientstruct['Referent'] );
				if( empty( $referent ) ) {
					$referent = $this->Orientstruct->Personne->Referent->PersonneReferent->find(
						'first',
						array(
							'conditions' => array(
								'PersonneReferent.personne_id' => $orientstruct['Personne']['id']
							),
							'recursive' => -1
						)
					);
					if( !empty( $referent ) ) {
						$orientstruct['Referent'] = $referent['PersonneReferent'];
					}
				}

				// Deuxième étape: premier référent renseigné pour la structure sélectionnée
				$referent = Hash::filter( (array)$orientstruct['Referent'] );
				if( empty( $referent ) && !empty( $orientstruct['Structurereferente']['id'] ) ) {
					$referent = $this->Orientstruct->Personne->Referent->find(
						'first',
						array(
							'conditions' => array(
								'Referent.structurereferente_id' => $orientstruct['Structurereferente']['id']
							),
							'recursive' => -1
						)
					);
					if( !empty( $referent ) ) {
						$orientstruct['Referent'] = $referent['Referent'];
					}
				}

				// Troisième étape pour le 58, on ajoute les informations des SAMS
				if( $departement == 58 ) {
					$query = $this->getIndexQuery($orientstruct['Orientstruct']['personne_id']);
					$query['conditions']['Orientstruct.id'] = $orientstruct['Orientstruct']['id'];
					$record = $this->Orientstruct->find('first', $query);
					$orientstruct['Sitecov58'] = $record['Sitecov58'];
				}
			}

			return $orientstruct;
		}

		/**
		 * FIXME: select max(rgorient), si on a besoin d'archiver
		 *
		 * @param integer $personne_id
		 * @return integer
		 */
		public function rgorientMax( $personne_id ) {
			$return = 0;

			$result = $this->Orientstruct->find(
				'first',
				array(
					'recursive' => -1,
					'fields' => 'rgorient',
					'order' => 'rgorient DESC',
					'conditions' => array(
						"{$this->Orientstruct->alias}.statut_orient" => 'Orienté',
						"{$this->Orientstruct->alias}.personne_id" => $personne_id
					),
				)
			);

			if (!empty($result)) {
				$return = $result['Orientstruct']['rgorient'];
			}

			return $return;
		}

		/**
		 * Retourne la dernière orientation orientée pour une personne.
		 *
		 * @param string $personneIdFied
		 * @param string $alias
		 * @return string
		 */
		public function sqDerniere( $personneIdFied = 'Personne.id', $alias = 'orientsstructs' ) {
			return $this->Orientstruct->sq(
				array(
					'fields' => array(
						"{$alias}.id"
					),
					'alias' => $alias,
					'conditions' => array(
						"{$alias}.personne_id = {$personneIdFied}",
						"{$alias}.statut_orient = 'Orienté'",
						"{$alias}.date_valid IS NOT NULL"
					),
					'order' => array(
						"{$alias}.date_valid DESC",
						"{$alias}.id DESC"
					),
					'limit' => 1
				)
			);
		}

		/**
		 * Fonction permettant la mise à jour de la table nonorientes66.
		 *
		 * @param integer $orientstruct_id L'id de l'orientation
		 * @return type
		 */
		public function updateNonoriente66( $orientstruct_id ) {
			$success = true;

			if( Configure::read( 'Cg.departement' ) == 66 ) {
				$orientationAvecEntreeNonoriente66 = $this->Orientstruct->find(
					'first',
					array(
						'fields' => array(
							'Nonoriente66.id'
						),
						'conditions' => array(
							'Orientstruct.id' => $orientstruct_id
						),
						'joins' => array(
							$this->Orientstruct->join( 'Personne', array(  'type' => 'INNER' ) ),
							$this->Orientstruct->Personne->join( 'Nonoriente66', array(  'type' => 'INNER' ) )
						),
						'contain' => false
					)
				);

				if( !empty( $orientationAvecEntreeNonoriente66 )  ) {
					$success = $this->Orientstruct->Nonoriente66->updateAllUnBound(
						array( 'Nonoriente66.orientstruct_id' => $orientstruct_id ),
						array(
							'"Nonoriente66"."id"' => $orientationAvecEntreeNonoriente66['Nonoriente66']['id']
						)
					);
				}
			}

			return $success;
		}

		/**
		 *
		 * @param integer $orientstruct_id
		 * @return string
		 */
		public function getPdfNonoriente66 ( $orientstruct_id, $user_id ) {
			$data = $this->getDataForPdf( $orientstruct_id, $user_id );

			// Options pour les traductions
			$Option = ClassRegistry::init( 'Option' );
			$options = array(
				'Personne' => array(
					'qual' => $Option->qual()
				),
				'Referent' => array(
					'qual' => $Option->qual()
				)
			);

			$nonoriente66 = $this->Orientstruct->Personne->Nonoriente66->find(
				'first',
				array(
					'conditions' => array(
						'Nonoriente66.orientstruct_id' => $orientstruct_id
					),
					'contain' => false
				)
			);
			$originePdfOrientation = Set::classicExtract( $nonoriente66, 'Nonoriente66.origine' );
			$typeOrientParentIdPdf = Set::classicExtract( $data, 'Typeorient.parentid' );
			$reponseAllocataire = Set::classicExtract( $nonoriente66, 'Nonoriente66.reponseallocataire' );


			$typesorientsParentidsSocial = Configure::read( 'Orientstruct.typeorientprincipale.SOCIAL' );
			$typesorientsParentidsEmploi = Configure::read( 'Orientstruct.typeorientprincipale.Emploi' );

			if( $originePdfOrientation == 'isemploi' ) {
				$modeleodt = 'Orientation/orientationpedefait.odt'; // INFO courrier 1
			}
			else {
				if( in_array( $typeOrientParentIdPdf, $typesorientsParentidsEmploi) ) {
					$modeleodt = 'Orientation/orientationpe.odt'; //INFO = courrier 3
				}
				else if( in_array( $typeOrientParentIdPdf, $typesorientsParentidsSocial) && ( $reponseAllocataire == 'O' ) ) {
					$modeleodt = 'Orientation/orientationsociale.odt';// INFO = courrier 4
				}
				else if( in_array( $typeOrientParentIdPdf, $typesorientsParentidsSocial) && ( $reponseAllocataire == 'N' ) ) {
					$modeleodt = 'Orientation/orientationsocialeauto.odt';// INFO = courrier 5
				}
				else if( in_array( $typeOrientParentIdPdf, $typesorientsParentidsSocial ) ) {
					$modeleodt = 'Orientation/orientationsociale.odt';// INFO = courrier 5
				}
			}

			$pdf = $this->Orientstruct->ged( $data, $modeleodt, false, $options );

			if( !empty( $pdf ) ) {
				$this->Orientstruct->Nonoriente66->updateAllUnBound(
					array( 'Nonoriente66.datenotification' => "'".date( 'Y-m-d' )."'" ),
					array(
						'"Nonoriente66"."id"' => $nonoriente66['Nonoriente66']['id']
					)
				);
			}

			return $pdf;
		}

		/**
		 * Retourne le PDF par défaut, stocké, ou généré par les appels aux méthodes getDataForPdf, modeleOdt et
		 * à la méthode ged du behavior Gedooo et le stocke,
		 *
		 * @param integer $id Id du CER
		 * @param integer $user_id Id de l'utilisateur connecté
		 * @return string
		 */
		public function getDefaultPdf( $id, $user_id ) {
			$Option = ClassRegistry::init( 'Option' );
			$options = Hash::merge(
				array (
					'Personne' => array(
						'qual' => $Option->qual()
					),
					'Referent' => array(
						'qual' => $Option->qual()
					)
				),
				$this->Orientstruct->Personne->Foyer->enums(),
				array(
					'Prestation' => array(
						'rolepers' => ClassRegistry::init('Prestation')->enum('rolepers'),
					),
					'Detaildroitrsa' => array(
						'oridemrsa' => ClassRegistry::init('Detaildroitrsa')->enum('oridemrsa'),
					),
				),
				$this->Orientstruct->enums()
			);

			$orientstruct = $this->getDataForPdf( $id, $user_id );
			$modeledoc = $this->modeleOdt( $orientstruct );

			$pdf = $this->Orientstruct->ged( $orientstruct, $modeledoc, false, $options );

			return $pdf;
		}

		/**
		 * Calcul du type de préorientation d'un allocataire (CG 93).
		 *
		 * Dernière version des règles de préorientation:
		 * 	- prise en compte des informations Pôle Emploi le 04/01/2011, par mail
		 * 	- changement règle 4 le 16/04/2010, par mail
		 *
		 * @param array $element
		 * @return string
		 */
		public function preOrientation( $element ) {
			$propo_algo = null;
/*
			/// Inscription Pôle Emploi ?
			$conditions = $this->Informationpe->qdConditionsJoinPersonneOnValues( 'Informationpe', $element['Personne'] );

			$sqDernierePourPersonne = $this->Informationpe->sqDernierePourPersonne( $element );
			$conditions[] = "Informationpe.id IN ( {$sqDernierePourPersonne} )";

			$informationpe = $this->Informationpe->find(
				'first',
				array(
					'fields' => array(
						'(
							SELECT
									"Historiqueetatpe"."etat"
								FROM "historiqueetatspe" AS "Historiqueetatpe"
								WHERE
									"Historiqueetatpe"."informationpe_id" = "Informationpe"."id"
									ORDER BY "Historiqueetatpe"."date" DESC LIMIT 1
						) AS "Historiqueetatpe__dernieretat"'
					),
					'conditions' => $conditions,
					'contain' => false
				)
			);

			// La personne se retrouve préorientée en emploi si la dernière information
			// venant de Pôle Emploi la concernant est une inscription
			if( !empty( $informationpe ) ) {
				if( @$informationpe['Historiqueetatpe']['dernieretat'] == 'inscription' ) {
					return 'Emploi';
				}
			}
*/
			// On ne peut pas préorienter à partir des informations Pôle Emploi
//			if( is_null( $propo_algo ) ) {
				/// Dsp
				$dsp = $this->Dsp->find(
					'first',
					array(
						'fields' => array(
							'Dsp.natlog',
							'Dsp.sitpersdemrsa',
							'Dsp.cessderact',
							'Dsp.hispro',
							'Detaildiflog.diflog',
						),
						'conditions' => array( 'Dsp.personne_id' => $element['Personne']['id'] ),
						'contain' => false,
						'joins' => array(
							array(
								'table'      => 'detailsdiflogs',
								'alias'      => 'Detaildiflog',
								'type'       => 'LEFT OUTER',
								'foreignKey' => false,
								'conditions' => array(
									'Detaildiflog.dsp_id = Dsp.id',
									'Detaildiflog.diflog' => '1006'
								)
							),
						)
					)
				);

				/// Règles de gestion déduites depuis les DSP
				if( !empty( $dsp ) ) {
					// Règle 1 (Prioritaire) : Code XML instruction : « NATLOG ». Nature du logement ?
					// 0904 = Logement d'urgence : CHRS → Orientation vers le Social
					// 0911 = Logement précaire : résidence sociale → Orientation vers le Social
					$natlog = Set::classicExtract( $dsp, 'Dsp.natlog' );
					if( empty( $propo_algo ) && !empty( $natlog ) ) {
						if( in_array( $natlog, array( '0904', '0911' ) ) ) {
							$propo_algo = 'Social';
						}
					}

					// Règle 2 (Prioritaire)  : Code XML instruction : « DIFLOG ». Difficultés logement ?
					// 1006 = Fin de bail, expulsion → Orientation vers le Service Social
					$diflog = Set::classicExtract( $dsp, 'Detaildiflog.diflog' );
					if( empty( $propo_algo ) && !empty( $diflog ) ) {
						if( $diflog == '1006' ) {
							$propo_algo = 'Social';
						}
					}

					// Règle 3 (Prioritaire)  : Code XML instruction : « sitpersdemrsa ». "Quel est le motif de votre demande de rSa ?"
					// 0102 = Fin de droits AAH → Orientation vers le Social
					// 0105 = Attente de pension vieillesse ou invalidité‚ ou d'allocation handicap → Orientation vers le Social
					// 0109 = Fin d'études → Orientation vers le Pôle Emploi
					// 0101 = Fin de droits ASSEDIC → Orientation vers le Pôle Emploi
					$sitpersdemrsa = Set::extract( $dsp, 'Dsp.sitpersdemrsa' );
					if( empty( $propo_algo ) && !empty( $sitpersdemrsa ) ) {
						if( in_array( $sitpersdemrsa, array( '0102', '0105' ) ) ) {
							$propo_algo = 'Social';
						}
						else if( in_array( $sitpersdemrsa, array( '0109', '0101' ) ) ) {
							$propo_algo = 'Emploi';
						}
					}

					// Règle 4 : Code XML instruction : « DTNAI ». Date de Naissance.
					$dtnai = Set::extract( $element, 'Personne.dtnai' );
					/// FIXME: change chaque année ...
					$cessderact = Set::extract( $dsp, 'Dsp.cessderact' );

					// Si le code CESSDERACT n'est pas renseigné : Règle 5
					if( empty( $propo_algo ) && !empty( $cessderact ) ) {
						$age = age( $dtnai );

						// Si - de 57 a :
						// "2701" : Encore en activité ou cessation depuis moins d'un an ->Pôle Emploi
						// "2702" : Cessation d'activité depuis plus d'un an -> PDV
						if( $age < 57 ) {
							if( $cessderact == '2701' ) {
								$propo_algo = 'Emploi';
							}
							else if( $cessderact == '2702' ) {
								$propo_algo = 'Socioprofessionnelle';
							}
						}

						// Si + de 57 a :
						// "2701" : Encore en activité ou cessation depuis moins d'un an -> PDV
						// "2702" : Cessation d'activité depuis plus d'un an ->Service Social
						else if( $age >= 57 ) {
							if( $cessderact == '2701' ) {
								$propo_algo = 'Socioprofessionnelle';
							}
							else if( $cessderact == '2702' ) {
								$propo_algo = 'Social';
							}
						}
					}

					// Règle 5 : Code XML instruction : « HISPRO ». Question : Passé professionnel ?
					// 1901 = Oui → Orientation vers le Pôle Emploi
					// 1902 = Oui → Orientation vers le PDV
					// 1903 = Oui → Orientation vers le PDV
					// 1904 = Oui → Orientation vers le PDV
					$hispro = Set::extract( $dsp, 'Dsp.hispro' );
					if( empty( $propo_algo ) && !empty( $hispro ) ) {
						if( $hispro == '1901' ) {
							$propo_algo = 'Emploi';
						}
						else if( in_array( $hispro, array( '1902', '1903', '1904' ) ) ) {
							$propo_algo = 'Socioprofessionnelle';
						}
					}
				}
//			}

			// On ne peut pas préorienter à partir des informations Pôle Emploi
			if( is_null( $propo_algo ) ) {
				/// Inscription Pôle Emploi ?
				$conditions = $this->Informationpe->qdConditionsJoinPersonneOnValues( 'Informationpe', $element['Personne'] );

				$sqDernierePourPersonne = $this->Informationpe->sqDernierePourPersonne( $element );
				$conditions[] = "Informationpe.id IN ( {$sqDernierePourPersonne} )";

				$informationpe = $this->Informationpe->find(
					'first',
					array(
						'fields' => array(
							'(
								SELECT
										"Historiqueetatpe"."etat"
									FROM "historiqueetatspe" AS "Historiqueetatpe"
									WHERE
										"Historiqueetatpe"."informationpe_id" = "Informationpe"."id"
										ORDER BY "Historiqueetatpe"."date" DESC LIMIT 1
							) AS "Historiqueetatpe__dernieretat"'
						),
						'conditions' => str_replace("\\'", "''", $conditions),
						'contain' => false
					)
				);

				// La personne se retrouve préorientée en emploi si la dernière information
				// venant de Pôle Emploi la concernant est une inscription
				if( !empty( $informationpe ) ) {
					if( @$informationpe['Historiqueetatpe']['dernieretat'] == 'inscription' ) {
						return 'Emploi';
					}
				}
			}

			return $propo_algo;
		}
	}