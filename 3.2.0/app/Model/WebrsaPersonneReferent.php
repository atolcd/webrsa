<?php
	/**
	 * Code source de la classe WebrsaPersonneReferent.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	App::uses('WebrsaAbstractLogic', 'Model');
	App::uses('WebrsaLogicAccessInterface', 'Model/Interface');
	App::uses('WebrsaModelUtility', 'Utility');

	/**
	 * La classe WebrsaPersonneReferent possède la logique métier web-rsa
	 * 
	 * @package app.Model
	 */
	class WebrsaPersonneReferent extends WebrsaAbstractLogic implements WebrsaLogicAccessInterface
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaPersonneReferent';

		/**
		 * Les modèles qui seront utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array('PersonneReferent');

		/**
		 * Ajoute les virtuals fields pour permettre le controle de l'accès à une action
		 * 
		 * @param array $query
		 * @return type
		 */
		public function completeVirtualFieldsForAccess(array $query = array(), array $params = array()) {
			$fields = array(
				'dfdesignation' => 'PersonneReferent.dfdesignation',
			);
			
			return Hash::merge($query, array('fields' => array_values($fields)));
		}
		
		/**
		 * Permet d'obtenir le nécéssaire pour calculer les droits d'accès métier à une action
		 * 
		 * @param array $conditions
		 * @return array
		 */
		public function getDataForAccess(array $conditions, array $params = array()) {
			$query = array(
				'fields' => array(
					'PersonneReferent.id',
					'PersonneReferent.personne_id',
				),
				'conditions' => $conditions,
				'joins' => array(
					$this->PersonneReferent->join('Personne')
				),
				'contain' => false,
				'order' => array(
					'PersonneReferent.dddesignation' => 'DESC',
					'PersonneReferent.id' => 'DESC',
				)
			);
			
			$results = $this->PersonneReferent->find('all', $this->completeVirtualFieldsForAccess($query));
			return $results;
		}
		
		/**
		 * Permet d'obtenir les paramètres à envoyer à WebrsaAccess pour une personne en particulier
		 * 
		 * @see WebrsaAccess::getParamsList
		 * @param integer $personne_id
		 * @param array $params - Liste des paramètres actifs
		 */
		public function getParamsForAccess($personne_id, array $params = array()) {
			$results = array();
			
			if (in_array('ajoutPossible', $params)) {
				$results['ajoutPossible'] = $this->ajoutPossible($personne_id);
			}
			
			return $results;
		}
		
		/**
		 * Permet de savoir si il est possible d'ajouter un enregistrement
		 * 
		 * @param integer $personne_id
		 * @return boolean
		 */
		public function ajoutPossible($personne_id) {
			$query = array(
				'fields' => array('PersonneReferent.id'),
				'conditions' => array(
					'PersonneReferent.personne_id' => $personne_id,
					'PersonneReferent.dfdesignation IS NULL',
				),
				'contain' => false,
			);
			$result = $this->PersonneReferent->find('first', $query);
			
			return empty($result);
		}
	}