<?php
	/**
	 * Code source de la classe WebrsaTitressuivisannulationsreduction.
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
	 * La classe WebrsaTitresuiviannulationreduction possède la logique métier web-rsa
	 *
	 * @package app.Model
	 */
	class WebrsaTitressuivisannulationsreduction extends WebrsaAbstractLogic implements WebrsaLogicAccessInterface
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaTitressuivisannulationsreduction';

		/**
		 * Les modèles qui seront utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array('Titresuiviannulationreduction');

		/**
		 * Ajoute les virtuals fields pour permettre le controle de l'accès à une action
		 *
		 * @param array $query
		 * @return type
		 */
		public function completeVirtualFieldsForAccess(array $query = array(), array $params = array()) {
			$departement = (integer)Configure::read('Cg.departement');
			$modelDepartement = 'Titresuiviannulationreduction'.$departement;
			$fields = array(

			);

			if (isset($this->Titresuiviannulationreduction->{$modelDepartement})) {
				if (!isset($query['joins'])) {
					$query['joins'] = array();
				}
				if (WebrsaModelUtility::findJoinKey($modelDepartement, $query) === false) {
					$query['joins'][] = $this->Titresuiviannulationreduction->join($modelDepartement);
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
					'Titresuiviannulationreduction.id',
					'Titrecreancier.id',
					'Creance.id',
					'Creance.foyer_id',
					'Foyer.id',
					'Personne.id',
				),
				'conditions' => $conditions,
				'joins' => array(
					$this->Titresuiviannulationreduction->join('Titrecreancier'),
					$this->Titresuiviannulationreduction->Titrecreancier->join('Creance'),
					$this->Titresuiviannulationreduction->Titrecreancier->Creance->join('Foyer'),
					$this->Titresuiviannulationreduction->Titrecreancier->Creance->Foyer->join('Personne')
				),
				'contain' => false,
				'order' => array(
					'Titresuiviannulationreduction.dtaction' => 'ASC'
				)
			);

			$results = $this->Titresuiviannulationreduction->find('all', $this->completeVirtualFieldsForAccess($query, $params));

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
		 * Permet d'obtenir la liste des fichiers liés à un dossier pcg
		 *
		 * @param integer $titreAnnReduc_id
		 * @return array
		 */
		public function findFichiers( $titreAnnReduc_id ) {
			return $this->Titresuiviannulationreduction->Fichiermodule->find(
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
						'Fichiermodule.modele' => 'Titresuiviannulationreduction',
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