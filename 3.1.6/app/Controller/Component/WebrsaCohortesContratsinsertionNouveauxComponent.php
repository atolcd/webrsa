<?php
	/**
	 * Code source de la classe WebrsaCohortesContratsinsertionNouveauxComponent.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller.Component
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'WebrsaAbstractCohortesComponent', 'Controller/Component' );

	/**
	 * La classe WebrsaCohortesContratsinsertionNouveauxComponent ...
	 *
	 * @package app.Controller.Component
	 */
	class WebrsaCohortesContratsinsertionNouveauxComponent extends WebrsaAbstractCohortesComponent
	{
		/**
		 * Retourne les options stockées en session, liées à l'utilisateur connecté.
		 *
		 * @return array
		 */
		protected function _optionsSession( array $params ) {
			$Controller = $this->_Collection->getController();

			return Hash::merge(
				parent::_optionsSession( $params ),
				array(
					'Contratinsertion' => array(
						'structurereferente_id' => $Controller->InsertionsBeneficiaires->structuresreferentes( array( 'type' => 'optgroup' ) ),
						'referent_id' => $Controller->InsertionsBeneficiaires->referents()
					)
				),
				$this->Allocataires->optionsSessionCommunautesr( 'Contratinsertion' )
			);
		}

		/**
		 * Permet de récupérer les cohorteFields du modele de recherche et de lui
		 * appliquer les valeurs par défaut
		 *
		 * @param array $params
		 * @return array
		 */
		protected function _getCohorteFields( array $params = array() ) {
			$Controller = $this->_Collection->getController();
			$fields = parent::_getCohorteFields($params);

			$fields['Contratinsertion.datevalidation_ci']['minYear'] = 2009;
			$fields['Contratinsertion.datevalidation_ci']['maxYear'] = date( 'Y' ) + 1;
			$fields['Contratinsertion.observ_ci']['rows'] = 2;

			return $fields;
		}
	}
?>