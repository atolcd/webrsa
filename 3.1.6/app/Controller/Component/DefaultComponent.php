<?php
	/**
	 * Fichier source de la classe DefaultComponent.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller.Component
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe DefaultComponent offre des méthodes de contrôleur "par défaut",
	 * à utiliser par exemple pour le paramétrage.
	 *
	 * @package app.Controller.Component
	 */
	class DefaultComponent extends Component
	{
		/**
		 * Contrôleur utilisant ce composant.
		 *
		 * @var Controller
		 */
		public $Controller = null;

		/**
		 * Initialisation du composant.
		 *
		 * @param Controller $controller
		 */
		public function initialize( Controller $controller ) {
			parent::initialize( $controller );
			$this->Controller =  $controller;
		}

		/**
		 * Avant le rendu de la page, on prépare un titre par défaut pour la page.
		 *
		 * @param Controller $controller Le contrôleur utilisant le component.
		 */
		public function beforeRender( Controller $controller ) {
			if( isset( $this->Controller->{$this->Controller->modelClass} ) ) {
				$model = $this->Controller->{$this->Controller->modelClass};
				$domain = Inflector::singularize( Inflector::tableize( $model->name ) );

				switch( $controller->action ) {
					case 'edit':
					case 'view':
					case 'delete':
						$varName = $domain;
						$controller->pageTitle = sprintf(
								__d( $domain, "{$controller->name}::{$controller->action}" ), Set::classicExtract( Set::classicExtract( $controller->viewVars, $varName ), "{$model->name}.{$model->displayField}" )
						);
						break;
					case 'add':
					case 'index':
					default:
						$controller->pageTitle = sprintf(
								__d( $domain, "{$controller->name}::{$controller->action}" )
						);
						break;
				}
			}
		}

		/**
		 * Complète un querydata à partir des opérations et des valeurs associées
		 * extraites des données renvoyées au contrôleur sous la clé "Search" et
		 * exécutre la méthode index().
		 *
		 * @param array $operations Les opérations utilisées par champ.
		 *	<pre>Ex.: array( 'User.birthday' => 'BETWEEN' )</pre>
		 * @param array $queryData Le querydata ,qui sera complété
		 */
		public function search( $operations, $queryData = array( ) ) {
			$search = Set::extract( $this->Controller->request->data, 'Search' );
			if( !empty( $search ) ) {
				$search = Hash::flatten( (array)$search );
				$search = Hash::filter( (array)$search );
				$search = Hash::expand( $search );

				if( !empty( $search ) ) {
					$search = Hash::remove( $search, 'active' );
					$conditions = $this->_conditions( $search, $operations );
					$queryData = Set::merge( $queryData, array( $this->Controller->modelClass => array( 'conditions' => $conditions ) ) );
				}

				$this->index( $queryData );
			}
		}

		/**
		 * Pagination des résultats du querydata sur la classe de modèle principale
		 * du contrôleur.
		 *
		 * @param array $queryData Le querydata à exécuter.
		 */
		public function index( $queryData = array( ) ) {
			$this->Controller->paginate = array(
				$this->Controller->modelClass => array(
					'limit' => 5,
					'recursive' => 0
				)
			);

			$this->Controller->paginate = Set::merge( $this->Controller->paginate, $queryData );
			$items = $this->Controller->paginate( $this->Controller->modelClass );

			$varname = Inflector::tableize( $this->Controller->modelClass );
			$this->Controller->set( $varname, $items );
		}

		/**
		 * Visualisation d'un élément de la classe de modèle principale du contrôleur.
		 *
		 * @param integer $id L'id technique de l'enregistrement à visualiser
		 */
		public function view( $id = null ) {
			$qd_item = array(
				'conditions' => array(
					$this->Controller->modelClass.'.id' => $id
				),
				'fields' => null,
				'order' => null,
				'recursive' => 1
			);
			$item = $this->Controller->{$this->Controller->modelClass}->find( 'first', $qd_item );
			$this->Controller->assert( !empty( $item ), 'invalidParameter' );

			$varname = strtolower( Inflector::singularize( $this->Controller->name ) );
			$this->Controller->set( $varname, $item );
		}

		/**
		 * Ajout d'un élément de la classe de modèle principale du contrôleur.
		 */
		public function add() {
			$args = func_get_args();
			call_user_func_array( array( $this, '_add_edit' ), $args );
		}

		/**
		 * Modification d'un élément de la classe de modèle principale du contrôleur.
		 *
		 * @param integer $id L'id technique de l'enregistrement à modifier
		 */
		public function edit( $id = null ) {
			$args = func_get_args();
			call_user_func_array( array( $this, '_add_edit' ), $args );
		}

		/**
		 * Méthode commune d'ajout et de modification d'un élément de la classe
		 * de modèle principale du contrôleur.
		 *
		 * @param integer $id L'id technique de l'enregistrement à modifier
		 */
		protected function _add_edit( $id = null ) {
			if( Set::check( $this->Controller->request->params, 'form.cancel' ) ) {
				$this->Controller->Session->setFlash( __( 'Save->cancel' ), 'flash/information' );
				$this->Controller->redirect( array( 'action' => 'index' ) );
			}

			// Retour à l'index en cas d'annulation
			if( !empty( $this->Controller->request->data ) && isset( $this->Controller->request->data['Cancel'] ) ) {
				$this->Controller->redirect( array( 'action' => 'index' ) );
			}

			if( $this->Controller->action == 'edit' ) {
				$qd_item = array(
					'conditions' => array(
						$this->Controller->modelClass.'.id' => $id
					),
					'fields' => null,
					'order' => null,
					'recursive' => 1
				);
				$item = $this->Controller->{$this->Controller->modelClass}->find( 'first', $qd_item );
				$this->Controller->assert( !empty( $item ), 'invalidParameter' );

				$varname = strtolower( Inflector::singularize( $this->Controller->name ) );
				$this->Controller->set( $varname, $item );
			}

			if( !empty( $this->Controller->request->data ) ) {
				if( Set::classicExtract( $this->Controller->request->params, "{$this->Controller->action}.operation" ) == 'saveAll' ) {
					if( $this->Controller->{$this->Controller->modelClass}->saveAll( $this->Controller->request->data ) ) {
						$this->Controller->Session->setFlash( __( 'Save->success' ), 'flash/success' );
						$this->Controller->redirect( array( 'action' => 'index' ) );
					}
				}
				else {
					$this->Controller->{$this->Controller->modelClass}->create( $this->Controller->request->data );
					if( $this->Controller->{$this->Controller->modelClass}->save() ) {
						$this->Controller->Session->setFlash( __( 'Save->success' ), 'flash/success' );
						$this->Controller->redirect( array( 'action' => 'index' ) );
					}
				}
			}
			else if( $this->Controller->action == 'edit' ) {
				$this->Controller->request->data = $item;

				// Assign checkboxes
				if( !empty( $this->Controller->{$this->Controller->modelClass}->hasAndBelongsToMany ) ) {
					$HABTMModelNames = array_keys( $this->Controller->{$this->Controller->modelClass}->hasAndBelongsToMany );
					foreach( $HABTMModelNames as $HABTMModelName )
						$this->Controller->request->data = Hash::insert( $this->Controller->request->data, "{$HABTMModelName}.{$HABTMModelName}", Set::extract( $this->Controller->request->data, "/{$HABTMModelName}/id" ) );
				}
			}

			$this->Controller->render( 'add_edit' );
		}

		/**
		 * Suppression d'un élément de la classe de modèle principale du contrôleur.
         * On peut s'assurer qu'aucun enregistrement n'est lié à l'entrée à
         * supprimer grâce au paramètre $assertAucuneOccurenceLiee.
		 *
		 * @param integer $id L'id technique de l'enregistrement à supprimer
         * @param boolean $assertAucuneOccurenceLiee
         * @throws error404Exception
         * @throws error500Exception
		 */
		public function delete( $id = null, $assertAucuneOccurenceLiee = false ) {
			$qd_item = array(
				'conditions' => array(
					$this->Controller->modelClass.'.id' => $id
				),
				'fields' => null,
				'order' => null,
				'recursive' => -1
			);
			$item = $this->Controller->{$this->Controller->modelClass}->find( 'first', $qd_item );
            if( empty( $item ) ) {
                throw new error404Exception();
            }

            if( $assertAucuneOccurenceLiee ) {
                App::import( 'Behaviors', 'Occurences' );
                $this->Controller->{$this->Controller->modelClass}->Behaviors->attach( 'Occurences' );
                $occurences = $this->Controller->{$this->Controller->modelClass}->occurences(
                    array(
                        "{$this->Controller->{$this->Controller->modelClass}->alias}.{$this->Controller->{$this->Controller->modelClass}->primaryKey}" => $id
                    )
                );

                if( empty( $occurences ) ) {
                    throw new error404Exception();
                }

                if( $occurences[$id] > 0 ) {
                    $message = "Erreur lors de la tentative de suppression de l'entrée d'id {$id} pour le modèle {$this->Controller->{$this->Controller->modelClass}->alias}: cette entrée possède {$occurences[$id]} enregistrements liés.";
                    throw new error500Exception( $message );
                }
            }

			if( $this->Controller->{$this->Controller->modelClass}->delete( $id ) ) {
				$this->Controller->Session->setFlash( __( 'Delete->success' ), 'flash/success' );
			}
			else {
				$this->Controller->Session->setFlash( __( 'Delete->error' ), 'flash/error' );
			}

			$this->Controller->redirect( $this->Controller->referer() );
		}

		/**
		 * Sorte de "super" postConditions(), retourne un querydata à partir des
		 * données et des opérations.
		 *
		 * @param array $data
		 * @param array $operations
		 * @return array
		 */
		protected function _conditions( array $data, array $operations ) {
			$conditions = array( );

			/// Reformat values
			$data = Hash::expand( Set::normalize( $data ) );
			if( !empty( $data ) ) {
				foreach( $data as $model => $params ) {
					$model = ClassRegistry::init( $model );
					foreach( $params as $field => $value ) {
						/// FORMAT VALUE -> date
						if( is_array( $value ) && ( array_keys( $value ) == array( 'day', 'month', 'year' ) ) ) {
							$value = "{$value['year']}-{$value['month']}-{$value['day']}";
						}
						/// FORMAT VALUE -> phone, montant, ...
						else {
							$model->create( array( $model->alias => array( $field => $value ) ) );
							$model->Behaviors->trigger( 'beforeValidate', array( $model ) );
							$value = Set::classicExtract( $model->data, "{$model->name}.{$field}" );
						}

						if( $model->getColumnType( $field ) == 'datetime' ) {
							$data[] = "{$model->alias}.{$field} BETWEEN '{$value}' AND '".date( 'Y-m-d', strtotime( $value ) + ( 24 * 60 * 60 ) )."'";
							$data = Hash::remove( $data, "{$model->alias}.{$field}" );
						}
						else {
							$data[$model->alias][$field] = $value;
						}
					}
				}
			}

			/// Special operations
			$operations = Hash::flatten( Set::normalize( $operations ) );
			if( !empty( $operations ) ) {
				foreach( $operations as $path => $operation ) {
					switch( strtoupper( $operation ) ) {
						case 'BETWEEN':
							if( Set::check( $data, "{$path}_from" ) && Set::check( $data, "{$path}_to" ) ) {
								$from = Set::classicExtract( $data, "{$path}_from" );
								$to = Set::classicExtract( $data, "{$path}_to" );

								foreach( array( 'from', 'to' ) as $var ) {
									if( is_array( ${$var} ) ) {
										${$var} = "{${$var}['year']}-{${$var}['month']}-{${$var}['day']}";
									}
								}

								$conditions[] = "{$path} BETWEEN '{$from}' AND '{$to}'";
								$data = Hash::remove( $data, "{$path}_from" );
								$data = Hash::remove( $data, "{$path}_to" );
							}
							break;
						case 'LIKE':
						case 'ILIKE':
							if( Set::check( $data, $path ) ) {
								$value = Set::classicExtract( $data, $path );
								list( $model, $field ) = model_field( $path );

								$model = ClassRegistry::init( $model );

								$conditions["{$path} ".( $model->getDataSource() instanceof Postgres ? 'ILIKE' : 'LIKE' )] = "%$value%";
								$data = Hash::remove( $data, $path );
							}
							break;
						default:
							if( Set::check( $data, $path ) ) {
								$value = Set::classicExtract( $data, $path );
								$conditions["{$path} {$operation}"] = $value;
								$data = Hash::remove( $data, $path );
							}
							break;
					}
				}
			}

			/// data that were not in special formatting.
			$data = Hash::flatten( (array)$data );
			$data = Hash::filter( (array)$data );
			$data = Hash::expand( $data );

			if( !empty( $data ) ) {
				$data = Hash::flatten( $data );
				foreach( $data as $path => $value ) {
					$conditions[$path] = $value;
				}
			}

			return $conditions;
		}
	}
?>