<?php
	/**
	 * Code source de la classe FiltresdefautComponent.
	 *
	 * PHP 5.3
	 *
	 * @package Search
	 * @subpackage Controller.Component
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe FiltresdefautComponent permet de mettre des valeurs par défaut
	 * dans l'attribut $this->request->data du contrôleur auquel il est attaché
	 * lorsque l'on appelle certaines actions du contrôleur.
	 *
	 * Utilisation dans le contrôleur:
	 * <pre>
	 * public $components = array(
	 *	'Filtresdefaut' => array( 'index' )
	 * );
	 * </pre>
	 *
	 * Utilisation dans le fichier de configuration.
	 * <pre>
	 * Configure::write(
	 * 	'Filtresdefaut.Dossiers_index',
	 * 	array(
	 * 		'Dossier.dernier' => '1',
	 * 		'Dossier.dtdemrsa_from' => '1979-01-24',
	 * 		'Dossier.dtdemrsa_to' => date( 'Y-m-d', strtotime( '+1 day') ),
	 * 		'Situationdossierrsa.etatdosrsa_choice' => '1',
	 * 		'Situationdossierrsa.etatdosrsa' => array( 2, 3, 4 )
	 * 	)
	 * );
	 * </pre>
	 *
	 * @package Search
	 * @subpackage Controller.Component
	 */
	class FiltresdefautComponent extends Component
	{
		/**
		 * Le nom du component.
		 *
		 * @var string
		 */
		public $name = 'Filtresdefaut';

		/**
		 * Paramètres du component.
		 *
		 * @var array
		 */
		public $settings = array( );

		/**
		 * Le contrôleur est-il dans un état qui demande une fusion avec les
		 * données par défaut ?
		 *
		 * @return boolean
		 */
		public function needsMerge() {
			$Controller = $this->_Collection->getController();

			return in_array( $Controller->action, (array)$this->settings )
					&& empty( $Controller->request->data );
		}

		/**
		 * Retournee la clé sous laquelle la variable de configuration sera lue
		 * pour le contrôleur auquel le component est attaché.
		 *
		 * @return string
		 */
		public function configureKey() {
			$Controller = $this->_Collection->getController();

			return "{$this->name}.{$Controller->name}_{$Controller->action}";
		}

		/**
		 * Retourne les valeurs par défaut du formulaire pour la clé courante.
		 *
		 * @see FiltresdefautComponent::configureKey()
		 *
		 * @return array
		 */
		public function values() {
			$key = $this->configureKey();
			return Hash::expand( (array)Configure::read( $key ) );
		}

		/**
		 * Fusion des données post de la requête au contrôleur et de la configuration
		 * stockée dans "{$this->name}.{$Controller->name}_{$Controller->action}".
		 *
		 * Les données du contrôleur écraseront les données de la configuration.
		 *
		 * @return void
		 */
		public function merge() {
			$Controller = $this->_Collection->getController();

			$filtresdefaut = $this->values();
			if( !empty( $filtresdefaut ) ) {
				$Controller->request->data = Hash::merge( $filtresdefaut, $Controller->request->data );
			}
		}

		/**
		 * Fusion éventuelle des données du contrôleur, après l'action de
		 * celui-ci et juste avant le rendu de la vue.
		 *
		 * @param Controller $controller Le contrôleur dans lequel fusionner les données
		 * @return void
		 */
		public function beforeRender( Controller $controller ) {
			parent::beforeRender( $controller );

			if( $this->needsMerge() ) {
				$this->merge();
			}
		}
	}
?>