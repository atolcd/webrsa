<?php
	/**
	 * Code source de l'interface WebrsaLogicAccessInterface.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model.Interface
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * L'interface WebrsaLogicAccessInterface concerne les modèles de logique métier
	 * implémentant les fonctions de contrôle d'accès
	 *
	 * @package app.Model.Interface
	 */
	interface WebrsaLogicAccessInterface
	{
		/**
		 * Permet d'obtenir le nécéssaire pour calculer les droits d'accès métier à une action
		 * 
		 * @param array $conditions
		 * @param array $params
		 * @return array
		 */
		public function getDataForAccess(array $conditions, array $params = array());
		
		/**
		 * Permet d'obtenir les paramètres à envoyer à WebrsaAccess pour une personne en particulier
		 * 
		 * @see WebrsaAccess::getParamsList
		 * @param integer $personne_id
		 * @param array $params - Liste des paramètres actifs
		 */
		public function getParamsForAccess($personne_id, array $params = array());
		
		/**
		 * Ajoute les virtuals fields pour permettre le controle de l'accès à une action
		 * 
		 * @param array $query
		 * @param array $params
		 * @return type
		 */
		public function completeVirtualFieldsForAccess(array $query = array(), array $params = array());
	}
?>