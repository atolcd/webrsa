<?php
	/**
	 * Code source de la classe WebrsaCohortePlanpauvreteorientationsIsemploistockImprime.
	 *
	 * PHP 7.2
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'WebrsaCohortePlanpauvreteorientations', 'Model' );

	/**
	 * La classe WebrsaCohortePlanpauvreteorientationsIsemploistockImprime ...
	 *
	 * @package app.Model
	 */
	class WebrsaCohortePlanpauvreteorientationsIsemploistockImprime extends WebrsaCohortePlanpauvreteorientations
	{

		/**
		 * Liste des champs de formulaire à inserer dans le tableau de résultats
		 *
		 * @var array
		 */
		public $cohorteFields = array(
			'Personne.id' => array( 'type' => 'hidden' ),
			'Historiqueetatpe.id' => array( 'type' => 'hidden' ),
			'Orientstruct.origine' => array( 'type' => 'hidden' ),
			'Orientstruct.personne_id' => array( 'type' => 'hidden' ),
			'Orientstruct.id' => array( 'type' => 'hidden' ),
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
				'Orientstruct' => 'INNER',
			);

			$query = parent::searchQuery($types);

			//Qui ont une orientation
			$query = $this->avecOrientation($query);

			// Stock
			$query = $this->stock($query);

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
			$query = parent::searchConditions($query, $search);
			$query['conditions'] = $this->conditionsDates( $query['conditions'], $search, 'Historiquedroit.created' );
			$query['conditions'] = $this->conditionsDates( $query['conditions'], $search, 'Orientstruct.date_valid' );

			return $query;
		}
	}