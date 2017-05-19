<?php
	/**
	 * Code source de la classe CompositionsregroupementsepsController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppController', 'Controller' );

	/**
	 * La classe CompositionsregroupementsepsController s'occupe du paramétrage
	 * des compositions des regroupements de l'EP.
	 *
	 * @package app.Controller
	 */
	class CompositionsregroupementsepsController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Compositionsregroupementseps';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array( 'WebrsaParametrages' );

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
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array( 'Compositionregroupementep', 'Regroupementep' );

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array();

		/**
		 * Méthodes ne nécessitant aucun droit.
		 *
		 * @var array
		 */
		public $aucunDroit = array();

		/**
		 * Correspondances entre les méthodes publiques correspondant à des
		 * actions accessibles par URL et le type d'action CRUD.
		 *
		 * @var array
		 */
		public $crudMap = array(
			'edit' => 'update',
			'index' => 'read',
		);

		/**
		 * Liste des tables à ne pas prendre en compte dans les enregistrements
		 * vérifiés pour éviter les suppressions en cascade intempestives.
		 *
		 * @var array
		 */
		public $blacklist = array();

		/**
		 * Liste des compositions des équipes pluridisciplinaires
		 */
		public function index() {
			$erreurs = array(
				'Merci d\'ajouter au moins un regroupement avant d\'en indiquer la composition.' => 0 == $this->Compositionregroupementep->Regroupementep->find( 'count' ),
				'Merci d\'ajouter au moins un membre avant d\'en indiquer la composition.' => 0 == $this->Compositionregroupementep->Fonctionmembreep->find( 'count' )
			);

			$query = array(
				'fields' => array(
					'Regroupementep.id',
					'Regroupementep.name'
				),
				'contain' => false,
				'order' => array(
					'Regroupementep.name ASC'
				)
			);

			$this->WebrsaParametrages->index( $query, array( 'modelClass' => 'Regroupementep' ) );
			$this->set( compact( 'erreurs' ) );
		}

		/**
		 * Modification d'une composition d'EP.
		 *
		 * @param integer $id
		 */
		public function edit( $id = null ) {
			$fonctionsmembreseps = $this->Compositionregroupementep->Fonctionmembreep->find( 'list' );

			if( false === empty( $this->request->data ) ) {
				// Retour à la liste en cas d'annulation
				if( isset( $this->request->data['Cancel'] ) ) {
					$this->redirect( array( 'action' => 'index' ) );
				}

				$prioritaires = Hash::extract( $this->request->data, 'Compositionregroupementep.{n}[prioritaire=1]' );

				if( true === empty( $prioritaires ) ) {
					$this->Flash->error( 'Merci de mettre au moins un membre prioritaire (les mettre tous prioritaires si aucune gestion).' );
				}
				else if( $this->Compositionregroupementep->saveAll( $this->request->data['Compositionregroupementep'] ) ) {
					$this->Flash->success( __( 'Save->success' ) );
					$this->redirect( array( 'action' => 'index' ) );
				}
				else {
					$this->Flash->error( __( 'Save->error' ) );
				}
			}
			else {
				$record = $this->Compositionregroupementep->Regroupementep->find(
					'first',
					array(
						'conditions' => array( 'Regroupementep.id' => $id ),
						'contain' => array(
							'Compositionregroupementep'
						)
					)
				);

				if( true === empty( $record ) ) {
					throw new NotFoundException();
				}

				$this->request->data = array(
					'Regroupementep' => $record['Regroupementep'],
					'Compositionregroupementep' => array()
				);

				foreach( $record['Compositionregroupementep'] as $compositionregroupementep ) {
					$this->request->data['Compositionregroupementep'][$compositionregroupementep['fonctionmembreep_id']] = $compositionregroupementep;
				}

				foreach( array_keys( $fonctionsmembreseps ) as $fonctionmembreep_id ) {
					if( false === isset( $this->request->data['Compositionregroupementep'][$fonctionmembreep_id] ) ) {
						$fields = array_keys( $this->Compositionregroupementep->schema() );
						$fields = array_combine( $fields, array_fill( 0, count( $fields ), null ) );
						$fields['fonctionmembreep_id'] = $fonctionmembreep_id;
						$fields['regroupementep_id'] = $id;
						$this->request->data['Compositionregroupementep'][$fonctionmembreep_id] = $fields;
					}
				}
			}

			$options = $this->Compositionregroupementep->enums();
			$this->set( compact( 'options', 'fonctionsmembreseps' ) );
		}
	}
?>