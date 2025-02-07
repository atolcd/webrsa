<?php
	/**
	 * Code source de la classe RapportFluxFranceTravail.
	 *
	 * PHP 7.2
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe RapportFluxFranceTravail ...
	 *
	 * @package app.Model
	 */
	class RapportFluxFranceTravail extends AppModel
	{

		/**
		 * Nom.
		 *
		 * @var string
		*/
		public $name = 'RapportFluxFranceTravail';

		public $useTable = 'rapport_flux_francetravail';

		public $useDbConfig = 'log';

	}