<?php
	/**
	 * Code source de la classe WebrsaRechercheCui.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaRecherche', 'Model/Abstractclass' );
	App::uses( 'ConfigurableQueryFields', 'ConfigurableQuery.Utility' );

	/**
	 * La classe WebrsaRechercheCui ...
	 *
	 * @package app.Model
	 */
	class WebrsaRechercheCui extends AbstractWebrsaRecherche
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaRechercheCui';

		/**
		 * Liste des clés de configuration utilisées par le moteur de recherche,
		 * pour vérification du paramétrage.
		 *
		 * @see checkParametrage()
		 *
		 * @var array
		 */
		public $keysRecherche = array(
			'ConfigurableQueryCuis.search.fields',
			'ConfigurableQueryCuis.search.innerTable',
			'ConfigurableQueryCuis.exportcsv'
		);

		/**
		 * Modèles utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array(
			'Allocataire',
			'Cui',
			'Canton',
		);

		/**
		 * Retourne le querydata de base, en fonction du département, à utiliser
		 * dans le moteur de recherche.
		 *
		 * @param array $types Les types de jointure alias => type
		 * @return array
		 */
		public function searchQuery( array $types = array() ) {
			$cgDepartement = Configure::read( 'Cg.departement' );
			$modelCuiDpt = 'Cui' . $cgDepartement;

			// Liste de modeles potentiel pour un CG donné
			$modelPotentiel = array(
				'Accompagnementcui' . $cgDepartement,
				'Decisioncui' . $cgDepartement,
				'Propositioncui' . $cgDepartement,
				'Rupturecui' . $cgDepartement,
				'Suspensioncui' . $cgDepartement,
				'Historiquepositioncui' . $cgDepartement,
			);

			$types += array(
				'Calculdroitrsa' => 'LEFT OUTER',
				'Foyer' => 'INNER',
				'Prestation' => 'INNER',
				'Adressefoyer' => 'LEFT OUTER',
				'Dossier' => 'INNER',
				'Adresse' => 'LEFT OUTER',
				'Situationdossierrsa' => 'INNER',
				'Detaildroitrsa' => 'LEFT OUTER',
				'Emailcui' => 'LEFT OUTER',
				'Partenairecui' => 'LEFT OUTER',
				'Adressecui' => 'LEFT OUTER',
				'Entreeromev3' => 'LEFT OUTER',
				$modelCuiDpt => 'INNER',
			);

			foreach ($modelPotentiel as $modelName){
				$types += array( $modelName => 'LEFT OUTER' );
			}

			$cacheKey = Inflector::underscore( $this->useDbConfig ).'_'.Inflector::underscore( $this->alias ).'_'.Inflector::underscore( __FUNCTION__ ).'_'.sha1( serialize( $types ) );
			$query = Cache::read( $cacheKey );

			if( $query === false ) {
				$query = $this->Allocataire->searchQuery( $types, 'Cui' );

				// 1. Ajout des champs supplémentaires
				$query['fields'] = array_merge(
					$query['fields'],
					ConfigurableQueryFields::getModelsFields(
						array(
							$this->Cui,
							$this->Cui->Emailcui,
							$this->Cui->Partenairecui,
							$this->Cui->Partenairecui->Adressecui,
						)
					),
					// Champs nécessaires au traitement de la search
					array(
						'Cui.id',
						'Cui.personne_id',
						'Cui.faitle'
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
					$this->Cui->join( 'Entreeromev3', array( 'type' => 'LEFT OUTER' ) ),
					$this->Cui->join( 'Partenairecui', array( 'type' => $types['Partenairecui'] ) ),
					$this->Cui->join( 'Emailcui',
						array(
							'type' => $types['Emailcui'],
							'conditions' => array(
								"Emailcui.id IN ( ".$this->Cui->Emailcui->sq( $emailQuery )." )"
							)
						)
					),
					$this->Cui->Partenairecui->join( 'Adressecui', array( 'type' => $types['Adressecui'] ) )
				);

				// Ajout des tables spécifiques

				if( isset( $this->Cui->{$modelCuiDpt} ) ) {
					$foreignKey = strtolower($modelCuiDpt) . '_id';

					// Liste de modeles obligatoire pour un CG donné
					$modelList = array(
						$this->Cui->{$modelCuiDpt}
					);

					array_push(
						$query['joins'],
						$this->Cui->join( $modelCuiDpt, array( 'type' => $types[$modelCuiDpt] ) )
					);

					foreach ( $modelPotentiel as $modelName ){
						if ( isset( $this->Cui->{$modelCuiDpt}->{$modelName} ) ){
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

							array_push($modelList, $this->Cui->{$modelCuiDpt}->{$modelName});
							array_push(
								$query['joins'],
								$this->Cui->{$modelCuiDpt}->join( $modelName, array( 'type' => $types[$modelName],
									'conditions' => array(
										"{$modelName}.id IN ( ".$this->Cui->{$modelCuiDpt}->{$modelName}->sq( $subQuery )." )"
									)) )
							);
						}
					}

					$query['fields'] = array_merge(
						$query['fields'],
						ConfigurableQueryFields::getModelsFields( $modelList )
					);
				}

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

			$paths = array(
				'Cui.niveauformation',
				'Cui.inscritpoleemploi',
				'Cui.sansemploi',
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

			$beneficiairede = (string)Hash::get( $search, 'Cui.beneficiairede' );
			if ( $beneficiairede !== '' ) {
				switch ($beneficiairede) {
					case '0': $type = 'ass'; break;
					case '1': $type = 'aah'; break;
					case '2': $type = 'ata'; break;
					default: $type = 'rsa'; break;
				}

				$query['conditions']['beneficiaire_' . $type] = 1;
			}

			return $query;
		}
	}
?>