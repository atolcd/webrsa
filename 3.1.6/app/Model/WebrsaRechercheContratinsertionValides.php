<?php
	/**
	 * Code source de la classe WebrsaRechercheContratinsertionValides.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaRechercheContratinsertion', 'Model/Abstractclass' );
	App::uses( 'ConfigurableQueryFields', 'ConfigurableQuery.Utility' );

	/**
	 * La classe WebrsaRechercheContratinsertionValides ...
	 *
	 * @package app.Model
	 */
	class WebrsaRechercheContratinsertionValides extends AbstractWebrsaRechercheContratinsertion
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaRechercheContratinsertionValides';
	}
?>