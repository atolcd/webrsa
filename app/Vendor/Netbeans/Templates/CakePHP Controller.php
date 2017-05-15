<#include "freemarker_functions.ftl">
<?php
	/**
	 * Code source de la classe ${name}.
	 *
<#if php_version??>
	 * PHP ${php_version}
	 *
</#if>
	 * @package app.Controller
	 * @license ${license}
	 */
	App::uses('AppController', 'Controller');

	/**
	 * La classe ${name} ...
	 *
	 * @package app.Controller
	 */
	class ${name} extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = '${class_name(name)?replace("Controller$", "","r")}';

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array();

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array();

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array();

		/**
		 * Pagination sur les <éléments> de la table.
		 */
		public function index() {
			$this->paginate = array(
				$this->modelClass => array(
					'limit' => 10
				)
			);

			$varname = Inflector::tableize( $this->modelClass );
			$this->set( $varname, $this->paginate() );
		}

		/**
		 * Formulaire d'ajout d'un <élément>.
		 */
		public function add() {
			$args = func_get_args();
			call_user_func_array( array( $this, 'edit' ), $args );
		}

		/**
		 * Formulaire de modification d'un <élément>.
		 *
		 * @throws NotFoundException
		 */
		public function edit( $id = null ) {
			if( !empty( $this->request->data ) ) {
				$this->{$this->modelClass}->begin();
				$this->{$this->modelClass}->create( $this->request->data );

				if( $this->{$this->modelClass}->save() ) {
					$this->{$this->modelClass}->commit();
					$this->Session->setFlash( 'Enregistrement effectué', 'flash/success' );
					$this->redirect( array( 'action' => 'index' ) );
				}
				else {
					$this->{$this->modelClass}->rollback();
					$this->Session->setFlash( 'Erreur lors de l\'enregistrement', 'flash/error' );
				}
			}
			else if( $this->action == 'edit' ) {
				$this->request->data = $this->{$this->modelClass}->find(
					'first',
					array(
						'conditions' => array(
							"{$this->modelClass}.id" => $id
						),
						'contain' => false
					)
				);

				if( empty( $this->request->data  ) ) {
					throw new NotFoundException();
				}
			}

			$this->render( 'edit' );
		}

		/**
		 * Suppression d'un <élément> et redirection vers l'index.
		 *
		 * @param integer $id
		 */
		public function delete( $id ) {
			$this->{$this->modelClass}->begin();

			if( $this->{$this->modelClass}->delete( $id ) ) {
				$this->{$this->modelClass}->commit();
				$this->Session->setFlash( 'Suppression effectuée', 'flash/success' );
			}
			else {
				$this->{$this->modelClass}->rollback();
				$this->Session->setFlash( 'Erreur lors de la suppression', 'flash/error' );
			}

			$this->redirect( array( 'action' => 'index' ) );
		}
	}
?>
