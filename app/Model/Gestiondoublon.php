<?php
	/**
	 * Code source de la classe Gestiondoublon.
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe Gestiondoublon ...
	 *
	 * @package app.Model
	 */
	class Gestiondoublon extends AppModel
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'Gestiondoublon';

		/**
		 * On n'utilise pas de table.
		 *
		 * @var boolean
		 */
		public $useTable = false;

		/**
		 * Behaviors utilisés par le modèle.
		 *
		 * @var array
		 */
		public $actsAs = array(
			'Conditionnable'
		);
		
		/**
		 * Si on ajoute un path en cmis, on stock le path pour éventuellement le supprimer en cas de mauvaise transaction
		 * @var array
		 */
		protected $_cmisNewPath = array();
		
		/**
		 * Si on modifie le path en cmis, on stock l'ancienne valeur pour les supprimer si la transaction s'est bien passé
		 * @var type 
		 */
		protected $_cmisOldPath = array();

		/**
		 * Existe-t'il des fichiers modules liés aux enregistrements que nous
		 * voulons fusionner ?
		 *
		 * @deprecated since version 2.10
		 * @param array $donnees
		 * @return array
		 */
		public function fichiersModuleLies( array $data, $extractPath = '/%s/%s/id' ) {
			$conditions = array();

			$modelNames = array_keys( $data );

			if( !empty( $modelNames ) ) {
				foreach( $modelNames as $modelName ) {
					if( $modelName != 'Fichiermodule' ) {
						$values = Set::extract( $data, str_replace( '%s', $modelName, $extractPath ) );
						$conditions[] = array(
							'Fichiermodule.modele' => $modelName,
							'Fichiermodule.fk_value' => $values
						);
					}
				}
			}

			$query = array(
				'fields' => array(
					'Fichiermodule.modele',
					'Fichiermodule.fk_value',
				),
				'conditions' => array(
					'OR' => $conditions
				),
				'contain' => false,
				'order' => array(
					'Fichiermodule.modele ASC',
					'Fichiermodule.fk_value DESC',
				)
			);

			$results = ClassRegistry::init( 'Fichiermodule' )->find( 'all', $query );

			return $results;
		}

		/**
		 *
		 * @see getAnciensDossiers()
		 *
		 * @param array $search
		 * @param integer $differenceThreshold
		 * @return array
		 */
		public function searchComplexes( array $search = array(), $differenceThreshold = 4 ) {
			// 1. Partie searchQueryComplexes()
			$cacheKey = Inflector::underscore( $this->useDbConfig ).'_'.Inflector::underscore( $this->alias ).'_'.Inflector::underscore( __FUNCTION__ )."_{$differenceThreshold}";
			$query = Cache::read( $cacheKey );

			if (!$query) {
				$Foyer = ClassRegistry::init('Foyer');
				
				// Refonte :
				$query = array(
					'fields' => array(
						'Foyer.id',
						'Foyer2.id',
						'Dossier.numdemrsa',
						'Dossier.dtdemrsa',
						'Dossier.matricule',
						'Demandeur.nom',
						'Demandeur.prenom',
						'Situationdossierrsa.etatdosrsa',
						'Adresse.nomcom',
						'Dossier2.numdemrsa',
						'Dossier2.dtdemrsa',
						'Dossier2.matricule',
						'Demandeur2.nom',
						'Demandeur2.prenom',
						'Situationdossierrsa2.etatdosrsa',
						'Adresse2.nomcom',
					),
					'joins' => array(
						$Foyer->join('Adressefoyer',								// Adressefoyer
							array(
								'type' => 'LEFT OUTER',
								'conditions' => array(
									'Adressefoyer.id IN ('.$Foyer->Adressefoyer->sqDerniereRgadr01('Foyer.id').')'
								)
							)
						),
						$Foyer->join('Dossier', array('type' => 'INNER')),			// Dossier
						$Foyer->join('Personne', array('type' => 'INNER')),
						$Foyer->Adressefoyer->join('Adresse',						// Adresse
							array('type' => 'LEFT OUTER')
						),
						$Foyer->Dossier->join('Situationdossierrsa',				// Situationdossierrsa
							array('type' => 'LEFT OUTER')
						),
						$Foyer->Personne->join('Prestation',
							array(
								'type' => 'INNER',
								'conditions' => array(
									'Prestation.rolepers' => array('DEM', 'CJT')
								)
							)
						),
						array(
							'alias' => '"Demandeur"',								// Demandeur
							'table' => 'personnes',
							'conditions' => '"Demandeur"."foyer_id" = "Foyer"."id"',
							'type' => 'INNER',
						),
						array(
							'alias' => '"PrestationDem"',							// PrestationDem
							'table' => 'prestations',
							'conditions' => array(
								"PrestationDem.personne_id = Demandeur.id",
								"PrestationDem.natprest" => 'RSA', 
								"PrestationDem.rolepers" => 'DEM',
							),
							'type' => 'INNER',
						),
						array(
							'table'      => 'personnes',
							'alias'      => 'p2',
							'type'       => 'INNER',
							'foreignKey' => false,
							'conditions' => $Foyer->Personne->WebrsaPersonne
								->conditionsRapprochementPersonne1Personne2('Personne', 'p2', false),
						),
						array(
							'table'      => 'foyers',
							'alias'      => 'Foyer2',
							'type'       => 'INNER',
							'foreignKey' => false,
							'conditions' => array(
								'p2.foyer_id = Foyer2.id',
							)
						),
						array(
							'alias' => '"Adressefoyer2"',							// Adressefoyer2
							'table' => 'adressesfoyers',
							'conditions' => array(
								'"Adressefoyer2"."foyer_id" = "Foyer2"."id"',
								'"Adressefoyer2"."id" IN ('.$Foyer->Adressefoyer->sqDerniereRgadr01('Foyer2.id').')'
							),
							'type' => 'LEFT OUTER'
						),
						array(
							'alias' => '"Adresse2"',								// Adresse2
							'table' => 'adresses',
							'conditions' => array(
								'"Adressefoyer2"."adresse_id" = "Adresse2"."id"',
							),
							'type' => 'LEFT OUTER'
						),
						array(
							'alias' => '"Dossier2"',								// Dossier2
							'table' => 'dossiers',
							'conditions' => '"Foyer2"."dossier_id" = "Dossier2"."id"',
							'type' => 'INNER'
						),
						array(
							'alias' => '"Situationdossierrsa2"',					// Situationdossierrsa2
							'table' => 'situationsdossiersrsa',
							'conditions' => array(
								'"Situationdossierrsa2"."dossier_id" = "Dossier2"."id"',
							),
							'type' => 'INNER',
						),
						array(
							'alias' => '"Demandeur2"',								// Demandeur2
							'table' => 'personnes',
							'conditions' => '"Demandeur2"."foyer_id" = "Foyer2"."id"',
							'type' => 'INNER',
						),
						array(
							'alias' => '"PrestationDem2"',							// PrestationDem2
							'table' => 'prestations',
							'conditions' => array(
								"PrestationDem2.personne_id = Demandeur2.id",
								"PrestationDem2.natprest" => 'RSA', 
								"PrestationDem2.rolepers" => 'DEM',
							),
							'type' => 'INNER',
						),
					),
					'contain' => false,
					'order' => array(
						'Demandeur.nom',
						'Demandeur.prenom',
						'Dossier.matricule',
						'Dossier.dtdemrsa DESC',
						'Dossier.id',
					),
					'conditions' => array()
				);
				
				$query['group'] = $query['fields'];
				$query['group'][] = 'Dossier.id';
				
				// Retirer les lignes taggés
				$valeurtag_id = Configure::read('Gestionsdoublons.index.Tag.valeurtag_id');
				if (Configure::read('Gestionsdoublons.index.useTag') && $valeurtag_id) { // N'est pas un doublon
					$joinEntiteTag = $Foyer->join('EntiteTag');
					$joinEntiteTag2 = array_words_replace($joinEntiteTag, array('EntiteTag' => 'EntiteTag2', 'Foyer' => 'Foyer2'));
					
					$joinEntiteTag['conditions'] = array(
						$joinEntiteTag['conditions'],
						'EntiteTag.tag_id IN (SELECT id FROM tags WHERE EntiteTag.tag_id = tags.id AND tags.valeurtag_id = '.$valeurtag_id.' AND tags.etat NOT IN (\'annule\', \'perime\') AND (tags.limite IS NULL OR tags.limite > NOW()))'
					);
					$joinEntiteTag2['conditions'] = array(
						'EntiteTag2.fk_value = Foyer2.id',
						'EntiteTag2.modele' => 'Foyer',
						'EntiteTag2.tag_id = EntiteTag.tag_id'
					);
					
					$query['joins'][] = $joinEntiteTag;
					$query['joins'][] = $joinEntiteTag2;
					$query['conditions'][] = 'EntiteTag2.id IS NULL';
				}

				Cache::write( $cacheKey, $query );
			}
			
			// 2. Partie searchConditionsComplexes()
			$query['conditions'] = $this->conditionsPersonneFoyerDossier( $query['conditions'], $search );
			$query['conditions'] = array_words_replace( $query['conditions'], array( 'Personne' => 'Demandeur' ) );
			
			if (Hash::get($search, 'Situationdossierrsa2.etatdosrsa_choice')) {
				$query['conditions']['Situationdossierrsa2.etatdosrsa'] = Hash::get($search, 'Situationdossierrsa2.etatdosrsa');
			}
			
			return $query;
		}
		
		/**
		 * Permet d'obtenir la liste des ids de deux foyers sous la forme :
		 * array( 
		 *		'foyerAGarder' => array( '20150', '20151' ),
		 *		'foyerASupprimer' => array( '20225', '20226' ),
		 * )
		 * 
		 * @param integer $foyerAGarderId
		 * @param integer $foyerASupprimerId
		 * @return array
		 */
		protected function _listPersonneIdPourFusion( $foyerAGarderId, $foyerASupprimerId ){
			$Foyer = ClassRegistry::init( 'Foyer' );
			
			foreach ( array($foyerAGarderId, $foyerASupprimerId) as $key => $id ){
				$key = $key === 0 ? 'personneAGarder' : 'personneASupprimer';
				$query = array(
					'fields' => array(
						'Personne.id'
					),
					'contain' => false,
					'joins' => array(
						$Foyer->join( 'Personne', array( 'type' => 'INNER' ) )
					),
					'conditions' => array(
						'Foyer.id' => $id
					)
				);
				$results = $Foyer->find( 'all', $query );

				foreach ( $results as $value ){
					$personne_idList[$key][] = $value['Personne']['id'];
				}
			}
			
			return $personne_idList;
		}
		
		/**
		 * Permet d'obtenir la liste des correspondances des personne_id entre un foyer à garder et celui à supprimer.
		 * Renvoi un array de cette structure :
		 * array(
		 *		0 => array(
		 *			'personneAGarder' => 25556,
		 *			'personneASupprimer' => 27751
		 *		)
		 * )
		 * 
		 * @param integer $foyerAGarderId
		 * @param integer $foyerASupprimerId
		 * @return array
		 */
		protected function _correspondancesId( $foyerAGarderId, $foyerASupprimerId ){
			$Foyer = ClassRegistry::init( 'Foyer' );
			$personne_idList = $this->_listPersonneIdPourFusion( $foyerAGarderId, $foyerASupprimerId );
			$correspondanceIds = array();

			foreach( $personne_idList['personneAGarder'] as $personneAGarder_id ){
				$aGarderQuery = array(
					'fields' => array(
						"SUBSTRING( TRIM( BOTH ' ' FROM \"Personne\".\"nir\" ) FROM 1 FOR 13 ) AS \"Personne__formatednir\"",
						"UPPER(Personne.nom) AS \"Personne__uppernom\"",
						"UPPER(Personne.prenom) AS \"Personne__upperprenom\"",
						"Personne.dtnai"
					),
					'contain' => false,
					'conditions' => array( 'Personne.id' => $personneAGarder_id )
				);
				$aGarder = $Foyer->Personne->find( 'first', $aGarderQuery );

				foreach( $personne_idList['personneASupprimer'] as $personneASupprimer_id ){
					$aSupprimerQuery = array(
						'fields' => 'Personne.id',
						'contain' => false,
						'conditions' => array( 
							'Personne.id' => $personneAGarder_id,
							"Personne.dtnai" => "{$aGarder['Personne']['dtnai']}",
							'OR' => array(
								array(
									"nir_correct13(Personne.nir)",
									"nir_correct13('{$aGarder['Personne']['formatednir']}')",
									"SUBSTRING( TRIM( BOTH ' ' FROM Personne.nir ) FROM 1 FOR 13 )" => "{$aGarder['Personne']['formatednir']}",
								),
								array(
									"UPPER(Personne.nom)" => "{$aGarder['Personne']['uppernom']}",
									"UPPER(Personne.prenom)" => "{$aGarder['Personne']['upperprenom']}",
								)
							)
						)
					);
					$result = $Foyer->Personne->find( 'first', $aSupprimerQuery );

					if ( !empty($result) ){
						$correspondanceIds[] = array(
							'personneAGarder' => $personneAGarder_id,
							'personneASupprimer' => $personneASupprimer_id
						);
						break;
					}
				}
			}
			
			return $correspondanceIds;
		}

		/**
		 * Fusion de deux foyers et des enregistrements liés.
		 *
		 * @param integer $foyer1_id
		 * @param integer $foyer2_id
		 * @param array $results
		 * @param array $data
		 * @return boolean
		 */
		public function fusionComplexe( $foyer1_id, $foyer2_id, array $results, array $data ) {
			$Foyer = ClassRegistry::init( 'Foyer' );
			$success = true;

			$foyerAGarderId = Hash::get( $data, 'Foyer.id' );
			$foyerASupprimerId = ( ( $foyerAGarderId == $foyer1_id ) ? $foyer2_id : $foyer1_id );

			$Foyer->begin();
			
			$success = $this->_deplacerFichiersLies( 'Foyer', $foyerAGarderId, $foyerASupprimerId );
			
			// Spécial dossier pcg
			if ( Configure::read( 'Cg.departement') == 66 && isset( $data['Dossierpcg66']['id'] ) && !empty( $data['Dossierpcg66']['id'] ) ){
				$correspondanceIds = $this->_correspondancesId( $foyerAGarderId, $foyerASupprimerId );
				
				foreach( $correspondanceIds as $correspondance ){
					$dataPersonnepcg66['Personnepcg66.personne_id'] = $correspondance['personneAGarder'];
					$condition['Personnepcg66.personne_id'] = $correspondance['personneASupprimer'];
					$Foyer->Personne->Personnepcg66->updateAll( $dataPersonnepcg66, $condition );
				}
			}
			
			foreach( $data as $modelName => $values ) {
				if( !in_array( $modelName, array( 'Foyer', 'Save' ) ) ) {
					$ids = Hash::extract( $results, "{n}.{$modelName}.{n}.id" );
					$idsAGarder = Hash::extract( $data, "{$modelName}.id" );
					$idsASupprimer = array_diff( $ids, $idsAGarder );

					if( !empty( $idsAGarder ) ) {
						$success = $Foyer->{$modelName}->updateAllUnbound(
							array( "{$modelName}.foyer_id" => $foyerAGarderId ),
							array( "{$modelName}.id" => $idsAGarder )
						) && $success;
					}

					if( !empty( $idsASupprimer ) ) {
						$success = $Foyer->{$modelName}->deleteAll(
							array( "{$modelName}.id" => $idsASupprimer )
						) && $success;
						
						if ( isset($Foyer->{$modelName}->Fichiermodule) ) {
							$success = $Foyer->{$modelName}->Fichiermodule->deleteAllUnbound(
								array(
									'Fichiermodule.modele' => $modelName,
									'Fichiermodule.fk_value' => $idsASupprimer
								)
							) && $success;
						}
					}
				}
			}
			
			$dossier = $Foyer->find(
				'first',
				array(
					'fields' => array( 'Dossier.id' ),
					'contain' => array(
						'Dossier'
					),
					'conditions' => array(
						'Foyer.id' => $foyerASupprimerId
					)
				)
			);

			$success = $Foyer->Dossier->delete( $dossier['Dossier']['id'] ) && $success;

			return $success;
		}
		
		/**
		 * On réaffecte les fichiers liés d'un idASupprimer vers un autre id
		 * 
		 * @param integer $id
		 * @param integer $idASupprimer
		 */
		protected function _deplacerFichiersLies( $modelName, $idAGarder, $idASupprimer ) {
			$Fichiermodule = ClassRegistry::init( 'Fichiermodule' );
			$success = true;
			$data = $Fichiermodule->find('all', 
				array(
					'fields' => 'Fichiermodule.id',
					'conditions' => array(
						'Fichiermodule.modele' => $modelName,
						'Fichiermodule.fk_value' => $idASupprimer
					)
				)
			);
			
			foreach ( (array)Hash::extract($data, '{n}.Fichiermodule.id') as $id ) {
				$success = $success && $this->_changePath( $id, $idAGarder );
			}
			
			return $success;
		}
		
		/**
		 * Changement du path d'un fichier suite à une modification du foreign_key
		 *
		 * @param integer $id
		 * @param integer $fk_value
		 */
        protected function _changePath( $id, $fk_value ) {
			$ModeleStockage = ClassRegistry::init( 'Fichiermodule' );
            $item = $ModeleStockage->find( 'first', array( 'conditions' => array( "Fichiermodule.id" =>  $id) ) );
			
			if( empty( $item['Fichiermodule']['cmspath'] ) && empty( $item['Fichiermodule']['document'] ) ) {
                $this->cakeError( 'error500' );
            }

			$data = array(
				'id' => Hash::get($item, "Fichiermodule.id"),
				'name' => Hash::get($item, "Fichiermodule.name"),
				'fk_value' => $fk_value,
				'document' => Hash::get($item, "Fichiermodule.document"),
				'modele' => Hash::get($item, "Fichiermodule.modele"),
				'cmspath' => null,
				'mime' => Hash::get($item, "Fichiermodule.mime"),
			);
			
            if( !empty( $item['Fichiermodule']['cmspath'] )  ) {
				$cmisData = Cmis::read( $item['Fichiermodule']['cmspath'], true );
                $data['document'] = $cmisData['content'];
				$this->_cmisNewPath[] = "/{$data['modele']}/{$data['fk_value']}/{$data['name']}";
				$this->_cmisOldPath[] = $item['Fichiermodule']['cmspath'];
            }
			
			$ModeleStockage->create($data);
			return $ModeleStockage->save();
        }
		
		/**
		 * Dans le cas d'un _changePath(), à la fin de la transaction, on supprime soit les nouveaux fichiers, soit les anciens
		 * 
		 * @param boolean $success
		 */
		public function cmisTransaction( $success ) {
			$pathList = $success ? $this->_cmisOldPath : $this->_cmisNewPath;
			foreach( $pathList as $path ) {
				Cmis::delete($path, true);
			}
		}
	}
?>