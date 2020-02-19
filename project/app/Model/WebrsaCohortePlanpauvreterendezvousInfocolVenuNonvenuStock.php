<?php
	/**
	 * Code source de la classe WebrsaCohortePlanpauvreterendezvousInfocolVenuNonvenuStock.
	 *
	 * PHP 7.2
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'WebrsaCohortePlanpauvreterendezvous', 'Model' );

	/**
	 * La classe WebrsaCohortePlanpauvreterendezvous ...
	 *
	 * @package app.Model
	 */
	class WebrsaCohortePlanpauvreterendezvousInfocolVenuNonvenuStock extends WebrsaCohortePlanpauvreterendezvous
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaCohortePlanpauvreterendezvousInfocolVenuNonvenuStock';

		/**
		 * Liste des champs de formulaire à inserer dans le tableau de résultats
		 *
		 * @var array
		 */
		public $cohorteFields = array(
			'Personne.id' => array( 'type' => 'hidden' ),
			'Dossier.id' => array( 'type' => 'hidden'),
			'Rendezvous.id' => array( 'type' => 'hidden' ),
			'Rendezvous.selection' => array( 'type' => 'checkbox' ),
		);

		public function __construct($id = false, $table = null, $ds = null)
		{
			parent::__construct($id, $table, $ds);
			// Ajout des boutons radios venu / non venu
			$this->loadModel('Rendezvous');
			$optionStatut = $this->Rendezvous->Statutrdv->find('list',
				array(
					'recursive' => -1,
					'fields' => array(
						'Statutrdv.id',
						'Statutrdv.libelle',
					),
					'conditions' => array(
						'OR' => array(
							array('Statutrdv.code_statut' => 'VENU'),
							array('Statutrdv.code_statut' => 'NONVENU')
						   )
					)
				)
			);
			$this->cohorteFields = array_merge(
				$this->cohorteFields,
				array(
					'Rendezvous.statutrdv_id' => array('type' => 'radio', 'options' => $optionStatut, 'legend' => false, 'value' => 1)
				)
			);
		}
		/**
		 * Retourne le querydata de base, en fonction du département, à utiliser
		 * dans le moteur de recherche.
		 *
		 * @param array $types Les types de jointure alias => type
		 * @return array
		 */
		public function searchQuery( array $types = array() ) {
			$query = parent::searchQuery($types);

			// Conditions pour les rendez-vous
			$query = $this->requeteParRendezvous ($query, 'cohorte_infocol_venu_nonvenu_stock');

			// Que les rendez-vous pas encore passés
			$query['conditions'][] = "Rendezvous.daterdv <= '" . date ('Y-m-d')."'";

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
			$params['nom_cohorte'] = 'cohorte_infocol_venu_nonvenu_stock';
			$success = parent::saveCohorte($data, $params, $user_id);

			return $success;
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

			return $query;
		}
	}
?>