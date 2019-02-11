<?php
	/**
	 * Code source de la classe WebrsaTag.
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
	 * La classe WebrsaTag possède la logique métier web-rsa
	 *
	 * @package app.Model
	 */
	class WebrsaTag extends WebrsaAbstractLogic implements WebrsaLogicAccessInterface
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaTag';

		/**
		 * Les modèles qui seront utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array('Tag');

		/**
		 * Ajoute les virtuals fields pour permettre le controle de l'accès à une action
		 * 
		 * @param array $query
		 * @return type
		 */
		public function completeVirtualFieldsForAccess(array $query = array(), array $params = array()) {
			$fields = array(
				'Tag.etat',
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
					'Tag.id',
					'Personne.id',
					'Foyer.id',
				),
				'conditions' => $conditions,
				'joins' => array(
					$this->Tag->join('EntiteTag'),
					$this->Tag->EntiteTag->join('Personne'),
					$this->Tag->EntiteTag->join('Foyer'),
				),
				'contain' => false,
				'order' => array(
					'Tag.created' => 'DESC',
					'Tag.id' => 'DESC',
				)
			);
			
			$results = $this->Tag->find('all', $this->completeVirtualFieldsForAccess($query));
			return $results;
		}
		
		/**
		 * Permet d'obtenir les paramètres à envoyer à WebrsaAccess pour une personne en particulier
		 * 
		 * @see WebrsaAccess::getParamsList
		 * @param integer $id
		 * @param array $params - Liste des paramètres actifs + array(modele => Personne|Foyer)
		 */
		public function getParamsForAccess($id, array $params = array()) {
			$results = array();
			
			if (in_array('ajoutPossible', $params)) {
				$results['ajoutPossible'] = $this->ajoutPossible($id, $params);
			}
			
			return $results;
		}
		
		/**
		 * Permet de savoir si il est possible d'ajouter un enregistrement
		 * 
		 * @param integer $personne_id
		 * @param array $params - Liste des paramètres actifs + array(modele => Personne|Foyer)
		 * @return boolean
		 */
		public function ajoutPossible($personne_id, array $params = array()) {
			return true;
		}
	}