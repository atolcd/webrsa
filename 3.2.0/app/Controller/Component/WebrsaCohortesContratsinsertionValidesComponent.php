<?php
	/**
	 * Code source de la classe WebrsaCohortesContratsinsertionValidesComponent.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller.Component
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'WebrsaRecherchesContratsinsertionComponent', 'Controller/Component' );

	/**
	 * La classe WebrsaCohortesContratsinsertionValidesComponent ...
	 *
	 * @package app.Controller.Component
	 */
	class WebrsaCohortesContratsinsertionValidesComponent extends WebrsaRecherchesContratsinsertionComponent
	{
		/**
		 *
		 * @param array $params
		 * @return array
		 */
		protected function _optionsEnums( array $params = array() ) {
			$result = parent::_optionsEnums( $params );

			unset( $result['Contratinsertion']['decision_ci']['E'] );

			return $result;
		}
	}
?>