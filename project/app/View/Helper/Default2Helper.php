<?php
	/**
	 * Fichier source de la classe Default2Helper.
	 *
	 * PHP 5.3
	 *
	 * @package app.View.Helper
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppHelper', 'View/Helper' );

	/**
	 * @url http://fr2.php.net/manual/fr/function.array-merge.php#95294
	 *
	 * @param type $a
	 * @param type $b
	 * @return type
	 */
	function array_extend( $a, $b ) {
		foreach($b as $k=>$v) {
			if( is_array($v) ) {
				if( !isset($a[$k]) ) {
					$a[$k] = $v;
				} else {
					$a[$k] = array_extend($a[$k], $v);
				}
			} else {
				$a[$k] = $v;
			}
		}
		return $a;
	}

	/**
	 * La classe Default2Helper ...
	 *
	 * @package app.View.Helper
	 */
	class Default2Helper extends AppHelper
	{
		public $helpers = array( 'Xhtml', 'Html', 'Xpaginator2', 'Locale', 'Xform', 'Type2', 'Permissions' );

		/**
		* TODO docs
		*/

		public function button( $type, $url, $htmlAttributes = array(), $confirmMessage = false ) {
			$enabled = ( isset( $htmlAttributes['enabled'] ) ? $htmlAttributes['enabled'] : true );
			$iconFileSuffix = ( ( $enabled ) ? '' : '_disabled' ); // TODO: les autres aussi

			$label = ( isset( $htmlAttributes['label'] ) ? $htmlAttributes['label'] : null );
			$htmlAttributes = array_filter_keys( $htmlAttributes, array( 'enabled', 'label' ), true );

			// TODO: une fonction ?
			$urlParams = Router::parse( preg_replace( '/^'.preg_quote( $this->request->base, '/' ).'/', '', Router::url( $url ) ) );
			$controllerName = Inflector::camelize( $urlParams['controller'] );

			if( empty( $label ) ) {
				$label = __( "Button::{$urlParams['action']}" );
			}

			if( isset( $url['controller'] ) && isset( $url['action'] ) ) {
				$enabled = $this->Permissions->check( $url['controller'], $url['action'] ) && $enabled;
			}

			$class = implode(
				' ',
				array(
					'button',
					$type,
					( $enabled ? 'enabled' : 'disabled' ),
					( isset( $htmlAttributes['class'] ) ? $htmlAttributes['class'] : null ),
				)
			);
			$htmlAttributes['class'] = $class;
			$htmlAttributes['escape'] = false;

			if( $enabled ) {
				return $this->Xhtml->link(
					$label,
					$url,
					$htmlAttributes,
					$confirmMessage
				);
			}
			else {
				return $this->Xhtml->tag( 'span', $label, $htmlAttributes, false, false );
			}
		}

		/**
		* @param string $path ie. User.id, User.0.id or Users::view
		* @param array $params
		* @return string
		* Valid keys for params:
		*	- domain
		*	- label
		*/

		public function label( $column, $options = array() ) {
			if( isset( $options['label'] ) ) {
				return $options['label'];
			}

			$domain = null;
			if( isset( $options['domain'] ) ) {
				$domain = $options['domain'];
			}

			// Posts::view
			if( strstr( $column, '::' ) !== false ) {
				list( $controller, $action ) = explode( '::', $column );

				if( empty( $options['domain'] ) ) {
					$domain = Inflector::singularize( Inflector::tableize( $controller ) );
				}

				return __d( $domain, $column );
			}

			// Post.id
			list( $currentModelName, $currentFieldName ) = model_field( $column );
			if( empty( $options['domain'] ) ) {
				$domain = Inflector::singularize( Inflector::tableize( $currentModelName ) );
			}

			return __d( $domain, "{$currentModelName}.{$currentFieldName}" );
		}

		/**
		* @param array $datas
		* @param string $path ie. User.id
		* @param array $params
		* @return string
		* Valid keys for params:
		*	- model
		*	- type
		*	- domain -> TODO: unneeded ?
		*	- tag
		*	- options ie. array( 'User' => array( 'status' => array( 1 => 'Enabled', 0 => 'Disabled' ) ) )
		*	- TODO: value et type
		*/

		public function format( $datas, $path, $params = array() ) {
			return $this->Type2->format( $datas, $path, $params );
		}

		/**
		*
		*/

		public function thead( $columns, $params = array() ) {
			$thead = array();
			$actions = Set::classicExtract( $params, 'actions' );

			$options = array();
			if( isset( $params['paginate'] ) && is_string( $params['paginate'] ) ) {
				$options['model'] = $params['paginate'];
			}

			foreach( Set::normalize( $columns ) as $column => $options ) {
				$label = $this->label( $column, $options );

				if( Set::check( $this->request->params, 'paging' ) && ( !isset( $options['sort'] ) || $options['sort'] ) ) {
					$thead[] = $this->Xpaginator2->sort( $label, $column, $options );
				}
				else {
					$thead[] = $label;
				}
			}

			$thead = $this->Html->tableHeaders( $thead );

			if( is_array( $actions ) && !empty( $actions ) ) {
				$thead = str_replace(
					'</tr>',
					'<th colspan="'.count( $actions ).'" class="action">Actions</th></tr>',
					$thead
				);
			}

			if( Set::check( $params, 'tooltip' ) ) {
				$thead = preg_replace( '/<\/tr>$/', "<th class=\"innerTableHeader noprint\">Informations complementaires</th></tr>", $thead );
			}

			return $this->Xhtml->tag( 'thead', $thead );
		}

		/**
		*
		*/

		public function actions( $line, $params ) {
			$actions = Set::normalize( Set::classicExtract( $params, 'actions' ) );
			$tds = array();

			if( is_array( $actions ) && !empty( $actions ) ) {
				foreach( $actions as $action => $actionParams ) {
					if( $this->_translateVisible( $line, $actionParams ) ) {
						list( $controller, $action ) = explode( '::', $action );
						$controllerUrl = Inflector::underscore( $controller );
						$modelName = Inflector::classify( $controllerUrl );
						$domain = Inflector::singularize( Inflector::tableize( $modelName ) );

						$primaryKey = $this->Type2->primaryKey( $modelName );
						$displayField = $this->Type2->displayField( $modelName );

						$primaryKeyValue = Set::classicExtract( $line, "{$modelName}.{$primaryKey}" );
						$displayFieldValue = Set::classicExtract( $line, "{$modelName}.{$displayField}" );

						$enabled = !$this->Type2->translateDisabled( $line, $actionParams );

						$label = @$actionParams['label'];

						// TODO
						unset( $actionParams['disabled'] );
						unset( $actionParams['condition'] );
						unset( $actionParams['label'] );

						// FIXME: à mettre dans le DefaultHelper 1.3
						$url = (array)Set::classicExtract( $actionParams, 'url' );
						$url = array_extend(
							array(
								'controller' => $controllerUrl,
								'action' => $action,
								$primaryKeyValue
							),
							$url
						);
						// TODO: c'est moche ?
						foreach( $url as $key => $value ) {
							$url[$key] = dataTranslate( $line, $value );
						}

						if( $action == 'delete' ) {
							///INFO: on ne surcharge pas le onclick et la class pour le type delete
							$value = $this->button(
								'delete',
								$url,
								array(
									'label' => $label,
									'enabled' => $enabled,
									'title' => sprintf(
										__d( $domain, "{$controller}::{$action}" ),
										$displayFieldValue
									)
								),
								sprintf(
									__d( $domain, "{$controller}::{$action}::confirm" ),
									$displayFieldValue
								)
							);
						}
						else {
							$hParams = array(
								'label' => $label,
								'enabled' => $enabled,
								'title' => sprintf(
									__d( $domain, "{$controller}::{$action}" ),
									$displayFieldValue
								)
							);

							foreach( array( 'onclick', 'class' ) as $h ) {
								if( isset( $actionParams[$h] ) ) {
									$hParams[$h] = $actionParams[$h];
								}
							}

							// Ajout d'un message de confirmation si nécessaire
							$confirmMessage = ( ( isset( $actionParams['confirm'] ) && !empty( $actionParams['confirm'] ) ) ? $actionParams['confirm'] : false );

							$value = $this->button(
								$action,
								$url,
								$hParams,
								$confirmMessage
							);
						}

						$tds[] = $this->Xhtml->tag( 'td', $value, array( 'class' => 'action' ) );
					}
				}
			}

			return implode( '', $tds );
		}

		/**
		*
		*/

		protected function _translateVisible( $data, $params ) {
			if( !isset( $params['condition'] ) ) {
				return true;
			}
			return $this->Type2->evaluate( $data, $params['condition'] );
		}

		/**
		*
		*/

		protected function _evalTrClass( $data, $params ) {
			extract( $params['params'] );
			return eval( 'return '.dataTranslate( $data, $params['eval'] ).';' );
		}

		/**
		* @param array $datas
		* @param array $cells ie. array( 'User.status' => array( 'domain' => 'Cohorte' ), 'User.userae' )
		* @param array $params
		* @return string
		* Valid keys for params:
		*	- domain
		*	- cohorte -> true/false
		*	- hidden
		*	- options ie. array( 'User' => array( 'status' => array( 1 => 'Enabled', 0 => 'Disabled' ) ) )
		* 	- tooltip
		*	- valuePath en paramètre de chacun des input
		*	- groupColumns
		*/

		public function index( $datas, $cells, $cohorteParams = array() ) {
			/// TODO: supprimer le bouton ajouter de l'index
			/// TODO: function
			$name = Inflector::camelize( $this->request->params['controller'] );
			$action = $this->action;
			// FIXME: est-ce plus correct + MAJ 1.3.x_default_helper
			$modelName = Inflector::classify( Inflector::underscore( $name ) );
			$cohorte = Set::classicExtract( $cohorteParams, 'cohorte' );
			$domain = isset( $cohorteParams['domain'] ) ? $cohorteParams['domain'] : Inflector::singularize( Inflector::tableize( $modelName ) );
			///

			$cells = Set::normalize( $cells );
			$cohorteOptions = Set::classicExtract( $cohorteParams, "options" );
			$cohorteHidden = Set::classicExtract( $cohorteParams, "hidden" );

			if( Set::check( $cohorteParams, "id" ) ) {
				$containerId = $value = Set::classicExtract( $cohorteParams, "id" );
				if( !$value ) {
					unset( $cohorteParams['id'] );
				}
			}
			else {
				$containerId = $cohorteParams['id'] = Inflector::camelize( "{$name}_{$action}" );
			}

			$oddOptions = array( 'class' => 'odd');
			$evenOptions = array( 'class' => 'even');

			$trs = array();
			foreach( $datas as $key => $data ) {
				$iteration = 0;
				$line = array();
				foreach( $cells as $path => $params ) {
					if( $this->_translateVisible( $data, $params ) ) {
						$params = $this->Type2->prepare( 'output', $path, $params );
						unset( $params['sort'] );

						list( $model, $field ) = model_field( $path );
						$validationErrors = $this->validationErrors[$modelName];

						$cohortePath = str_replace( ".", ".$key.", $path );
						$type = Set::classicExtract( $params, 'input' );
						unset( $params['input'] );

						if( !empty( $cohorteOptions ) && !isset( $params['options'] ) ) {
							$params['options'] = $cohorteOptions;
						}

						// TODO
						if( !Set::check( $this->request->data, $cohortePath ) ) {
							$params['value'] = Set::classicExtract( $data, $path );
						}

						$hiddenFields = '';
						if( ( $cohorte == true ) && ( $iteration == 0 ) && !empty( $cohorteHidden ) ) {
							foreach( Set::normalize( $cohorteHidden ) as $hiddenPath => $hiddenParams ) {
								$hiddenParams = Set::merge( $hiddenParams, array( 'type' => 'hidden' ) );
								if( !Set::check( $this->request->data, $cohortePath ) ) {
									if( !Set::check( $hiddenParams, 'value' ) ) {
										if( Set::check( $hiddenParams, 'valuePath' ) ) {
											$hiddenParams['value'] = Set::classicExtract( $data, $hiddenParams['valuePath'] );
											unset( $hiddenParams['valuePath'] );
										}
										else {
											$hiddenParams['value'] = Set::classicExtract( $data, $hiddenPath );
										}
									}
								}

								$hiddenFields .= $this->Xform->input( str_replace( ".", ".$key.", $hiddenPath ), $hiddenParams );
							}
						}

						if( !empty( $type ) ) {
							switch( $type ) {
								case 'radio':
								case 'checkbox':
								case 'select':
								case 'text':
								case 'date':
									$params['type'] = $type;
									$params['label'] = false;
									$params['legend'] = false;
									$params['div'] = false;

									if( !in_array( $type, array( 'select', 'radio' ) ) ) {
										unset( $params['options'] );
									}
									else if( Set::check( $cohorteParams, "options.{$model}.{$field}" ) ) {
										$params['options'] = Set::classicExtract( $cohorteParams, "options.{$model}.{$field}" );
									}

									if( !isset( $params['multiple'] ) && !in_array( $type, array( 'radio' ) ) ) {
										unset( $params['legend'] );
									}

									if( in_array( $type, array( 'radio' ) ) ) {
										unset( $params['label'] );
									}

									if( !Set::check( $this->request->data, $path ) ) {
										if( Set::check( $params, 'valuePath' ) ) {
											$value = Set::classicExtract( $data, $params['valuePath'] );
											unset( $params['valuePath'] );
										}
									}

									/// TODO: avec $this->request->data
									if( $type == 'checkbox' && Set::check( $params, 'value' ) ) {
										$params['checked'] = ( $params['value'] ? true : false );
									}


									$tdParams = array( 'class' => "input {$type}" );
									if( Set::check( $validationErrors, "{$key}.{$field}" ) ) {
										$tdParams = $this->addClass( $tdParams, 'error' );
									}

									$params['disabled'] = $this->Type2->translateDisabled( $data, $params );
									$line[] = $this->Xhtml->tag( 'td', $hiddenFields.$this->Type2->input( $cohortePath, $params )/*.$error*/, $tdParams );
									break;

								case 'heureseance':
									// Champ caché pour récupérer l'id du passage
									$tabHidden = explode(".", $params['hidden']);
									$idPassageCommissionEp = $data[$tabHidden[0]][$tabHidden[1]];
									$hiddenFields .= $this->Xform->input( str_replace( ".", ".$key.", $params['hidden'] ), array( 'label' => false, 'type' => 'hidden', 'value' => $idPassageCommissionEp ));
									$heureAlerte = '';

									if (!is_null ($data['Passagecommissionep']['heureseance'])) {
										$alertes = Configure::read( 'commissionep.heure.alertes' );

										// Comparaison des heures
										$heurePassage = new DateTime ($params['dateseance']);
										$temp = explode(':', $data['Passagecommissionep']['heureseance']);
										$heurePassage->setTime($temp[0], $temp[1]);
										$heureEnErreur = false;

										// Comparaison avec l'heure de début
										if ($alertes['journee.debut']) {
											$heureDeb = new DateTime ($params['dateseance']);
											if ($heureDeb->format('H') == 0) {
												$temp = explode(':', Configure::read( 'commissionep.heure.debut.standard' ));
												$heureDeb->setTime($temp[0], $temp[1]);
											}
											$interval = $heurePassage->diff($heureDeb);
											if (($interval->h > 0 || $interval->i > 0) && $interval->invert == 0) { // Avant l'heure de début
												$heureEnErreur = true;
											}
										}

										// Comparaison avec l'heure de fin
										if ($alertes['journee.fin']) {
											$temp = explode(':', Configure::read( 'commissionep.heure.fin.standard' ));
											$heureFin = new DateTime ($params['dateseance']);
											$heureFin->setTime($temp[0], $temp[1]);
											$interval = $heurePassage->diff($heureFin);
											if ((($interval->h > 0 || $interval->i > 0) && $interval->invert == 1) // Après l'heure de fin
												|| ($interval->h == 0 && $interval->i == 0 && $interval->invert == 0)) { // Égal à l'heure de fin
												$heureEnErreur = true;
											}
										}

										// Comparaison avec la pause méridienne
										if ($alertes['pause.meridienne']) {
											$temp = Configure::read( 'commissionep.heure.debut.pause.meridienne' );
											$pauseDeb = new DateTime ($params['dateseance']);
											$pauseDeb->setTime($temp['heure'], $temp['minute']);
											$temp = Configure::read( 'commissionep.heure.fin.pause.meridienne' );
											$pauseFin = new DateTime ($params['dateseance']);
											$pauseFin->setTime($temp['heure'], $temp['minute']);
											$diffDeb = $heurePassage->diff ($pauseDeb);
											$diffFin = $heurePassage->diff ($pauseFin);
											if (((($diffDeb->h > 0 || $diffDeb->i > 0) && $diffDeb->invert == 1) // Après l'heure de début
												|| ($diffDeb->h == 0 && $diffDeb->i == 0 && $diffDeb->invert == 0)) // Égal à l'heure de début
												&& (($diffFin->h > 0 || $diffFin->i > 0) && $diffFin->invert == 0)) { // Avant l'heure de fin
												$heureEnErreur = true;
											}
										}

										// Comparaison avec les autres dates
										if ($alertes['meme.heure']) {
											$touteslesheuresdepassage = $params['touteslesheuresdepassage'];
											foreach ($touteslesheuresdepassage as $keyB => $valueB) {
												if (!is_null($valueB)) {
													$heureComparee = new DateTime ($params['dateseance']);
													$temp = explode(':', $valueB);
													$heureComparee->setTime($temp[0], $temp[1]);
													$interval = $heurePassage->diff($heureComparee);

													if ($interval->h == 0 && $interval->i == 0 && $interval->invert == 0 // Égal à une heure de passage
														&& $idPassageCommissionEp != $keyB) { // N'est pas lui-même
														$heureEnErreur = true;
														break;
													}
												}
											}
										}

										if ($heureEnErreur) {
											$heureAlerte = 'style="background: #f20000;"';
										}

										$data['Passagecommissionep']['heureseance'] = substr($data['Passagecommissionep']['heureseance'], 0, -3);
									}

									$line[] = '<td class="input heureseance">'.$hiddenFields.'<input '.$heureAlerte.' name="data[Passagecommissionep]['.$key.'][heureseance]"  type="text" value="'.$data['Passagecommissionep']['heureseance'].'" id="Passagecommissionep'.$key.'Heureseance"/></td>';
									break;

								default:
									$params['disabled'] = $this->Type2->translateDisabled( $data, $params );
									$line[] = $this->Xhtml->tag( 'td', $hiddenFields.$this->Type2->format( $data, $path, $params ) );
							}
						}
						else {
							$td = $this->Type2->format( $data, $path, Set::merge( $params, array( 'tag' => 'td' ) ) );
							$line[] = preg_replace( '/<\/td>$/', "$hiddenFields</td>", $td );
						}
						$iteration++;
					}
				}

				$line = implode( '', $line ).$this->actions( $data, $cohorteParams );
				if( Set::check( $cohorteParams, 'tooltip' ) ) {
					$tooltip = Set::extract( $cohorteParams, 'tooltip' );
					$tooltip = $this->view( $data, $tooltip, array( 'widget' => 'table', 'class' => 'innerTable', 'id' => "innerTable{$containerId}{$key}" ) );
					$line .= $this->Xhtml->tag( 'td', $tooltip, array( 'class' => 'innerTableCell noprint' ) );
				}

				$trOptions = ( ( ( $key + 1 ) % 2 ) ?  $oddOptions : $evenOptions );
				/// TODO: prefixer l'id du conteneur si présent + si l'id est à false -> pas d'id, sinon calcul auto
				$trOptions['id'] = $containerId.'Row'.( $key + 1 );

				if( isset( $cohorteParams['trClass'] ) ) {
					$trOptions = $this->addClass( $trOptions, $this->_evalTrClass( $data, $cohorteParams['trClass'] ) );
				}

				$trs[] = $this->Xhtml->tag( 'tr', $line, $trOptions );
			}

			$return = '';

			/// Liste d'actions communes à la table
			if( Set::check( $cohorteParams, 'add' ) ) { // TODO: ensemble d'actions
				$actions = Set::normalize( Set::classicExtract( $cohorteParams, 'add' ) );

				if( $actions == true ) {
					$controllerName = Inflector::camelize( $this->request->params['controller'] );
					/// INFO: modification pour personaliser l'url
					/// INFO2: modification pour permettre de désactiver le bouton add
					$url = array();
					$disabled = false;
					foreach( $actions as $text => $actionParams ) {
						if ( $text == 'disabled' ) {
							$disabled = $actionParams;
						}
						elseif ( preg_match( '/\.add/', $text ) && !empty( $actionParams ) ) {
							$url = $actionParams;
						}
					}
					if( !empty( $actions['url'] ) ) {
						$url = $actions['url'];
					}
					if ( empty( $url ) ) {
						$url = array( 'controller' => $this->request->params['controller'], 'action' => 'add' );
					}
					$actions = array(
						"{$controllerName}::add" => array( 'url' => $url, 'disabled' => $disabled )
					);
				}

				$lis = array();
				foreach( $actions as $text => $actionParams ) {
					$lis[] = $this->Xhtml->tag(
						'li',
						$this->button(
							$actionParams['url']['action'],
							$actionParams['url'],
							array( 'title' => __d( $domain, $text ), 'enabled' => ( ( isset( $actionParams['url']['disabled'] ) ) ? !$actionParams['url']['disabled'] : true ) && ( ( isset( $actionParams['disabled'] ) ) ? !$actionParams['disabled'] : true ) )
						),
						array( 'class' => $actionParams['url']['action'] )
					);
				}
				$return .= $this->Xhtml->tag(
					'ul',
					implode( "\n", $lis ),
					array( 'class' => 'actions' )
				);
			}

			if( empty( $trs ) ) {
				return $return.$this->Xhtml->tag(
					'p',
					__d( $domain, "{$modelName}::index::empty" ),
					array( 'class' => 'notice' )
				);
			}

			$tableOptions = array( 'id' => $containerId );
			if( Set::check( $cohorteParams, 'tooltip' ) ) { /// TODO: th
				$tableOptions['class'] = 'tooltips';
			}

			/// TODO
			$paginateModel = $modelName;
			if( isset( $params['paginate'] ) && is_string( $cohorteParams['paginate'] ) ) {
				$paginateModel = $cohorteParams['paginate'];
			}

			if( Set::check( $cohorteParams, 'paginate' ) ) {
				$paginateModel = Set::classicExtract( $cohorteParams, 'paginate' );
			}

			$thead = $this->thead( $cells, $cohorteParams );
			if( Set::check( $cohorteParams, 'groupColumns' ) ) {
				$groupColumns = Set::classicExtract( $cohorteParams, 'groupColumns' );
				$thead = $this->groupColumns( $thead, $groupColumns );
			}

			$pagination = $this->Xpaginator2->paginationBlock( $paginateModel, Set::merge( $this->request->params['pass'], $this->request->params['named'] ) );
			$return .= $pagination.$this->Xhtml->tag(
				'table',
				$thead.
				$this->Xhtml->tag( 'tbody', implode( '', $trs ) ),
				$tableOptions
			).$pagination;

			if( $cohorte == true ) {
				$options = array( 'novalidate' => true, 'url' => Set::merge( array( 'controller' => $this->request->params['controller'], 'action' => $this->request->params['action'] ), $this->request->params['pass'], $this->request->params['named'] ) );
				if( isset( $cohorteParams['cohorteFormId'] ) ) {
					$options['id'] = $cohorteParams['cohorteFormId'];
				}
				$return = $this->Xform->create( null, $options ).$return;
			}

			/// Hidden -> TODO $this->request->data
			if( ( $cohorte == true ) && Set::check( $cohorteParams, 'search' ) ) {
				foreach( Set::extract( $cohorteParams, 'search' ) as $searchModelField ) {
					$key = "Search.$searchModelField";
					$return .= $this->Xform->input( $key, array( 'type' => 'hidden' ) );
				}
			}
			/// TODO: ids
			if( $cohorte == true ) {
				if( Set::check( $this->request->data, 'Search' ) ) { /// TODO: + page / sort / ...
					$search = Set::extract( $this->request->data, 'Search' );
					if( !empty( $search ) ) {
						$search = Hash::flatten( array( 'Search' => $search ) );
						foreach( $search as $path => $value ) {
							$return .= '<div>'.$this->Xform->input( $path, array( 'type' => 'hidden', 'id' => null ) ).'</div>';
						}
					}
				}

				if( Set::check( $cohorteParams, 'cohortehidden' ) ) {
					if( !empty( $cohorteParams['cohortehidden'] ) ) {
						foreach( $cohorteParams['cohortehidden'] as $cpath => $coptions ) {
							$return .= $this->Xform->input( $cpath, Set::merge( array( 'type' => 'hidden' ), $coptions ) );
						}
					}
				}

				$defaultLabelCohorteParams = array( 'name' => 'cohorte', 'div' => false );

				$submits = '';
				if( isset( $cohorteParams['labelcohorte'] ) ) {
					if( !is_array( $cohorteParams['labelcohorte'] ) ) {
						$cohorteParams['labelcohorte'] = array( $cohorteParams['labelcohorte'] => $defaultLabelCohorteParams );
					}
					foreach( Set::normalize( $cohorteParams['labelcohorte'] ) as $labelcohorte => $labelcohorteparams ) {
						$submits .= $this->Xform->submit( $labelcohorte, Set::merge( $defaultLabelCohorteParams, (array)$labelcohorteparams ) );
					}
				}
				else {
					$submits .= $this->Xform->submit( __( 'Validate' ), $defaultLabelCohorteParams );
				}

				$return .= $this->Xhtml->tag( 'div', $submits, array( 'class' => 'submit' ) );

				$return .= $this->Xform->end();

				$css = ( Configure::read( 'debug' ) > 0 ? $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) ) : null );
				$return = $css.$return;
			}

			return $return;
		}

		/**
		*
		*/

		public function subform( $fields, $formParams = array() ) {
			$default = array();

			$fields = Set::normalize( $fields );
			foreach( $fields as $fieldName => $options ) {
				$fields[$fieldName] = Set::merge( $default, $options );
			}

			$return = '';

			foreach( $fields as $path => $params ) {
				list( $fieldModelName, $fieldModelfield ) = model_field( $path );
				if( !Set::check( $params, 'options' ) ) {
					$options = Set::extract( $formParams, "options.{$fieldModelName}.{$fieldModelfield}" );
					if( !empty( $options ) ) {
						$params['options'] = $options;
					}
				}
				$return .= $this->Type2->input( $path, $params );
			}

			return $return;
		}

		/**
		*
		*/

		public function form( $fields, $formParams = array() ) {
			$name = Inflector::camelize( $this->request->params['controller'] );
			$action = $this->action;
			/// TODO: vérifier, c'est tjs le classify du nom de la table
			$modelName = Inflector::classify( $this->request->params['controller'] );
			$domain = Inflector::singularize( Inflector::tableize( $modelName ) );

			$primaryKey = $this->Type2->primaryKey( $modelName );
			$primaryKeyValue = Set::classicExtract( $this->request->data, "{$modelName}.{$primaryKey}" );

			$return = '';
			$return .= $this->Xform->create( null, array( 'novalidate' => true, 'inputDefaults' => array( 'domain' => $domain ) ) );

			if( !empty( $primaryKeyValue ) ) {
				$return .= $this->Xform->input( "{$modelName}.{$primaryKey}" );
			}

			$return .= $this->subform( $fields, $formParams );

			/// Form buttons -> TODO: en faire une fonction
			$submit = array( 'Save' => 'submit' );
			if( Set::check( $formParams, 'submit' ) ) {
				$submit = Set::classicExtract( $formParams, 'submit' );
				if( is_string( $submit ) ) {
					$submit = array( $submit => 'submit' );
				}
			}

			$buttons = array();
			$default = array( 'type' => 'submit' );
			foreach( $submit as $value => $options ) {
				if( is_string( $options ) ) {
					$options = array( 'type' => $options );
				}
				$options = Set::merge( $default, $options );
				$options['class'] = "input {$options['type']}";
				$buttons[] = $this->Xform->button( __( $value ), $options );
			}

			$return .= $this->Xhtml->tag( 'div', implode( ' ', $buttons ), array( 'class' => 'submit' ) );
			$return .= $this->Xform->end();

			$css = ( Configure::read( 'debug' ) > 0 ? $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) ) : null );
			return $css.$return;
		}

		/**
		*
		*/

		public function search( array $fields, array $params = array() ) {
			$params = Set::merge(
				array( 'form' => true, 'novalidate' => true ),
				$params
			);
			$form = $params['form'];
			unset( $params['form'] );

			$domain = strtolower( Inflector::classify( $this->request->params['controller'] ) );

			$params['inputDefaults'] = Set::merge(
				array(
					'required' => false,
					'domain' => $domain,
					// TODO: le faire pour les bons input
				),
				Set::extract(
					$params,
					'inputDefaults'
				)
			);

			$paramsOptions = Set::extract( $params, 'options' );
			unset( $params['options'] );

			// Was search data sent ?
			$data = ( !empty( $this->request->data ) ? array_keys( $this->request->data ) : array() );
			$data = Hash::expand( Set::normalize( $data ) );
			// FIXME: ajouter le bouton pour le déplier

			$return = '';

			if( !empty( $form ) ) {
				$return .= $this->Xform->create( null, $params );
			}

			foreach( Set::normalize( $fields ) as $fieldName => $options ) {
				list( $fieldModelName, $fieldModelfield ) = model_field( $fieldName );

				/// TODO: function ?
				if( Set::check( $paramsOptions, "{$fieldModelName}.{$fieldModelfield}" ) && empty( $options['options'] ) ) {
					$options['options'] = Set::classicExtract( $paramsOptions, "{$fieldModelName}.{$fieldModelfield}" );
				}

				list( $options['model'], $options['field'] ) = model_field( $fieldName );
				$options = $this->Type2->prepare( 'input', $fieldName, $options );
				$return .= $this->Type2->input( "Search.$fieldName", $options );
			}

			if( !empty( $form ) ) {
				$return .= '<div>'.$this->Xform->input( "Search.active", array( 'value' => true, 'type' => 'hidden' ) ).'</div>';
				$return .= $this->Xform->submit( __( 'Search' ) );
				$return .= $this->Xform->end();
			}

			$css = ( Configure::read( 'debug' ) > 0 ? $this->Html->css( array( 'all.form' ), 'stylesheet', array( 'media' => 'all', 'inline' => false ) ) : null );
			return $css.$return;
		}

		/**
		* @param array $item
		* @param array $columns ie. array( 'User.status', 'User.userae' )
		* @param array $options
		* @return string
		* Valid keys for params:
		*	- widget -> dl, table
		*	- options ie. array( 'User' => array( 'status' => array( 1 => 'Enabled', 0 => 'Disabled' ) ) )
		*   - domain
		* TODO: $this->defaultModel() à la place de $this->request->params, 'controller'
		*/

		public function view( $item, $columns, $options = array() ) { // TODO: rename options en viewParams
			$widget = Set::classicExtract( $options, 'widget' );
			$widget = ( empty( $widget ) ? 'table' : $widget );
			unset( $options['widget'] );

			$name = Inflector::camelize( Set::classicExtract( $this->request->params, 'controller' ) ); // TODO -> params + params -> table/list
			$modelName = Inflector::classify( $name );

			$rows = array();
			$lineNr = 1;
			foreach( Set::normalize( $columns ) as $column => $columnOptions ) {
				$columnOptions = $this->Type2->prepare( 'output', $column, $columnOptions );
				list( $columnModel, $columnField ) = model_field( $column );
				$columnDomain = Inflector::singularize( Inflector::tableize( $columnModel ) );
				/// dans une fonction ?

				if( !Set::check( $columnOptions, 'domain' ) ) {
					if( Set::check( $options, 'domain' ) ) {
						$columnOptions['domain'] = $options['domain'];
					}
					else {
						$columnOptions['domain'] = $columnDomain;
					}
				}

				$formatOptions = $labelOptions = $columnOptions = $this->addClass( $columnOptions, ( ( $lineNr % 2 ) ?  'odd' : 'even' ) );

				/// TODO
				unset(
					$columnOptions['domain'],
					$columnOptions['type'],
					$columnOptions['null'],
					$columnOptions['default'],
					$columnOptions['country'],
					$columnOptions['length'],
					$columnOptions['virtual'],
					$columnOptions['key'],
					$columnOptions['options'],
					$columnOptions['dateFormat'],
					$columnOptions['maxlength'],
					$columnOptions['suffix'],
					$columnOptions['currency'],
					$columnOptions['value'],
					$columnOptions['empty']
				);

				$line = $this->Xhtml->tag(
					( ( $widget == 'table' ) ? 'th' : 'dt' ),
					$this->label( $column, $labelOptions ),
					$columnOptions
				);

				$params = array( 'tag' => ( ( $widget == 'table' ) ? 'td' : 'dd' ) );
				foreach( array( 'options', 'type', 'class', 'domain' ) as $optionsKey ) {
					if( isset( $columnOptions[$optionsKey] ) ) {
						$params[$optionsKey] = $columnOptions[$optionsKey];
					}
				}

				if( $widget == 'dl' ) {
					$params['class'] = $columnOptions['class'];
				}

				if( Set::check( $options, 'options' ) && !Set::check( $params, 'options' ) ) {
					$params['options'] = $options['options'];
				}

				$params = Set::merge( $params, $formatOptions );
				unset( $params['null'], $params['country'], $params['length'] );

				$line .= $this->Type2->format( $item, $column, $params );

				if( $widget == 'table' ) {
					$rows[] = $this->Xhtml->tag( 'tr', $line, array( 'class' => $params['class'] ) );
				}
				else {
					$rows[] = $line;
				}

				$lineNr++;
			}

			$defaultOptions = array(
				'class' => 'view',
			);

			$options = Set::merge( $defaultOptions, $options );
			unset( $options['options'] );

			if( $widget == 'table' ) {
				$return = $this->Xhtml->tag(
					'table',
					$this->Xhtml->tag(
						'tbody',
						implode( '', $rows )
					),
					$options
				);
			}
			else {
				$return = $this->Xhtml->tag(
					'dl',
					implode( '', $rows ),
					$options
				);
			}

			return $return;
		}

		/**
		* TODO: faire h( la traduction )
		*/

		public function groupColumns( $thead, $group ) {
			preg_match_all( '/(<th(?!\w).*<\/th>)/U', $thead, $matches, PREG_PATTERN_ORDER );
			$ths = $matches[0];
			$firstline = array();
			$secondline = array();

			$group = Set::normalize( $group );
			$groupedColumns = Hash::flatten( $group );

			foreach( $ths as $position => $th ) {
				if( in_array( $position, $groupedColumns ) ) {
					$key = array_search( $position, $groupedColumns );
					if( preg_match( '/^(.*)\.0$/', $key, $matches ) ) { // premier
						$firstline[] = '<th colspan="'.count( $group[$matches[1]] ).'">'.$matches[1].'</th>';
					}

					$secondline[] = $th;
				}
				else {
					$firstline[] = preg_replace( '/(<th(?!\w))/U', '<th rowspan="2"', $th );
				}
			}

			return "<thead><tr>".implode( $firstline )."</tr><tr>".implode( $secondline )."</tr></thead>";
		}
	}
?>