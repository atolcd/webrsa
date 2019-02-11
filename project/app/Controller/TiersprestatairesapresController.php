<?php
	/**
	 * Code source de la classe TiersprestatairesapresController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaParametragesController', 'Controller' );

	/**
	 * La classe TiersprestatairesapresController s'occupe du paramétrage des tiers
	 * prestataires APRE du CD 93.
	 *
	 * @package app.Controller
	 */
	class TiersprestatairesapresController extends AbstractWebrsaParametragesController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Tiersprestatairesapres';

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Xform',
			'Default3' => array(
				'className' => 'Default.DefaultDefault'
			)
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array( 'Tiersprestataireapre' );

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(
			'add' => 'Tiersprestatairesapres:edit'
		);

		/**
		 * Liste des tiers prestataires APRE.
		 */
		public function index() {
			$query = array(
				'fields' => array_merge(
					$this->Tiersprestataireapre->fields(),
					array(
						'Tiersprestataireapre.adresse',
						$this->Tiersprestataireapre->sqHasLinkedRecords( true, $this->blacklist )
					)
				)
			);
			$this->WebrsaParametrages->index( $query, array( 'blacklist' => $this->blacklist ) );
		}
	}
?>