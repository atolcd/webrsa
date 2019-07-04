<?php
	/**
	 * Fichier source de la classe XhtmlHelper.
	 *
	 * PHP 5.3
	 *
	 * @package app.View.Helper
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'ControllerCache', 'Model/Datasource' );
	App::uses( 'HtmlHelper', 'View/Helper' );

	/**
	 * La classe XhtmlHelper est une classe d'aide à l'écriture rapide de code
	 * HTML, qui surcharge la classe HtmlHelper du coeur de CakePHP.
	 *
	 * Certaines méthodes sont "magiques", celles correspondant aux clés de
	 * l'attribut $_linksMap.
	 *
	 * @package app.View.Helper
	 */
	class XhtmlHelper extends HtmlHelper
	{

		/**
		 * Contient les correspondances entre les méthodes xxxxxLink et leurs
		 * paramètres, à utiliser dans la méthode __call.
		 *
		 * Les valeurs correspondent aux paramètres $imagePath et $label de la
		 * méthode _buttonLink. Les paramètres suivants ($title, $url et
		 * $enabled = true) sont fournis par les paramètres passés lors de
		 * l'appel de ces fonctions "magiques".
		 *
		 * @see XhtmlHelper::_call(), XhtmlHelper::_call(), XhtmlHelper::_buttonLink, XhtmlHelper::$_linksMapAttributes
		 * @var array
		 */
		protected $_linksMap = array(
			'addLink' => array( 'icons/add.png', 'Ajouter' ),
			'addComiteLink' => array( 'icons/add.png', 'Ajouter un nouveau Comité' ),
			'addEquipeLink' => array( 'icons/add.png', 'Ajouter une nouvelle Equipe' ),
			'addSimpleLink' => array( 'icons/add.png', 'Ajouter une préconisation d\'orientation' ),
			'addPieceLink' => array( 'icons/add.png', 'Ajouter une pièce' ),
			'cancelLink' => array( 'icons/cancel.png', 'Annuler' ),
			'editLink' => array( 'icons/pencil.png', 'Modifier' ),
			'validateLink' => array( 'icons/tick.png', 'Valider' ),
			'actionsLink' => array( 'icons/lightning.png', 'Actions' ),
			'aidesLink' => array( 'icons/ruby.png', 'Aides' ),
			'ajoutcomiteLink' => array( 'icons/add.png', 'Ajout comité' ),
			'attachLink' => array( 'icons/attach.png', 'Visualiser' ),
			'printLink' => array( 'icons/printer.png', 'Imprimer' ),
			'notificationsApreLink' => array( 'icons/application_view_list.png', 'Notifications' ),
			'notificationsCer66Link' => array( 'icons/application_view_list.png', 'Notifications OP' ),
			'rapportLink' => array( 'icons/page_attach.png', 'Rapport' ),
			'treatmentLink' => array( 'icons/page_attach.png', 'Traitements' ),
			'reorientLink' => array( 'icons/door_out.png', 'Réorientation' ),
			'revertToLink' => array( 'icons/arrow_undo.png', 'Revenir à cette version' ),
			'presenceLink' => array( 'icons/pencil.png', 'Présences' ),
			'reponseLink' => array( 'icons/pencil.png', 'Réponses' ),
			'affecteLink' => array( 'icons/pencil.png', 'Affecter les dossiers' ),
			'avenantLink' => array( 'icons/add.png', 'Avenant' ),
			// Avec des valeurs de $attributes (voir $this->_linksMapAttributes)
			'conseilLink' => array( 'icons/door_out.png', 'Traitement par CD' ),
			'courrierLink' => array( 'icons/page_white_text.png', 'Courrier d\'information' ),
			'decisionLink' => array( 'icons/user_comment.png', 'Décisions' ),
			'equipeLink' => array( 'icons/door_out.png', 'Traitement par équipe' ),
			'printListLink' => array( 'icons/printer.png', 'Version imprimable' ),
			'exportLink' => array( 'icons/page_white_get.png', 'Télécharger le tableau' ),
			'ordreLink' => array( 'icons/book_open.png', 'Ordre du jour' ),
			'periodeImmersionLink' => array( 'icons/page_attach.png', 'Périodes d\'immersion' ),
			'remiseLink' => array( 'icons/money.png', 'Enregistrer les remises' ),
			'recgraLink' => array( 'icons/money_add.png', 'Recours gracieux' ),
			'recconLink' => array( 'icons/money_delete.png', 'Recours contentieux' ),
			'relanceLink' => array( 'icons/hourglass.png', 'Relancer' ),
			'propositionDecisionLink' => array( 'icons/user_comment.png', 'Propositions de décision' ),
		);

		/**
		 * Contient les paramètres supplémentaires à passer dans la variable
		 * $attributes pour certaines méthodes se trouvant dans $_linksMap.
		 *
		 * @see XhtmlHelper::_call(), XhtmlHelper::_call(), XhtmlHelper::_buttonLink, XhtmlHelper::$_linksMap
		 * @var array
		 */
		protected $_linksMapAttributes = array(
			'conseilLink' => array( 'class' => 'internal' ),
			'courrierLink' => array( 'class' => 'internal' ),
			'decisionLink' => array( 'class' => 'internal' ),
			'equipeLink' => array( 'class' => 'internal' ),
			'printListLink' => array( 'class' => 'external' ),
			'exportLink' => array( 'class' => 'external' ),
			'ordreLink' => array( 'class' => 'internal' ),
			'periodeImmersionLink' => array( 'class' => 'internal' ),
			'remiseLink' => array( 'class' => 'internal' ),
			'recgraLink' => array( 'class' => 'internal' ),
			'recconLink' => array( 'class' => 'internal' ),
			'relanceLink' => array( 'class' => 'internal' ),
			'propositionDecisionLink' => array( 'class' => 'internal' ),
		);

		/**
		 * Ajout d'un paramètre "enabled" dans $htmlAttributes qui permet de marquer un lien comme
		 * "désactivé" (texte grisé) lorsque "enabled" vaut "false".
		 *
		 * Gère le paramètre escape/escapeTitle pour CakePHP 1.2, 1.3 et 2.x.
		 *
		 * @param string $title
		 * @param mixed $url
		 * @param array $htmlAttributes
		 * @param string $confirmMessage
		 * @param boolean $escapeTitle
		 * @return string
		 */
		public function link( $title, $url = null, $htmlAttributes = array( ), $confirmMessage = false, $escapeTitle = true ) {
			if( isset( $htmlAttributes['escape'] ) ) {
				$escapeTitle = $htmlAttributes['escape'];
			}

			if( isset( $htmlAttributes['enabled'] ) && $htmlAttributes['enabled'] == false ) {
				if( $escapeTitle ) {
					$title = h( $title );
				}
				$htmlAttributes['class'] = ( isset( $htmlAttributes['class'] ) ? "{$htmlAttributes['class']} disabled" : "disabled" );

				return "<span class=\"{$htmlAttributes['class']}\">{$title}</span>";
			}
			else {
				unset( $htmlAttributes['enabled'] );

				$htmlAttributes['escape'] = $escapeTitle;
				return parent::link( $title, $url, $htmlAttributes, $confirmMessage );
			}
		}

		/**
		 * Gère le paramètre $escape et la clé 'escape' de $attributes pour
		 * CakePHP 1.2, 1.3 et 2.x.
		 *
		 * Si le texte est vide, il sera transformé en un espace blanc, ce qui
		 * permettra d'avoir une balise HTML correcete.
		 *
		 * @param string $name
		 * @param string $text
		 * @param array $attributes
		 * @param boolean $escape
		 * @return string
		 */
		public function tag( $name, $text = null, $attributes = array( ), $escape = false ) {
			if( is_string( $attributes ) ) {
				$attributes = array( 'class' => $attributes );
			}

			if( isset( $attributes['escape'] ) ) {
				$escape = $attributes['escape'];
			}

			if( is_null( $text ) || strlen( $text ) == 0 ) {
				$text = ' ';
			}

			$attributes['escape'] = $escape;
			return parent::tag( $name, $text, $attributes );
		}


		/**
		 * Retourne la traduction d'une valeur booléenne en français, éventuellement
		 * accompagnée de l'icone correspondante.
		 *
		 * @param mixed $boolean Une valeur "booléenne" (true/false, 0/1, '0'/'1', 'O'/'N')
		 * @param boolean $showIcon Doit-on retourner l'icone correspondante avec la traduction ?
		 * @return string
		 */
		public function boolean( $boolean, $showIcon = true ) {
			if( is_string( $boolean ) ) {
				if( in_array( $boolean, array( 'O', 'N' ) ) ) {
					$boolean = Set::enum( $boolean, array( 'O' => true, 'N' => false ) );
				}
				else if( in_array( $boolean, array( '1', '0' ) ) ) {
					$boolean = Set::enum( $boolean, array( '1' => true, '0' => false ) );
				}
			}

			if( is_int( $boolean ) ) {
				$boolean = ( ( $boolean === 0 ) ? false : true );
			}

			if( $boolean === true ) {
				$image = 'icons/accept.png';
				$alt = 'Oui';
			}
			else if( $boolean === false ) {
				$image = 'icons/stop.png';
				$alt = 'Non';
			}
			else {
				return;
			}

			if( $showIcon ) {
				return $this->image( $image, array( 'alt' => '' ) ).' '.$alt;
			}
			else {
				return $alt;
			}
		}

		/**
		 * 2 utilisations, donc à mettre ailleurs (surtout vu la complexité de la méthode)
		 *
		 * @see grep -nri "html\->details" app/views | grep -v "\.svn"
		 */
		public function details( $rows = array( ), $options = array( ), $oddOptions = array( 'class' => 'odd' ), $evenOptions = array( 'class' => 'even' ) ) {
			$default = array(
				'type' => 'table',
				'empty' => true
			);

			$options = Set::merge( $default, $options );

			$type = Set::classicExtract( $options, 'type' );
			$allowEmpty = Set::classicExtract( $options, 'empty' );

			if( !in_array( $type, array( 'list', 'table' ) ) ) {
				trigger_error( sprintf( __( 'Type type "%s" not supported in XhtmlHelper::details.' ), $type ), E_USER_WARNING );
				return;
			}

			$return = null;
			if( count( $rows ) > 0 ) {
				$class = 'odd';
				foreach( $rows as $row ) {
					if( $allowEmpty || (!empty( $row[1] ) || valid_int( $row[1] ) ) ) {
						// TODO ?
						$currentOptions = ( ( $class == 'even' ) ? $evenOptions : $oddOptions );

						if( ( empty( $row[1] ) && !valid_int( $row[1] ) ) ) {
							$currentOptions = $this->addClass( $currentOptions, 'empty' );
						}

						$classes = Set::classicExtract( $currentOptions, 'class' );
						if( (!empty( $row[1] ) || valid_int( $row[1] ) ) ) {
							$currentOptions['class'] = implode( ' ', Set::merge( $classes, array( 'answered' ) ) );
						}

						$question = $row[0];
						$answer = ( (!empty( $row[1] ) || valid_int( $row[1] ) ) ? $row[1] : ' ' );

						if( $type == 'table' ) {
							$return .= $this->tag(
									'tr', $this->tag( 'th', $question ).$this->tag( 'td', $answer ), $currentOptions
							);
						}
						else if( $type == 'list' ) {
							$return .= $this->tag( 'dt', $question, $currentOptions );
							$return .= $this->tag( 'dd', $answer, $currentOptions );
						}

						$class = ( ( $class == 'odd' ) ? 'even' : 'odd' );
					}
				}

				if( !empty( $return ) ) {
					foreach( array( 'type', 'empty' ) as $key ) {
						unset( $options[$key] );
					}
					if( $type == 'table' ) {
						$return = $this->tag(
								'table', $this->tag(
										'tbody', $return
								), $options
						);
					}
					else if( $type == 'list' ) {
						$return = $this->tag(
								'dl', $return, $options
						);
					}
				}
			}

			return $return;
		}

		/**
		 *
		 * @param string $imagePath
		 * @param string $label
		 * @param string $title
		 * @param mixed $url
		 * @param boolean $enabled
		 * @param array $attributes
		 * @return string
		 */
		protected function _buttonLink( $imagePath, $label, $title, $url, $enabled = true, $attributes = array( ), $confirmMessage = false ) {
			$settings = array( 'escape' => false, 'title' => $title, 'enabled' => $enabled );

			if( is_array( $attributes ) ) {
				$options = array_merge( $settings, $attributes );
			}

			$content = $this->image( $imagePath, array( 'alt' => '' ) ).' '.$label;
			return $this->link( $content, $url, $options, $confirmMessage );
		}

		/**
		 * Provide non fatal errors on missing method calls.
		 *
		 * @fixme vérifier les paramètres ($params) et lancer une erreur si besoin
		 * @info $params: ( $title, $url, $enabled = true )
		 *
		 * @param string $method Method to invoke
		 * @param array $params Array of params for the method.
		 * @return void
		 */
		public function __call( $method, $params ) {
			if( preg_match( '/Link$/', $method, $matches ) && isset( $this->_linksMap[$method] ) ) {
				$matched = $this->_linksMap[$method];

				$attributes = array();
				if( isset( $this->_linksMapAttributes[$method] ) ) {
					$attributes = $this->_linksMapAttributes[$method];
				}

				return $this->_buttonLink( $matched[0], $matched[1], $params[0], $params[1], ( isset( $params[2] ) ? $params[2] :  true ), $attributes );
			}
			else {
				return parent::__call( $method, $params );
			}
		}

		/**
		 *
		 * @param string $title
		 * @param mixed $url
		 * @param boolean $enabled
		 * @return string
		 */
		public function deleteLink( $title, $url, $enabled = true ) {
			$content = $this->image(
					'icons/delete.png',
					array( 'alt' => '' )
				).' Supprimer';
			if( $enabled ) {
				return $this->link(
					$content,
					$url,
					array( 'escape' => false, 'title' => $title ),
					$title.' ?'
				);
			}
			else{
				return '<span class="disabled">'.$content.'</span>';
			}
		}

		/**
		 * @fixme $attributes
		 *
		 * @param type $title
		 * @param type $url
		 * @param type $enabled
		 * @param type $external
		 * @return type
		 */
		public function fileLink( $title, $url, $enabled = true, $external = false ) {
			return $this->_buttonLink( 'icons/attach.png', 'Fichiers liés', $title, $url, $enabled, array( 'class' => $external ? 'external' : 'internal' ) );
		}

		/**
		 * @fixme $attributes
		 *
		 * @param type $title
		 * @param type $url
		 * @param type $enabled
		 * @param type $external
		 * @return type
		 */
		public function viewLink( $title, $url, $enabled = true, $external = false ) {
			return $this->_buttonLink( 'icons/zoom.png', 'Voir', $title, $url, $enabled, array( 'class' => $external ? 'external' : 'internal' ) );
		}

		/**
		 *
		 * @param type $title
		 * @param type $url
		 * @param type $enabled
		 * @param type $confirmMessage
		 * @return type
		 */
		public function printCohorteLink( $title, $url, $enabled = true, $confirmMessage = 'Etes-vous sûr de vouloir imprimer la cohorte ?', $elementName = 'popup' ) {
			$content = $this->image( 'icons/printer.png', array( 'alt' => '' ) ).' '.$title;

			if( $enabled ) {
				$View = new View( null, false );

				return $View->element( $elementName ).$this->link(
					$content,
					$url,
					array(
						'escape' => false,
						'title' => $title,
						'onclick' => "var conf = confirm( '".str_replace( "'", "\\'", $confirmMessage )."' ); if( conf ) { impressionCohorte( this ); } return conf;"
					)
				);
			}
			else {
				return '<span class="disabled">'.$content.'</span>';
			}
		}

		/**
		 * @fixme $attributes
		 *
		 * @param type $title
		 * @param type $htmlAttributes
		 * @param type $enabled
		 * @return type
		 */
		public function printLinkJs( $title, $htmlAttributes = array( ), $enabled = true ) {
			return $this->_buttonLink( 'icons/printer.png', $title, $title, '#', $enabled, Set::merge( array( 'escape' => false ), $htmlAttributes ) );
		}

		/**
		 * @fixme $title
		 *
		 * @param type $title
		 * @param type $url
		 * @param type $enabled
		 * @return type
		 */
		public function saisineEpLink( $title, $url, $enabled = true ) {
			return $this->_buttonLink( 'icons/folder_table.png', $title, $title, $url, $enabled );
		}

		/**
		 * Retourne une image (avec un attribut 'title') lorsqu'un dossier est
		 * verrouillé.
		 *
		 * @param array $data Les données qui permettent de faire cette déduction.
		 * @return string
		 */
		public function lockedDossier( $data ) {
			$return = null;

			if( $data['Dossier']['locked'] ) {
				$title = 'Dossier verrouillé';

				if( isset( $data['Dossier']['locking_user'] ) && isset( $data['Dossier']['locked_to'] ) ) {
					$title = sprintf( 'Dossier verrouillé par %s jusqu\'au %s', $data['Dossier']['locking_user'], strftime( '%d/%m/%Y à %H:%M:%S', strtotime( $data['Dossier']['locked_to'] ) ) );
				}

				$return = $this->image( 'icons/lock.png', array( 'alt' => '', 'title' => $title ) );
			}

			return $return;
		}

		/**
		 *
		 * @param array $data Les données du menu du dossier.
		 * @return string
		 */
		public function lockerIsMe( $data ) {
			$return = null;

			if( $data['Dossier']['locker_is_me'] ) {
				$isRead = ( ControllerCache::crudMap( Inflector::camelize( $this->request->params['controller'] ), $this->request->params['action'] ) == 'read' );

				if( $isRead ) {
					$return = $this->link(
						$this->image( 'icons/key.png', array( 'alt' => '' ) ),
						array(
							'controller' => 'dossiers',
							'action' => 'unlock',
							$data['Dossier']['id']
						),
						array( 'escape' => false, 'title' => 'Déverrouiller le dossier' )
					);
				}
				else {
					$return = $this->image( 'icons/key_disabled.png', array( 'alt' => '', 'title' => 'Passez sur de la visualisation pour déverrouiller le dossier' ) );
				}
			}

			return $return;
		}
	}
?>