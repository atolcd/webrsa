<?php
	/**
	 * Code source de la classe CreancealimentaireController.
	 *
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppController', 'Controller' );

	/**
	 * La classe CreancealimentaireController ...
	 *
	 * @package app.Controller
	 */
	class CreancesalimentairesController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Creancesalimentaires';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'DossiersMenus',
			'Jetons2',
			'WebrsaAccesses'
			);

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
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
			'Creancealimentaire'
			);

		/**
		 * Pagination sur les <éléments> de la table.
		 */
		public function index($personne_id) {

			$this->set('dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu(array('personne_id' => $personne_id)));

			$this->set( 'options', $this->Creancealimentaire->enums() );

			$creancesalimentaires = $this->Creancealimentaire->find(
			    'all',
				array(
					'conditions' => array(
						'Creancealimentaire.personne_id' => $personne_id
					),
					'order' => array(
						'Creancealimentaire.ddcrealim DESC',
						'Creancealimentaire.id DESC',
					 ),
					'contain' => FALSE
				)
			);

			$this->set( 'creancesalimentaires', $creancesalimentaires );
			$this->set( 'urlmenu', '/creancesalimentaires/index/'.$personne_id );
		}

	}
?>
