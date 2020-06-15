<?php
	/**
	 * Code source de la classe WebrsaRechercheApreEligibilite.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'WebrsaRechercheApre', 'Model' );

	/**
	 * La classe WebrsaRechercheApreEligibilite ...
	 *
	 * @package app.Model
	 */
	class WebrsaRechercheApreEligibilite extends WebrsaRechercheApre
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaRechercheApreEligibilite';

		/**
		 * Liste des clés de configuration utilisées par le moteur de recherche,
		 * pour vérification du paramétrage.
		 *
		 * @see checkParametrage()
		 *
		 * @var array
		 */
		public $keysRecherche = array(
			'ConfigurableQueryApres.search_eligibilite.fields',
			'ConfigurableQueryApres.search_eligibilite.innerTable',
			'ConfigurableQueryApres.exportcsv_eligibilite'
		);

		/**
		 *
		 * @param array $types
		 * @return array
		 */
		public function searchQuery( array $types = array() ) {
			$departement = Configure::read( 'Cg.departement' );
			$types += array(
				'Calculdroitrsa' => ( $departement == 93 ? 'LEFT OUTER' : 'INNER' ),
				'Foyer' => 'INNER',
				'Prestation' => 'LEFT OUTER',
				'Adressefoyer' => 'LEFT OUTER',
				'Dossier' => 'INNER',
				'Adresse' => 'LEFT OUTER',
				'Situationdossierrsa' => 'LEFT OUTER',
				'Detaildroitrsa' => 'LEFT OUTER',
				'PersonneReferent' => 'LEFT OUTER',
				'Personne' => 'INNER',
				'Structurereferente' => 'LEFT OUTER',
				'Referent' => 'LEFT OUTER',
				$this->Apre->name => 'LEFT OUTER',
				'Aideapre66' => 'LEFT OUTER',
				'Typeaideapre66' => 'LEFT OUTER',
				'Themeapre66' => 'LEFT OUTER',
				'Relanceapre' => 'LEFT OUTER',
				'ApreComiteapre' => 'LEFT OUTER',
				'Comiteapre' => 'LEFT OUTER'
			);

			$cacheKey = Inflector::underscore( $this->useDbConfig ).'_'.Inflector::underscore( $this->alias ).'_'.Inflector::underscore( __FUNCTION__ ).'_'.sha1( serialize( $types ) );
			$query = Cache::read( $cacheKey );

			if( $query === false ) {
				$query = parent::searchQuery( $types );

				if( $departement == 93 ) {
					$this->Apre->Relanceapre->deepAfterFind = false;

					$query['fields'] = array_merge(
						$query['fields'],
						ConfigurableQueryFields::getModelsFields(
							array(
								$this->Apre->Relanceapre,
								$this->Apre->ApreComiteapre,
								$this->Apre->ApreComiteapre->Comiteapre
							)
						)
					);

					$query['joins'][] = $this->Apre->join(
						'Relanceapre',
						array(
							'type' => $types['Relanceapre'],
							'conditions' => array(
								'Relanceapre.id IN ( '.$this->Apre->Relanceapre->sqDerniere().' )'
							)
						)
					);

					$query['joins'][] = $this->Apre->join(
						'ApreComiteapre',
						array(
							'type' => $types['ApreComiteapre'],
							'conditions' => array(
								'ApreComiteapre.id IN ( '.$this->Apre->ApreComiteapre->sqDernierComiteApre().' )'
							)
						)
					);

					$query['joins'][] = $this->Apre->ApreComiteapre->join( 'Comiteapre', array( 'type' => $types['Comiteapre'] ) );

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
			$query = parent::searchConditions( $query, $search );

			$eligibiliteapre = (string)Hash::get( $search, 'Apre.eligibiliteapre' );
			if( $eligibiliteapre !== '' ) {
				$query['conditions']['Apre.eligibiliteapre'] = $eligibiliteapre;
			}

			return $query;
		}
	}
?>