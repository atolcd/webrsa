<?php
	/**
	 * Code source de la classe PasswordComponent.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller.Component
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe PasswordComponent s'occupe de la génération de mot de passes
	 * aléatoires.
	 *
	 * @url http://bakery.cakephp.org/articles/deldan/2010/09/22/password-generator
	 *
	 * @package app.Controller.Component
	 */
	class PasswordComponent extends Component
	{
		/**
		 * Contrôleur utilisant ce component.
		 *
		 * @var Controller
		 */
		public $Controller = null;

		/**
		 * Paramètres de ce component
		 *
		 * @var array
		 */
		public $settings = array( );

		/**
		 * Components utilisés par ce component.
		 *
		 * @var array
		 */
		public $components = array( );

		/**
		 * Les caratères possibles pour la génération de mots de passe aléatoires.
		 *
		 * @var array
		 */
		protected $_possible = array(
			'number' => true,
			'lower' => true,
			'upper' => true,
			'symbol' => true,
			'typesafe' => true,
		);

		/**
		 * Les classes de caractères obligatoires pour la génération de mots de
		 * passe aléatoires.
		 *
		 * @var array
		 */
		protected $_required = array(
			'number' => true,
			'lower' => false,
			'upper' => false,
			'symbol' => true,
		);

		/**
		 * Retourne un tableau contenant les classes de caractères possibles,
		 * suivant les $options passées en paramètres. Les options par défaut
		 * sont les suivantes (voir self::$_possible):
		 *
		 * <pre>
		 * array(
		 * 	'number' => true,
		 * 	'lower' => true,
		 * 	'upper' => true,
		 * 	'symbol' => true,
		 * 	'typesafe' => true,
		 * )
		 * </pre>
		 *
		 * Il est possible de surcharger les options par défaut grâce à
		 * Configure::write( 'Password.possible', array( ... ) );
		 *
		 * @param array $options
		 * @return array
		 */
		public function possible( array $options = array() ) {
			$options = Hash::merge(
				(array)$this->_possible,
				(array)Configure::read( 'Password.possible' ),
				$options
			);

			$possible = array();

			if( $options['number'] ) {
				$possible['number'] = '0123456789';
			}

			if( $options['lower'] ) {
				$possible['lower'] = 'abcdefghijklmnopqrstuvwxyz';
			}

			if( $options['upper'] ) {
				$possible['upper'] = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
			}

			if( $options['symbol'] ) {
				$possible['symbol'] = ',;.!?*+-';
			}

			if( $options['typesafe'] ) {
				if( isset( $possible['number'] ) ) {
					$possible['number'] = str_replace( array( '0', '1' ), '', $possible['number'] );
				}

				if( isset( $possible['lower'] ) ) {
					$possible['lower'] = str_replace( array( 'l', 'i', 'o' ), '', $possible['lower'] );
				}

				if( isset( $possible['upper'] ) ) {
					$possible['upper'] = str_replace( array( 'I', 'O' ), '', $possible['upper'] );
				}
			}

			return $possible;
		}

		/**
		 * Retourne un mot de passe généré aléatoirement.
		 * @see PasswordComponent::possible() pour les $options.
		 *
		 * @url http://maord.com/
		 *
		 * @param array $length
		 * @param array $options
		 * @return string
		 * @throws NotFoundException
		 */
		public function generate( $length = 8, array $options = array() ) { // FIXME: $options
			$possible = $this->possible( (array)Hash::get( $options, 'possible' ) );

			$required = Hash::merge(
				(array)$this->_required,
				(array)Configure::read( 'Password.required' ),
				(array)Hash::get( $options, 'required' )
			);

			$password = '';
			foreach( $required as $class => $value ) {
				if( $value && isset( $possible[$class] ) ) {
					$password .= substr( str_shuffle( $possible[$class] ), 0, 1 );
				}
			}

			if( $length < strlen( $password ) ) {
				throw new InternalErrorException();
			}

			return str_shuffle( $password.substr( str_shuffle( implode( $possible ) ) , 0 , $length - strlen( $password ) ) );
		}
	}
?>