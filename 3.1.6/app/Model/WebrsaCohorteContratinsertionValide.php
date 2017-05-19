<?php
	/**
	 * Code source de la classe WebrsaCohorteContratinsertionValide.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'WebrsaCohorteContratinsertionNouveau', 'Model' );

	/**
	 * La classe WebrsaCohorteContratinsertionValide ...
	 *
	 * @package app.Model
	 */
	class WebrsaCohorteContratinsertionValide extends WebrsaCohorteContratinsertionNouveau
	{
		/**
		 * Retourne le querydata de base, en fonction du département, à utiliser
		 * dans le moteur de recherche.
		 *
		 * @param array $types Les types de jointure alias => type
		 * @return array
		 */
		public function searchQuery( array $types = array() ) {
			$types += array(
				'Cer93' => 'INNER',
				'Personne' => 'INNER',
				'Referent' => 'LEFT OUTER',
				'Foyer' => 'INNER',
				'Orientstruct' => 'LEFT OUTER',
				'Prestation' => 'INNER',
				'Dossier' => 'INNER',
				'Adressefoyer' => 'LEFT OUTER',
				'Adresse' => 'LEFT OUTER',
				'Typeorient' => 'LEFT OUTER',
				'Structurereferente' => 'LEFT OUTER',
				'Situationdossierrsa' => 'INNER',
				'Detaildroitrsa' => 'LEFT OUTER'
			);

			$query = parent::searchQuery( $types );

			$query['conditions'] = array(
				'Contratinsertion.decision_ci <>' => 'E',
				'Contratinsertion.decision_ci IS NOT NULL',
			);

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

			// Filtre par décision
			$decision_ci = Hash::get( $search, 'Contratinsertion.decision_ci' );
			if( !empty( $decision_ci ) ) {
				$query['conditions']['Contratinsertion.decision_ci'] = $decision_ci;
			}

			// Filtre par date de validation
			$query['conditions'] = $this->conditionsDate( $query['conditions'], $search, 'Contratinsertion.datevalidation_ci' );

			return $query;
		}
	}
?>