<?php
	/**
	 * Code source de la classe WebrsaOrientsstructsValidationsComponent.
	 *
	 * PHP 7.2
	 *
	 * @package app.Controller.Component
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'WebrsaAbstractCohortesOrientsstructsComponent', 'Controller/Component' );

	/**
	 * La classe WebrsaOrientsstructsValidationsComponent ...
	 *
	 * @package app.Controller.Component
	 */
	class WebrsaOrientsstructsValidationsComponent extends WebrsaAbstractCohortesOrientsstructsComponent
	{

		/**
		 * Ajoute et modifie l'enum pour les cohortes liées aux validations d'orientation
		 *
		 * La mise en cache se fera dans ma méthode _options().
		 *
		 * @return array
		 */
		public function customEnums( $result, $Controller ) {
			// Récupération des structures orientantes
			$result['Orientstruct']['structureorientante_id'] = $Controller->Orientstruct->Structurereferente->listeStructWorkflow();

			/**
			 * Orientation externe par prestataire pour le CD 93 uniquement
			 * On ne veut proposer que les origines des prestataires
			 */
			$result['Orientstruct']['origine'] = $Controller->Orientstruct->enumOrigine($result['Orientstruct']['origine']);

			// Personnalisation des statuts d'orientation
			unset($result['Orientstruct']['statut_orient']['En attente']);
			$result['Orientstruct']['statut_orient']['Orienté'] = 'Valider';
			$result['Orientstruct']['statut_orient']['Refusé'] = 'Refuser';

			return $result;
		}
	}
