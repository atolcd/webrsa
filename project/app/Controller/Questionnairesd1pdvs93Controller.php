<?php
	/**
	 * Code source de la classe Questionnairesd1pdvs93Controller.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppController', 'Controller' );

	/**
	 * La classe Questionnairesd1pdvs93Controller ...
	 *
	 * @package app.Controller
	 */
	class Questionnairesd1pdvs93Controller extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Questionnairesd1pdvs93';

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
			'Default',
			'Default3' => array(
				'className' => 'Default.DefaultDefault'
			),
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Questionnaired1pdv93',
		);

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(

		);

		/**
		 * Méthodes ne nécessitant aucun droit.
		 *
		 * @var array
		 */
		public $aucunDroit = array(

		);

		/**
		 * Correspondances entre les méthodes publiques correspondant à des
		 * actions accessibles par URL et le type d'action CRUD.
		 *
		 * @var array
		 */
		public $crudMap = array(
			'add' => 'create',
			'delete' => 'delete',
			'index' => 'read',
			'view' => 'read',
		);

		/**
		 * Pagination sur les <élément>s de la table.
		 *
		 * @return void
		 */
		public function index( $personne_id ) {
			$this->set( 'dossierMenu', $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $personne_id ) ) );

			$this->_setEntriesAncienDossier( $personne_id, 'Questionnaired1pdv93' );

			// Remplit-on les conditions initiales ? / Messages à envoyer à l'utilisateur
			$messages = $this->Questionnaired1pdv93->messages( $personne_id );
			$ajoutPossible = $this->Questionnaired1pdv93->addEnabled( $messages );
			$this->set( compact( 'messages', 'ajoutPossible' ) );

			$subQuery = array(
				'alias' => 'historiquesdroits',
				'fields' => array( 'historiquesdroits.id' ),
				'conditions' => array(
					'Questionnaired1pdv93.personne_id = historiquesdroits.personne_id',
					'Questionnaired1pdv93.created::DATE <= historiquesdroits.modified::DATE',
					'Questionnaired1pdv93.created::DATE >= historiquesdroits.created::DATE'
				),
				'order' => array( 'historiquesdroits.modified ASC' ),
				'limit' => 1
			);

			$querydata = array(
                'fields' => array(
                    'Personne.id',
                    'Personne.qual',
                    'Personne.nom',
                    'Personne.prenom',
                    'Questionnaired1pdv93.id',
                    'Questionnaired1pdv93.date_validation',
                    'Rendezvous.daterdv',
					'Structurereferente.id',
					'Structurereferente.lib_struc',
                    'Statutrdv.libelle',
                    'Historiquedroit.etatdosrsa',
                    'Historiquedroit.toppersdrodevorsa',
                    'Historiquedroit.created',
                    'Historiquedroit.modified',
                ),
                'joins' => array(
                    $this->Questionnaired1pdv93->join( 'Personne', array( 'type' => 'INNER' ) ),
                    $this->Questionnaired1pdv93->join( 'Rendezvous', array( 'type' => 'INNER' ) ),
                    $this->Questionnaired1pdv93->Rendezvous->join( 'Statutrdv', array( 'type' => 'INNER' ) ),
					$this->Questionnaired1pdv93->Rendezvous->join( 'Structurereferente', array( 'type' => 'INNER' ) ),
                    $this->Questionnaired1pdv93->Personne->join( 'Historiquedroit',
						array(
							'type' => 'LEFT OUTER',
							'conditions' => array(
								'Historiquedroit.id IN ( '.$this->Questionnaired1pdv93->Personne->Historiquedroit->sq( $subQuery ).' )'
							)
						)
					)
                ),
                'contain' => false,
                'conditions' => array(
                    'Questionnaired1pdv93.personne_id' => $personne_id
                ),
                'order' => array(
                    'Questionnaired1pdv93.modified DESC'
                ),
                'limit' => 10
			);

			$personne = $this->Questionnaired1pdv93->Personne->find(
				'first',
				array(
					'conditions' => array(
						'Personne.id' => $personne_id
					),
					'contain' => false
				)
			);

            $historiquesdroit = $this->Questionnaired1pdv93->Personne->Historiquedroit->find(
				'all',
				array(
					'conditions' => array(
						'Historiquedroit.personne_id' => $personne_id
					),
					'contain' => false,
					'order' => array( 'Historiquedroit.created ASC' )
				)
			);

			$questionnairesd1pdvs93 = $this->WebrsaAccesses->getIndexRecords( $personne_id, $querydata );

            $options = Hash::merge(
				$this->Questionnaired1pdv93->enums(),
				$this->Questionnaired1pdv93->Situationallocataire->enums()
			);

            $optionsHisto = array(
				'Historiquedroit' => array(
					'etatdosrsa' => ClassRegistry::init('Dossier')->enum('etatdosrsa')
				)
			);
			$options = Set::merge( $options, $optionsHisto );

			$this->set( compact( 'personne_id', 'questionnairesd1pdvs93', 'personne', 'historiquesdroit', 'options' ) );
		}

		/**
		 * Suppression d'un questionnaire D1, de la Situationallocataire associée
		 * et redirection vers l'index.
		 *
		 * FIXME: ne supprime pas la Situationallocataire liée
		 *
		 * @param integer $id
		 */
		public function delete( $id ) {
			$this->WebrsaAccesses->check( $id );

			$personne_id = $this->Questionnaired1pdv93->personneId( $id );

			$querydata = array(
				'fields' => array(
					'Rendezvous.structurereferente_id',
					'Situationallocataire.id'
				),
				'conditions' => array(
					'Questionnaired1pdv93.id' => $id
				),
				'contain' => array(
					'Rendezvous',
					'Situationallocataire'
				)
			);

			$questionnaired1pdv93 = $this->Questionnaired1pdv93->find( 'first', $querydata );
			if( empty( $questionnaired1pdv93 ) ) {
				throw new NotFoundException();
			}

			// @deprecated
			$permission = WebrsaPermissions::checkD1D2( Hash::get( $questionnaired1pdv93, 'Rendezvous.structurereferente_id' ) );
			if( !$permission ) {
				throw new Error403Exception( null );
			}

			$this->Questionnaired1pdv93->begin();

			if( $this->Questionnaired1pdv93->Situationallocataire->delete( $questionnaired1pdv93['Situationallocataire']['id'], true ) ) {
				$this->Questionnaired1pdv93->commit();
				$this->Flash->success( __( 'Delete->success' ) );
			}
			else {
				$this->Questionnaired1pdv93->rollback();
				$this->Flash->error( __( 'Delete->error' ) );
			}

			$this->redirect( array( 'action' => 'index', $personne_id ) );
		}

		/**
		 * Formulaire d'ajout d'un élémént.
		 *
		 * @return void
		 */
		public function add( $personne_id ) {
			$this->WebrsaAccesses->check( null, $personne_id );

			$messages = $this->Questionnaired1pdv93->messages( $personne_id );
			$add_enabled = $this->Questionnaired1pdv93->addEnabled( $messages );
			if( !$add_enabled ) {
				throw new InternalErrorException( "Impossible d'ajouter une formulaire D1 à cet allocataire cette année." );
			}

			$dossierMenu = $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $personne_id ) );
			$this->Jetons2->get( $dossierMenu['Dossier']['id'] );

			// Retour à l'index en cas d'annulation
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->Jetons2->release( $dossierMenu['Dossier']['id'] );
				$this->redirect( array( 'action' => 'index', $personne_id ) );
			}

			if( !empty( $this->request->data ) ) {
				$this->Questionnaired1pdv93->begin();

				if( $this->Questionnaired1pdv93->saveFormData( $personne_id, $this->request->data ) ) {
					$this->Questionnaired1pdv93->commit();
					$this->Jetons2->release( $dossierMenu['Dossier']['id'] );
					$this->Flash->success( __( 'Save->success' ) );
					$this->redirect( array( 'action' => 'index', $personne_id ) );
				}
				else {
					$this->Questionnaired1pdv93->rollback();
					$this->Flash->error( __( 'Save->error' ) );
				}
			}
			else {
				$this->request->data = $this->Questionnaired1pdv93->prepareFormData( $personne_id );
			}

			$personne = $this->Questionnaired1pdv93->Personne->find(
				'first',
				array(
					'conditions' => array(
						'Personne.id' => $personne_id
					),
					'contain' => false
				)
			);

			$options = Hash::merge(
				$this->Questionnaired1pdv93->enums(),
				$this->Questionnaired1pdv93->Situationallocataire->enums()
			);

			$options['Questionnaired1pdv93']['rendezvous_id'] = $this->Questionnaired1pdv93->Personne->Rendezvous->WebrsaRendezvous->findListPersonneId( $personne_id );
			$options['Situationallocataire']['nati'] = ClassRegistry::init( 'WebrsaTableausuivipdv93' )->nati;
			$options = $this->Questionnaired1pdv93->filterOptions( $options );

			$this->set( compact( 'personne_id', 'options', 'dossierMenu', 'personne' ) );
		}

		/**
		 *
		 * @param integer $id
		 * @throws error404Exception
		 */
		public function view( $id ) {
			$this->WebrsaAccesses->check( $id );

			$questionnaired1pdv93 = $this->Questionnaired1pdv93->find(
				'first',
				array(
					'conditions' => array(
						'Questionnaired1pdv93.id' => $id
					),
					'contain' => array(
						'Personne',
						'Rendezvous',
						'Situationallocataire',
					)
				)
			);

			if( empty( $questionnaired1pdv93 ) ) {
				throw new error404Exception();
			}

			$questionnaired1pdv93 = $this->Questionnaired1pdv93->completeDataForView( $questionnaired1pdv93 );

			$dossierMenu = $this->DossiersMenus->getAndCheckDossierMenu( array( 'personne_id' => $questionnaired1pdv93['Personne']['id'] ) );

			$options = Hash::merge(
				$this->Questionnaired1pdv93->enums(),
				$this->Questionnaired1pdv93->Situationallocataire->enums()
			);

			$this->set( 'urlmenu', "/questionnairesd1pdvs93/index/{$questionnaired1pdv93['Questionnaired1pdv93']['personne_id']}" );

			$this->set( compact( 'questionnaired1pdv93', 'dossierMenu', 'options' ) );
		}
	}
?>
