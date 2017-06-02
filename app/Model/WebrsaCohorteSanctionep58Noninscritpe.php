<?php
	/**
	 * Code source de la classe WebrsaCohorteSanctionep58Noninscritpe.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaCohorteSanctionep58', 'Model/Abstractclass' );

	/**
	 * La classe WebrsaCohorteSanctionep58Noninscritpe ...
	 *
	 * @package app.Model
	 */
	class WebrsaCohorteSanctionep58Noninscritpe extends AbstractWebrsaCohorteSanctionep58
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaCohorteSanctionep58Noninscritpe';

		/**
		 * Liste des clés de configuration utilisées par le moteur de recherche,
		 * pour vérification du paramétrage.
		 *
		 * @see checkParametrage()
		 *
		 * @var array
		 */
		public $keysRecherche = array(
			'ConfigurableQuerySanctionseps58.cohorte_noninscritspe.fields',
			'ConfigurableQuerySanctionseps58.cohorte_noninscritspe.innerTable',
			'ConfigurableQuerySanctionseps58.exportcsv_noninscritspe',
		);
	}
?>