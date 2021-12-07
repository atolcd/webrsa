<?php
	/**
	 * Code source de la classe WebrsaCohorteReferentModif.
	 *
	 * PHP 7.2
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'WebrsaCohorteReferent', 'Model' );

	/**
	 * La classe WebrsaCohorteReferentModif ...
	 *
	 * @package app.Model
	 */
	class WebrsaCohorteReferentModif extends WebrsaCohorteReferent
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaCohorteReferentModif';

		/**
		 * Modèles utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array(
			'Personne',
			'Allocataire',
			'Structurereferente',
			'Referent',
			'PersonneReferent'
		);

		/**
		 * Liste des champs de formulaire à inserer dans le tableau de résultats
		 *
		 * @var array
		 */
		public $cohorteFields = array(
			'Dossier.id' => array( 'type' => 'hidden' ),
			'Personne.id' => array( 'type' => 'hidden' ),
			'PersonneReferent.id' => array( 'type' => 'hidden' ),
			'PersonneReferent.selection' => array( 'type' => 'checkbox' ),
			'PersonneReferent.structurereferente_id' => array( 'type' => 'select', 'required' => true, 'empty' => true ),
			'PersonneReferent.referent_id' => array( 'type' => 'select', 'required' => true, 'empty' => true),
			'PersonneReferent.dddesignation' => array( 'type' => 'date' ),
		);

		/**
		 * Retourne le querydata de base, en fonction du département, à utiliser
		 * dans le moteur de recherche.
		 *
		 * @param array $types Les types de jointure alias => type
		 * @return array
		 */
		public function searchQuery( array $types = array() ) {
			$query = parent::searchQuery( $types );

			$query['fields']['PersonneReferent.id'] = 'PersonneReferent.id';
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

			// Ajout de la condition pour avoir obligatoirement un référent
			$query['conditions'][] = array( 'Referentparcours.id IS NOT NULL' );

			return $query;
		}
	}