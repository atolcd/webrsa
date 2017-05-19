<?php
	/**
	 * Code source de la classe StructuresreferentesController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe StructuresreferentesController ...
	 *
	 * @package app.Controller
	 */
	class StructuresreferentesController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Structuresreferentes';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'Search.SearchPrg' => array(
				'actions' => array(
					'index',
				),
			),
		);

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Default3' => array(
				'className' => 'ConfigurableQuery.ConfigurableQueryDefault'
			),
			'Translator',
			'Xform',
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Structurereferente',
			'Apre',
			'Option',
			'Orientstruct',
			'Referent',
			'Typeorient',
			'WebrsaStructurereferente',
			'Zonegeographique',
		);
		
		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 * 
		 * @var array
		 */
		public $commeDroit = array(
			'add' => 'Structuresreferentes:edit',
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
			'edit' => 'update',
			'index' => 'read',
		);

		protected function _setOptions() {
			$this->set( 'typevoie', $this->Option->typevoie() );

			$options = $this->Structurereferente->enums();

			if( Configure::read( 'Cg.departement' ) == 58 ) {
				$options['Structurereferente']['typestructure']['oa'] = 'Structure liée à un PPAE';
				$options['Structurereferente']['typestructure']['msp'] = 'Structure débouchant sur CER pro';
			}

			foreach( array( 'Typeorient' ) as $linkedModel ) {
				$field = Inflector::singularize( Inflector::tableize( $linkedModel ) ).'_id';
				$options = Hash::insert( $options, "{$this->modelClass}.{$field}", $this->{$this->modelClass}->{$linkedModel}->find( 'list' ) );
			}

			$this->set( 'options', $options );
		}

		/**
		 * Moteur de recherche par structures référentes
		 */
		public function index() {
			$search = (array)Hash::get( $this->request->data, 'Search' );
			if( !empty( $search ) ) {
				$query = $this->WebrsaStructurereferente->search( $search );
				$query['limit'] = 20;
				$this->paginate = $query;
				$results = $this->paginate( 'Structurereferente', array(), array(), !Hash::get($search, 'Pagination.nombre_total') );
				$this->set( compact( 'results' ) );
			}

			$departement = (int)Configure::read( 'Cg.departement' );
			$options = $this->Structurereferente->enums();
			$options['Structurereferente']['typeorient_id'] = $this->Structurereferente->Typeorient->find( 'list' );
			if( 93 === $departement ) {
				$options['Structurereferente']['communautesr_id'] = $this->Structurereferente->Communautesr->find( 'list' );
			}
			$this->set( compact( 'options' ) );
		}

		public function add() {
			// Retour à l'index en cas d'annulation
			if( !empty( $this->request->data ) && isset( $this->request->data['Cancel'] ) ) {
				$this->redirect( array( 'action' => 'index' ) );
			}
			$this->set( 'options', $this->Typeorient->listOptions() );
			$zg = $this->Zonegeographique->find(
				'list',
				array(
					'fields' => array(
						'Zonegeographique.id',
						'Zonegeographique.libelle'
					)
				)
			);
			$this->set( 'zglist', $zg );

			$type = $this->Typeorient->find(
				'list',
				array(
					'fields' => array(
						'Typeorient.id',
						'Typeorient.lib_type_orient',
					)
				)
			);
			$this->set( 'type', $type );

			if( !empty( $this->request->data ) ) {
				if( $this->Structurereferente->saveAll( $this->request->data ) ) {
					$this->Session->setFlash( 'Enregistrement effectué', 'flash/success' );
					$this->redirect( array( 'controller' => 'structuresreferentes', 'action' => 'index' ) );
				}
			}
			$this->_setOptions();
			$this->render( 'add_edit' );
		}

		public function edit( $structurereferente_id = null ) {
			// Retour à l'index en cas d'annulation
			if( !empty( $this->request->data ) && isset( $this->request->data['Cancel'] ) ) {
				$this->redirect( array( 'action' => 'index' ) );
			}
			// TODO : vérif param
			// Vérification du format de la variable
			$this->assert( valid_int( $structurereferente_id ), 'error404' );
			$this->set( 'options', $this->Typeorient->listOptions() );
			$zg = $this->Zonegeographique->find(
				'list',
				array(
					'fields' => array(
						'Zonegeographique.id',
						'Zonegeographique.libelle'
					)
				)
			);
			$this->set( 'zglist', $zg );

			$type = $this->Typeorient->find(
				'list',
				array(
					'fields' => array(
						'Typeorient.lib_type_orient'
					)
				)
			);
			$this->set( 'type', $type );

			if( !empty( $this->request->data ) ) {
				if( $this->Structurereferente->saveAll( $this->request->data ) ) {
					$this->Session->setFlash( 'Enregistrement effectué', 'flash/success' );
					$this->redirect( array( 'controller' => 'structuresreferentes', 'action' => 'index' ) );
				}
			}
			else {
				$structurereferente = $this->Structurereferente->find(
					'first',
					array(
						'conditions' => array(
							'Structurereferente.id' => $structurereferente_id,
						),
						'contain' => array(
							'Zonegeographique'
						)
					)
				);
				$this->request->data = $structurereferente;
			}
			$this->_setOptions();

			$this->render( 'add_edit' );
		}

		public function delete( $structurereferente_id = null ) {
			// Vérification du format de la variable
			if( !valid_int( $structurereferente_id ) ) {
				$this->cakeError( 'error404' );
			}

			// Recherche de l'enregistrement
			if( false === $this->Structurereferente->Behaviors->attached( 'Occurences' ) ) {
				$this->Structurereferente->Behaviors->attach( 'Occurences' );
			}

			$query = array(
				'fields' => array_merge(
					$this->Structurereferente->fields(),
					array(
						$this->Structurereferente->sqHasLinkedRecords()
					)
				),
				'contain' => false,
				'conditions' => array(
					'Structurereferente.id' => $structurereferente_id
				)
			);
			$structurereferente = $this->Structurereferente->find( 'first', $query );


			// Mauvais paramètre
			if( empty( $structurereferente ) ) {
				$this->cakeError( 'error404' );
			}

			// Structure référente encore liée à d'autres enregistrements ?
			if( true === $structurereferente['Structurereferente']['has_linkedrecords'] ) {
				$msgid = 'Tentative de suppression de la structure référente d\'id %d par l\'utilisateur %s alors que celle-ci est encore liée à des enregistrements';
				$msgstr = sprintf( $msgid, $structurereferente_id, $this->Session->read( 'Auth.User.username' ) );
				throw new RuntimeException( $msgstr, 500 );
			}

			// Tentative de suppression
			$this->Structurereferente->begin();
			if( $this->Structurereferente->delete( array( 'Structurereferente.id' => $structurereferente_id ) ) ) {
				$this->Structurereferente->commit();
				$this->Session->setFlash( 'Suppression effectuée', 'flash/success' );
			}
			else {
				$this->Structurereferente->rollback();
				$this->Session->setFlash( 'Impossible de supprimer l\'enregistrement', 'flash/error' );
			}

			$this->redirect( array( 'controller' => 'structuresreferentes', 'action' => 'index' ) );
		}
	}

?>