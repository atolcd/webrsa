<?php
	/**
	 * Code source de la classe WebrsaRecourgracieux.
	 *
	 * PHP 7.2
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	App::uses('WebrsaAbstractLogic', 'Model');
	App::uses('WebrsaLogicAccessInterface', 'Model/Interface');
	App::uses('WebrsaModelUtility', 'Utility');

	/**
	 * La classe WebrsaRecourgracieux possède la logique métier web-rsa
	 *
	 * @package app.Model
	 */
	class WebrsaRecourgracieux extends WebrsaAbstractLogic implements WebrsaLogicAccessInterface
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaRecourgracieux';

		/**
		 * Les modèles qui seront utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array('Recourgracieux');

		/**
		 * Ajoute les virtuals fields pour permettre le controle de l'accès à une action
		 *
		 * @param array $query
		 * @return type
		 */
		public function completeVirtualFieldsForAccess(array $query = array(), array $params = array()) {
			$departement = (integer)Configure::read('Cg.departement');
			$modelDepartement = 'Recourgracieux'.$departement;
			$fields = array(
				'Foyer.id'
			);
			$query['contain'] = array (
				'Foyer'
			);
			if (isset($this->Recourgracieux->{$modelDepartement})) {
				if (!isset($query['joins'])) {
					$query['joins'] = array();
				}
				if (WebrsaModelUtility::findJoinKey($modelDepartement, $query) === false) {
					$query['joins'][] = $this->Recourgracieux->join($modelDepartement);
				}
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
					'Recourgracieux.id',
					'Recourgracieux.etat',
					'Foyer.id',
					'Personne.id',
				),
				'conditions' => $conditions,
				'joins' => array(
					$this->Recourgracieux->Foyer->join('Personne')
				),
				'contain' => false,
				'order' => array(
					'Recourgracieux.created' => 'ASC'
				)
			);

			$results = $this->Recourgracieux->find('all', $this->completeVirtualFieldsForAccess($query, $params));

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

			if ( in_array('ajoutPossible', $params) ) {
				$results['ajoutPossible'] = $this->ajoutPossible($personne_id, $params);
			}
			return $results;
		}

		/**
		 * Permet d'obtenir la liste des fichiers liés à un dossier pcg
		 *
		 * @param integer $titreAnnReduc_id
		 * @return array
		 */
		public function findFichiers( $titreAnnReduc_id ) {
			return $this->Recourgracieux->Fichiermodule->find(
				'all',
				array(
					'fields' => array(
						'Fichiermodule.id',
						'Fichiermodule.name',
						'Fichiermodule.fk_value',
						'Fichiermodule.modele',
						'Fichiermodule.cmspath',
						'Fichiermodule.mime',
						'Fichiermodule.created',
						'Fichiermodule.modified',
					),
					'conditions' => array(
						'Fichiermodule.modele' => 'Recourgracieux',
						'Fichiermodule.fk_value' => $titreAnnReduc_id,
					),
					'contain' => false
				)
			);
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