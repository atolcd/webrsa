<?php
	/**
	 * Code source de la classe ServicesinstructeursController.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaParametragesController', 'Controller' );

	/**
	 * La classe ServicesinstructeursController s'occupe du paramétrage des services
	 * instructeurs.
	 *
	 * @package app.Controller
	 */
	class ServicesinstructeursController extends AbstractWebrsaParametragesController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Servicesinstructeurs';

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array( 'Serviceinstructeur' );

		/**
		 * Utilise les droits d'un autre Controller:action
		 * sur une action en particulier
		 *
		 * @var array
		 */
		public $commeDroit = array(
			'add' => 'Servicesinstructeurs:edit'
		);

		/**
		 * Liste des services instructeurs.
		 */
		public function index() {
			if( false === $this->Serviceinstructeur->Behaviors->attached( 'Occurences' ) ) {
				$this->Serviceinstructeur->Behaviors->attach( 'Occurences' );
			}

			$cacheKey = $this->Serviceinstructeur->useDbConfig.'_'.__CLASS__.'_'.__FUNCTION__;
			$query = Cache::read( $cacheKey );

			if( false === $query ) {
				$query = array(
					'fields' => $this->Serviceinstructeur->fields(),
					'contain' => false,
					'limit' => 100,
					'maxLimit' => 101
				);
				array_remove( $query['fields'], 'Serviceinstructeur.sqrecherche' );

				$query['fields'] = array_merge(
					$query['fields'],
					array( '( "Serviceinstructeur"."sqrecherche" IS NOT NULL ) AS "Serviceinstructeur__sqrecherche"' ),
					array( $this->Serviceinstructeur->sqHasLinkedRecords( true ) )
				);

				Cache::write( $cacheKey, $query );
			}

			$this->WebrsaParametrages->index( $query );
		}

		/**
		 * Formulaire de modification d'un service instructeur.
		 *
		 * @param integer $serviceinstructeur_id
		 * @throws NotFoundException
		 */
		public function edit( $serviceinstructeur_id = null ) {
			if( !empty( $this->request->data ) ) {
				// Retour à l'index en cas d'annulation
				if( isset( $this->request->data['Cancel'] ) ) {
					$this->redirect( array( 'action' => 'index' ) );
				}

				$this->Serviceinstructeur->create( $this->request->data );
				$this->Serviceinstructeur->begin();
				if( $this->Serviceinstructeur->save( null, array( 'atomic' => false ) ) ) {
					$this->Serviceinstructeur->commit();
					$this->Flash->success( __( 'Save->success' ) );
					$this->redirect( array( 'controller' => 'servicesinstructeurs', 'action' => 'index' ) );
				}
				else {
					$this->Serviceinstructeur->rollback();
					$this->Flash->error( __( 'Save->error' ) );
					$sqrecherche = (string)Hash::get( $this->request->data, 'Serviceinstructeur.sqrecherche' );
					if( '' !== $sqrecherche ) {
						$sqlError = $this->Serviceinstructeur->testSqRechercheConditions( $sqrecherche );
						$this->set( compact( 'sqlError' ) );
					}
				}
			}
			else if( $this->action == 'edit' ) {
				$serviceinstructeur = $this->Serviceinstructeur->find(
					'first',
					array(
						'conditions' => array(
							'Serviceinstructeur.id' => $serviceinstructeur_id,
						),
						'contain' => false
					)
				);

				if( true === empty( $serviceinstructeur ) ) {
					throw new NotFoundException();
				}

				$this->request->data = $serviceinstructeur;
			}

			$options = $this->Serviceinstructeur->enums();
			$this->set( compact( 'options' ) );
			$this->render( 'add_edit' );
		}
	}
?>