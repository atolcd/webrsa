<?php
	/**
	 * Code source de la classe WebrsaCohortesReferentsComponent.
	 *
	 * PHP 7.2
	 *
	 * @package app.Controller.Component
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'WebrsaAbstractCohortesComponent', 'Controller/Component' );

	/**
	 * La classe WebrsaCohortesReferentsComponent ...
	 *
	 * @package app.Controller.Component
	 */
	class WebrsaCohortesReferentsComponent extends WebrsaAbstractCohortesComponent
	{
		/**
		 * Components utilisés par ce component.
		 *
		 * @var array
		 */
		public $components = array(
			'InsertionsBeneficiaires',
		);

		/**
		 * Retourne les options stockées en session, liées à l'utilisateur connecté.
		 *
		 * @return array
		 */
		protected function _optionsSession( array $params ) {
			$options = parent::_optionsSession($params);

			// Copie des options PersonneReferent vers PR pour accéder aux options dans la cohorte
			if(isset($options['PersonneReferent'])) {
				$options['PR'] = $options['PersonneReferent'];
			}
			return $options;
		}

	}
?>