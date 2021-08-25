<?php
	/**
	 * Code source de la classe Sitescovs58Controller.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaParametragesController', 'Controller' );

	/**
	 * La classe Sitescovs58Controller s'occupe du paramétrage des sites d'actions
	 * médico-sociale  COV.
	 *
	 * @package app.Controller
	 */
	class Sitescovs58Controller extends AbstractWebrsaParametragesController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Sitescovs58';

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Sitecov58',
			'Canton',
			'CantonSitecov58',
			'Option'
		);

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
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(
			'add' => 'Sitescovs58:edit'
		);

		/**
		 * Méthodes ne nécessitant aucun droit.
		 *
		 * @var array
		 */
		public $aucunDroit = array(
			'associer',
			'desassocier'
		);

		/**
		 * Liste des tables à ne pas prendre en compte dans les enregistrements
		 * vérifiés pour éviter les suppressions en cascade intempestives.
		 *
		 * @var array
		 */
		public $blacklist = array( 'sitescovs58_zonesgeographiques' );

		/**
		 * Listes des sites COV.
		 *
		 */
		public function index() {
			$this->WebrsaParametrages->index ();

			$options = $this->viewVars['options'];
			$options['Sitecov58']['actif'] = array (__d ('sitecov58', 'Sitecov58.non'), __d ('sitecov58', 'Sitecov58.oui'));
			$this->set( compact( 'options' ) );
		}

		/**
		 * Formulaire de modification d'un site COV.
		 *
		 * @param integer $id
		 */
		public function edit( $id = null ) {
			$params = array(
				'query' => array(
					'contain' => array(
						'Zonegeographique'
					)
				),
				'view' => 'add_edit'
			);
			$this->WebrsaParametrages->edit( $id, $params );

			$options = $this->viewVars['options'];
			$options['Zonegeographique']['Zonegeographique'] = $this->Sitecov58->Zonegeographique->find( 'list' );

			// Ajout des types de voie
			$options['type_voie'] = $this->Option->libtypevoie();

			$this->set( compact( 'options', 'id' ) );
		}

		/**
		 * Formulaire de gestion des adresses d'un site COV.
		 *
		 * @param integer $id
		 */
		public function adresse ( $id, $canton_id = null, $action = null ) {
			$results = array ();

			if (is_numeric ($canton_id) && $action == 'associer') {
				$this->associer ($id, $canton_id);
			}
			if (is_numeric ($canton_id) && $action == 'desassocier') {
				$this->desassocier ($id, $canton_id);
			}

			$recherche = $this->Session->read('Search.Canton');
			if (empty( $this->request->data ) && !empty ($recherche)) {
				$this->request->data = $recherche;
			}

			if( false === empty( $this->request->data ) ) {
				$this->Session->write('Search.Canton', $this->request->data);

				$query = $this->Canton->search( $this->request->data['Search'] );
				$query['fields'] = array_merge(
					$query['fields'],
					$this->CantonSitecov58->fields ()
				);
				$query['joins'][] = array (
					'table' => 'cantons_sitescovs58',
					'alias' => 'CantonSitecov58',
					'type' => 'LEFT OUTER',
					'conditions' => array (
						'"Canton"."id" = "CantonSitecov58"."canton_id"'
					)
				);
				$query['conditions'][] = '"CantonSitecov58"."canton_id" IS NULL';
				$query['order'][] = '"Canton"."canton" ASC, "Zonegeographique"."libelle" ASC ';
				$results = $this->Canton->find ('all', $query);
			}

			$query = array ();
			$query = $this->Canton->search( array () );
			$query['joins'][] = array (
				'table' => 'cantons_sitescovs58',
				'alias' => 'CantonSitecov58',
				'type' => 'LEFT OUTER',
				'conditions' => array (
					'"Canton"."id" = "CantonSitecov58"."canton_id"'
				)
			);
			$query['conditions'][] = '"CantonSitecov58"."sitecov58_id" = '.$id;
			$query['order'][] = '"Canton"."canton" ASC, "Zonegeographique"."libelle" ASC ';
			$cantonSitecov58s = $this->Canton->find ('all', $query);

			$sitecov58 = $this->Sitecov58->find ('first', array ('conditions' => array ('id' => $id), 'recursive' => -1));

			$options = $this->Canton->enums();
			$options['Canton']['zonegeographique_id'] = $this->Canton->Zonegeographique->find( 'list' );
			$this->set( compact( 'options', 'id', 'cantonSitecov58s', 'results', 'sitecov58' ) );
		}

		public function associer ($id, $canton_id) {
			$result = $this->CantonSitecov58->find (
				'first',
				array (
					'conditions' => array (
						'sitecov58_id' => $id,
						'canton_id' => $canton_id
					)
				)
			);

			if (!isset ($result['CantonSitecov58']['id'])) {
				$this->CantonSitecov58->set(array(
				    'sitecov58_id' => $id,
				    'canton_id' => $canton_id
				));

				$saved = $this->CantonSitecov58->save();

				if( $saved ) {
					$this->CantonSitecov58->commit();
					$this->Flash->success( __( 'Save->success' ) );
				}
				else {
					$this->CantonSitecov58->rollback();
					$this->Flash->error( __( 'Save->error' ) );
				}
			}
		}

		public function desassocier ($id, $canton_id) {
			$result = $this->CantonSitecov58->find (
				'first',
				array (
					'conditions' => array (
						'sitecov58_id' => $id,
						'canton_id' => $canton_id
					)
				)
			);

			if (isset ($result['CantonSitecov58']['id'])) {
				$deleted = $this->CantonSitecov58->delete ($result['CantonSitecov58']['id']);

				if( $deleted ) {
					$this->CantonSitecov58->commit();
					$this->Flash->success( __( 'Save->success' ) );
				}
				else {
					$this->CantonSitecov58->rollback();
					$this->Flash->error( __( 'Save->error' ) );
				}
			}
		}
	}