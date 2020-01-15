<?php
	/**
	 * Code source de la classe PiecesemailsController.
	 *
	 * PHP 7.2
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'OccurencesBehavior', 'Model/Behavior' );
	App::uses( 'AppController', 'Controller' );

	/**
	 * La classe PiecesemailsController ...
	 *
	 * @package app.Controller
	 */
	class PiecesemailsController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Piecesemails';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'Default',
			'Fileuploader',
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
			'Fileuploader',
			'Theme',
			'Xform',
		);

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Piecemail',
			'Option',
		);

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(
			'view' => 'Piecesemails:index',
		);

		/**
		 * Méthodes ne nécessitant aucun droit.
		 *
		 * @var array
		 */
		public $aucunDroit = array(
			'ajaxfiledelete',
			'ajaxfileupload',
			'download',
			'fileview',
		);

		/**
		 * Correspondances entre les méthodes publiques correspondant à des
		 * actions accessibles par URL et le type d'action CRUD.
		 *
		 * @var array
		 */
		public $crudMap = array(
			'add' => 'create',
			'ajaxfiledelete' => 'delete',
			'ajaxfileupload' => 'update',
			'delete' => 'delete',
			'download' => 'read',
			'edit' => 'update',
			'fileview' => 'read',
			'index' => 'read',
			'view' => 'read',
		);

		protected function _setOptions() {
			$options = $this->Piecemail->enums();
			$this->set( compact( 'options' ) );
		}

		/**
		 * http://valums.com/ajax-upload/
		 * http://doc.ubuntu-fr.org/modules_php
		 * increase post_max_size and upload_max_filesize to 10M
		 * debug( array( ini_get( 'post_max_size' ), ini_get( 'upload_max_filesize' ) ) ); -> 10M
		 */
		public function ajaxfileupload() {
			$this->Fileuploader->ajaxfileupload();
		}

		/**
		 * http://valums.com/ajax-upload/
		 * http://doc.ubuntu-fr.org/modules_php
		 * increase post_max_size and upload_max_filesize to 10M
		 * debug( array( ini_get( 'post_max_size' ), ini_get( 'upload_max_filesize' ) ) ); -> 10M
		 * FIXME: traiter les valeurs de retour
		 */
		public function ajaxfiledelete() {
			$this->Fileuploader->ajaxfiledelete();
		}

		/**
		 * Fonction permettant de visualiser les fichiers chargés dans la vue avant leur envoi sur le serveur
		 *
		 * @param integer $id
		 */
		public function fileview( $id ) {
			$this->Fileuploader->fileview( $id );
		}

		/**
		 * Téléchargement des fichiers préalablement associés
		 *
		 * @param integer $fichiermodule_id
		 */
		public function download( $fichiermodule_id ) {
			$this->assert( !empty( $fichiermodule_id ), 'error404' );
			$this->Fileuploader->download( $fichiermodule_id );
		}
		/**
		*   Ajout à la suite de l'utilisation des nouveaux helpers
		*   - default.php
		*   - theme.php
		*/

		public function index() {
			$occurenceQuery = array(
				'fields' => 'Email.id',
				'joins' => array(
					$this->Piecemail->join( 'Email', array( 'type' => 'INNER' ) )
				),
				'limit' => 1
			);
			// On Alias
			$prepareSq = str_replace( 'Piecemail', 'piecesmails', $this->Piecemail->sq($occurenceQuery));

			// On remet la bonne condition
			$occurenceSq = str_replace( '"piecesmails"."id"', '"Piecemail"."id"', $prepareSq );

			$querydata = array(
				'fields' => array_merge(
					$this->Piecemail->fields(),
					array(
						$this->Piecemail->Fichiermodule->sqNbFichiersLies( $this->Piecemail, 'nb_fichiers_lies' ),
						"(".$occurenceSq.") AS Piecemail__occurence"
					)
				),
				'contain' => false,
				'recursive' => -1,
				'order' => array( 'Piecemail.name ASC' ),
			);
			$this->paginate = $querydata;
			$piecesemails = $this->paginate('Piecemail');
			$this->set( compact('piecesemails'));
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
		*/

		protected function _add_edit( $id = null){
			// Retour à la liste en cas d'annulation
			if( isset( $this->request->data['Cancel'] ) ) {
				$this->redirect( array( 'controller' => 'piecesemails', 'action' => 'index' ) );
			}

			$fichiers = array();
			if( !empty( $this->request->data ) ) {
				$this->Piecemail->begin();

				// Rend inactif si n'a pas de pièce jointe
				if ( $this->request->data['Piecemail']['haspiecejointe'] === '0' ){
					$this->request->data['Piecemail']['actif'] = 0;
				}

				$this->Piecemail->create( $this->request->data );
				$success = $this->Piecemail->save( null, array( 'atomic' => false ) );

				if( $success ){
					$path = $this->action === 'add' ? '0' : $this->Piecemail->id;
					$dir = $this->Fileuploader->dirFichiersModule( $this->action, $path );
					$success = $success && $this->Fileuploader->saveFichiers(
						$dir,
						FALSE,
						( ( $this->action == 'add' ) ? $this->Piecemail->id : $id )
					);
				}
				if( $success ) {
					$this->Piecemail->commit();
					$this->Flash->success( __( 'Save->success' ) );
					$this->redirect( array( 'action' => 'index' ) );
				}
				else {
					$fichiers = $this->Fileuploader->fichiers( $id, false );
					$this->Piecemail->rollback();
					$this->Flash->error( __( 'Save->error' ) );
				}
			}
			else if( $this->action == 'edit' ) {
				$this->request->data = $this->Piecemail->find(
					'first',
					array(
						'contain' => false,
						'conditions' => array( 'Piecemail.id' => $id )
					)
				);
				$this->assert( !empty( $this->request->data ), 'error404' );
			}
			else{
				// La case actif est activée par defaut
				$this->request->data['Piecemail']['actif'] = true;
			}

			$fichiersEnBase = array();
			if(false === empty($id)) {
				$fichiersEnBase = Hash::extract(
					$this->Fileuploader->fichiersEnBase( $id ),
					'{n}.Fichiermodule'
				);
			}
			$this->set( 'fichiersEnBase', $fichiersEnBase );

			$this->set( 'fichiers', $fichiers );
			$this->_setOptions();
			$this->render( 'add_edit' );
		}

		/**
		*
		*/

		public function delete( $id ) {
			$this->Default->delete( $id );
		}

		/**
		*
		*/

		public function view( $id ) {
			$this->Default->view( $id );
			$this->_setOptions();
		}
	}
?>