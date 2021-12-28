<?php
	/**
	 * Code source de la classe WebrsaCohorteReferentAjout.
	 *
	 * PHP 7.2
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'WebrsaCohorteReferent', 'Model' );

	/**
	 * La classe WebrsaCohorteReferentAjout ...
	 *
	 * @package app.Model
	 */
	class WebrsaCohorteReferentAjout extends WebrsaCohorteReferent
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaCohorteReferentAjout';

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
			'Personne.id' => array( 'type' => 'hidden' ),
			'PR.selection' => array( 'type' => 'checkbox' ),
			'PR.structurereferente_id' => array( 'type' => 'select', 'required' => true, 'empty' => true ),
			'PR.referent_id' => array( 'type' => 'select', 'required' => true, 'empty' => true),
			'PR.dddesignation' => array( 'type' => 'date' ),
		);

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

			// Modification de la jointure de PersonneReferent pour rendre l'URL finale moins longue (max 4096 caractères)
			foreach($query['joins'] as $key => $join) {
				if($join['alias'] == 'PersonneReferent') {
					$query['joins'][$key]['alias'] = 'PR';
					$query['joins'][$key]['conditions'] = array(
						'"PR"."personne_id" = "Personne"."id"'
					);
				}
				if($join['alias'] == 'Referentparcours') {
					$query['joins'][$key]['conditions'] = '"PR"."referent_id" = "Referentparcours"."id"';
				}
			}

			// Ajout de la condition pour ne pas avoir de référent
			$query['conditions'][] = array( 'Referentparcours.id IS NULL' );

			return $query;
		}
	}