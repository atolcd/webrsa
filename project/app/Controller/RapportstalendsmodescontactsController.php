<?php
	/**
	 * Code source de la classe RapportstalendsmodescontactsController.
	 *
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppController', 'Controller' );

	/**
	 * La classe RapportstalendsmodescontactsController ...
	 *
	 * @package app.Controller
	 */
	class RapportstalendsmodescontactsController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Rapportstalendsmodescontacts';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
		);

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Paginator',
			'Default3' => array(
				'className' => 'Default.DefaultDefault'
			)
		);

		/**
		 * Correspondances entre les méthodes publiques correspondant à des
		 * actions accessibles par URL et le type d'action CRUD.
		 *
		 * @var array
		 */
		public $crudMap = array(
			'index' => 'read'
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Rapporttalendmodescontact',
			'Rejettalendcreance'
		);

		/**
		 * Pagination sur les <éléments> de la table.
		 */
		public function index() {

			$query = "
				select
					created
					, fichier
					, count(*) as nombre_total_rejets
					, count(*) filter (where motif ilike 'PAS_DEMANDEUR') as PAS_DEMANDEUR
					, count(*) filter (where motif ilike 'ANCIEN_DOSSIER') as ANCIEN_DOSSIER
					, count(*) filter (where motif ilike 'AUCUN_NIR') as AUCUN_NIR
					, count(*) filter (where motif ilike 'AUCUN_MATRICULE') as AUCUN_MATRICULE
				from rapportstalendmodescontacts
				group by created, fichier
				order by created desc
			";

			$Rapportstalendsmodescontacts = $this->Rapporttalendmodescontact->query($query);

			$this->set( 'Rapportstalendsmodescontacts', $Rapportstalendsmodescontacts );
		}
	}
?>