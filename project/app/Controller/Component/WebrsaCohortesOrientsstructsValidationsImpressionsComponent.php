<?php
	/**
	 * Code source de la classe WebrsaCohortesOrientsstructsValidationsImpressionsComponent.
	 *
	 * PHP 7.2
	 *
	 * @package app.Controller.Component
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'WebrsaCohortesOrientsstructsImpressionsComponent', 'Controller/Component' );

	/**
	 * La classe WebrsaCohortesOrientsstructsValidationsImpressionsComponent ...
	 *
	 * @package app.Controller.Component
	 */
	class WebrsaCohortesOrientsstructsValidationsImpressionsComponent extends WebrsaCohortesOrientsstructsImpressionsComponent
	{

		/**
		 * Components utilisés par ce component.
		 *
		 * @var array
		 */
		public $components = array('WebrsaOrientsstructsValidations');

		/**
		 * Retourne les options de type "enum", c'est à dire liées aux schémas des
		 * tables de la base de données.
		 *
		 * La mise en cache se fera dans ma méthode _options().
		 *
		 * @return array
		 */
		protected function _optionsEnums( array $params ) {
			$result = parent::_optionsEnums($params);
			$Controller = $this->_Collection->getController();

			// Customisations liées au workflow de validation
			$result = $this->WebrsaOrientsstructsValidations->customEnums($result, $Controller);

			return $result;
		}
	}
