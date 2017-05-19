<?php
	/**
	 * Code source de la classe WebrsaRechercheBilanparcours66.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaRecherche', 'Model/Abstractclass' );
	App::uses( 'ConfigurableQueryFields', 'ConfigurableQuery.Utility' );

	/**
	 * La classe WebrsaRechercheBilanparcours66 ...
	 *
	 * @package app.Model
	 */
	class WebrsaRechercheBilanparcours66 extends AbstractWebrsaRecherche
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaRechercheBilanparcours66';

		/**
		 * Liste des clés de configuration utilisées par le moteur de recherche,
		 * pour vérification du paramétrage.
		 *
		 * @see checkParametrage()
		 *
		 * @var array
		 */
		public $keysRecherche = array(
			// FIXME: le bon modèle, maintenant ?
			'ConfigurableQueryContratsinsertion.search.fields',
			'ConfigurableQueryContratsinsertion.search.innerTable',
			'ConfigurableQueryContratsinsertion.exportcsv'
		);

		/**
		 * Modèles utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array(
			'Allocataire',
			'Bilanparcours66',
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
			$types += array(
				'Calculdroitrsa' => 'LEFT OUTER',
				'Foyer' => 'INNER',
				'Prestation' => 'LEFT OUTER',
				'Adressefoyer' => 'INNER',
				'Dossier' => 'INNER',
				'Adresse' => 'INNER',
				'Situationdossierrsa' => 'INNER',
				'Detaildroitrsa' => 'LEFT OUTER',
				'PersonneReferent' => 'LEFT OUTER',
				'Personne' => 'INNER',
				'Typeorient' => 'LEFT OUTER',
				'Structurereferente' => 'INNER',
				'Structurereferenteparcours' => 'LEFT OUTER',
				'Orientstruct' => 'LEFT OUTER',
				'Referent' => 'INNER',
				'Dossierep' => 'LEFT OUTER',
			);

			$cacheKey = Inflector::underscore( $this->useDbConfig ).'_'.Inflector::underscore( $this->alias ).'_'.Inflector::underscore( __FUNCTION__ ).'_'.sha1( serialize( $types ) );
			$query = Cache::read( $cacheKey );

			if( $query === false ) {
				$query = $this->Allocataire->searchQuery( $types, 'Bilanparcours66' );

				// 1. Ajout des champs supplémentaires
				$query['fields'] = array_merge(
					$query['fields'],
					ConfigurableQueryFields::getModelsFields(
						array(
							$this->Bilanparcours66,
							$this->Bilanparcours66->Referent,
							$this->Bilanparcours66->Personne->PersonneReferent,
							$this->Bilanparcours66->Personne->Dossierep,
							$this->Bilanparcours66->Structurereferente
						)
					),
					// Champs nécessaires au traitement de la search
					array(
						'Bilanparcours66.id',
						'Bilanparcours66.personne_id',
					)
				);

				// 2. Jointure
				$joinDossierep = $this->Bilanparcours66->Personne->join( 'Dossierep', array( 'type' => $types['Dossierep'] ) );
				$joinDossierep['conditions'] = '("Defautinsertionep66"."dossierep_id" = "Dossierep"."id") OR ("Saisinebilanparcoursep66"."dossierep_id" = "Dossierep"."id")';
				$query['joins'] = array_merge(
					$query['joins'],
					array(
						$this->Bilanparcours66->join( 'Structurereferente', array( 'type' => $types['Structurereferente'] ) ),
						$this->Bilanparcours66->join( 'Referent', array( 'type' => $types['Referent'] ) ),
						$this->Bilanparcours66->Structurereferente->join( 'Typeorient', array( 'type' => $types['Typeorient'] ) ),
						$this->Bilanparcours66->join( 'Orientstruct', array( 'type' => $types['Orientstruct'] ) ),
						$this->Bilanparcours66->join( 'Defautinsertionep66', array( 'type' => 'LEFT OUTER' ) ),
						$this->Bilanparcours66->join( 'Saisinebilanparcoursep66', array( 'type' => 'LEFT OUTER' ) ),
						$joinDossierep
					)
				);

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

			/**
			 * Generateur de conditions
			 */
			$paths = array(
				'Bilanparcours66.proposition',
				'Bilanparcours66.choixparcours',
				'Bilanparcours66.examenaudition',
				'Bilanparcours66.maintienorientation',
				'Bilanparcours66.structurereferente_id',
				'Bilanparcours66.positionbilan',
			);

			// Fils de dependantSelect
			$pathsToExplode = array(
				'Bilanparcours66.referent_id',
			);

			$pathsDate = array(
				'Bilanparcours66.datebilan'
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
			$hasmanifestation = Hash::get($search, 'Bilanparcours66.hasmanifestation');
			if (in_array($hasmanifestation, array('0','1'))) {
				$query['conditions'][] = '('
					. 'SELECT COUNT("manifestationsbilansparcours66"."id") '
					. 'FROM manifestationsbilansparcours66 '
					. 'WHERE "manifestationsbilansparcours66"."bilanparcours66_id" = "Bilanparcours66"."id"'
					. ') '.($hasmanifestation === '0' ? '=' : '>').' 0'
				;
			}

			return $query;
		}
	}
?>