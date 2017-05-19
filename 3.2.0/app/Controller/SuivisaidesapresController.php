<?php
	/**
	 * Code source de la classe SuivisaidesapresController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaParametragesController', 'Controller' );

	/**
	 * La classe SuivisaidesapresController ...
	 *
	 * @package app.Controller
	 */
	class SuivisaidesapresController extends AbstractWebrsaParametragesController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Suivisaidesapres';

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array( 'Suiviaideapre' );

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(
			'add' => 'Suivisaidesapres:edit'
		);

		/**
		 * Liste des participants aux comités d'examen APRE
		 */
		public function index() {
			$query = array(
				'fields' => array_merge(
					$this->Suiviaideapre->fields(),
					array(
						'Suiviaideapre.nom_complet',
						$this->Suiviaideapre->sqHasLinkedRecords( true, $this->blacklist )
					)
				)
			);
			$this->WebrsaParametrages->index( $query );
		}
	}

?>