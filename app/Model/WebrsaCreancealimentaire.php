<?php
	/**
	 * Code source de la classe WebrsaCreancealimentaire.
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
	 * La classe WebrsaCreancealimentaire possède la logique métier web-rsa
	 *
	 * @package app.Model
	 */
	class WebrsaCreancealimentaire extends WebrsaAbstractLogic implements WebrsaLogicAccessInterface
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaCreancealimentaire';

		/**
		 * Les modèles qui seront utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array('Creancealimentaire');

		/**
		 * Ajoute les virtuals fields pour permettre le controle de l'accès à une action
		 *
		 * @param array $query
		 * @return type
		 */
		public function completeVirtualFieldsForAccess(array $query = array(), array $params = array()) {
			$departement = (integer)Configure::read('Cg.departement');
			$modelDepartement = 'Creancealimentaire'.$departement;
			$fields = array(

			);

			if (isset($this->Creancealimentaire->{$modelDepartement})) {
				if (!isset($query['joins'])) {
					$query['joins'] = array();
				}
				if (WebrsaModelUtility::findJoinKey($modelDepartement, $query) === false) {
					$query['joins'][] = $this->Cui->join($modelDepartement);
				}

				$fields[] = $modelDepartement.'.cui_id';

			}

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
					'Creancealimentaire.id',
					'Creancealimentaire.personne_id',
					'Personne.id',
				),
				'conditions' => $conditions,
				'joins' => array(
					$this->Creancealimentaire->join('Personne')
				),
				'contain' => false,
				'order' => array(
					'Creancealimentaire.created' => 'DESC',
					'Creancealimentaire.id' => 'DESC',
				)
			);

			$results = $this->Creancealimentaire->find('all', $this->completeVirtualFieldsForAccess($query, $params));
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
			return false;
		}

	}