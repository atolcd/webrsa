<?php
	/**
	 * Code source de la classe WebrsaAdressefoyer.
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
	 * La classe WebrsaAdressefoyer possède la logique métier web-rsa
	 *
	 * @package app.Model
	 */
	class WebrsaAdressefoyer extends WebrsaAbstractLogic implements WebrsaLogicAccessInterface
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaAdressefoyer';

		/**
		 * Les modèles qui seront utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array('Adressefoyer');

		/**
		 * Ajoute les virtuals fields pour permettre le controle de l'accès à une action
		 * 
		 * @param array $query
		 * @return type
		 */
		public function completeVirtualFieldsForAccess(array $query = array(), array $params = array()) {
			$fields = array();
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
					'Adressefoyer.id',
					'Adressefoyer.foyer_id',
				),
				'conditions' => $conditions,
				'joins' => array(
					$this->Adressefoyer->join('Foyer')
				),
				'contain' => false,
				'order' => array(
					'Adressefoyer.rgadr' => 'ASC',
					'Adressefoyer.id' => 'DESC',
				)
			);
			
			$results = $this->Adressefoyer->find('all', $this->completeVirtualFieldsForAccess($query));
			return $results;
		}
		
		/**
		 * Permet d'obtenir les paramètres à envoyer à WebrsaAccess pour une personne en particulier
		 * 
		 * @see WebrsaAccess::getParamsList
		 * @param integer $foyer_id
		 * @param array $params - Liste des paramètres actifs
		 */
		public function getParamsForAccess($foyer_id, array $params = array()) {
			$results = array();
			
			if (in_array('ajoutPossible', $params)) {
				$results['ajoutPossible'] = $this->ajoutPossible($foyer_id);
			}
			
			return $results;
		}
		
		/**
		 * Permet de savoir si il est possible d'ajouter un enregistrement
		 * 
		 * @param integer $foyer_id
		 * @return boolean
		 */
		public function ajoutPossible($foyer_id) {
			return true;
		}
	}