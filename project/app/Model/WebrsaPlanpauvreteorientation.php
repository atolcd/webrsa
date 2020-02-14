<?php
	/**
	 * Code source de la classe WebrsaPlanpauvreteorientation.
	 *
	 * PHP 7.2
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	App::uses('WebrsaAbstractLogic', 'Model');
	App::uses('WebrsaLogicAccessInterface', 'Model/Interface');
	App::uses('WebrsaOrientstruct', 'Model');

	/**
	 * La classe WebrsaPlanpauvreteorientation possède la logique métier web-rsa
	 *
	 * @package app.Model
	 */
	class WebrsaPlanpauvreteorientation extends WebrsaOrientstruct
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
        public $name = 'WebrsaPlanpauvreteorientation';

        /**
		 * Permet d'obtenir le nécéssaire pour calculer les droits d'accès métier à une action
		 *
		 * @param array $conditions
		 * @return array
		 */
		public function getDataForAccess(array $conditions, array $params = array()) {
			$conditions['Orientstruct.id'] = $conditions['Planpauvreteorientation.id'];
			unset($conditions['Planpauvreteorientation.id']);
			parent::getDataForAccess($conditions, $params);
		}
	}