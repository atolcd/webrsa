<?php
	/**
	 * Code source de la classe WebrsaFoyerspiecesjointe.
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
	 * La classe WebrsaFoyerspiecesjointe possède la logique métier web-rsa
	 *
	 * @package app.Model
	 */
	class WebrsaFoyerpiecejointe extends WebrsaAbstractLogic implements WebrsaLogicAccessInterface
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaFoyerpiecejointe';

		/**
		 * Les modèles qui seront utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array('Foyerpiecejointe');

		/**
		 * Ajoute les virtuals fields pour permettre le controle de l'accès à une action
		 *
		 * @param array $query
		 * @return type
		 */
		public function completeVirtualFieldsForAccess(array $query = array(), array $params = array()) {
			$departement = (integer)Configure::read('Cg.departement');
			$modelDepartement = 'Foyerpiecejointe'.$departement;
			$fields = array();

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
				'alias' => 'Foyerpiecejointe',
				'fields' => array(
					'Foyerpiecejointe.id',
					'Foyerpiecejointe.foyer_id',
					'Foyerpiecejointe.created',
					'User.username',
					'Fichiermodule.name',
					'Categoriepiecejointe.nom',
					'Foyerpiecejointe.id',
					'Foyerpiecejointe.id',
				),
				'conditions' => $conditions,
				'contain' => false,
			);

			return $this->Foyerpiecejointe->find('all', $this->completeVirtualFieldsForAccess($query, $params));

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