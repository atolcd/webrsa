<?php
	/**
	 * Code source de la classe WebrsaCohorteCreance.
	 *
	 * PHP 7.2
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaCohorte', 'Model/Abstractclass' );
	App::uses( 'ConfigurableQueryFields', 'ConfigurableQuery.Utility' );

	/**
	 * La classe WebrsaCohorteCreance ...
	 *
	 * @package app.Model
	 */
	class WebrsaCohorteCreance extends AbstractWebrsaCohorte
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaCohorteCreance';

		/**
		 * Modèles utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array(
			'Allocataire',
			'Creance',
			'Canton',
			'Dossier',
			'Foyer',
			'Titrecreancier'
		);

		/**
		 * Liste des champs de formulaire à inserer dans le tableau de résultats
		 *
		 * @var array
		 */
		public $cohorteFields = array(
			'Creance.id' => array( 'type' => 'hidden' ),
			'Creance.foyer_id' => array( 'type' => 'hidden' ),
			'Creance.selection' => array( 'type' => 'checkbox' ),
		);

		/**
		 * Retourne le querydata de base, en fonction du département, à utiliser
		 * dans le moteur de recherche.
		 *
		 * @param array $types Les types de jointure alias => type
		 * @return array
		 */
		public function searchQuery( array $types = array() ) {
			$departement = Configure::read( 'Cg.departement' );

			$types += array(
					'Calculdroitrsa' => 'LEFT OUTER',
					'Foyer' => 'INNER',
					'Prestation' => $departement == 66 ? 'LEFT OUTER' : 'INNER',
					'Personne' => 'INNER',
					'Adressefoyer' => 'LEFT OUTER',
					'Dossier' => 'INNER',
					'Adresse' => 'LEFT OUTER',
					'Situationdossierrsa' => 'LEFT OUTER',
					'Detaildroitrsa' => 'LEFT OUTER',
					'Creance' => 'INNER JOIN',
					'Titrecreancier' => 'LEFT OUTER JOIN'
			);
			$cacheKey = Inflector::underscore( $this->useDbConfig ).'_'.Inflector::underscore( $this->alias ).'_'.Inflector::underscore( __FUNCTION__ ).'_'.sha1( serialize( $types ) );
			$query = Cache::read( $cacheKey );

			if( $query === false ) {
				$query = $this->Allocataire->searchQuery( $types, 'Creance' );

				// 1. Ajout des champs supplémentaires
				$query['fields'] = array_merge(
					$query['fields'],
					ConfigurableQueryFields::getModelsFields(
						array(
							$this->Creance,
							$this->Foyer
						)
					),
					// Champs nécessaires au traitement de la search
					array(
						'Personne.id',
						'Dossier.id',
						'Creance.id',
						'Creance.foyer_id'
					)
				);

				// 2. Jointures
				$query['joins'] = array_merge(
					$query['joins'],
					array(
						$this->Dossier->Foyer->Personne->join(
							'Dsp',
							array(
								'type' => 'LEFT OUTER',
								'conditions' => array(
									'Dsp.id IN ( '.$this->Dossier->Foyer->Personne->Dsp->WebrsaDsp->sqDerniereDsp().' )'
								)
							)
						),
						$this->Dossier->Foyer->Personne->join(
							'DspRev',
							array(
								'type' => 'LEFT OUTER',
								'conditions' => array(
									'DspRev.id IN ( '.$this->Dossier->Foyer->Personne->DspRev->sqDerniere().' )'
								)
							)
						),
						$this->Dossier->Foyer->Personne->join(
							'Orientstruct',
							array(
								'type' => 'LEFT OUTER',
								'conditions' => array(
									'Orientstruct.statut_orient' => 'Orienté',
									'Orientstruct.id IN ( '.$this->Dossier->Foyer->Personne->Orientstruct->WebrsaOrientstruct->sqDerniere().' )'
								)
							)
						),
						$this->Dossier->Foyer->Personne->Orientstruct->join( 'Structurereferente', array( 'type' => 'LEFT OUTER' ) ),
						$this->Dossier->Foyer->Personne->Orientstruct->join( 'Typeorient', array( 'type' => 'LEFT OUTER' ) ),
					)
				);

				// 4. Tri par défaut
				//$query['order'] = array( 'Contratinsertion.df_ci' => 'ASC' );

				Cache::write( $cacheKey, $query );
			}

			return $query;
		}

		/**
		 * Complète les conditions du querydata avec le contenu des filtres de
		 * recherche.
		 *
		 * @param array $query
		 * @param array $search
		 * @return array
		 */
		public function searchConditions( array $query, array $search ) {
			$query = $this->Allocataire->searchConditions( $query, $search );

			// if Origine de la créances Selected then Creances.orgcre LIKE
			$originecreance = (string)Hash::get( $search, 'Creance.orgcre' );
			if ( !empty($originecreance) ) {
				$query['conditions'][] = " Creance.orgcre LIKE '".$originecreance."'" ;
			}

			// if Motif indus de la créances Selected then Creances.motiindu LIKE
			$motiinducreance = (string)Hash::get( $search, 'Creance.motiindu' );
			if ( !empty($motiinducreance) ) {
				$query['conditions'][] = " Creance.motiindu LIKE '".$motiinducreance."'"  ;
			}

			//
			$arrayDtimplcre_from = Hash::get( $search, 'Creance.dtimplcre_from' );
			$arrayDtimplcre_to = Hash::get( $search, 'Creance.dtimplcre_to' );
			if ( !empty($arrayDtimplcre_from) && !empty($arrayDtimplcre_to)) {
				$dtimplcre_from = date_cakephp_to_sql( $arrayDtimplcre_from );
				$dtimplcre_to = date_cakephp_to_sql( $arrayDtimplcre_to );
				$query['conditions'][] = " Creance.dtimplcre BETWEEN '".$dtimplcre_from ."' AND '".$dtimplcre_to."'";
			}

			//
			$arrayMoismoucompta_from = Hash::get( $search, 'Creance.moismoucompta_from' );
			$arrayMoismoucompta_to = Hash::get( $search, 'Creance.moismoucompta_to' );
			if ( !empty($arrayMoismoucompta_from) && !empty($arrayMoismoucompta_to)) {
				$moismoucompta_from = date_cakephp_to_sql( $arrayMoismoucompta_from );
				$moismoucompta_to = date_cakephp_to_sql( $arrayMoismoucompta_to );
				$query['conditions'][] = " Creance.moismoucompta BETWEEN '".$moismoucompta_from ."' AND '".$moismoucompta_to."'";
			}

			// if etat de la créances Selected then Creances.etat LIKE
			$etatcreance = (string)Hash::get( $search, 'Creance.etat' );
			if ( !empty($etatcreance) ) {
				$query['conditions'][] = " Creance.etat LIKE '".$etatcreance."'"  ;
			}

			// if sanstitrecreancier checked then Titrecreancier.id IS NULL
			$sanstitrecreancier = (string)Hash::get( $search, 'Creance.sanstitrecreancier' );
			if ($sanstitrecreancier === '1') {
				$query['conditions'][] = " Titrecreancier.id IS NULL ";
				$query['joins'][] = array (
					'table' => '"titrescreanciers"',
					'alias' => 'Titrecreancier',
					'type' => 'LEFT OUTER ',
					'conditions' => '"Titrecreancier"."creance_id" = "Creance"."id"'
				);
			}

			// Début des spécificités par département
			$departement = Configure::read( 'Cg.departement' );

			// Recherche dossier PCG
			$etat_dossierpcg66 = (string)Hash::get( $search, 'Dossierpcg66.has_dossierpcg66' );
			if ($etat_dossierpcg66 === '0') {
				$query['conditions'][] = 'NOT ' . ' EXISTS ( ' . $this->Dossier->Foyer->dossiersPCG66 () . ' )';
			}
			else if ($etat_dossierpcg66 === '1') {
				$query['conditions'][] = ' EXISTS ( ' . $this->Dossier->Foyer->dossiersPCG66 () . ' )';
			}

			// CD 66: Personne ne possédant pas d'orientation et sans entrée Nonoriente66
			if( $departement == 66 ) {
				$exists = (string)Hash::get( $search, 'Personne.has_orientstruct' );
				if( $exists === '0' ) {
					$this->Dossier->Foyer->Personne->Behaviors->load('LinkedRecords');
					$sql = $this->Dossier->Foyer->Personne->linkedRecordVirtualField( 'Nonoriente66' );
					$query['conditions'][] = ' ' . $sql;
				}
				else if ( $exists === '1' ) {
					$this->Dossier->Foyer->Personne->Behaviors->load('LinkedRecords');
					$sql = $this->Dossier->Foyer->Personne->linkedRecordVirtualField( 'Nonoriente66' );
					$query['conditions'][] = 'NOT ' . $sql;
				}
			}
			return $query;
		}

		/**
		 * Tentative de sauvegarde de nouveaux dossiers de COV pour la thématique
		 * à partir de la cohorte.
		 *
		 * @param array $data
		 * @param array $params
		 * @param integer $user_id
		 * @return boolean
		 */
		public function saveCohorte( array $data, array $params = array(), $user_id = null ) {
			$success = true;

			//Pour chaque ligne
			foreach ( $data as $key => $value ) {
				// Si non selectionné, on retire tout
				if ( $value['Creance']['selection'] === '0' ) {
					unset($data[$key]);
					continue;
				}else{
					// On vérifie si un titre de recette n'a pas été crée entre temps
					$titrecreancierExists = $this->Titrecreancier->find('first',array ('recursive' => -1, 'conditions' => array ('creance_id' => $value['Creance']['id'])));
					if ( empty ( $titrecreancierExists) ) {
						unset($data[$key]);
						continue;
					}

					//Initialisation
					$this->Titrecreancier->begin();
					$creance_id = $value['Creance']['id'];
					$titrecreancier = array();

					//Remplissage des données depuis la créance
					$titrecreancier = $this->Titrecreancier->getInfoTitrecreancier($titrecreancier, $value['Creance']['id'], $value['Creance']['foyer_id'] );

					//Rajout des données manquantes
					$titrecreancier['Titrecreancier']['creance_id'] = $creance_id;
					$titrecreancier['Titrecreancier']['etat'] = 'CREE';
					$titrecreancier['Titrecreancier']['dtemissiontitre'] = date("Y-m-d");
					$titrecreancier['Titrecreancier']['mntinit'] = $titrecreancier['Titrecreancier']['mnttitr'];
					$titrecreancier['Titrecreancier']['instructionencours'] = 0;
					$titrecreancier['Titrecreancier']['cjtactif'] = 0;

					//Validation de la sauvegarde
					if( $this->Titrecreancier->saveAll( $titrecreancier, array( 'validate' => 'only' ) ) ) {
						if( $this->Titrecreancier->saveAll( $titrecreancier, array( 'atomic' => false ) ) ) {
							if (
								!$this->Creance->setEtatOnForeignChange($creance_id,$titrecreancier['Titrecreancier']['etat'],'cohorte_preparation') &&
								!$this->Historiqueetat->setHisto(
									$this->Titrecreancier->name,
									$this->Titrecreancier->id,
									$creance_id,
									'cohorte_preparation',
									$titrecreancier['Titrecreancier']['etat'],
									$this->Titrecreancier->foyerId($creance_id)
									)
							){
								$success = false;
								break;
							}
						} else {
							$success = false;
							break;
						}
					} else {
						$success = false;
						break;
					}
				}
			}

			// Si aucun n'a échoué et qu'on as pas tout vidé
			if($success && !empty($data) ){
				//On commit à la BDD
				$this->Creance->commit();
				$this->Titrecreancier->commit();
			}else{
				//Sinon nétoyage total
				$this->Creance->rollback();
				$this->Titrecreancier->rollback();
				$success = false;
			}

			return $success;
		}
	}