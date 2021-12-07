<?php
	/**
	 * Code source de la classe WebrsaCohorteReferent.
	 *
	 * PHP 7.2
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaCohorte', 'Model/Abstractclass' );
	App::uses( 'ConfigurableQueryFields', 'ConfigurableQuery.Utility' );
	App::uses( 'WebrsaAbstractCohortesComponent', 'Controller/Component' );

	/**
	 * La classe WebrsaCohorteReferent ...
	 *
	 * @package app.Model
	 */
	class WebrsaCohorteReferent extends AbstractWebrsaCohorte
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaCohorteReferent';

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
		 * Retourne le querydata de base, en fonction du département, à utiliser
		 * dans le moteur de recherche.
		 *
		 * @param array $types Les types de jointure alias => type
		 * @return array
		 */
		public function searchQuery( array $types = array() ) {
			$cacheKey = Inflector::underscore( $this->useDbConfig ).'_'.Inflector::underscore( $this->alias ).'_'.Inflector::underscore( __FUNCTION__ );
			$query = Cache::read( $cacheKey );

			if( $query === false ) {
				$departement = Configure::read( 'Cg.departement' );

				$types += array(
					'Calculdroitrsa' => 'LEFT OUTER',
					'Foyer' => 'INNER',
					'Prestation' => $departement == 66 ? 'LEFT OUTER' : 'INNER',
					'Personne' => 'INNER',
					'Adressefoyer' => 'LEFT OUTER',
					'Dossier' => 'INNER',
					'Adresse' => 'LEFT OUTER',
					'Situationdossierrsa' => 'LEFT OUTER',
					'Detaildroitrsa' => 'LEFT OUTER'
				);
				$query = $this->Allocataire->searchQuery( $types, 'Personne' );
				$query['fields'] = array_merge(
					array(
						0 => 'Dossier.id',
						1 => 'Personne.id'
					),
					$query['fields']
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

			return $query;
		}

		/**
		 * Tentative de sauvegarde des modifications de dossier
		 *
		 * @param array $data
		 * @param array $params
		 * @param integer $user_id
		 * @return boolean
		 */
		public function saveCohorte( array $data, array $params = array(), $user_id = null ) {
			$saveData = array();
			$clotures = array();
			foreach( $data as $key => $value ) {
				if($value['PersonneReferent']['selection'] == 1) {
					// Récupération de l'id du référent
					$idReferent = explode('_', $value['PersonneReferent']['referent_id'])[1];

					// Si nous récupérons un id de personnereferent, on doit mettre une date de fin d'attribution
					if(isset($value['PersonneReferent']['id'])) {
						$clotures[] = array(
							'id' => $value['PersonneReferent']['id'],
							'dfdesignation' => date_cakephp_to_sql($value['PersonneReferent']['dddesignation'])
						);
					}
					$saveData[] = array(
						'personne_id' => $value['Personne']['id'],
						'structurereferente_id' => $value['PersonneReferent']['structurereferente_id'],
						'referent_id' => $idReferent,
						'dddesignation' => date_cakephp_to_sql($value['PersonneReferent']['dddesignation'])
					);
				}
			}

			$success = true;
			if(isset($clotures)) {
				$success = $this->PersonneReferent->saveMany($clotures);
			}

			$success = $this->PersonneReferent->saveMany($saveData) && $success;

			return $success;
		}
	}