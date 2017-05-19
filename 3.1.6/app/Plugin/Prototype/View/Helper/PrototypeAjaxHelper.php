<?php
	/**
	 * Code source de la classe PrototypeAjaxHelper.
	 *
	 * PHP 5.3
	 *
	 * @package Prototype
	 * @subpackage View.Helper
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe PrototypeAjaxHelper fournit des méthodes Ajax de haut niveau au
	 * moyen de la librairie javascript prototypejs.
	 *
	 * @package Prototype
	 * @subpackage View.Helper
	 */
	class PrototypeAjaxHelper extends AppHelper
	{
		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array( 'Html' );

		/**
		 * Le contenu du buffer.
		 *
		 * @var string
		 */
		public $script = '';

		/**
		 * Le nom du bloc qui contiendra le buffer.
		 *
		 * @var string
		 */
		public $block = 'scriptBottom';

		/**
		 * Permet de spécifier si on utilise le buffer ou si on retourne le
		 * bout de code javascript directement.
		 *
		 * @var boolean
		 */
		public $useBuffer = true;

		/**
		 * Surcharge du constructeur avec possibilité de choisir les paramètres
		 * block et useBuffer.
		 *
		 * @param View $View
		 * @param array $settings
		 */
		public function __construct( View $View, $settings = array( ) ) {
			parent::__construct( $View, $settings );
			$settings = $settings + array(
				'block' => 'scriptBottom',
				'useBuffer' => true
			);
			$this->block = $settings['block'];
			$this->useBuffer = $settings['useBuffer'];
		}

		/**
		 * Met à jour via ajax une div au chargement de la page, ainsi que lors
		 * d'une mise à jour de l'un des champs.
		 *
		 * Utilisé une seule fois dans app/View/Fichesprescriptions93/add_edit.ctp
		 *
		 * @param string $update
		 * @param string|array $url
		 * @param string|array $fields
		 * @return string
		 */
		public function updateDivOnFieldsChange( $update, $url, $fields ) {
			$function = __FUNCTION__.$this->domId( Inflector::camelize( $update ) );
			$url = Router::url( $url );

			$fields = (array)$fields;

			$parameters = array();
			$observers = array(
				"document.observe( 'dom:loaded', function() { {$function}(); } );"
			);

			foreach( $fields as $field ) {
				$key = 'data['.str_replace( '.', '][', $field ).']';
				$domId = $this->domId( $field );

				$parameters[] = "'{$key}': \$F( '{$domId}' )";

				$observers[] = "Event.observe( \$( '{$domId}' ), 'change', function() { {$function}(); } );";
			}

			$script = "function {$function}() {
		new Ajax.Updater(
			'{$update}',
			'{$url}',
			{
				asynchronous: true,
				evalScripts: true,
				parameters: { ".implode( ',', $parameters )." }
			}
		);
	}
	".implode( "\n", $observers );

			return $this->render( $script );
		}

		/**
		 * Permet d'obsverver des champs de formulaires, avec les paramètres
		 * suivants:
		 * {{{
		 *	- prefix: prévient le serveur qu'un préfixe sera à prendre en compte (ex.: Search)
		 *	- url: l'URL à appeler pour l'action ajax
		 *	- onload: si l'état des champs (dans this->request->data) doit être envoyé au chargement de la page
		 *	- delay: le nombre de millisecondes de délai à utiliser avant l'envoi lorsque l'événement est de type keyup ou keydown. Par défaut: 500.
		 *	- min: le nombre minimum de caractères devant être remplis lorsque l'événement est de type keyup ou keydown. Par défaut: 3.
		 * }}}
		 *
		 * Pour chacun des champs, on peut spécifier dans un array:
		 * {{{
		 *	- event:
		 *		* null|change (defaut): modification de listes déroulantes
		 *		* keyup|keydown: touches pressées ou relâchées dans un champ texte, désactive l'autocomplétion du navigateur
		 *		* false: n'observe pas, envoie seulement la valeur au déclenchement des autres événement
		 * }}}
		 *
		 * @param string|array $fields Les id (HTML) des champs à observer
		 * @param array $params Les paramètres dont les clés sont décrites ci-dessus
		 * @return string
		 */
		public function observe( $fields, array $params = array() ) {
			$default = array(
				'prefix' => null,
				'url' => Router::url(),
				'onload' => true,
				'min' => 3,
				'delay' => 500
			);
			$params += $default;
			$fields = Hash::normalize( (array)$fields );

			$md5 = md5( serialize( $fields ) );
			$parametersName = "ajax_parameters_{$md5}";

			// Les paramètres
			$url = Router::url( $params['url'] );
			$domIds = array();
			foreach( array_keys( $fields ) as $path ) {
				$domIds[] = $this->domId( $path );
			}
			$script = "var {$parametersName} = { 'url': '{$url}', 'prefix': '{$params['prefix']}', 'fields': [ '".implode( "', '", $domIds )."' ], 'min': '{$params['min']}', 'delay': '{$params['delay']}' };\n";

			// Les Event.observe()
			foreach( $fields as $path => $value ) {
				$domId = $this->domId( $path );

				$event = Hash::get( (array)$value, 'event' );
				$event = ( $event === null ? 'change' : $event );

				if( $event !== false ) {
					if( in_array( $event, array( 'keyup', 'keydown' ) ) ) {
						$script .= "\$( '{$domId}' ).writeAttribute( 'autocomplete', 'off' );";
					}

					$script .= "Event.observe( \$( '{$domId}' ), '{$event}', function(event) { ajax_action( event, {$parametersName} ); } );\n";
				}
			}

			// onLoad ?
			if( $params['onload'] ) {
				$values = array();
				foreach( array_keys( $fields ) as $path ) {
					$domId = $this->domId( $path );
					$value = str_replace( "'", "\\'", Hash::get( $this->request->data, $path ) );
					$values[] = "'{$domId}': '{$value}'";
				}
				$onloadParametersName = "ajax_onload_parameters_{$md5}";

				$script .= "var {$onloadParametersName} =  Object.clone( {$parametersName} );
				{$onloadParametersName}['values'] = { ".implode( ", ", $values )." };
				document.observe( 'dom:loaded', function(event) { ajax_action( event, {$onloadParametersName} ); } );\n";
			}

			return $this->render( $script );
		}

		/**
		 * Ajoute le contenu dans le buffer si useBuffer est à true, sinon retourne
		 * le script dans une fonction déclenchée au chargement de la page.
		 *
		 * @param string $script Le code javascript à ajouter.
		 */
		public function render( $script ) {
			if( $this->useBuffer ) {
				$this->script = "{$this->script}\n{$script}";
			}
			else {
				return $this->Html->scriptBlock( $script );
			}
		}

		/**
		 * Ajoute le contenu du buffer dans une fonction déclenchée au chargement
		 * de la page, dans le block scriptBottom (par défaut), si useBuffer est
		 * à true;
		 *
		 * @param string $layoutFile The layout about to be rendered.
		 */
		public function beforeLayout( $layoutFile ) {
			parent::beforeLayout( $layoutFile );

			if( $this->useBuffer ) {
				$this->Html->scriptBlock( $this->script, array( 'block' => $this->block ) );
			}
		}

	}
?>