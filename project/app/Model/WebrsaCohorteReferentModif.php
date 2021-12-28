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
			'Personne.id' => array( 'type' => 'hidden' ),
			'PR.id' => array( 'type' => 'hidden' ),
			'PR.selection' => array( 'type' => 'checkbox' ),
			'PR.structurereferente_id' => array( 'type' => 'select', 'required' => true, 'empty' => true ),
			'PR.referent_id' => array( 'type' => 'select', 'required' => true, 'empty' => true),
			'PR.dddesignation' => array( 'type' => 'date' ),
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

			$query['fields'] = array_merge(
				$query['fields'],
				array(
					'PR.id',
					'PR.structurereferente_id',
					'PR.referent_id'
				)
			);

			// Modifications des alias et conditions concernant la table PersonneReferent
			// pour rendre l'URL finale moins longue (max 4096 caractères)
			foreach ($query['joins'] as $key => $join){
				if($join['alias'] == 'PersonneReferent'){
					$query['joins'][$key]['alias'] = 'PR';
					$query['joins'][$key]['type'] = 'INNER';
					$query['joins'][$key]['conditions'] = '
						"PR"."personne_id" = "Personne"."id"
						AND "PR"."id" IN (
							SELECT "personnes_referents"."id"
							FROM personnes_referents
							WHERE
								"personnes_referents"."personne_id" = "Personne"."id"
							ORDER BY
								"personnes_referents"."dddesignation" DESC,
								"personnes_referents"."id" DESC
							LIMIT 1
						)';
				}
				if($join['alias'] == 'Referentparcours') {
					$query['joins'][$key]['conditions'] = '"PR"."referent_id" = "Referentparcours"."id"';
				}
			}

			// Tri des joins pour mettre les INNER en premier
			$innerList = array();
			$leftList = array();
			foreach($query['joins'] as $key => $join) {
				if(strpos($join['type'], 'INNER') !== false) {
					$innerList[] = $join;
				} else {
					$leftList[] = $join;
				}
			}
			$query['joins'] = array_merge($innerList, $leftList);

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