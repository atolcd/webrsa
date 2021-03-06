<?php
	/**
	 * Code source de la classe WebrsaCreance.
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
	 * La classe WebrsaCreance possède la logique métier web-rsa
	 *
	 * @package app.Model
	 */
	class WebrsaCreance extends WebrsaAbstractLogic implements WebrsaLogicAccessInterface
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaCreance';

		/**
		 * Les modèles qui seront utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array('Creance','Titrecreancier');

		/**
		 * Ajoute les virtuals fields pour permettre le controle de l'accès à une action
		 *
		 * @param array $query
		 * @return type
		 */
		public function completeVirtualFieldsForAccess(array $query = array(), array $params = array()) {
			$departement = Configure::read('Cg.departement');
			$modelDepartement = 'Creance'.$departement;
			$fields = array();
			$query['fields'] = array_merge(
				$query['fields'],
				$this->Creance->Titrecreancier->fields()
			);
			$query['joins'][] = $this->Creance->join('Titrecreancier');

			return $query;
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
					'Creance.id',
					'Creance.foyer_id',
					'Creance.orgcre',
					'Creance.etat',
					'Titrecreancier.id',
					'Titrecreancier.etat',
					'Foyer.id',
				),
				'conditions' => $conditions,
				'joins' => array(
					$this->Creance->join('Foyer')
				),
				'contain' => false,
				'order' => array(
					'Creance.dtimplcre' => 'DESC'
				)
			);

			return $this->Creance->find('all', $this->completeVirtualFieldsForAccess($query, $params));

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
				$results['ajoutPossible'] = $this->ajoutPossible($personne_id, $params);
			}

			return $results;
		}

		/**
		 * Permet de savoir si il est possible d'ajouter un enregistrement
		 *
		 * @param integer $personne_id
		 * @param array $params
		 * @return boolean
		 */
		public function ajoutPossible($personne_id, array $params = array()) {
			return true;
		}

	}