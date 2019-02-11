<?php
	/**
	 * Code source de la classe WebrsaRecherchesEntretiensComponent.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller.Component
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'WebrsaAbstractRecherchesComponent', 'Controller/Component' );

	/**
	 * La classe WebrsaRecherchesEntretiensComponent ...
	 *
	 * @package app.Controller.Component
	 */
	class WebrsaRecherchesEntretiensComponent extends WebrsaAbstractRecherchesComponent
	{
		/**
		 * Surcharge de la méthode params pour limiter les utilisateurs externes
		 * au code INSEE ou à la valeur de structurereferente_id de l'Entretien.
		 *
		 * @param array $params
		 * @return array
		 */
		protected function _params( array $params = array() ) {
			$defaults = array(
				'structurereferente_id' => 'Entretien.structurereferente_id'
			);

			return parent::_params( $params + $defaults );
		}
	}
?>