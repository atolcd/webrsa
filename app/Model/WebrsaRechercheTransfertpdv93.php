<?php
	/**
	 * Code source de la classe WebrsaRechercheTransfertpdv93.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaRecherche', 'Model/Abstractclass' );
	App::uses( 'ConfigurableQueryFields', 'ConfigurableQuery.Utility' );

	/**
	 * La classe WebrsaRechercheTransfertpdv93 ...
	 *
	 * @package app.Model
	 */
	class WebrsaRechercheTransfertpdv93 extends AbstractWebrsaRecherche
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaRechercheTransfertpdv93';

		/**
		 * Modèles utilisés par ce modèle
		 *
		 * @var array
		 */
		public $uses = array( 'Dossier', 'Allocataire', 'Transfertpdv93' );

		/**
		 * Liste des clés de configuration utilisées par le moteur de recherche,
		 * pour vérification du paramétrage.
		 *
		 * @see checkParametrage()
		 *
		 * @var array
		 */
		public $keysRecherche = array(
			'ConfigurableQueryTransfertspdvs93.search.fields',
			'ConfigurableQueryTransfertspdvs93.search.innerTable',
			'ConfigurableQueryTransfertspdvs93.exportcsv'
		);

		/**
		 * Retourne le querydata de base à utiliser dans le moteur de recherche.
		 *
		 * @param array $types Les types de jointure alias => type
		 * @return array
		 */
		public function searchQuery( array $types = array() ) {
			$types += array(
				'Adressefoyer' => 'INNER',
				'Transfertpdv93' => 'INNER',
				'VxOrientstruct' => 'INNER',
				'NvOrientstruct' => 'LEFT OUTER',
			);

			$cacheKey = Inflector::underscore( $this->Transfertpdv93->useDbConfig ).'_'.Inflector::underscore( $this->Transfertpdv93->alias ).'_'.Inflector::underscore( __FUNCTION__ ).'_'.sha1( serialize( $types ) );
			$query = Cache::read( $cacheKey );

			if( $query === false ) {
				$query = $this->Allocataire->searchQuery( $types, 'Dossier' );

				// 1°) On recherche la jointure sur Adressefoyer pour la remplacer car on ne veut que les rangs 02 et 03
				foreach( $query['joins'] as $key => $join ) {
					if( $join['alias'] === 'Adressefoyer' ) {
						$query['joins'][$key] = $this->Dossier->Foyer->join(
							'Adressefoyer',
							array(
								'type' => $types['Adressefoyer'],
								'conditions' => array(
									'Adressefoyer.rgadr' => array( '02', '03' )
								)
							)
						);
					}
				}

				// Préparation de la jointure de Transfertpdv93 avec NvOrientstruct et VxOrientstruct
				$joinTransfertpdv93 = array();
				$j1 = array_words_replace( $this->Dossier->Foyer->Personne->Orientstruct->join( 'NvTransfertpdv93', array( 'type' => 'INNER' ) ), array( 'NvTransfertpdv93' => 'Transfertpdv93', 'Orientstruct' => 'NvOrientstruct' ) );
				$j2 = array_words_replace( $this->Dossier->Foyer->Personne->Orientstruct->join( 'VxTransfertpdv93', array( 'type' => 'INNER' ) ), array( 'VxTransfertpdv93' => 'Transfertpdv93', 'Orientstruct' => 'VxOrientstruct' ) );
				$joinTransfertpdv93 = $j1;
				$joinTransfertpdv93['conditions'] = array(
					$joinTransfertpdv93['conditions'],
					$j2['conditions']
				);

				$query['joins'] = array_merge(
					$query['joins'],
					array(
						array_words_replace( $this->Dossier->Foyer->Personne->join( 'Orientstruct', array( 'type' => $types['VxOrientstruct'] ) ), array('Orientstruct' => 'VxOrientstruct')),
						array_words_replace( $this->Dossier->Foyer->Personne->join( 'Orientstruct', array( 'type' => $types['NvOrientstruct'] ) ), array('Orientstruct' => 'NvOrientstruct')),
						$joinTransfertpdv93,
						array_words_replace( $this->Dossier->Foyer->Personne->Orientstruct->VxTransfertpdv93->VxOrientstruct->join( 'Structurereferente', array( 'type' => 'INNER' ) ), array( 'Structurereferente' => 'VxStructurereferente' ) ),
						array_words_replace( $this->Dossier->Foyer->Personne->Orientstruct->NvTransfertpdv93->NvOrientstruct->join( 'Structurereferente', array( 'type' => 'INNER' ) ), array( 'Structurereferente' => 'NvStructurereferente' ) )
					)
				);

				// Ajout des champs supplémentaires
				$query['fields'] = array_merge(
					array(
						'Dossier.id',
						'Transfertpdv93.id',
						'Personne.id'
					),
					$query['fields'],
					ConfigurableQueryFields::getModelsFields(
						array(
							$this->Transfertpdv93,
							$this->Transfertpdv93->VxOrientstruct,
							$this->Transfertpdv93->NvOrientstruct
						)
					),
					array_words_replace(
						ConfigurableQueryFields::getModelsFields(
							array(
								$this->Transfertpdv93->VxOrientstruct->Structurereferente
							)
						),
						array( 'Structurereferente' => 'VxStructurereferente' )
					),
					array_words_replace(
						ConfigurableQueryFields::getModelsFields(
							array(
								$this->Transfertpdv93->NvOrientstruct->Structurereferente
							)
						),
						array( 'Structurereferente' => 'NvStructurereferente' )
					)

				);

				// TODO: VxAdressefoyer ?

				$query['conditions'][] = 'Adressefoyer.id = Transfertpdv93.vx_adressefoyer_id';
				$query['conditions'][] = '( DATE_PART( \'year\', "Transfertpdv93"."created" ) + 1 || \'-03-31\' )::DATE >= DATE_TRUNC( \'day\', NOW() )';

				$query['order'] = array(
					'Transfertpdv93.created DESC',
					'Dossier.numdemrsa ASC',
					'Dossier.id ASC',
					'Personne.nom ASC',
					'Personne.prenom ASC'
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

			// Filtre par date d'orientation (nouvelle, = date de transfert)
			$query['conditions'] = $this->conditionsDates( $query['conditions'], $search, 'NvOrientstruct.date_valid' );

			// Filtre par type d'orientation (qui devrait être la même entre VxOrientstruct et NvOrientstruct)
			$typeorient_id = Hash::get( $search, 'Orientstruct.typeorient_id' );
			if( !empty( $typeorient_id ) ) {
				$query['conditions'][] = array(
					'OR' => array(
						'VxOrientstruct.typeorient_id' => $typeorient_id,
						'NvOrientstruct.typeorient_id' => $typeorient_id,
					)
				);
			}

			// Filtre par structure référente source et/ou cible
			foreach( array( 'Vx', 'Nv' ) as $prefix ) {
				$modelName = "{$prefix}Orientstruct";
				$value = Hash::get( $search, "{$modelName}.structurereferente_id" );
				if( !empty($value) ) {
					$query['conditions']["{$modelName}.structurereferente_id"] = suffix($value);
				}
			}

			// Condition sur le projet insertion emploi territorial de la nouvelle structure d'orientation
			$query['conditions'] = $this->conditionCommunautesr(
				$query['conditions'],
				$search,
				array( 'NvOrientstruct.communautesr_id' => 'NvOrientstruct.structurereferente_id' )
			);

			return $query;
		}
	}
?>