<?php
	/**
	 * Code source de la classe WebrsaCohorteTag.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaCohorte', 'Model/Abstractclass' );
	App::uses( 'ConfigurableQueryFields', 'ConfigurableQuery.Utility' );

	/**
	 * La classe WebrsaCohorteTag ...
	 *
	 * @package app.Model
	 */
	class WebrsaCohorteTag extends AbstractWebrsaCohorte
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaCohorteTag';

		/**
		 * Modèles utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array(
			'Allocataire',
			'Canton',
			'Tag'
		);
		
		/**
		 * Liste des champs de formulaire à inserer dans le tableau de résultats
		 * 
		 * @var array
		 */
		public $cohorteFields = array();
		
		/**
		 * Valeurs par défaut pour le préremplissage des champs du formulaire de cohorte
		 * array( 
		 *		'Mymodel' => array( 'Myfield' => 'MyValue' ) )
		 * )
		 * 
		 * @var array
		 */
		public $defaultValues = array();
		
		/**
		 * Complète les conditions du querydata avec le contenu des filtres de
		 * recherche.
		 *
		 * @param array $query
		 * @param array $search
		 * @return array
		 */
		public function searchConditions( array $query, array $search ) {
			if ( Hash::get($search, 'Requestmanager.name') ) {
				$result = ClassRegistry::init('Requestmanager')->find('first', 
					array( 
						'fields' => 'Requestmanager.json',
						'conditions' => array(
							'Requestmanager.id' => Hash::get($search, 'Requestmanager.name') 
						)
					)
				);
				
				$json = json_decode(Hash::get($result, 'Requestmanager.json'), true);
				
				/**
				 * Ignore selon valeur tag
				 */
				if ( Hash::get($search, 'Tag.valeurtag_id') ) {
					$sq = $this->Tag->sq(
						array(
							'alias' => 'tags',
							'fields' => 'tags.id',
							'joins' => array(
								array(
									'alias' => 'entites_tags',
									'table' => 'entites_tags',
									'conditions' => array(
										'entites_tags.tag_id = tags.id'
									),
									'type' => 'INNER'
								)
							),
							'conditions' => array(
								array('OR' => array(
									'tags.limite IS NULL',
									'tags.limite > NOW()',
								)),
								'tags.etat' => array( 'encours', 'traite' ),
								'tags.valeurtag_id' => Hash::get($search, 'Tag.valeurtag_id'),
								'OR' => array(
									array(
										'entites_tags.modele' => 'Personne',
										'entites_tags.fk_value = Personne.id'
									),
									array(
										'entites_tags.modele' => 'Foyer',
										'entites_tags.fk_value = Foyer.id'
									),
								)
							),
							'limit' => 1
						)
					);
					$json['conditions'][] = "NOT EXISTS({$sq})";
				}				
				
				return $json;
			}
			
			$query = $this->Allocataire->searchConditions( $query, $search );
			
			/**
			 * Generateur de conditions
			 */
			$paths = array(
				'Prestation.rolepers'
			);
			
			// Fils de dependantSelect
			$pathsToExplode = array(
			);

			$pathsDate = array(
			);

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

			/**
			 * Conditions spéciales
			 */
			
			/**
			 * Ignore selon valeur tag
			 */
			if ( Hash::get($search, 'Tag.valeurtag_id') ) {
				$sq = $this->Tag->sq(
					array(
						'alias' => 'tags',
						'fields' => 'tags.id',
						'joins' => array(
							array(
								'alias' => 'entites_tags',
								'table' => 'entites_tags',
								'conditions' => array(
									'entites_tags.tag_id = tags.id'
								),
								'type' => 'INNER'
							)
						),
						'conditions' => array(
							array('OR' => array(
								'tags.limite IS NULL',
								'tags.limite > NOW()',
							)),
							'tags.etat' => array( 'encours', 'traite' ),
							'tags.valeurtag_id' => Hash::get($search, 'Tag.valeurtag_id'),
							'OR' => array(
								array(
									'entites_tags.modele' => 'Personne',
									'entites_tags.fk_value = Personne.id'
								),
								array(
									'entites_tags.modele' => 'Foyer',
									'entites_tags.fk_value = Foyer.id'
								),
							)
						),
						'limit' => 1
					)
				);
				$query['conditions'][] = "NOT EXISTS({$sq})";
			}
			
			/**
			 * Couple/Isolé avec/sans enfant(s)
			 */
			$sqEnfants = $this->Tag->EntiteTag->Foyer->vfNbEnfants();
			$isolement = $this->Tag->EntiteTag->Foyer->sitfam_isole;
			
			$conditions = array();
			foreach( (array)Hash::get($search, 'Foyer.composition') as $value ) {
				switch ($value) {
					case 'cpl_sans_enf': $conditions[] = array(
							'NOT' => array( 'Foyer.sitfam' => $isolement ),
							"({$sqEnfants})" => 0
						);
						break;
					case 'cpl_avec_enf': $conditions[] = array(
							'NOT' => array( 'Foyer.sitfam' => $isolement ),
							"({$sqEnfants}) >" => 0
						);
						break;
					case 'iso_sans_enf': $conditions[] = array(
							'Foyer.sitfam' => $isolement,
							"({$sqEnfants})" => 0
						);
						break;
					case 'iso_avec_enf': $conditions[] = array(
							'Foyer.sitfam' => $isolement,
							"({$sqEnfants}) >" => 0
						);
						break;
				}
			}
			$query['conditions'][]['OR'] = $conditions;
			
			/**
			 * Nombre d'enfants
			 */
			if ( Hash::get($search, 'Foyer.nb_enfants') !== null ) {
				$operateur = Hash::get($search, 'Foyer.nb_enfants') === '0' ? '=' : '>=';
				$query['conditions']["({$sqEnfants}) {$operateur}"] = Hash::get($search, 'Foyer.nb_enfants');
			}
			
			/**
			 * Conditions allocataire hebergé
			 */
			if ( Hash::get($search, 'Adresse.heberge') !== null ) {
				$sq = $this->Tag->EntiteTag->Personne->DspRev->sqHeberge();
				$condition = array(
					'OR' => array(
						"Adresse.complideadr LIKE 'CHEZ%'",
						"Adresse.complideadr LIKE 'CZ%'",
						"Adresse.compladr LIKE 'CHEZ%'",
						"Adresse.compladr LIKE 'CZ%'",
						"Adresse.lieudist LIKE 'CHEZ%'",
						"Adresse.lieudist LIKE 'CZ%'",
						"EXISTS({$sq})"
					)
				);
				if ( Hash::get($search, 'Adresse.heberge') === '0' ) {
					$query['conditions'][]['NOT'] = $condition;
				}
				else {
					$query['conditions'][] = $condition;
				}
			}
			
			/*
			 * Condition montant RSA
			 */
			if (Hash::get($search, 'Detailcalculdroitrsa.mtrsavers') !== null) {
				list($min, $max) = explode('_', Hash::get($search, 'Detailcalculdroitrsa.mtrsavers'));
				$query['conditions'][] = "Detailcalculdroitrsa.mtrsavers BETWEEN {$min} AND {$max}";
			}
			
			return $query;
		}

		/**
		 * Logique de sauvegarde de la cohorte
		 * 
		 * @param type $data
		 * @param type $params
		 * @return boolean
		 */
		public function saveCohorte( array $data, array $params = array(), $user_id = null ) {
			foreach ( $data as $key => $value ) {
				if ( $value['Tag']['selection'] === '0' ) {
					unset($data[$key]);
				}
				else {
					// On récupère la bonne foreign key
					$data[$key]['EntiteTag']['fk_value'] = $value[$value['EntiteTag']['modele']]['id'];
					
					// On ne garde que l'essentiel
					$data[$key] = array('EntiteTag' => $data[$key]['EntiteTag'], 'Tag' => $data[$key]['Tag']);
					unset($data[$key]['Tag']['selection']);
				}
			}
			
			// Sauvegarde un par un
			$validationErrors = array();
			$success = true;
			$this->Tag->begin();
			foreach ($data as $key => $value) {
				$this->Tag->create($value['Tag']);
				$success = $this->Tag->save( null, array( 'atomic' => false ) ) && $success;
				$validationErrors['Tag'][$key] = $this->Tag->validationErrors;
				
				$value['EntiteTag']['tag_id'] = $this->Tag->id;
				$this->Tag->EntiteTag->create($value['EntiteTag']);
				$success = $this->Tag->EntiteTag->save( null, array( 'atomic' => false ) ) && $success;
				$validationErrors['EntiteTag'][$key] = $this->Tag->EntiteTag->validationErrors;
			}
			
			foreach ((array)Hash::filter($validationErrors) as $alias => $errors) {
				ClassRegistry::getObject($alias)->validationErrors = $errors;
			}
			
			if ($success) {
				$this->Tag->commit();
			} else {
				$this->Tag->rollback();
			}
			
			return $success;
		}
		
		/**
		 * Retourne le querydata de base, en fonction du département, à utiliser
		 * dans le moteur de recherche.
		 *
		 * @param array $types Les types de jointure alias => type
		 * @return array
		 */
		public function searchQuery( array $types = array() ) {
			$types += array(
				'Calculdroitrsa' => 'LEFT OUTER',
				'Foyer' => 'INNER',
				'Prestation' => 'LEFT OUTER',
				'Adressefoyer' => 'LEFT OUTER',
				'Dossier' => 'INNER',
				'Adresse' => 'LEFT OUTER',
				'Situationdossierrsa' => 'INNER',
				'Detaildroitrsa' => 'LEFT OUTER',
				'PersonneReferent' => 'LEFT OUTER',
				'Personne' => 'LEFT OUTER',
				'Structurereferente' => 'LEFT OUTER',
				'Referent' => 'LEFT OUTER',
				'DspRev' => 'LEFT OUTER',
				
				'Tag' => 'LEFT OUTER',
				'Valeurtag' => 'LEFT OUTER',
				'Categorietag' => 'LEFT OUTER',
				'Detailcalculdroitrsa' => 'LEFT OUTER',
			);

			$cacheKey = Inflector::underscore( $this->useDbConfig ).'_'.Inflector::underscore( $this->alias ).'_'.Inflector::underscore( __FUNCTION__ ).'_'.sha1( serialize( $types ) );
			$query = Cache::read( $cacheKey );

			if( $query === false ) {
				$query = $this->Allocataire->searchQuery( $types, 'Dossier' );

				// 1. Ajout des champs supplémentaires
				$query['fields'] = array_merge(
					$query['fields'],
					ConfigurableQueryFields::getModelsFields(
						array(
							$this->Tag,
							$this->Tag->EntiteTag,
							$this->Tag->Valeurtag,
							$this->Tag->Valeurtag->Categorietag,
							$this->Tag->EntiteTag->Personne->DspRev,
							$this->Tag->EntiteTag->Foyer->Dossier->Detaildroitrsa->Detailcalculdroitrsa,
						)
					),
					// Champs nécessaires au traitement de la search
					array(
						'Personne.id',
						'Foyer.id',
						'Dossier.id',
						'Foyer.nb_enfants' => '('.$this->Tag->EntiteTag->Foyer->vfNbEnfants().') AS "Foyer__nb_enfants"',
					)
				);

				// 2. Jointures
				$joinDsp = $this->Tag->EntiteTag->Personne->join('DspRev', array('type' => $types['DspRev']));
				$sqDsp = $this->Tag->EntiteTag->Personne->DspRev->sq(
					array(
						'alias' => 'dsps_revs',
						'table' => 'dsps_revs',
						'fields' => 'dsps_revs.id',
						'conditions' => array(
							'dsps_revs.personne_id = Personne.id'
						),
						'order' => array(
							'dsps_revs.created' => 'DESC'
						),
						'limit' => 1
					)
				);
				$joinDsp['conditions'] = array(
					$joinDsp['conditions'],
					"DspRev.id IN ($sqDsp)"
				);
				
				$query['joins'] = array_merge(
					$query['joins'],
					array(
						$joinDsp,
						$this->Tag->EntiteTag->Foyer->Dossier->Detaildroitrsa->join('Detailcalculdroitrsa', array('type' => $types['Detailcalculdroitrsa']))
					)
				);

				Cache::write( $cacheKey, $query );
			}

			return $query;
		}
	}
?>