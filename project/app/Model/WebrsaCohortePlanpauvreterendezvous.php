<?php
	/**
	 * Code source de la classe WebrsaCohortePlanpauvreterendezvous.
	 *
	 * PHP 7.2
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'WebrsaCohortePlanpauvrete', 'Model' );

	/**
	 * La classe WebrsaCohortePlanpauvreterendezvous ...
	 *
	 * @package app.Model
	 */
	class WebrsaCohortePlanpauvreterendezvous extends WebrsaCohortePlanpauvrete
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaCohortePlanpauvreterendezvous';

		/**
		 * Autres modèles utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array(
			'Allocataire',
			'Personne'
		 );

		/**
		 * Liste des champs de formulaire à inserer dans le tableau de résultats
		 *
		 * @var array
		 */
		public $cohorteFields = array(
			'Personne.id' => array( 'type' => 'hidden' ),
			'Dossier.id' => array( 'type' => 'hidden'),
			'Rendezvous.personne_id' => array( 'type' => 'hidden' ),
			'Rendezvous.selection' => array( 'type' => 'checkbox' ),
			'Rendezvous.structurereferente_id' => array( 'type' => 'select' ),
			'Rendezvous.permanence_id' => array( 'type' => 'select' ),
			'Rendezvous.daterdv' => array( 'type' => 'date' ),
			'Rendezvous.heurerdv' => array('type' => 'time', 'timeFormat' => '24','minuteInterval'=> 5,  'empty' => true, 'hourRange' => array( 8, 19 )),
			'Rendezvous.objetrdv' => array( 'type' => 'textarea' ),
			'Rendezvous.commentairerdv' => array( 'type' => 'textarea' ),
		);

		/**
		 * Liste des conditions supplémentaires éventuelles pour les tests
		 * réalisés par la méthode WebrsaAbstractCohortesComponent::checkHiddenCohorteValues
		 *
		 * @var array
		 */
		public $checkHiddenCohorteValuesConditions = array(
		);

		/**
		 * Retourne l'id du statut de rdv selon le nom de la cohorte
		 * @param string nomCohorte
		 * @param bool isSave
		 * @return int id
		 */
		public function getStatutId($nomCohorte, $isSave = false) {
			$this->loadModel('Rendezvous');
			$config = Configure::read('ConfigurableQuery.Planpauvreterendezvous.' . $nomCohorte);

			$condition = $isSave === true ? $config['cohorte']['config']['save']['Statutrdv.code_statut'] : $config['cohorte']['config']['recherche']['Statutrdv.code_statut'];

			if($condition === '') return '';

			$statutRdv = $this->Rendezvous->Statutrdv->find('first', array(
				'recursive' => -1,
				'conditions' => array(
					'Statutrdv.code_statut' => $condition
				)
			) );
			return $statutRdv['Statutrdv']['id'];
		}



		/**
		 * Ajoute dans la requête une condition pour n'avoir que le dernier rendez vous
		 * @param array query
		 * @return array query
		 */
		public function onlyDernierRDV($query) {
			$this->loadModel('Rendezvous');
			foreach( $query['joins'] as $key => $join) {
				if( $join['alias'] == 'Rendezvous') {
					$query['joins'][$key]['conditions'] .= ' " AND Rendezvous.id IN (' . $this->Rendezvous->sqDernier('Personne.id') . ')';
					return $query;
				}
			}
		}

		/**
		 * Retourne l'id du type de rdv selon le nom de la cohorte
		 * @param string nomCohorte
		 * @param bool isSave
		 * @return int id
		 */
		public function getTypeRdvId($nomCohorte, $isSave = false) {
			$this->loadModel('Rendezvous');
			$config = Configure::read('ConfigurableQuery.Planpauvreterendezvous.' . $nomCohorte);

			$condition = $isSave === true ? $config['cohorte']['config']['save']['Typerdv.code_type'] : $config['cohorte']['config']['recherche']['Typerdv.code_type'];

			$typeRdv = $this->Rendezvous->Typerdv->find('first', array(
				'recursive' => -1,
				'conditions' => array(
						'Typerdv.code_type' => $condition
					)
			) );

			return $typeRdv['Typerdv']['id'];
		}

		/**
		 * Retourne le querydata de base, en fonction du département, à utiliser
		 * dans le moteur de recherche.
		 *
		 * @param array $types Les types de jointure alias => type
		 * @return array
		 */
		public function searchQuery( array $types = array() ) {
			$types += array(
				// INNER JOIN
				'Foyer' => 'INNER',
				'Prestation' => 'INNER',
				'Adressefoyer' => 'INNER',
				'Calculdroitrsa' => 'INNER',
				'Dossier' => 'INNER',
				'Situationdossierrsa' => 'INNER',
				'Adresse' => 'INNER',
				// LEFT OUTER JOIN
				'Orientstruct' => 'LEFT OUTER',
				'Rendezvous' => 'LEFT OUTER',
				'Typerdv' => 'LEFT OUTER',
			);
			$cacheKey = Inflector::underscore( $this->useDbConfig ).'_'.Inflector::underscore( $this->alias ).'_'.Inflector::underscore( __FUNCTION__ ).'_'.sha1( serialize( $types ) );
			$query = Cache::read( $cacheKey );
			if( $query === false ) {
				App::uses('WebrsaModelUtility', 'Utility');

				$query = $this->Allocataire->searchQuery( $types, 'Personne' );

				$query['fields']['Personne.id'] = 'DISTINCT ON ("Personne"."id") "Personne"."id" as "Personne__id"';
				// 1. Ajout des champs supplémentaires
				$query['fields'] = array_merge(
					$query['fields'],
					// Champs nécessaires au traitement de la search
					array(
						'Foyer.id',
						'Dossier.id',
					)
				);
				// 2. Jointure
 				$query['joins'] = array_merge(
					$query['joins'],
					array(
						$this->Personne->join('Historiquedroit'),
						$this->Personne->join('Orientstruct'),
						$this->Personne->join('Rendezvous'),
						$this->Personne->join('Contratinsertion')
					)
				);
				// 4. Conditions
				// SDD & DOV
				$query = $this->sdddov($query);

				Cache::write( $cacheKey, $query );
			}

			return $query;
		}

		/**
		 * Logique de sauvegarde de la cohorte
		 *
		 * @param type $data
		 * @param type $params
		 * @return boolean
		 */
		public function saveCohorte( array $data, array $params = array(), $user_id = null ) {
			$this->loadModel('Rendezvous');
			$typeRdv = $this->getTypeRdvId($params['nom_cohorte'], true);
			$statutRdv = $this->getStatutId($params['nom_cohorte'], true);

			foreach ( $data as $key => $value ) {
				// Si non selectionné, on retire tout
				if ( $value['Rendezvous']['selection'] === '0' ) {
					unset($data[$key]);
					continue;
				}

				// On ajoute les données nécessaire à l'enregistrement
				$data[$key]['Rendezvous']['typerdv_id'] = $typeRdv;
				if(!isset($data[$key]['Rendezvous']['statutrdv_id']) || $data[$key]['Rendezvous']['statutrdv_id'] === '')
				{
					$data[$key]['Rendezvous']['statutrdv_id'] = $statutRdv;
				}
				$data[$key]['Rendezvous']['personne_id'] = $value['Personne']['id'];
				$data[$key] = $data[$key]['Rendezvous'];

				// on supprime la selection car inutile pour l'enregistrement
				unset($data[$key]['selection']);
			}
			$this->Rendezvous->begin();
			$success = !empty($data) && $this->Rendezvous->saveAll($data, array('atomic' => false));

			if ($success) {
				$this->Rendezvous->commit();
			} else {
				$this->Rendezvous->rollback();
			}
			return $success;
		}

	}
?>