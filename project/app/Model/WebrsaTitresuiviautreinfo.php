<?php
	/**
	 * Code source de la classe WebrsaTitresuiviautreinfo.
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
	 * La classe WebrsaTitresuiviautreinfo possède la logique métier web-rsa
	 *
	 * @package app.Model
	 */
	class WebrsaTitresuiviautreinfo extends WebrsaAbstractLogic implements WebrsaLogicAccessInterface
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaTitresuiviautreinfo';

		/**
		 * Les modèles qui seront utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array('Titresuiviautreinfo');

		/**
		 * Ajoute les virtuals fields pour permettre le controle de l'accès à une action
		 *
		 * @param array $query
		 * @return type
		 */
		public function completeVirtualFieldsForAccess(array $query = array(), array $params = array()) {
			$departement = Configure::read('Cg.departement');
			$modelDepartement = 'Titresuiviautreinfo'.$departement;
			$fields = array(

			);

			if (isset($this->Titresuiviautreinfo->{$modelDepartement})) {
				if (!isset($query['joins'])) {
					$query['joins'] = array();
				}
				if (WebrsaModelUtility::findJoinKey($modelDepartement, $query) === false) {
					$query['joins'][] = $this->Titresuiviautreinfo->join($modelDepartement);
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
                    'Titresuiviautreinfo.id',
					'Titrecreancier.id',
					'Creance.id',
					'Creance.foyer_id',
					'Foyer.id',
					'Personne.id',
				),
				'conditions' => $conditions,
				'joins' => array(
                    $this->Titresuiviautreinfo->join('Titrecreancier'),
					$this->Titresuiviautreinfo->Titrecreancier->join('Creance'),
					$this->Titresuiviautreinfo->Titrecreancier->Creance->join('Foyer'),
					$this->Titresuiviautreinfo->Titrecreancier->Creance->Foyer->join('Personne')
				),-+
				'contain' => false,
				'order' => array(
					'Titresuiviautreinfo.dtautreinfo' => 'ASC'
				)
			);

			$results = $this->Titresuiviautreinfo->find('all', $this->completeVirtualFieldsForAccess($query, $params));

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
			return $this->Titresuiviautreinfo->Fichiermodule->find(
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
						'Fichiermodule.modele' => 'Titresuiviautreinfo',
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