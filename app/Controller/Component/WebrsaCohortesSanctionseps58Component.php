<?php
	/**
	 * Code source de la classe WebrsaCohortesSanctionseps58Component.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller.Component
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'WebrsaAbstractCohortesComponent', 'Controller/Component' );

	/**
	 * La classe WebrsaCohortesSanctionseps58Component ...
	 *
	 * @package app.Controller.Component
	 */
	class WebrsaCohortesSanctionseps58Component extends WebrsaAbstractCohortesComponent
	{
		/**
		 * Retourne les options de type "enum", c'est à dire liées aux schémas des
		 * tables de la base de données.
		 *
		 * La mise en cache se fera dans ma méthode _options().
		 *
		 * @return array
		 */
		protected function _optionsEnums( array $params ) {
			$Controller = $this->_Collection->getController();

			if( isset( $Controller->Personne ) === false ) {
				$Controller->loadModel( 'Personne' );
			}

			if( isset( $Controller->Informationpe ) === false ) {
				$Controller->loadModel( 'Informationpe' );
			}

			$enums = Hash::merge(
				$Controller->Informationpe->enums(),
				$Controller->Informationpe->Historiqueetatpe->enums(),
				$Controller->Personne->Dossierep->enums(),
				$Controller->Personne->Dossierep->Sanctionep58->enums(),
				$Controller->Personne->Foyer->Dossier->Suiviinstruction->Serviceinstructeur->enums(),
				$Controller->Personne->Orientstruct->enums(),
				$Controller->Personne->Orientstruct->Referent->enums(),
				$Controller->Personne->Orientstruct->Structurereferente->enums(),
				$Controller->Personne->Orientstruct->Typeorient->enums()
			);

			return Hash::merge(
				parent::_optionsEnums( $params ),
				$enums,
				array(
					'Referentorientant' => $enums['Referent'],
					'Structureorientante' => $enums['Structurereferente']
				)
			);
		}
	}
?>