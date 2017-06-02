<?php
	/**
	 * Code source de la classe Indicateursuivi.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );
	App::uses( 'Sanitize', 'Utility' );

	/**
	 * La classe Indicateursuivi ...
	 *
	 * @package app.Model
	 */
	class Indicateursuivi extends AppModel
	{
		public $name = 'Indicateursuivi';
		public $useTable = false;
		public $actsAs = array('Conditionnable');

		public function search( $mesCodesInsee, $filtre_zone_geo, $params ) {
			$conditions = array();

			$Dossier = ClassRegistry::init( 'Dossier' );
			$Informationpe = ClassRegistry::init( 'Informationpe' );

			$conditions[] = $this->conditionsAdresse( $conditions, $params, $filtre_zone_geo, $mesCodesInsee );
			$conditions[] = $this->conditionsDernierDossierAllocataire( $conditions, $params );
			$conditions[] = $this->conditionsPersonneFoyerDossier( $conditions, $params );

			// Filtre par orientation
			if( isset($params['Orientstruct']['structurereferente_id']) && !empty($params['Orientstruct']['structurereferente_id']) ) {
				$structurereferente_id = $params['Orientstruct']['structurereferente_id'];
				$structurereferente_id = explode('_', $structurereferente_id);
				$conditions[] = 'Orientstruct.structurereferente_id = '.$structurereferente_id[1];
			}

			if( isset($params['Orientstruct']['referent_id']) && !empty($params['Orientstruct']['referent_id']) ) {
				$conditions[] = 'Orientstruct.referent_id = '.$params['Orientstruct']['referent_id'];
			}

			// Filtre par chargé d'évaluation
			if( isset($params['Propoorientationcov58']['structureorientante_id']) && !empty($params['Propoorientationcov58']['structureorientante_id']) ) {
				$conditions[] = 'Propoorientationcov58.structureorientante_id = '.suffix( $params['Propoorientationcov58']['structureorientante_id'] );
			}

			if( isset($params['Propoorientationcov58']['referentorientant_id']) && !empty($params['Propoorientationcov58']['referentorientant_id']) ) {
				$conditions[] = 'Propoorientationcov58.referentorientant_id = '.$params['Propoorientationcov58']['referentorientant_id'];
			}


			// Conditions de base pour qu'un allocataire puisse passer en EP
			$conditions['Prestation.rolepers'] = array( 'DEM' );
			$conditions['Calculdroitrsa.toppersdrodevorsa'] = '1';
			$conditions['Situationdossierrsa.etatdosrsa'] = $Dossier->Situationdossierrsa->etatOuvert();
			$conditions[] = 'Adressefoyer.id IN ( '.$Dossier->Foyer->Adressefoyer->sqDerniereRgadr01( 'Foyer.id' ).' )';

			// La dernière orientation du demandeur
			$conditions[] = array(
				'OR' => array(
					'Orientstruct.id IS NULL',
					'Orientstruct.id IN ( '.$Dossier->Foyer->Personne->Orientstruct->WebrsaOrientstruct->sqDerniere().' )'
				)
			);

			// Le dernier PersonneReferent
			$conditions[] = 'PersonneReferent.id IN ( '.$Dossier->Foyer->Personne->PersonneReferent->sqDerniere( 'Personne.id' ).' )';

			// Le dernier contrat du demandeur
			$conditions[] = array(
				'OR' => array(
					'Contratinsertion.id IS NULL',
					'Contratinsertion.id IN ( '.$Dossier->Foyer->Personne->Contratinsertion->WebrsaContratinsertion->sqDernierContrat().' )'
				)
			);

			// Le dossier d'EP le plus récent
			$conditions[] = array(
				'OR' => array(
					'Dossierep.id IS NULL',
					'Dossierep.id IN ( '.$Dossier->Foyer->Personne->Dossierep->sqDernierPassagePersonne().' )'
				)
			);

			// La dernière information venant de Pôle Emploi, si celle-ci est une inscription
			$conditions[] = array(
				'OR' => array(
					'Informationpe.id IS NULL',
					'Informationpe.id IN ( '.$Informationpe->sqDerniere().' )'
				)
			);
			
			$query = array(
				'fields' => array(
					'Dossier.dtdemrsa',
					'Dossier.matricule',
					'Dossier.id',
					$Dossier->Foyer->Personne->sqVirtualField( 'nom_complet' ),
// 					'Personne.qual',
// 					'Personne.nom',
// 					'Personne.prenom',
					'Personne.dtnai',
					'Personne.id',
					'Foyer.id',
					'Prestation.rolepers',
					'Dossier.numdemrsa',
					'Adresse.numvoie',
					'Adresse.libtypevoie',
					'Adresse.nomvoie',
					'Adresse.compladr',
					'Adresse.codepos',
					'Adresse.nomcom',
					'Orientstruct.id',
					'Orientstruct.date_valid',
					'Orientstruct.rgorient',
					$Dossier->Foyer->Personne->Dossiercov58->Propoorientationcov58->Referentorientant->sqVirtualField( 'nom_complet' ),
					str_replace( 'Referent', 'Referentunique', $Dossier->Foyer->Personne->Referent->sqVirtualField( 'nom_complet' ) ),
					'Contratinsertion.dd_ci',
					'Contratinsertion.df_ci',
					'Contratinsertion.rg_ci',
					'Historiqueetatpe.etat',
					'Historiqueetatpe.date',
// 					'( CASE WHEN "Historiqueetatpe"."etat" = \'inscription\' THEN "Historiqueetatpe"."date" ELSE NULL END ) AS "Historiqueetatpe__date"',
					'Commissionep.dateseance',
					'Dossierep.themeep'
				),
				'recursive' => -1,
				'joins' => array(
					$Dossier->join( 'Foyer', array( 'type' => 'INNER' ) ),
					$Dossier->join( 'Situationdossierrsa', array( 'type' => 'INNER' ) ),
					$Dossier->join( 'Detaildroitrsa', array( 'type' => 'INNER' ) ),
					$Dossier->Foyer->join( 'Personne', array( 'type' => 'INNER' ) ),
					$Dossier->Foyer->join( 'Adressefoyer', array( 'type' => 'INNER' ) ),
					$Dossier->Foyer->Adressefoyer->join( 'Adresse', array( 'type' => 'INNER' ) ),
					$Dossier->Foyer->Personne->join( 'Prestation', array( 'type' => 'INNER' ) ),
					$Dossier->Foyer->Personne->join( 'Contratinsertion', array( 'type' => 'LEFT OUTER' ) ),
					$Dossier->Foyer->Personne->join( 'Orientstruct', array( 'type' => 'LEFT OUTER' ) ),
					$Dossier->Foyer->Personne->join( 'Dossiercov58', array( 'type' => 'LEFT OUTER' ) ),
					$Dossier->Foyer->Personne->Dossiercov58->join( 'Propoorientationcov58', array( 'type' => 'LEFT OUTER' ) ),
					$Dossier->Foyer->Personne->Dossiercov58->Propoorientationcov58->join( 'Referentorientant', array( 'type' => 'LEFT OUTER' ) ),


					$Dossier->Foyer->Personne->join( 'PersonneReferent', array( 'type' => 'LEFT OUTER' ) ),
					array_words_replace( $Dossier->Foyer->Personne->PersonneReferent->join( 'Referent', array( 'type' => 'LEFT OUTER' ) ), array( 'Referent' => 'Referentunique' ) ),

					$Dossier->Foyer->Personne->join( 'Calculdroitrsa', array( 'type' => 'INNER' ) ),
					// Partie EP
					$Dossier->Foyer->Personne->join( 'Dossierep', array( 'type' => 'LEFT OUTER' ) ),
					$Dossier->Foyer->Personne->Dossierep->join( 'Passagecommissionep', array( 'type' => 'LEFT OUTER' ) ),
					$Dossier->Foyer->Personne->Dossierep->Passagecommissionep->join( 'Commissionep', array( 'type' => 'LEFT OUTER' ) ),

					$Informationpe->joinPersonneInformationpe(),
					$Informationpe->Historiqueetatpe->joinInformationpeHistoriqueetatpe(),
				),
				'order' => array( 'Personne.nom ASC' ),
				'limit' => 10,
				'conditions' => $conditions
			);
			return $query;
		}

		/**
		 * Retourne le querydata de base à utiliser dans le moteur de recherche.
		 *
		 * @fixme $type n'est pas utilisé
		 *
		 * @param array $types Les types de jointure alias => type
		 * @return array
		 */
		public function searchQuery( array $types = array() ) {
			$cacheKey = Inflector::underscore( $this->useDbConfig ).'_'.Inflector::underscore( $this->alias ).'_'.Inflector::underscore( __FUNCTION__ );
			$query = Cache::read( $cacheKey );

			if( $query === false ) {
				$Allocataire = ClassRegistry::init( 'Allocataire' );

				$query = $Allocataire->searchQuery( $types );

				$Personne = ClassRegistry::init( 'Personne' );

				// 1. La personne est le demandeur
				$query['conditions']['Prestation.rolepers'] = 'DEM';
				$query['fields'][] = 'Personne.nom_complet';

				// 2. Ajout des jointures sur le conjoint
				$replacements = array(
					'Personne' => 'Conjoint',
					'Prestation' => 'Prestationcjt',
				);

				$query['fields'] = Hash::merge(
					$query['fields'],
					array_words_replace(
						$Personne->fields(),
						$replacements
					)
				);

				$sq = $Personne->Prestation->sq(
					array(
						'alias' => 'prestationscjt',
						'fields' => array( 'prestationscjt.personne_id' ),
						'conditions' => array(
							'prestationscjt.natprest' => 'RSA',
							'prestationscjt.rolepers' => 'CJT'
						)
					)
				);

				$query['joins'][] = array_words_replace(
					$Personne->Foyer->join( 'Personne', array( 'type' => 'LEFT OUTER', 'conditions' => array( "Personne.id IN ( {$sq} )" ) ) ),
					$replacements
				);

				$query['fields'][] = str_replace( 'Personne', 'Conjoint', $Personne->sqVirtualField( 'nom_complet' ) );
				$query['fields'][] = str_replace( 'Personne', 'Conjoint', $Personne->sqVirtualField( 'nom_complet_court' ) );

				// 3. Ajout des jointures sur la dernière orientation
				$query['fields'] = Hash::merge(
					$query['fields'],
					$Personne->Orientstruct->fields()
				);
				$query['joins'][] = $Personne->join(
					'Orientstruct',
					array(
						'type' => 'LEFT OUTER',
						'conditions' => array(
							'Orientstruct.id IN ( '.$Personne->Orientstruct->WebrsaOrientstruct->sqDerniere().' )',
						)
					)
				);

				// 4. Ajout des jointures sur le dernier CER
				$query['fields'] = Hash::merge(
					$query['fields'],
					$Personne->Contratinsertion->fields()
				);
				$query['joins'][] = $Personne->join(
					'Contratinsertion',
					array(
						'type' => 'LEFT OUTER',
						'conditions' => array(
							'Contratinsertion.id IN ( '.$Personne->Contratinsertion->WebrsaContratinsertion->sqDernierContrat( 'Personne.id', true ).' )',
						)
					)
				);

				// 5. Ajout des jointures sur le référent orientant
				$query['fields'] = Hash::merge(
					$query['fields'],
					$Personne->Orientstruct->Propoorientationcov58nv->Referentorientant->fields()
				);
				$query['joins'][] = $Personne->Orientstruct->join( 'Propoorientationcov58nv', array( 'type' => 'LEFT OUTER' ) );
				$query['joins'][] = $Personne->Orientstruct->Propoorientationcov58nv->join( 'Referentorientant', array( 'type' => 'LEFT OUTER' ) );
				$query['fields'][] = $Personne->Orientstruct->Propoorientationcov58nv->Referentorientant->sqVirtualField( 'nom_complet' );

				// 6. Dernière information Pôle Emploi
				$Informationpe = ClassRegistry::init( 'Informationpe' );
				$query['fields'] = Hash::merge(
					$query['fields'],
					$Informationpe->fields()
				);
				$query['joins'][] = $Informationpe->joinPersonneInformationpe();

				$query['fields'] = Hash::merge(
					$query['fields'],
					$Informationpe->Historiqueetatpe->fields()
				);
				$query['joins'][] = $Informationpe->Historiqueetatpe->joinInformationpeHistoriqueetatpe();

				// 7. Ajout des jointures sur le dernier passage en EP et son dossier d'EP
				$query['fields'] = Hash::merge(
					$query['fields'],
					$Personne->Dossierep->fields()
				);
				$query['joins'][] = $Personne->join(
					'Dossierep',
					array(
						'type' => 'LEFT OUTER',
						'conditions' => array(
							'Dossierep.id IN ( '.$Personne->Dossierep->sqDernierPassagePersonne( 'Personne.id' ).' )',
						)
					)
				);

				$query['fields'] = Hash::merge(
					$query['fields'],
					$Personne->Dossierep->Passagecommissionep->fields()
				);
				$query['joins'][] = $Personne->Dossierep->join(
					'Passagecommissionep',
					array(
						'type' => 'LEFT OUTER',
						'conditions' => array(
							'Passagecommissionep.id IN ( '.$Personne->Dossierep->Passagecommissionep->sqDernier().' )',
						)
					)
				);

				$query['fields'] = Hash::merge(
					$query['fields'],
					$Personne->Dossierep->Passagecommissionep->Commissionep->fields()
				);
				$query['joins'][] = $Personne->Dossierep->Passagecommissionep->join( 'Commissionep', array( 'type' => 'LEFT OUTER' ) );

				$query['order'] = 'Personne.nom_complet_court';

				// Enregistrement dans le cache
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
			// 1. On complète les conditions de base de l'allocataire
			$Allocataire = ClassRegistry::init( 'Allocataire' );
			$query = $Allocataire->searchConditions( $query, $search );

			// 2. Conditions sur le référent orientant
			$paths = array( 'Referentorientant.structurereferente_id', 'Referentorientant.id' );
			foreach( $paths as $path ) {
				$value = suffix( Hash::get( $search, $path ) );
				if( !empty( $value ) ) {
					$query['conditions'][$path] = $value;
				}
			}

			return $query;
		}

		/**
		 * Retourne un querydata suivant les filtres renvoyés par le moteur de
		 * recherche.
		 *
		 * @todo AbstractSearch
		 *
		 * @param array $search
		 * @return array
		 */
		public function search58( array $search = array(), array $types = array() ) {
			$query = $this->searchQuery( $types );
			$query = $this->searchConditions( $query, $search );


			$Personne = ClassRegistry::init( 'Personne' );
			$query['fields'] = array(
				'Dossier.id',
				'Dossier.matricule',
				'Personne.nom_complet',
				'Personne.dtnai',
				'Adresse.numvoie',
				'Adresse.libtypevoie',
				'Adresse.nomvoie',
				'Adresse.complideadr',
				'Adresse.compladr',
				'Adresse.codepos',
				'Adresse.nomcom',
				str_replace( 'Personne', 'Conjoint', $Personne->sqVirtualField( 'nom_complet' ) ),
				'Dossier.dtdemrsa', //'Date ouverture de droits',
				$Personne->Dossiercov58->Propoorientationcov58->Referentorientant->sqVirtualField( 'nom_complet' ),
				'Orientstruct.date_valid', //'Date orientation (COV)',
				'Orientstruct.rgorient', //'Rang orientation (COV)',
				str_replace( 'Referent', 'Referentparcours', $Personne->PersonneReferent->Referent->sqVirtualField( 'nom_complet' ) ),
				'Contratinsertion.dd_ci', //'Date debut (CER)',
				'Contratinsertion.df_ci', //'Date fin (CER)',
				'Contratinsertion.rg_ci', //'Rang (CER)',
				'Historiqueetatpe.etat', //'Dernier état Pole Emploi',
				'Historiqueetatpe.date', //'Date inscription Pole Emploi',
				'Commissionep.dateseance', //'Date (EP)',
				'Dossierep.themeep', //'Motif (EP)'
			);

			return $query;
		}

		/**
		 * Retourne les options nécessaires au formulaire de recherche, au formulaire,
		 * aux impressions, ...
		 *
		 * @param array $params <=> array( 'allocataire' => true )
		 * @return array
		 */
		public function options( array $params = array() ) {
			$options = array();
			$params = $params + array( 'allocataire' => true );

			if( Hash::get( $params, 'allocataire' ) ) {
				$Allocataire = ClassRegistry::init( 'Allocataire' );

				$options = $Allocataire->options();
			}

			$Personne = ClassRegistry::init( 'Personne' );

			$Informationpe = ClassRegistry::init( 'Informationpe' );
			$options = Hash::merge(
				$options,
				$Personne->Dossierep->enums(),
				$Informationpe->Historiqueetatpe->enums()
			);

			return $options;
		}
	}
?>