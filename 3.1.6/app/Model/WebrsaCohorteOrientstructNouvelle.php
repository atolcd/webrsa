<?php
	/**
	 * Code source de la classe WebrsaCohorteOrientstructNouvelle.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'WebrsaAbstractCohorteOrientstruct', 'Model' );

	/**
	 * La classe WebrsaCohorteOrientstructNouvelle ...
	 *
	 * @package app.Model
	 */
	class WebrsaCohorteOrientstructNouvelle extends WebrsaAbstractCohorteOrientstruct
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaCohorteOrientstructNouvelle';

		/**
		 * Liste des clés de configuration utilisées par le moteur de recherche,
		 * pour vérification du paramétrage.
		 *
		 * @see checkParametrage()
		 *
		 * @var array
		 */
		public $keysRecherche = array(
			'ConfigurableQueryOrientsstructs.cohorte_nouvelle.fields',
			'ConfigurableQueryOrientsstructs.cohorte_nouvelle.innerTable'
		);

		/**
		 * Spécifie le statut_orient pour cette cohorte-ci puisqu'on sous-classe.
		 *
		 * @see WebrsaAbstractCohorteOrientstruct::searchQuery()
		 *
		 * @var string
		 */
		public $statut_orient = 'Non orienté';
	}
?>