<?php
	/**
	 * Code source de la classe PrototypeObserverHelper.
	 *
	 * PHP 5.3
	 *
	 * @package Prototype
	 * @subpackage View.Helper
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * La classe PrototypeObserverHelper fournit des méthodes de haut niveau pour
	 * observer des changements de valeurs de champs de formulaires au moyen de la
	 * librairie javascript prototypejs.
	 *
	 * @package Prototype
	 * @subpackage View.Helper
	 */
	class PrototypeObserverHelper extends AppHelper
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
		 * Fournit le code javascript permettant de désactiver les boutons de
		 * soumission d'un formumlaire lors de son envoi afin de ne pas renvoyer
		 * celui-ci plusieurs fois avant que le reqête n'ait abouti.
		 *
		 * @param string $form L'id du formulaire au sens Prototype
		 * @param string $message Le message (optionnel) qui apparaîtra en haut du formulaire
		 * @return string
		 */
		public function disableFormOnSubmit( $form = null, $message = null ) {
			if( $form === null ) {
				$form = Inflector::camelize( Inflector::singularize( $this->request->params['controller'] )."_{$this->request->params['action']}" ).'Form';
			}

			if( empty( $message ) ) {
				$script = "observeDisableFormOnSubmit( '{$form}' );";
			}
			else {
				$message = str_replace( "'", "\\'", $message );
				$script = "observeDisableFormOnSubmit( '{$form}', '{$message}' );";
			}

			return $this->render( $script );
		}

		/**
		 * Permet de désactiver et éventuellement de masquer un ensemble de champs
		 * suivant qu'une case à cocher est cochée ou non.
		 *
		 * @param string $master Le chemin CakePHP de la case à cocher
		 * @param string|array $slaves Les id des champs
		 * @param boolean $condition true pour désactiver lorsque la case est cochée, false sinon
		 * @param boolean $hide true pour en plus cacher les champs lorsqu'ils sont désactivés
		 * @return array
		 */
		public function disableFieldsOnCheckbox( $master, $slaves, $condition = false, $hide = false ) {
			$master = $this->domId( $master );

			$slaves = $this->_toJsSlaves( $slaves );

			$condition = ( $condition ? 'true' : 'false' );
			$hide = ( $hide ? 'true' : 'false' );

			$script = "observeDisableFieldsOnCheckbox( '{$master}', {$slaves}, {$condition}, {$hide} );";

			return $this->render( $script );
		}

		/**
		 * Permet de désactiver et éventuellement de masquer un fieldset suivant
		 * qu'une case à cocher est cochée ou non.
		 *
		 * @param string $master Le chemin CakePHP de la case à cocher
		 * @param string $slave L'id HTML du fieldset
		 * @param boolean $condition true pour désactiver lorsque la case est cochée, false sinon
		 * @param boolean $hide true pour en plus cacher le fieldset lorsqu'il est désactivé
		 * @return array
		 */
		public function disableFieldsetOnCheckbox( $master, $slave, $condition = false, $hide = false ) {
			$master = $this->domId( $master );
			$condition = ( $condition ? 'true' : 'false' );
			$hide = ( $hide ? 'true' : 'false' );

			$script = "observeDisableFieldsetOnCheckbox( '{$master}', '{$slave}', {$condition}, {$hide} );";

			return $this->render( $script );
		}

		/**
		 * Retourne le code javascript permettant de faire dépendre des input
		 * select non-multiples entre eux, suivant le principe suivant: on prend
		 * le suffixe de la valeur du maître et elle doit correspondre au préfixe
		 * de la valeur de l'esclave.
		 *
		 * @param array $fields En clé le champ maître au sens CakePHP, en valeur le champ esclave au sens CakePHP.
		 * @return string
		 */
		public function dependantSelectOld( array $fields ) {
			$script = '';

			foreach( $fields as $masterField => $slaveField ) {
				$masterField = $this->domId( $masterField );
				$slaveField = $this->domId( $slaveField );
				$script .= "dependantSelectOld( '{$slaveField}', '{$masterField}' );\n";
			}

			return $this->render( $script );
		}

		/**
		 * Permet de désactiver et éventuellement de masquer un ensemble de champs
		 * suivant la valeur d'un champ maître.
		 *
		 * @param string $master Le chemin CakePHP du champ maître
		 * @param string|array $slaves Les chemins CakePHP des champs à désactiver
		 * @param mixed $values Les valeurs à prendre en compte pour le champ maître
		 * @param boolean $condition true pour désactiver lorsque le champ maître a une des valeurs, false sinon
		 * @param boolean $hide true pour en plus cacher les champs esclaves lorsqu'ils sont désactivés
		 * @return string
		 */
		public function disableFieldsOnValue( $master, $slaves, $values, $condition, $hide = false ) {
			$master = $this->domId( $master );

			$slaves = $this->_toJsSlaves( $slaves );

			$values = $this->_toJsValues( $values );

			$condition = ( $condition ? 'true' : 'false' );

			$hide = ( $hide ? 'true' : 'false' );

			$script = "observeDisableFieldsOnValue( '{$master}', {$slaves}, {$values}, {$condition}, {$hide} );";
			return $this->render( $script );
		}

		/**
		 * Permet de désactiver et éventuellement de masquer un fieldset suivant
		 * la valeur d'un champ maître.
		 *
		 * @param string $master Le chemin CakePHP du champ maître
		 * @param string $slave Les chemins CakePHP du fieldset
		 * @param mixed $values Les valeurs à prendre en compte pour le champ maître
		 * @param boolean $condition true pour désactiver lorsque le champ maître a une des valeurs, false sinon
		 * @param boolean $hide true pour en plus cacher le fieldset lorsqu'il est désactivé
		 * @return string
		 */
		public function disableFieldsetOnValue( $master, $slave, $values, $condition, $hide = false ) {
			$master = $this->domId( $master );

			$values = $this->_toJsValues( $values );

			$condition = ( $condition ? 'true' : 'false' );

			$hide = ( $hide ? 'true' : 'false' );

			$script = "observeDisableFieldsetOnValue( '{$master}', '{$slave}', {$values}, {$condition}, {$hide} );";
			return $this->render( $script );
		}

		/**
		 * Permet de désactiver et éventuellement de masquer un fieldset suivant
		 * la valeur d'un champ maître.
		 *
		 * @param string $formId Identifiant du formulaire qui contient le bouton radio
		 * @param string $master Le chemin CakePHP du champ maître
		 * @param string $slave Le chemin CakePHP du fieldset
		 * @param mixed $values Les valeurs à prendre en compte pour le champ maître
		 * @param boolean $condition true pour désactiver lorsque le champ maître a une des valeurs, false sinon
		 * @param boolean $hide true pour en plus cacher le fieldset lorsqu'il est désactivé
		 * @return string
		 */
		public function disableFieldsetOnRadioValue( $formId, $master, $slave, $values, $condition, $hide = false ) {
			$master = $this->_toName( $master );

			$values = $this->_toJsValues( $values );

			$condition = ( $condition ? 'true' : 'false' );

			$hide = ( $hide ? 'true' : 'false' );

			$script = "observeDisableFieldsetOnRadioValue( '{$formId}', '{$master}', '{$slave}', {$values}, {$condition}, {$hide} );";
			return $this->render( $script );
		}

		/**
		 * Permet de désactiver et éventuellement de masquer un ensemble de champs
		 * suivant la valeur d'un champ maître.
		 *
		 * @param string $formId Identifiant du formulaire qui contient le bouton radio
		 * @param string $master Le chemin CakePHP du champ maître
		 * @param string|array $slaves Les chemins CakePHP des champs à désactiver
		 * @param mixed $values Les valeurs à prendre en compte pour le champ maître
		 * @param boolean $condition true pour désactiver lorsque le champ maître a une des valeurs, false sinon
		 * @param boolean $hide true pour en plus cacher les champs esclaves lorsqu'ils sont désactivés
		 * @return string
		 */
		public function disableFieldsOnRadioValue( $formId, $master, $slaves, $values, $condition, $hide = false ){
			$master = $this->_toName( $master );

			$slaves = $this->_toJsSlaves( $slaves );

			$values = $this->_toJsValues( $values );

			$condition = ( $condition ? 'true' : 'false' );

			$hide = ( $hide ? 'true' : 'false' );

			$script = "observeDisableFieldsOnRadioValue( '{$formId}', '{$master}', {$slaves}, {$values}, {$condition}, {$hide} );";

			return $this->render( $script );
		}

		/**
		 * Ajoute le contenu dans le buffer si useBuffer est à true, sinon retourne
		 * le script dans une fonction déclenchée au chargement de la page.
		 *
		 * @param string $script Le code javascript à ajouter.
		 */
		public function render( $script ) {
			$script = "try {\n\t${script};\n} catch( e ) {\n\tconsole.error( e );\n}";
			if( $this->useBuffer ) {
				$this->script = "{$this->script}\n{$script}";
			}
			else {
				return $this->Html->scriptBlock( "document.observe( 'dom:loaded', function() { {$script} } );" );
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
				$this->Html->scriptBlock( "document.observe( 'dom:loaded', function() {{$this->script}\n} );", array( 'block' => $this->block ) );
			}
		}

		/**
		 * Retourne le name d'un input (data[User][id]) à partir de son chemin (User.id).
		 *
		 * @param string $path
		 * @return string
		 */
		protected function _toName( $path ) {
			return 'data['.implode( '][', explode( '.', $path ) ).']';
		}

		/**
		 * Transforme un array|string php en array javascript
		 * Donne l'attribu undefined aux élements null
		 *
		 * @param Mixed $array Contenu de l'array cible sous forme String ou Array php
		 * @return String
		 */
		protected function _toJsValues( $array ){
			$values = (array)$array;

			foreach( $values as $i => $value ) {
				if( $value === null ) {
					$value = 'undefined';
				}
				else {
					$value = "'{$value}'";
				}
				$values[$i] = $value;
			}
			return "[ ".implode( ", ", $values )." ]";
		}

		/**
		 * Transforme un array|string php en array javascript
		 * Appel la fonction $this->domId() sur chaques elements avant transformation
		 *
		 * @param Mixed $slaves Contenu de l'array cible sous forme String ou Array php
		 * @return String
		 */
		protected function _toJsSlaves( $slaves ){
			$values = (array)$slaves;

			foreach( $values as $i => $slave ) {
				$values[$i] = $this->domId( $slave );
			}

			return "[ '".implode( "', '", $values )."' ]";
		}

		/**
		 * Retourne le code javascript permettant de faire dépendre des input
		 * select non-multiples entre eux, suivant le principe suivant: on prend
		 * le suffixe de la valeur du maître et elle doit correspondre au préfixe
		 * de la valeur de l'esclave.
		 *
		 * @param array $fields En clé le champ maître au sens CakePHP, en valeur le champ esclave au sens CakePHP.
		 * @return string
		 */
		public function dependantSelect( array $fields ) {
			$script = '';

			foreach( $fields as $masterField => $slaveField ) {
				$masterField = $this->domId( $masterField );
				$slaveField = $this->domId( $slaveField );
				$script .= "dependantSelect( '{$slaveField}', '{$masterField}' );\n";
			}

			return $this->render( $script );
		}
	}
?>