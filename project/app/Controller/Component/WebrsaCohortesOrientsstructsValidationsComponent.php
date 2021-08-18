<?php
	/**
	 * Code source de la classe WebrsaCohortesOrientsstructsValidationsComponent.
	 *
	 * PHP 7.2
	 *
	 * @package app.Controller.Component
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'WebrsaAbstractCohortesOrientsstructsComponent', 'Controller/Component' );

	/**
	 * La classe WebrsaCohortesOrientsstructsValidationsComponent ...
	 *
	 * @package app.Controller.Component
	 */
	class WebrsaCohortesOrientsstructsValidationsComponent extends WebrsaAbstractCohortesOrientsstructsComponent
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
