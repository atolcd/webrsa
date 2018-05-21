<?php
	/**
	 * Code source de la classe WebrsaRechercheDsp.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaRecherche', 'Model/Abstractclass' );

	/**
	 * La classe WebrsaRechercheDsp ...
	 *
	 * @package app.Model
	 */
	class WebrsaRechercheDsp extends AbstractWebrsaRecherche
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaRechercheDsp';

		/**
		 * Liste des clés de configuration utilisées par le moteur de recherche,
		 * pour vérification du paramétrage.
		 *
		 * @see checkParametrage()
		 *
		 * @var array
		 */
		public $keysRecherche = array(
			'ConfigurableQueryDsps.search.fields',
			'ConfigurableQueryDsps.search.innerTable',
			'ConfigurableQueryDsps.exportcsv'
		);

		/**
		 * Retourne le querydata de base, en fonction du département, à utiliser
		 * dans le moteur de recherche.
		 *
		 * @param array $types Les types de jointure alias => type
		 * @return array
		 */
		public function searchQuery( array $types = array() ) {
			$types = array(
				'Calculdroitrsa' => 'LEFT OUTER',
				'Foyer' => 'INNER',
				'Prestation' => 'INNER',
				'Adressefoyer' => 'LEFT OUTER',
				'Dossier' => 'INNER',
				'Adresse' => 'LEFT OUTER',
				'Situationdossierrsa' => 'LEFT OUTER',
				'Detaildroitrsa' => 'LEFT OUTER'
				// TODO: tous les types
			);

			$cacheKey = Inflector::underscore( $this->useDbConfig ).'_'.Inflector::underscore( $this->alias ).'_'.Inflector::underscore( __FUNCTION__ ).'_'.sha1( serialize( $types ) );
			$query = Cache::read( $cacheKey );

			if( $query === false ) {
				$Allocataire = ClassRegistry::init( 'Allocataire' );
				$Dsp = ClassRegistry::init( 'Dsp' );

				$query = $Allocataire->searchQuery( $types, 'Personne' );

				// Ajout des spécificités du moteur de recherche
				$departement = (int)Configure::read( 'Cg.departement' );

				// INNER sur l'adressefoyer ?
				// LEFT OUTER ci-dessous
				// Memo
				// DspRev
				// Dsp
				// Modecontact
				// Deractromev3
				// Deractromev3Rev
				// Deractromev3__Familleromev3
				// Deractromev3Rev__Familleromev3
				// Deractromev3__Domaineromev3
				// Deractromev3Rev__Domaineromev3
				// Deractromev3__Metierromev3
				// Deractromev3Rev__Metierromev3
				// Deractromev3__Appellationromev3
				// Deractromev3Rev__Appellationromev3
				// Deractdomiromev3
				// Deractdomiromev3Rev
				// Deractdomiromev3Rev__Familleromev3
				// Deractdomiromev3__Domaineromev3
				// ...

				$query['fields'] = array_merge(
					array(
						'Dsp.id',
						'DspRev.id',
						'Personne.id',
						'Dossier.numdemrsa'
					),
					$query['fields'],
					ConfigurableQueryFields::getModelsFields(
						array(
							$Dsp,
							$Dsp->DspRev,
							$Dsp->Personne->Memo,
							$Dsp->Personne->Foyer->Modecontact
						)
					)
				);

				foreach( array_keys( $Dsp->schema() ) as $fieldName ) {
					$query['fields']["Donnees.{$fieldName}"] = $this->_searchCaseFieldDspDspRev( $fieldName, 'Dsp', 'DspRev' );
				}
				foreach( array_keys( (array)$Dsp->virtualFields ) as $fieldName ) {
					$query['fields']["Donnees.{$fieldName}"] = $this->_searchCaseFieldDspDspRev( $fieldName, 'Dsp', 'DspRev' );
				}

				// 2. Ajout d'autres champs virtuels
				// 2.1. Nombre d'enfants du foyer
				$query['fields']['Foyer.nbenfants'] = '( '.$Dsp->Personne->Foyer->vfNbEnfants().' ) AS "Foyer__nbenfants"';

				// 2.2 Nombre de fichiers liés
				$Fichiermodule = ClassRegistry::init( 'Fichiermodule' );
				$sqlDsp = $Fichiermodule->sqNbFichiersLies( $Dsp );
				$sqlDspRev = str_replace( '"Dsp"', '"DspRev"', $sqlDsp );
				$query['fields']['Donnees.nb_fichiers_lies'] = "( CASE WHEN \"Dsp\".\"id\" IS NOT NULL THEN ( {$sqlDsp} ) ELSE ( {$sqlDspRev} ) END ) AS \"Donnees__nb_fichiers_lies\"";

				// 2.3 Nature de la prestation
				$qdVirtualField = array(
					'fields' => array( "Detailcalculdroitrsa.natpf" ),
					'conditions' => array(
						'Detaildroitrsa.dossier_id = Dossier.id'
					),
					'contain' => false,
					'joins' => array(
						$Dsp->Personne->Foyer->Dossier->Detaildroitrsa->join( 'Detailcalculdroitrsa', array( 'type' => 'INNER' ) )
					)
				);
				$virtualField = '( '.$Dsp->Personne->Foyer->Dossier->vfListe( $qdVirtualField ).' ) AS "Detaildroitrsa__natpf"';
				$query['fields']['Detaildroitrsa.natpf'] = $virtualField;

				$query['joins'] = array_merge(
					$query['joins'],
					array(
						array(
							'table' => 'dsps_revs',
							'alias' => 'DspRev',
							'type' => 'LEFT OUTER',
							'foreignKey' => false,
							'conditions' => array(
								'DspRev.personne_id = Personne.id',
								'DspRev.id IN ( '.$Dsp->DspRev->sqDerniere( 'Personne.id' ).' )'
							)
						),
						array(
							'table' => 'dsps',
							'alias' => 'Dsp',
							'type' => 'LEFT OUTER',
							'foreignKey' => false,
							'conditions' => array(
								'Dsp.personne_id = Personne.id',
								'Dsp.personne_id NOT IN ( '.$Dsp->DspRev->sq(
										array(
											'alias' => 'tmp_dsps_revs2',
											'fields' => array(
												'tmp_dsps_revs2.personne_id'
											),
											'conditions' => array(
												'tmp_dsps_revs2.personne_id = Dsp.personne_id'
											)
										)
								).' )'
							)
						),
						$Dsp->Personne->Foyer->join(
							'Modecontact',
							array(
								'type' => 'LEFT OUTER',
								'conditions' => array(
									'Modecontact.id IN ( '.$Dsp->Personne->Foyer->Modecontact->sqDerniere( 'Foyer.id', array( 'Modecontact.autorutitel' => 'A' ) ).' )',
								)
							)
						),
						$Dsp->Personne->join(
							'Memo',
							array(
								'type' => 'LEFT OUTER',
								'conditions' => array(
									'Memo.id IN ( '.$Dsp->Personne->Memo->sqDernier().' )'
								)
							)
						),
					)
				);

				// 4. Champs et jointures ROME V2 (CG 66)
				if( Configure::read( 'Cg.departement' ) == 66 ) {
					foreach( $Dsp->WebrsaDsp->modelesRomeV2 as $alias ) {
						foreach( array_keys( $Dsp->{$alias}->schema() ) as $fieldName ) {
							$unwanted = ( $fieldName === 'id' || strpos( $fieldName, '_id' ) === strlen( $fieldName ) - 3 );
							if( !$unwanted ) {
								$query['fields']["{$alias}.{$fieldName}"] = $this->_searchCaseFieldDspDspRev( $fieldName, $alias, "{$alias}Rev", $alias );
							}
						}

						$query['joins'][] = $Dsp->join( $alias, array( 'type' => 'LEFT OUTER' ) );
						$query['joins'][] = array_words_replace(
							$Dsp->DspRev->join( $alias, array( 'type' => 'LEFT OUTER' ) ),
							array( $alias => "{$alias}Rev" )
						);
					}
				}

				// 5. Champs et jointures ROME V3
				if( Configure::read( 'Romev3.enabled' ) ) {
					$aliases = array_keys( ClassRegistry::init( 'Entreeromev3' )->belongsTo );
					foreach( $Dsp->WebrsaDsp->romev3LinkedModels as $modelAlias ) {
						$modelAliasRev = "{$modelAlias}Rev";
						$modelAliasDonnees = $modelAlias;

						// Ajout des jointures
						$query['joins'][] = $Dsp->join( $modelAlias, array( 'type' => 'LEFT OUTER' ) );
						$query['joins'][] = $Dsp->DspRev->join( $modelAliasRev, array( 'type' => 'LEFT OUTER' ) );

						foreach( $aliases as $alias ) {
							$modelAliasDonnees = "{$modelAlias}__".Inflector::underscore( $alias );
							$query['fields'][str_replace( '__', '.', $modelAliasDonnees )] = $this->_searchCaseFieldDspDspRev( 'name', "{$modelAlias}__{$alias}", "{$modelAliasRev}__{$alias}", $modelAliasDonnees );

							$query['joins'][] = array_words_replace(
								$Dsp->{$modelAlias}->join( $alias, array( 'type' => 'LEFT OUTER' ) ),
								array( $alias => "{$modelAlias}__{$alias}" )
							);

							$query['joins'][] = array_words_replace(
								$Dsp->DspRev->{$modelAliasRev}->join( $alias, array( 'type' => 'LEFT OUTER' ) ),
								array( $alias => "{$modelAliasRev}__{$alias}" )
							);
						}
					}
				}

				// 7. Champs virtuels modèles liés cases à cocher
				foreach( $Dsp->WebrsaDsp->getCheckboxes() as $linkedModelName => $params ) {
					$linkedFieldName = $params['name'];

					$fields = array();
					foreach( array( 'Dsp', 'DspRev' ) as $modelName ) {
						if( $modelName == 'DspRev' ) {
							$linkedModelName = "{$linkedModelName}Rev";
						}

						$foreignKey = Inflector::underscore( $modelName ).'_id';

						// Champ virtuel
						$qdVirtualField = array(
							'fields' => array( "{$linkedModelName}.{$linkedFieldName}" ),
							'conditions' => array(
								"{$linkedModelName}.{$foreignKey} = {$modelName}.id"
							),
							'contain' => false
						);

						$fields[$modelName] = $Dsp->Personne->{$modelName}->{$linkedModelName}->vfListe( $qdVirtualField );
					}

					$virtualField = "( CASE WHEN \"Dsp\".\"id\" IS NOT NULL THEN {$fields['Dsp']} ELSE {$fields['DspRev']} END ) AS \"Donnees__{$linkedFieldName}\"";
					$query['fields']["Donnees.{$linkedFieldName}"] = $virtualField; // INFO: à ajouter dans le query de base (?)
				}

				$query['conditions'][] = array(
					'OR' => array(
						'Dsp.id IS NOT NULL',
						'DspRev.id IS NOT NULL'
					)
				);

				Cache::write( $cacheKey, $query );
			}

			return $query;
		}

		/**
		 * Retourne un array de conditions permettant de s'assurer cibler à la fois
		 * le modèle Dsp et le modèle DspRev.
		 *
		 * @param array $condition
		 * @param array $aliases
		 * @return array
		 */
		protected function _searchConditionDspDspRev( array $condition, array $aliases = array( 'Dsp' => 'DspRev' ) ) {
			$return = array(
				'OR' => array(
					$condition,
					array_words_replace( $condition, $aliases )
				)
			);

			return $return;
		}

		/**
		 * Retourne une condition permettant d'obtenir un champ dans un modèle
		 * principal (si la Dsp existe) ou un modèle secondaire (si la DspRev
		 * existe).
		 *
		 * @param string $fieldName Nom du champ
		 * @param string $modelNamePrimary Nom du modèle principal
		 * @param string $modelNameSecondary Nom du modèle secondaire
		 * @param string $modelNameResult Nom du modèle de résultat
		 * @return string
		 */
		protected function _searchCaseFieldDspDspRev( $fieldName, $modelNamePrimary, $modelNameSecondary, $modelNameResult = 'Donnees' ) {
			return "( CASE WHEN \"Dsp\".\"id\" IS NOT NULL THEN \"{$modelNamePrimary}\".\"{$fieldName}\" ELSE \"{$modelNameSecondary}\".\"{$fieldName}\" END ) AS \"{$modelNameResult}__{$fieldName}\"";
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
			$Allocataire = ClassRegistry::init( 'Allocataire' );
			$Dsp = ClassRegistry::init( 'Dsp' );

			$query = $Allocataire->searchConditions( $query, $search );

			// Secteur d'activité et code métier, texte libre
			foreach( array( 'libsecactderact', 'libderact', 'libsecactdomi', 'libactdomi', 'libsecactrech', 'libemploirech' ) as $fieldName ) {
				if( !empty( $search['Dsp'][$fieldName] ) ) {
					$query['conditions'][] = $this->_searchConditionDspDspRev( array( "Dsp.{$fieldName} ILIKE" => "%{$search['Dsp'][$fieldName]}%" ) );
				}
			}

			$champs = array( 'nivetu', 'hispro' );
			if( Configure::read( 'Cg.departement' ) == 66 ) {
				$champs = array_merge( $champs, array( 'libsecactderact66_secteur_id', 'libderact66_metier_id', 'libsecactdomi66_secteur_id', 'libactdomi66_metier_id', 'libsecactrech66_secteur_id', 'libemploirech66_metier_id' ) );
			}
			foreach( $champs as $fieldName ) {
				if( !empty( $search['Dsp'][$fieldName] ) ) {
					$query['conditions'][] = $this->_searchConditionDspDspRev( array( "Dsp.{$fieldName}" => suffix( $search['Dsp'][$fieldName] ) ) );
				}
			}

			// Conditions modèles liés cases à cocher
			foreach( $Dsp->WebrsaDsp->searchCheckboxes as $linkedModelName ) {
				$linkedFieldName = $Dsp->WebrsaDsp->checkboxes['all'][$linkedModelName]['name'];
				$value = Hash::get( $search, "{$linkedModelName}.{$linkedFieldName}" );

				if( !empty( $value ) ) {
					// Dsp
					$tableName = Inflector::tableize( $linkedModelName );
					$sqDsp = $Dsp->{$linkedModelName}->sq(
							array(
								'alias' => $tableName,
								'fields' => array( "{$tableName}.id" ),
								'conditions' => array(
									"{$tableName}.dsp_id = Dsp.id",
									"{$tableName}.{$linkedFieldName}" => $value,
								),
								'contain' => false,
							)
					);

					// DspRev
					$tableName = Inflector::tableize( $linkedModelName ).'_revs';
					$linkedModelName = "{$linkedModelName}Rev";
					$sqDspRev = $Dsp->DspRev->{$linkedModelName}->sq(
							array(
								'alias' => $tableName,
								'fields' => array( "{$tableName}.id" ),
								'conditions' => array(
									"{$tableName}.dsp_rev_id = DspRev.id",
									"{$tableName}.{$linkedFieldName}" => $value,
								),
								'contain' => false,
							)
					);

					$query['conditions'][] = array(
						'OR' => array(
							"EXISTS( {$sqDsp} )",
							"EXISTS( {$sqDspRev} )",
						)
					);
				}
			}

			// Filtres concernant le catalogue ROME V3
			if( Configure::read( 'Romev3.enabled' ) ) {
				$conditionsDspRomeV3 = array();
				$aliases = array();

				foreach( $Dsp->WebrsaDsp->romev3LinkedModels as $alias ) {
					$aliases[$alias] = "{$alias}Rev";
					foreach( $Dsp->WebrsaDsp->romev3Fields as $fieldName ) {
						$field = "{$alias}.{$fieldName}";
						$value = suffix( Hash::get( $search, $field ) );
						if( !empty( $value ) ) {
							$conditionsDspRomeV3[$field] = $value;
						}
					}
				}

				if( !empty( $conditionsDspRomeV3 ) ) {
					$query['conditions'][] = $this->_searchConditionDspDspRev( $conditionsDspRomeV3, $aliases );
				}
			}

			return $query;
		}
	}
?>