<?php
	/**
	 * Code source de la classe WebrsaRendezvous.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	App::uses('WebrsaAbstractLogic', 'Model');
	App::uses('WebrsaLogicAccessInterface', 'Model/Interface');
	App::uses('WebrsaRendezvous', 'Model');

	/**
	 * La classe WebrsaRendezvous possède la logique métier web-rsa
	 *
	 * @package app.Model
	 */
	class WebrsaPlanpauvreterendezvous extends WebrsaRendezvous
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaPlanpauvreterendezvous';

		/**
		 * Permet d'obtenir le nécéssaire pour calculer les droits d'accès métier à une action
		 *
		 * @param array $conditions
		 * @return array
		 */
		public function getDataForAccess(array $conditions, array $params = array()) {
			$conditions['Rendezvous.id'] = $conditions['Planpauvreterendezvous.id'];
			unset($conditions['Planpauvreterendezvous.id']);
			parent::getDataForAccess($conditions, $params);
		}
	}