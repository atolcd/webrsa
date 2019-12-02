<?php
	/**
	 * Code source de la classe CantonsController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaParametragesController', 'Controller' );

	/**
	 * La classe CantonsController s'occupe du paramétrage des cantons.
	 *
	 * @package app.Controller
	 */
	class CantonsController extends AbstractWebrsaParametragesController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Cantons';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'Search.SearchPrg' => array(
				'actions' => array(
					'index' => array( 'filter' => 'Search' )
				)
			),
			'WebrsaParametrages'
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array (
			'Canton',
			'Personne'
		);

		/**
		 * Méthodes ne nécessitant aucun droit.
		 *
		 * @var array
		 */
		public $aucunDroit = array(
			'adressesnonassociees'
		);

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(
			'add' => 'Cantons:edit'
		);

		/**
		 * Liste des tables à ne pas prendre en compte dans les enregistrements
		 * vérifiés pour éviter les suppressions en cascade intempestives.
		 *
		 * @var array
		 */
		public $blacklist = array( 'adresses_cantons' );

		/**
		 * Liste des cantons
		 */
		public function index() {
			if( false === $this->Canton->Behaviors->attached( 'Occurences' ) ) {
				$this->Canton->Behaviors->attach( 'Occurences' );
			}

			// Ajout d'un message lors d'un enregistrement réussi
			$notice = null;
			if( 'success' === $this->Session->read( 'Message.flash.params.class' ) ) {
				$notice = 'Attention, en cas de modifications sur les cantons, il peut être utile de lancer AdresseCantonShell en console pour recalculer les relations entre Adresses et Cantons';
			}

			$recherche = $this->Session->read('Search.Canton');
			if (empty( $this->request->data ) && !empty ($recherche)) {
				$this->request->data = $recherche;
			}

			if( false === empty( $this->request->data ) ) {
				$this->Session->write('Search.Canton', $this->request->data);

				$query = $this->Canton->search( $this->request->data['Search'] );
				$query['fields'][] = $this->Canton->sqHasLinkedRecords( true, $this->blacklist );
				$query['limit'] = 100;
				$this->paginate = $query;
				$results = $this->paginate( 'Canton' );
				$this->set( compact( 'results' ) );
			}

			$options = $this->Canton->enums();
			$options['Canton']['zonegeographique_id'] = $this->Canton->Zonegeographique->find( 'list' );
			$this->set( compact( 'options', 'notice' ) );
		}

		/**
		 * Formulaire de modification d'un canton
		 *
		 * @param integer $id
		 */
		public function edit( $id = null ) {
			$this->WebrsaParametrages->edit( $id, array( 'view' => 'add_edit' ) );

			$cantons = $this->Canton->find (
				'all',
				array (
					'fields' => array ('DISTINCT ON ("Canton"."canton") "Canton"."canton" AS "Canton__canton"'),
					'conditions' => array ('"Canton"."canton" NOT LIKE \'\''),
					'recursive' => -1,
					'order' => array ('"Canton"."canton" ASC'),
				)
			);
			$this->set( compact( 'cantons' ) );

			$options = $this->viewVars['options'];
			$options['Canton']['zonegeographique_id'] = $this->Canton->Zonegeographique->find( 'list' );
			$this->set( compact( 'options' ) );
		}

		/**
		 * Export des adresses non associées
		 *
		 * @param integer $id
		 */
		public function adressesnonassociees () {
			$filename = 'adresses_erronees';

			$query = array (
				'fields' => array (
					'Personne.id',
					'Personne.qual',
					'Personne.nom',
					'Personne.nomnai',
					'Personne.prenom',
					'Personne.prenom2',
					'Personne.prenom3',
					'Personne.dtnai',
					'Adresse.id',
					'Adresse.numvoie',
					'Adresse.nomvoie',
					'Adresse.complideadr',
					'Adresse.compladr',
					'Adresse.lieudist',
					'Adresse.codepos',
					'Adresse.pays',
					'Adresse.libtypevoie',
					'Adresse.numcom',
					'Adresse.nomcom'
				),
				'joins' => array (
					array (
						'table' => 'dossiers',
						'alias' => 'Dossier',
						'type' => 'INNER',
						'conditions' => array ('Dossier.id = Foyer.dossier_id')
					),
					array (
						'table' => 'situationsdossiersrsa',
						'alias' => 'Situationdossierrsa',
						'type' => 'INNER',
						'conditions' => array ('Dossier.id = Situationdossierrsa.dossier_id')
					),
					array (
						'table' => 'adressesfoyers',
						'alias' => 'AdresseFoyer',
						'type' => 'INNER',
						'conditions' => array (
							'Foyer.id = AdresseFoyer.foyer_id',
							'rgadr LIKE \'01\''
						)
					),
					array (
						'table' => 'adresses',
						'alias' => 'Adresse',
						'type' => 'INNER',
						'conditions' => array ('Adresse.id = AdresseFoyer.adresse_id')
					),
					array (
						'table' => 'adresses_cantons',
						'alias' => 'AdresseCanton',
						'type' => 'INNER',
						'conditions' => array ('Adresse.id = AdresseCanton.adresse_id')
					),
					array (
						'table' => 'cantons',
						'alias' => 'Canton',
						'type' => 'INNER',
						'conditions' => array (
							'Canton.id = AdresseCanton.canton_id',
							'Canton.canton LIKE \'\''
						)
					),
				),
				'conditions' => array (
					'Situationdossierrsa.etatdosrsa' => array( '2', '3', '4' ),
					'Calculdroitrsa.toppersdrodevorsa' => '1'
				)
			);

			$resultats = $this->Personne->find ('all', $query);
			$export = array ();

			$line = array ();
			$line[] = 'Personne.qual';
			$line[] = 'Personne.nom';
			$line[] = 'Personne.nomnai';
			$line[] = 'Personne.prenom';
			$line[] = 'Personne.prenom2';
			$line[] = 'Personne.prenom3';
			$line[] = 'Personne.dtnai';

			$line[] = 'Adresse.numvoie';
			$line[] = 'Adresse.libtypevoie';
			$line[] = 'Adresse.nomvoie';
			$line[] = 'Adresse.lieudist';
			$line[] = 'Adresse.complideadr';
			$line[] = 'Adresse.compladr';
			$line[] = 'Adresse.codepos';
			$line[] = 'Adresse.nomcom';
			$line[] = 'Adresse.numcom';
			$line[] = 'Adresse.pays';

			$line[] = 'NouvelleAdresse.numvoie';
			$line[] = 'NouvelleAdresse.libtypevoie';
			$line[] = 'NouvelleAdresse.nomvoie';
			$line[] = 'NouvelleAdresse.lieudist';
			$line[] = 'NouvelleAdresse.complideadr';
			$line[] = 'NouvelleAdresse.compladr';
			$line[] = 'NouvelleAdresse.codepos';
			$line[] = 'NouvelleAdresse.nomcom';
			$line[] = 'NouvelleAdresse.numcom';
			$line[] = 'NouvelleAdresse.pays';

			$export[] = $line;

			foreach ($resultats as $resultat) {
				$line = array ();
				$line[] = $resultat['Personne']['qual'];
				$line[] = $resultat['Personne']['nom'];
				$line[] = $resultat['Personne']['nomnai'];
				$line[] = $resultat['Personne']['prenom'];
				$line[] = $resultat['Personne']['prenom2'];
				$line[] = $resultat['Personne']['prenom3'];
				$line[] = $resultat['Personne']['dtnai'];

				$line[] = $resultat['Adresse']['numvoie'];
				$line[] = $resultat['Adresse']['libtypevoie'];
				$line[] = $resultat['Adresse']['nomvoie'];
				$line[] = $resultat['Adresse']['lieudist'];
				$line[] = $resultat['Adresse']['complideadr'];
				$line[] = $resultat['Adresse']['compladr'];
				$line[] = $resultat['Adresse']['codepos'];
				$line[] = $resultat['Adresse']['nomcom'];
				$line[] = $resultat['Adresse']['numcom'];
				$line[] = $resultat['Adresse']['pays'];

				$line[] = '';
				$line[] = '';
				$line[] = '';
				$line[] = '';
				$line[] = '';
				$line[] = '';
				$line[] = '';
				$line[] = '';
				$line[] = '';
				$line[] = '';

				$export[] = $line;
			}

			$this->layout = '';
			$this->set( compact( 'filename', 'export' ) );
			$this->render('exportcsv');
		}
	}
?>