<?php
	/**
	 * Code source de la classe WebrsaAbstractLogic.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe WebrsaAbstractLogic ...
	 *
	 * @todo uses et loadModel() dans AppModel, afin de pouvoir utiliser
	 * facilement (et en Lazy Loading) les classes de logique dans les modèles Cake ?
	 *
	 * @package app.Model
	 */
	abstract class WebrsaAbstractLogic extends AppModel
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaAbstractLogic';

		/**
		 * On n'utilise pas de table.
		 *
		 * @var mixed
		 */
		public $useTable = false;
	}
?>