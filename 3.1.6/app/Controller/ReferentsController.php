<?php
	/**
	 * Code source de la classe ReferentsController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	App::uses('Folder', 'Utility');
	App::uses('File', 'Utility');
	App::uses('DefaultUrl', 'Default.Utility');
	App::uses('WebrsaPermissions', 'Utility');
	
	/**
	 * La classe ReferentsController ...
	 *
	 * @package app.Controller
	 */
	class ReferentsController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Referents';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'Default',
			'InsertionsBeneficiaires',
			'Search.SearchPrg' => array(
				'actions' => array(
					'index',
					'clotureenmasse',
				),
			),
			'Workflowscers93',
		);

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Default',
			'Default2',
			'Default3' => array(
				'className' => 'Default.DefaultDefault'
			),
			'Search.SearchForm',
			'Xform',
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Referent',
			'Option',
			'Structurereferente',
		);
		
		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 * 
		 * @var array
		 */
		public $commeDroit = array(
			'add' => 'Referents:edit',
		);
		
		/**
		 * Méthodes ne nécessitant aucun droit.
		 *
		 * @var array
		 */
		public $aucunDroit = array(
			'ajax_getreferent',
		);
		
		/**
		 * Correspondances entre les méthodes publiques correspondant à des
		 * actions accessibles par URL et le type d'action CRUD.
		 *
		 * @var array
		 */
		public $crudMap = array(
			'add' => 'create',
			'ajax_getreferent' => 'read',
			'clotureenmasse' => 'read',
			'cloturer' => 'read',
			'delete' => 'delete',
			'edit' => 'update',
			'index' => 'read',
		);

		protected function _setOptions() {

			$this->set( 'qual', $this->Option->qual() );
			$this->set( 'referent', $referent = $this->Referent->find( 'list' ) );

			$options = array();
			$options = $this->Referent->enums();
			$options['Referent']['id'] = $referent;
			$options['Dernierreferent']['prevreferent_id'] = $this->Referent->find('list',
				array(
					'joins' => array($this->Referent->join('Dernierreferent')),
					'conditions' => array('Dernierreferent.referent_id = Dernierreferent.dernierreferent_id'),
					'order' => array('Referent.nom', 'Referent.prenom')
				)
			);

			$structuresreferentes_ids = $this->Workflowscers93->getUserStructurereferenteId( false );
			$options['Referent']['structurereferente_id'] = $this->InsertionsBeneficiaires->structuresreferentes(
				array(
					'type' => 'optgroup',
					'conditions' => (
						false === empty( $structuresreferentes_ids )
						? array( 'Structurereferente.id' => $structuresreferentes_ids )
						: array()
					)
				)
			);
			$options['Referent']['has_datecloture'] = array( '0' => 'Non', '1' => 'Oui' );

			$this->set( compact( 'options' ) );
		}


		public function index() {
			// Retour à la liste en cas d'annulation
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->redirect( array( 'controller' => 'parametrages', 'action' => 'index' ) );
			}

			if( !empty( $this->request->data ) ) {
				$search = $this->request->data['Search'];
				$queryData = $this->Referent->WebrsaReferent->search($search);
				$queryData['limit'] = 20;
				$this->paginate = $queryData;
				$referents = $this->paginate( 'Referent', array(), array(), !Hash::get($search, 'Pagination.nombre_total') );

				$this->set( 'referents', $referents );

			}
			$this->_setOptions();
		}

		/**
		*
		*/

		public function add() {
			$args = func_get_args();
			call_user_func_array( array( $this, '_add_edit' ), $args );
		}

		/**
		*
		*/

		public function edit() {
			$args = func_get_args();
			call_user_func_array( array( $this, '_add_edit' ), $args );
		}

		/**
		 *
		 * @param integer $id
		 */
		public function _add_edit( $id = null ) {
			$options = $this->Referent->enums();
			$options['Referent']['structurereferente_id'] = $this->Referent->Structurereferente->find( 'list' );
			$options['Dernierreferent']['prevreferent_id'] = $this->Referent->find('list',
				array(
					'joins' => array($this->Referent->join('Dernierreferent')),
					'conditions' => array('Dernierreferent.referent_id = Dernierreferent.dernierreferent_id'),
					'order' => array('Referent.nom', 'Referent.prenom')
				)
			);
			$this->set( compact( 'options' ) );

			$bindDernierreferent = $this->Referent->hasOne['Dernierreferent'];
			$this->Referent->unbindModelAll();
			$this->Referent->bindModel( array( 'hasOne' => array( 'Dernierreferent' => $bindDernierreferent ) ) );
			call_user_func_array( array( $this->Default, $this->action ), array( $id ) );
		}

		public function delete( $referent_id = null ) {
			// Vérification du format de la variable
			if( !valid_int( $referent_id ) ) {
				$this->cakeError( 'error404' );
			}

			// Recherche de l'enregistrement
			if( false === $this->Referent->Behaviors->attached( 'Occurences' ) ) {
				$this->Referent->Behaviors->attach( 'Occurences' );
			}

			$query = array(
				'fields' => array_merge(
					$this->Referent->fields(),
					array(
						$this->Referent->sqHasLinkedRecords(true, array('derniersreferents'))
					)
				),
				'contain' => false,
				'conditions' => array(
					'Referent.id' => $referent_id
				)
			);
			$referent = $this->Referent->find( 'first', $query );


			// Mauvais paramètre
			if( empty( $referent ) ) {
				$this->cakeError( 'error404' );
			}

			// Référent encore lié à d'autres enregistrements ?
			if( true === $referent['Referent']['has_linkedrecords'] ) {
				$msgid = 'Tentative de suppression du référent d\'id %d par l\'utilisateur %s alors que celui-ci est encore lié à des enregistrements';
				$msgstr = sprintf( $msgid, $referent_id, $this->Session->read( 'Auth.User.username' ) );
				throw new RuntimeException( $msgstr, 500 );
			}

			// Tentative de suppression
			$this->Referent->begin();
			if( $this->Referent->delete( array( 'Referent.id' => $referent_id ) ) ) {
				$this->Referent->commit();
				$this->Session->setFlash( 'Suppression effectuée', 'flash/success' );
			}
			else {
				$this->Referent->rollback();
				$this->Session->setFlash( 'Impossible de supprimer l\'enregistrement', 'flash/error' );
			}

			$this->redirect( array( 'controller' => 'referents', 'action' => 'index' ) );
		}

		/**
		*	Clôture en masse des référents
		*/

	/**
		 * Formulaire de clôture d'un référent du parcours.
		 *
		 * @param integer $id L'id technique de l'enregistrement dans la table personnes_referents
		 * @return void
		 */
		public function cloturer( $id = null ) {
			$this->assert( valid_int( $id ), 'invalidParameter' );

			$referent = $this->Referent->find(
				'first',
				array(
					'conditions' => array(
						'Referent.id' => $id
					)
				)
			);
			$this->assert( !empty( $referent ), 'invalidParameter' );

			// Les administrateurs n'ont pas accès à la cohorte de clôture en masse
			$referer = Hash::get( $this->request->data, 'Referent.referer' );
			if( null !== $referer ) {
				$redirectUrl = Router::parse( $referer );
				$redirectUrl = array_merge(
					$redirectUrl,
					$redirectUrl['pass'],
					$redirectUrl['named']
				);
				unset( $redirectUrl['pass'], $redirectUrl['named'] );
			}
			else {
				$redirectUrl = (
					WebrsaPermissions::check( $this->name, 'clotureenmasse' )
					? array( 'controller' => 'referents', 'action' => 'clotureenmasse' )
					: array( 'controller' => 'referents', 'action' => 'index' )
				);
			}

			// Retour à la liste en cas d'annulation
			if( !empty( $this->request->data ) && isset( $this->request->data['Cancel'] ) ) {
				$this->redirect( $redirectUrl );
			}

			// Tentative d'enregistrement du formulaire
			if( !empty( $this->request->data ) ) {
				$this->Referent->begin();

				$datedfdesignation = ( is_array( $this->request->data['Referent']['datecloture'] ) ? date_cakephp_to_sql( $this->request->data['Referent']['datecloture'] ) : $this->request->data['Referent']['datecloture'] );

				$count = $this->Referent->PersonneReferent->find(
					'count',
					array(
						'conditions' => array(
							'PersonneReferent.referent_id' => $id,
							'PersonneReferent.dfdesignation IS NULL'
						)
					)
				);

				$success = true;
				if( $count > 0 ) {
					$success = $this->Referent->PersonneReferent->updateAllUnBound(
						array( 'PersonneReferent.dfdesignation' => '\''.$datedfdesignation.'\'' ),
						array(
							'"PersonneReferent"."referent_id"' => $id,
							'PersonneReferent.dfdesignation IS NULL'
						)
					);
				}

				if( $success ) {
					$success = $this->Referent->updateAllUnBound(
						array( 'Referent.datecloture' => '\''.$datedfdesignation.'\'' ),
						array(
							'"Referent"."id"' => $id
						)
					) && $success;

					$this->Referent->commit();
					$this->Session->setFlash( 'Enregistrement effectué', 'flash/success' );
					$this->redirect( $redirectUrl );
				}
				else {
					$this->Session->setFlash( 'Erreur lors de l\'enregistrement', 'flash/error' );
					$this->Referent->rollback();
				}
			}
			else {
				$this->request->data = $referent;
			}

			if( null === $referer ) {
				$this->request->data['Referent']['referer'] = $this->referer( null, true );
			}
		}


        /**
         * Fonction de clôture en masse des référents, cloisonnée selon le type de structure
         * Uniquement pour les CPDV
         */
		public function clotureenmasse() {
			$structurereferente_id = $this->Workflowscers93->getUserStructurereferenteId();

			if( !empty( $this->request->data ) ) {
				$queryData = $this->Referent->WebrsaReferent->search( (array)Hash::get( $this->request->data, 'Search' ) );
				$queryData['limit'] = 20;
                $queryData['conditions'][] = array( 'Referent.structurereferente_id' => $structurereferente_id );
				$this->paginate = $queryData;

				$progressivePaginate = !Hash::get( $this->request->data, 'Search.Pagination.nombre_total' );
				$referents = $this->paginate( 'Referent', array(), array(), $progressivePaginate );

				$this->set( 'referents', $referents );

			}
			$this->_setOptions();
            $this->render( 'index' );
		}

		/**
		 * Lecture de la table Dernierreferent pour ajax sur add_edit
		 */
		public function ajax_getreferent() {
			$id = Hash::get($this->request->data, 'id');
			$json = false;

			if (!empty($id) && preg_match('/^[\d]+$/', (string)$id)) {
				$json = $this->Referent->find('first', array(
					'fields' => array_merge(
						$this->Referent->fields(),
						$this->Referent->Dernierreferent->fields()
					),
					'joins' => array(
						$this->Referent->join('Dernierreferent')
					),
					'contain' => false,
					'conditions' => array('Referent.id' => $id),
				));
			}

			$this->set('json', Hash::flatten($json, '__'));
			$this->layout = 'ajax';
			$this->view = '/Elements/json';
		}
	}
?>