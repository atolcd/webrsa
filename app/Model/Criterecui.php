<?php
	/**
	 * Fichier source de la classe Criterecui.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'Sanitize', 'Utility' );

	/**
	 * La classe Criterecui s'occupe du moteur de recherche des CUIs (CG 58, 66 et 93).
	 *
	 * @package app.Model
	 * @deprecated since version 3.0.0
	 * @see WebrsaRechercheCui
	 */
	class Criterecui extends AppModel
	{
		public $name = 'Criterecui';

		public $useTable = false;

		public $actsAs = array( 'Conditionnable' );


		/**
		 * @todo permettre de paramétrer les champs
		 * @todo mettre les critères par défaut dans le webrsa.inc
		 *
		 * @return array
		 */
		public function searchQuery() {
			$Allocataire = ClassRegistry::init( 'Allocataire' );
			$Cui = ClassRegistry::init( 'Cui' );

			$cacheKey = Inflector::underscore( $this->useDbConfig ).'_'.Inflector::underscore( $this->alias ).'_'.Inflector::underscore( __FUNCTION__ );
			$query = Cache::read( $cacheKey );

			if( $query === false ) {
				$types = array(
					'Calculdroitrsa' => 'LEFT OUTER',
					'Foyer' => 'INNER',
					'Prestation' => 'INNER',
					'Adressefoyer' => 'LEFT OUTER',
					'Dossier' => 'INNER',
					'Adresse' => 'LEFT OUTER',
					'Situationdossierrsa' => 'INNER',
					'Detaildroitrsa' => 'LEFT OUTER',
					'Entreeromev3' => 'LEFT OUTER',
				);
				$query = $Allocataire->searchQuery( $types, 'Cui' );

				// 1. Ajout des champs supplémentaires
				$query['fields'] = array_merge(
					$query['fields'],
					ConfigurableQueryFields::getModelsFields(
						array(
							$Cui,
							$Cui->Emailcui,
							$Cui->Partenairecui,
							$Cui->Partenairecui->Adressecui,
						)
					),
					// Champs nécessaires au traitement de la search
					array(
						'Cui.id',
						'Cui.personne_id'
					)
				);

				// 2. Ajout des jointures supplémentaires
				
				// Joiture spéciale pour les emails
				$emailQuery = array(
					'alias' => 'emailscuis',
					'fields' => array( "emailscuis.id" ),
					'contain' => false,
					'conditions' => array(
						"emailscuis.cui_id = Cui.id",
						"emailscuis.dateenvoi IS NOT NULL",
					),
					'order' => array(
						"emailscuis.dateenvoi" => "DESC",
					),
					'limit' => 1
				);
				
				
				array_unshift(
					$query['joins'],
					$Cui->join( 'Partenairecui', array( 'type' => 'LEFT OUTER' ) ),
					$Cui->join( 'Entreeromev3', array( 'type' => 'LEFT OUTER' ) ),
					$Cui->join( 'Emailcui', 
						array( 
							'type' => 'LEFT OUTER',
							'conditions' => array(
								"Emailcui.id IN ( ".$Cui->Emailcui->sq( $emailQuery )." )"
							)
						) 
					),
					$Cui->Partenairecui->join( 'Adressecui', array( 'type' => 'LEFT OUTER' ) )
				);
				
				// Ajout des tables spécifiques
				$cgDepartement = Configure::read( 'Cg.departement' );
				$modelCuiDpt = 'Cui' . $cgDepartement;
				if( isset( $Cui->{$modelCuiDpt} ) ) {
					$foreignKey = strtolower($modelCuiDpt) . '_id';
					
					// Liste de modeles obligatoire pour un CG donné
					$modelList = array(
						$Cui->{$modelCuiDpt}
					);
					
					array_push(
						$query['joins'],
						$Cui->join( $modelCuiDpt, array( 'type' => 'INNER' ) )
					);
					
					// Liste de modeles potentiel pour un CG donné
					$modelPotentiel = array(
						'Accompagnementcui' . $cgDepartement,
						'Decisioncui' . $cgDepartement,
						'Propositioncui' . $cgDepartement,
						'Rupturecui' . $cgDepartement,
						'Suspensioncui' . $cgDepartement,
						'Historiquepositioncui' . $cgDepartement,
					);
					foreach ( $modelPotentiel as $modelName ){
						if ( isset( $Cui->{$modelCuiDpt}->{$modelName} ) ){
							$tableName = Inflector::tableize($modelName);
							$subQuery = array(
								'alias' => $tableName,
								'fields' => array( "{$tableName}.id" ),
								'contain' => false,
								'conditions' => array(
									"{$tableName}.{$foreignKey} = {$modelCuiDpt}.id"
								),
								'order' => array(
									"{$tableName}.created" => "DESC"
								),
								'limit' => 1
							);

							array_push($modelList, $Cui->{$modelCuiDpt}->{$modelName});
							array_push(
								$query['joins'],
								$Cui->{$modelCuiDpt}->join( $modelName, array( 'type' => 'LEFT OUTER',
									'conditions' => array(
										"{$modelName}.id IN ( ".$Cui->{$modelCuiDpt}->{$modelName}->sq( $subQuery )." )"
									)) )
							);
						}
					}

					$query['fields'] = array_merge(
						$query['fields'],
						ConfigurableQueryFields::getModelsFields( $modelList )
					);
				}

				// 3. Tri par défaut: date, heure, id
				$query['order'] = array(
					'Personne.nom' => 'ASC',
					'Personne.prenom' => 'ASC',
					'Cui.id' => 'ASC'
				);
				
				Cache::write( $cacheKey, $query );
			}

			return $query;
		}

		/**
		 * @todo: filtres spécifiques aux RDV
		 *
		 * @param array $query
		 * @param array $search
		 * @return array
		 */
		public function searchConditions( array $query, array $search ) {
			$Allocataire = ClassRegistry::init( 'Allocataire' );
			$Cui = ClassRegistry::init( 'Cui' );

			$query = $Allocataire->searchConditions( $query, $search );
			
			$paths = array(
				'Cui.niveauformation',
				'Cui.inscritpoleemploi',
				'Cui.sansemploi',
				'Cui.beneficiairede',
				'Cui.majorationrsa',
				'Cui.rsadepuis',
				'Cui.travailleurhandicape',
				'Cui.typecontrat',
				'Cui.partenaire_id',
				'Adressecui.commune',
				'Adressecui.canton',
				'Entreeromev3.familleromev3_id',
			);
			
			$pathsToExplode = array(
				'Entreeromev3.domaineromev3_id',
				'Entreeromev3.metierromev3_id',
				'Entreeromev3.appellationromev3_id',
			);
			
			$pathsDate = array( 
				'Cui.dateembauche',
				'Cui.findecontrat',
				'Cui.effetpriseencharge',
				'Cui.finpriseencharge',
				'Cui.decisionpriseencharge',
				'Cui.faitle',
			);

			if ( Configure::read( 'Cg.departement' ) == 66 ){
				$paths = array_merge( $paths, 
					array( 
						'Cui66.typeformulaire',
						'Cui.secteurmarchand',
						'Cui66.typecontrat',
						'Cui66.dossiereligible',
						'Cui66.dossierrecu',
						'Cui66.dossiercomplet',
						'Cui66.etatdossiercui66',
						'Emailcui.textmailcui66_id',
						'Decisioncui66.decision',
					)
				);
				
				
				$pathsDate = array_merge( $pathsDate, 
					array( 
						'Cui66.dateeligibilite',
						'Cui66.datereception',
						'Cui66.datecomplet',
						'Emailcui.insertiondate',
						'Emailcui.created',
						'Emailcui.dateenvoi',
						'Decisioncui66.datedecision',
						'Historiquepositioncui66.created',
					)
				);
			}
			
			foreach( $paths as $path ) {
				$value = Hash::get( $search, $path );
				if( $value !== null && $value !== '' ) {
					$query['conditions'][$path] = $value;
				}
			}
			
			foreach( $pathsToExplode as $path ) {
				$value = Hash::get( $search, $path );
				if( $value !== null && $value !== '' && strpos($value, '_') > 0 ) {
					list(,$value) = explode('_', $value);
					$query['conditions'][$path] = $value;
				}
			}
			
			$query['conditions'] = $this->conditionsDates( $query['conditions'], $search, $pathsDate );

			return $query;
		}

		/**
		 *
		 * @param array $search
		 * @return array
		 */
		public function search( array $search ) {
			$query = $this->searchQuery();
			$query = $this->searchConditions( $query, $search );

			return $query;
		}


		/**
		 * Vérification que les champs spécifiés dans le paramétrage par les clés
		 * Cohortesrendezvous.search.fields, Cohortesrendezvous.search.innerTable
		 * et Cohortesrendezvous.exportcsv dans le webrsa.inc existent bien dans
		 * la requête de recherche renvoyée par la méthode search().
		 *
		 * @return array
		 */
		public function checkParametrage( array $params = array() ) {
			$keys = array( 'Criterescuis.search.fields', 'Criterescuis.exportcsv' );
			$query = $this->search( array() );

			$return = ConfigurableQueryFields::getErrors( $keys, $query );

			return $return;
		}

		/**
		 * Exécute les différentes méthods du modèle permettant la mise en cache.
		 * Utilisé au préchargement de l'application (/prechargements/search).
		 *
		 * Export de la liste des champs disponibles pour le moteur de recherche
		 * dans le fichier app/tmp/Cohorterendezvous__searchQuery__cgXX.csv.
		 *
		 * @return boolean true en cas de succès, false en cas d'erreur,
		 * 	null pour les méthodes qui ne font rien.
		 */
		public function prechargement() {
			$success = true;

			$query = $this->searchQuery();
			$success = $success && !empty( $query );

			// Export des champs disponibles
			$fileName = TMP.DS.'logs'.DS.__CLASS__.'__searchQuery__cg'.Configure::read( 'Cg.departement' ).'.csv';
			ConfigurableQueryFields::exportQueryFields( $query, Inflector::tableize( $this->name ), $fileName );

			return $success;
		}
	}
?>