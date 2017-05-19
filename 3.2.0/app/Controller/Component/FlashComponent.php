<?php
	/**
	 * Code source de la classe FlashComponent.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller.Component
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'Component', 'Controller' );
	App::uses( 'CakeSession', 'Model/Datasource' );

	/**
	 * La classe FlashComponent simplifie les messages flash de CakePHP 2.x.
	 * Inspirée par la classe eponyme de CakePHP 3.x, elle utilise directement
	 * la classe CakeSession.
	 *
	 * $this->Flash->success( 'Succès' );
	 * $this->Flash->error( 'Erreur' );
	 * $this->Flash->warning( 'Avertissement' );
	 * $this->Flash->notice( 'Note' );
	 *
	 * @package app.Controller.Component
	 */
	class FlashComponent extends Component
	{
		/**
		 *
		 * @param string $message
		 * @param array $options Clés key, element, params
		 */
		public function set( $message, array $options = array() ) {
			$options += array(
				'key' => 'flash',
				'element' => 'default',
				'params' => array()
			);

			CakeSession::write( 'Message.' . $options['key'], array( 'message' => $message ) + $options );
		}

		/**
		 * Méthode magique d'ajout d'éléments flash.
		 * Le nom de la méthode est ajouté en tant que classe.
		 *
		 * @see FlashComponent::set()
		 *
		 * @param string $name
		 * @param array $args
		 * @throws InternalErrorException
		 */
		public function __call( $name, array $args ) {
			if( count( $args ) < 1 ) {
				throw new InternalErrorException( 'Flash message missing.' );
			}

			$options = array( 'params' => array() );
			$class = Inflector::underscore( $name );
			$options['params']['class'] = false === empty($options['params']['class'])
				? $options['params']['class'] . ' ' . $class
				: $class;

			if( false === empty( $args[1] ) ) {
				$options += (array) $args[1];
			}

			$this->set( $args[0], $options );
		}
	}
?>