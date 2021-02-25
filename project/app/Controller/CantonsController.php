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
			'Adresse'
		);

		/**
		 * Méthodes ne nécessitant aucun droit.
		 *
		 * @var array
		 */
		public $aucunDroit = array();

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

			$options = $this->Canton->enums();
			$options['Canton']['zonegeographique_id'] = $this->Canton->Zonegeographique->find( 'list' );

			$cantons = $this->Canton->find (
				'all',
				array (
					'fields' => array ('DISTINCT ON ("Canton"."canton") "Canton"."canton" AS "Canton__canton"'),
					'conditions' => array ('"Canton"."canton" NOT LIKE \'\''),
					'recursive' => -1,
					'order' => array ('"Canton"."canton" ASC'),
				)
			);
			foreach($cantons as $canton) {
				$options['Canton']['canton'][$canton['Canton']['canton']] = $canton['Canton']['canton'];
			}

			$this->set( compact( 'cantons' ) );
			$this->set( compact( 'options' ) );
		}

		/**
		 * Export des adresses non associées
		 *
		 * @param integer $id
		 */
		public function adressesnonassociees () {
			$filename = 'adresses_erronees';

			$resultats = $this->_generationAdressesnonassociees();
			$export = $this->_generationAdressesnonassocieesCsv($resultats);

			$this->layout = '';
			$this->set( compact( 'filename', 'export' ) );
			$this->render('exportcsv');
		}

		/**
		 * Génération des adresses non associées
		 *
		 * @retrun array
		 */
		protected function _generationAdressesnonassociees () {
			$restrictionCantonMulti = array ();
			$cantonMultis = Configure::read('Canton.multi');
			if (is_array ($cantonMultis) && !empty ($cantonMultis)) {
				foreach ($cantonMultis as $cantonMulti) {
					$restrictionCantonMulti[] = 'Adresse.numcom LIKE \''.$cantonMulti.'\'';
				}
			}

			$query = array (
				'fields' => array (
					'DISTINCT ON ("Personne"."id") "Personne"."id"',
					'Personne.qual',
					'Personne.nom',
					'Personne.nomnai',
					'Personne.prenom',
					'Personne.prenom2',
					'Personne.prenom3',
					'Personne.dtnai',
					'Dossier.matricule',
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
						'table' => 'adressesfoyers',
						'alias' => 'Adressefoyer',
						'type' => 'LEFT OUTER',
						'conditions' => array ('Adresse.id = Adressefoyer.adresse_id')
					),
					array (
						'table' => 'adresses_cantons',
						'alias' => 'AdresseCanton',
						'type' => 'LEFT OUTER',
						'conditions' => array ('Adresse.id = AdresseCanton.adresse_id')
					),
					array (
						'table' => 'cantons',
						'alias' => 'Canton',
						'type' => 'LEFT OUTER',
						'conditions' => array ('Canton.id = AdresseCanton.canton_id')
					),
					array (
						'table' => 'foyers',
						'alias' => 'Foyer',
						'type' => 'LEFT OUTER',
						'conditions' => array ('Foyer.id = Adressefoyer.foyer_id')
					),
					array (
						'table' => 'personnes',
						'alias' => 'Personne',
						'type' => 'LEFT OUTER',
						'conditions' => array ('Personne.foyer_id = Foyer.id')
					),
					array (
						'table' => 'calculsdroitsrsa',
						'alias' => 'Calculdroitrsa',
						'type' => 'LEFT OUTER',
						'conditions' => array ('Personne.id = Calculdroitrsa.personne_id')
					),
					array (
						'table' => 'prestations',
						'alias' => 'Prestation',
						'type' => 'LEFT OUTER',
						'conditions' => array ('Personne.id = Prestation.personne_id')
					),
					array (
						'table' => 'dossiers',
						'alias' => 'Dossier',
						'type' => 'LEFT OUTER',
						'conditions' => array ('Dossier.id = Foyer.dossier_id')
					),
					array (
						'table' => 'situationsdossiersrsa',
						'alias' => 'Situationdossierrsa',
						'type' => 'LEFT OUTER',
						'conditions' => array ('Dossier.id = Situationdossierrsa.dossier_id')
					),
				),
				'conditions' => array (
					"Adresse.codepos LIKE '".Configure::read('Cg.departement')."%'",
					!empty ($restrictionCantonMulti) ? '('.implode(' OR ', $restrictionCantonMulti).")" : '',
					"Adresse.nomcom IS NOT NULL",
					"Adresse.nomcom <> ''",
					"Adressefoyer.rgadr LIKE '01'",
					'Situationdossierrsa.etatdosrsa' => array( '2', '3', '4' ),
					'Calculdroitrsa.toppersdrodevorsa' => '1',
					'Adresse.id not in (select "adresse_id" from "adresses_cantons")',
					"Prestation.rolepers like 'DEM'"
				),
				'recursive' => -1
			);
			return $this->Adresse->find ('all', $query);
		}

		/**
		 * Génération de l'esport des adresses non associées
		 *
		 * @param array $resultats
		 * @return array
		 */
		protected function _generationAdressesnonassocieesCsv ($resultats) {
			$export = array ();

			$line = array ();
			$line[] = __m('Personne.qual');
			$line[] = __m('Personne.nom');
			$line[] = __m('Personne.nomnai');
			$line[] = __m('Personne.prenom');
			$line[] = __m('Personne.prenom2');
			$line[] = __m('Personne.prenom3');
			$line[] = __m('Personne.dtnai');

			$line[] = __m('Dossier.matricule');

			$line[] = __m('Adresse.numvoie');
			$line[] = __m('Adresse.libtypevoie');
			$line[] = __m('Adresse.nomvoie');
			$line[] = __m('Adresse.lieudist');
			$line[] = __m('Adresse.complideadr');
			$line[] = __m('Adresse.compladr');
			$line[] = __m('Adresse.codepos');
			$line[] = __m('Adresse.nomcom');
			$line[] = __m('Adresse.numcom');
			$line[] = __m('Adresse.pays');

			$line[] = __m('NouvelleAdresse.numvoie');
			$line[] = __m('NouvelleAdresse.libtypevoie');
			$line[] = __m('NouvelleAdresse.nomvoie');
			$line[] = __m('NouvelleAdresse.lieudist');
			$line[] = __m('NouvelleAdresse.complideadr');
			$line[] = __m('NouvelleAdresse.compladr');
			$line[] = __m('NouvelleAdresse.codepos');
			$line[] = __m('NouvelleAdresse.nomcom');
			$line[] = __m('NouvelleAdresse.numcom');
			$line[] = __m('NouvelleAdresse.pays');

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

			return $export;
		}

		/**
		 * Export des adresses sans canton
		 *
		 * @param integer $id
		 */
		public function adressessanscanton () {
			$filename = 'adresses_erronees';

			$resultats = $this->_generationAdressessanscanton();
			$export = $this->_generationAdressessanscantonCsv($resultats);

			$this->layout = '';
			$this->set( compact( 'filename', 'export' ) );
			$this->render('exportcsv');
		}

		/**
		 * Génération des adresses sans canton
		 *
		 * @retrun array
		 */
		protected function _generationAdressessanscanton () {
			$query = array (
				'fields' => array (
					'Canton.id',
					'Canton.canton',
					'Zonegeographique.libelle',
					'Canton.numvoie',
					'Canton.libtypevoie',
					'Canton.nomvoie',
					'Canton.nomcom',
					'Canton.codepos',
					'Canton.numcom'
				),
				'joins' => array (
					array (
						'table' => 'zonesgeographiques',
						'alias' => 'Zonegeographique',
						'type' => 'LEFT OUTER',
						'conditions' => array ('Zonegeographique.id = Canton.zonegeographique_id')
					)
				),
				'conditions' => array(
					'OR' => array(
						'canton' => '',
						'canton IS NULL'
					)
				),
				'recursive' => -1
			);
			return $this->Canton->find ('all', $query);
		}

		/**
		 * Génération de l'export des adresses sans canton
		 *
		 * @param array $resultats
		 * @return array
		 */
		protected function _generationAdressessanscantonCsv ($resultats) {
			$export = array ();

			$line = array ();
			$line[] = __m('Canton.id');
			$line[] = __m('Canton.canton');
			$line[] = __m('Zonegeographique.libelle');
			$line[] = __m('Canton.numvoie');
			$line[] = __m('Canton.libtypevoie');
			$line[] = __m('Canton.nomvoie');
			$line[] = __m('Canton.nomcom');
			$line[] = __m('Canton.codepos');
			$line[] = __m('Canton.numcom');

			$export[] = $line;

			foreach ($resultats as $resultat) {
				$line = array ();
				$line[] = $resultat['Canton']['id'];
				$line[] = $resultat['Canton']['canton'];
				$line[] = $resultat['Zonegeographique']['libelle'];
				$line[] = $resultat['Canton']['numvoie'];
				$line[] = $resultat['Canton']['libtypevoie'];
				$line[] = $resultat['Canton']['nomvoie'];
				$line[] = $resultat['Canton']['nomcom'];
				$line[] = $resultat['Canton']['codepos'];
				$line[] = $resultat['Canton']['numcom'];

				$export[] = $line;
			}

			return $export;
		}
	}