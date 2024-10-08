<?php
	/**
	 * Code source de la classe.
	 *
	 * PHP 7.2
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'WebrsaCohortePlanpauvreterendezvous', 'Model' );

	/**
	 * La classe...
	 *
	 * @package app.Model
	 */
	class WebrsaCohortePlanpauvreterendezvousInfocolSecondRdvNouveaux extends WebrsaCohortePlanpauvreterendezvous
	{

		public function __construct($id = false, $table = null, $ds = null)
		{
			parent::__construct($id, $table, $ds);
			$this->cohorteFields = $this->addReferentCohorteFields();
		}

		/**
		 * Retourne le querydata de base, en fonction du département, à utiliser
		 * dans le moteur de recherche.
		 *
		 * @param array $types Les types de jointure alias => type
		 * @return array
		 */
		public function searchQuery( array $types = array(), $nouvelentrant = true ) {
			$query = parent::searchQuery($types, $nouvelentrant);
			$query = $this->onlyDernierRDV($query);

			// Champs supplémentaire
			$query['fields'] = array_merge(
				$query['fields'],
				array(
					'Permanence.libpermanence',
					'Rendezvous.id',
					'Rendezvous.daterdv',
					'Rendezvous.heurerdv'
				)
			);

			// Jointure supplémentaire
			$query['joins'] = array_merge(
				$query['joins'],
				array(
					$this->Personne->Rendezvous->Structurereferente->join('Permanence'),
				)
			);

			// Conditions
			// Gestion du type de RDV
			$query['conditions'][] = "Rendezvous.typerdv_id = " . $this->getTypeRdvId ('cohorte_infocol_second_rdv_nouveaux');
			$query['conditions'][] = "Rendezvous.statutrdv_id = " . $this->getStatutId('cohorte_infocol_second_rdv_nouveaux');
			// Et les personnes n'ont pas de second rendez-vous
			$query['conditions'][] = 'Personne.id NOT IN (SELECT personne_id FROM rendezvous WHERE typerdv_id = '.$this->getTypeRdvId ('cohorte_infocol_second_rdv_nouveaux', true).')';

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
			$params['nom_cohorte'] = 'cohorte_infocol_second_rdv_nouveaux';
			$success = parent::saveCohorte($data, $params, $user_id);

			return $success;
		}
	}