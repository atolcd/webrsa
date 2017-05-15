<?php
	/**
	 * Code source de la classe WebrsaRechercheFicheprescription93.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaRecherche', 'Model/Abstractclass' );
	App::uses( 'ConfigurableQueryFields', 'ConfigurableQuery.Utility' );

	/**
	 * La classe WebrsaRechercheFicheprescription93 ...
	 *
	 * @package app.Model
	 */
	class WebrsaRechercheFicheprescription93 extends AbstractWebrsaRecherche
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaRechercheFicheprescription93';

		/**
		 * Modèles utilisés par ce modèle
		 *
		 * @var array
		 */
		public $uses = array( 'Ficheprescription93', 'Allocataire' );

		/**
		 * Liste des clés de configuration utilisées par le moteur de recherche,
		 * pour vérification du paramétrage.
		 *
		 * @see checkParametrage()
		 *
		 * @var array
		 */
		public $keysRecherche = array(
			'ConfigurableQueryFichesprescriptions93.search.fields',
			'ConfigurableQueryFichesprescriptions93.search.innerTable',
			'ConfigurableQueryFichesprescriptions93.exportcsv'
		);

		/**
		 * Retourne le querydata de base à utiliser dans le moteur de recherche.
		 *
		 * @param array $types Les types de jointure alias => type
		 * @return array
		 */
		public function searchQuery( array $types = array() ) {
			$types += array(
				'Ficheprescription93' => 'LEFT OUTER',
				'Referent' => 'LEFT OUTER',
				'Actionfp93' => 'LEFT OUTER',
				'Adresseprestatairefp93' => 'LEFT OUTER',
				'Prestatairefp93' => 'LEFT OUTER',
				'Filierefp93' => 'LEFT OUTER',
				'Prestatairehorspdifp93' => 'LEFT OUTER',
				'Categoriefp93' => 'LEFT OUTER',
				'Thematiquefp93' => 'LEFT OUTER'
			);

			$cacheKey = Inflector::underscore( $this->Ficheprescription93->useDbConfig ).'_'.Inflector::underscore( $this->Ficheprescription93->alias ).'_'.Inflector::underscore( __FUNCTION__ ).'_'.sha1( serialize( $types ) );
			$query = Cache::read( $cacheKey );

			if( $query === false ) {
				$query = $this->Allocataire->searchQuery( $types );

				// Ajout des champs supplémentaires
				$query['fields'] = array_merge(
					array(
						'Dossier.id',
						'Ficheprescription93.id',
						'Personne.id',
						'Ficheprescription93.personne_id'
					),
					$query['fields'],
					ConfigurableQueryFields::getModelsFields(
						array(
							$this->Ficheprescription93,
							$this->Ficheprescription93->Actionfp93,
							$this->Ficheprescription93->Referent,
							$this->Ficheprescription93->Filierefp93,
							$this->Ficheprescription93->Prestatairehorspdifp93,
							$this->Ficheprescription93->Actionfp93->Adresseprestatairefp93->Prestatairefp93,
							$this->Ficheprescription93->Filierefp93->Categoriefp93,
							$this->Ficheprescription93->Filierefp93->Categoriefp93->Thematiquefp93
						)
					)
				);

				// Surcharge de certains suivant que l'action soit PDI ou hors PDI
				$query['fields']['Actionfp93.name'] = '( CASE WHEN "Thematiquefp93"."type" = \'horspdi\' THEN "Ficheprescription93"."actionfp93" ELSE "Actionfp93"."name" END ) AS "Actionfp93__name"';
				$query['fields']['Prestatairefp93.name'] = '( CASE WHEN "Thematiquefp93"."type" = \'horspdi\' THEN "Prestatairehorspdifp93"."name" ELSE "Prestatairefp93"."name" END ) AS "Prestatairefp93__name"';

				// Ajout des jointures supplémentaires
				$query['joins'] = array_merge(
					$query['joins'],
					array(
						$this->Ficheprescription93->Personne->join( 'Ficheprescription93', array( 'type' => $types['Ficheprescription93'] ) ),
						$this->Ficheprescription93->join( 'Actionfp93', array( 'type' => $types['Actionfp93'] ) ),
						$this->Ficheprescription93->join( 'Referent', array( 'type' => $types['Referent'] ) ),
						$this->Ficheprescription93->join( 'Filierefp93', array( 'type' => $types['Filierefp93'] ) ),
						$this->Ficheprescription93->join( 'Prestatairehorspdifp93', array( 'type' => $types['Prestatairehorspdifp93'] ) ),
						$this->Ficheprescription93->Actionfp93->join( 'Adresseprestatairefp93', array( 'type' => $types['Adresseprestatairefp93'] ) ),
						$this->Ficheprescription93->Actionfp93->Adresseprestatairefp93->join( 'Prestatairefp93', array( 'type' => $types['Prestatairefp93'] ) ),
						$this->Ficheprescription93->Filierefp93->join( 'Categoriefp93', array( 'type' => $types['Categoriefp93'] ) ),
						$this->Ficheprescription93->Filierefp93->Categoriefp93->join( 'Thematiquefp93', array( 'type' => $types['Thematiquefp93'] ) )
					)
				);

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
			$query = $this->Allocataire->searchConditions( $query, $search );

			$ficheprescription93Exists = Hash::get( $search, 'Ficheprescription93.exists' );
			if( !in_array( $ficheprescription93Exists, array( null, '' ), true ) ) {
				if( $ficheprescription93Exists ) {
					$query['conditions'][] = 'Ficheprescription93.id IS NOT NULL';
				}
				else {
					$query['conditions'][] = 'Ficheprescription93.id IS NULL';
				}
			}

			// 2.1 Ajout des filtres supplémentaires concernant l'action et le prestataire de la fiche de precription:
			$paths = array( 'Ficheprescription93.typethematiquefp93_id', 'Ficheprescription93.thematiquefp93_id', 'Ficheprescription93.categoriefp93_id', 'Ficheprescription93.filierefp93_id', 'Ficheprescription93.prestatairefp93_id', 'Ficheprescription93.actionfp93_id' );
			foreach( $paths as $path ) {
				$value = Hash::get( $search, $path );
				if( !empty( $value ) ) {
					if( isset( $this->Ficheprescription93->correspondances[$path] ) ) {
						$correspondance = $this->Ficheprescription93->correspondances[$path];
					}
					else {
						$correspondance = $path; // @todo pas utilisé ?
					}
					$query['conditions'][$correspondance] = $value;
				}
			}

			// 2.2 Ajout des filtres supplémentaires concernant l'action et le prestataire hors pdi de la fiche de precription:
			$paths = array( 'Ficheprescription93.actionfp93', 'Prestatairehorspdifp93.name' );
			foreach( $paths as $path ) {
				$value = Hash::get( $search, $path );
				if( !empty( $value ) ) {
					$query['conditions']["UPPER( {$path} ) LIKE"] = '%'.noaccents_upper( Sanitize::clean( $value, array( 'encode' => false ) ) ).'%';
				}
			}

			// 3. Recherche par valeur exacte.
			$paths = array(
				'Ficheprescription93.statut',
				'Ficheprescription93.benef_retour_presente',
				'Ficheprescription93.personne_recue',
				'Ficheprescription93.personne_retenue',
				'Ficheprescription93.personne_a_integre',
			);
			foreach( $paths as $path ) {
				$value = Hash::get( $search, $path );
				if( !in_array( $value, array( null, '' ), true ) ) {
					$query['conditions'][$path] = $value;
				}
			}

			// 4. Recherche par numéro de convention
			$value = suffix( Hash::get( $search, 'Actionfp93.numconvention' ) );
			if( !empty( $value ) ) {
				$query['conditions']['UPPER( Actionfp93.numconvention ) LIKE'] = strtoupper( $value ).'%';
			}

			// 5. Plages de dates
			$paths = array( 'Ficheprescription93.created', 'Ficheprescription93.date_signature', 'Ficheprescription93.rdvprestataire_date', 'Ficheprescription93.date_transmission', 'Ficheprescription93.date_retour', 'Ficheprescription93.df_action' );
			foreach( $paths as $path ) {
				$query['conditions'] = $this->conditionsDates( $query['conditions'], $search, $path );
			}

			// 6. Possède certaines dates
			$paths = array( 'Ficheprescription93.has_date_bilan_mi_parcours', 'Ficheprescription93.has_date_bilan_final', 'Ficheprescription93.has_date_retour' );
			foreach( $paths as $path ) {
				$value = Hash::get( $search, $path );
				if( !in_array( $value, array( null, '' ), true ) ) {
					$path = str_replace( '.has_', '.', $path );
					if( $value ) {
						$query['conditions'][] = "{$path} IS NOT NULL";
					}
					else {
						$query['conditions'][] = "{$path} IS NULL";
					}
				}
			}

			// 7. Référent (et sa structure liée) ayant réalisé la fiche
			$paths = array(
				'Ficheprescription93.structurereferente_id' => 'Referent.structurereferente_id',
				'Ficheprescription93.referent_id' => 'Ficheprescription93.referent_id'
			);
			foreach( $paths as $searchPath => $realPath ) {
				$value = suffix( Hash::get( $search, $searchPath ) );
				if( !empty( $value ) ) {
					$query['conditions'][] = array( $realPath => $value );
				}
			}

			// Condition sur le projet de ville territorial de la structure de la fiche de prescription
			$query['conditions'] = $this->conditionCommunautesr(
				$query['conditions'],
				$search,
				array( 'Ficheprescription93.communautesr_id' => 'Referent.structurereferente_id' )
			);

			return $query;
		}
	}
?>