<?php
	/**
	 * Code source de la classe SearchFormHelper.
	 *
	 * PHP 5.3
	 *
	 * @package Search
	 * @subpackage View.Helper
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppHelper', 'View/Helper' );

	/**
	 * La classe SearchFormHelper fournit des méthodes génériques pour des éléments
	 * de formulaires. Utilise la librairire javascript prototype.js.
	 *
	 * @package Search
	 * @subpackage View.Helper
	 */
	class SearchFormHelper extends AppHelper
	{
		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Form',
			'Html',
			'Prototype.PrototypeObserver' => array(
				'useBuffer' => false
			)
		);

		/**
		 * Fournit le code javascript permettant de désactiver les boutons de
		 * soumission d'un formumlaire lors de son envoi afin de ne pas renvoyer
		 * celui-ci plusieurs fois avant que le reqête n'ait abouti.
		 *
		 * @deprecated See PrototypeObserverHelper::disableFormOnSubmit()
		 *
		 * @param string $form L'id du formulaire au sens Prototype
		 * @param string $message Le message (optionnel) qui apparaîtra en haut du formulaire
		 * @return string
		 */
		public function observeDisableFormOnSubmit( $form, $message = null ) {
			if( empty( $message ) ) {
				$out = "document.observe( 'dom:loaded', function() {
					observeDisableFormOnSubmit( '{$form}' );
				} );";
			}
			else {
				$message = str_replace( "'", "\\'", $message );

				$out = "document.observe( 'dom:loaded', function() {
					observeDisableFormOnSubmit( '{$form}', '{$message}' );
				} );";
			}

			return "<script type='text/javascript'>{$out}</script>";
		}

		/**
		 * Méthode générique permettant de retourner un ensemble de cases à cocher au sein d'un
		 * fieldset, activées ou désactivées par une autre case à cocher située au-dessus du fieldset.
		 *
		 * Les traductions - "{$path}_choice" pour la case à cocher d'activation/désactivation et
		 * $path pour le fieldset sont faites dans le fichier de traduction correspondant au nom
		 * du contrôleur.
		 *
		 * Remplacements possibles:
		 * //			echo $this->Search->etatdosrsa($etatdosrsa);
		 * echo $this->SearchForm->dependantCheckboxes( 'Situationdossierrsa.etatdosrsa', $etatdosrsa );
		 * @see SearchHelper
		 *
		 * @param string $path
		 * @param array $params
		 * @return string
		 */
		public function dependantCheckboxes( $path, array $params = array() ) {
			$default = array(
				'domain' => 'search_plugin',
				'options' => array(),
				'hide' => false,
				'buttons' => false,
				'autoCheck' => false,
				'hiddenField' => true
			);
			$params = $params + $default;

			$options = $params['options'];

			$fieldsetId = $this->domId( "{$path}_fieldset" );
			$choicePath = "{$path}_choice";

			$selector = 'input[name=\\\'data['.str_replace( '.', '][', $path ).'][]\\\']';

			$choiceParams = array(
				'label' => __d( $params['domain'], $choicePath ),
				'type' => 'checkbox'
			);

			if( Hash::get( $params, 'autoCheck' ) ) {
				$choiceParams['onclick'] = "try { toutCocher( '{$selector}' ); } catch( e ) { console.log( e ); };";
			}

			$input = $this->Form->input( $choicePath, $choiceParams );

			// Boutons "Tout cocher" / "Tout décocher" optionnels
			$buttons = null;
			if( Hash::get( $params, 'buttons' ) ) {
				$buttons = $this->Html->tag(
					'div',
					$this->Form->button( 'Tout cocher', array( 'type' => 'button', 'onclick' => "try { toutCocher( '{$selector}' ); } catch( e ) { console.log( e ); }; return false;" ) )
					.$this->Form->button( 'Tout décocher', array( 'type' => 'button', 'onclick' => "toutDecocher( '{$selector}' ); return false;" ) ),
					array(
						'class' => 'buttons'
					)
				);
			}

			$input .= $this->Html->tag(
				'fieldset',
				$this->Html->tag( 'legend', __d( $params['domain'], $path ) )
				.$buttons
				.$this->Form->input(
					$path,
					array(
						'label' => false,
						'type' => 'select',
						'multiple' => 'checkbox',
						'options' => $options,
						'fieldset' => false,
						'class' => Hash::get( $params, 'class' ),
						'hiddenField' => Hash::get( $params, 'hiddenField' )
					)
				),
				array( 'id' => $fieldsetId )
			);

			$script = $this->PrototypeObserver->disableFieldsetOnCheckbox( $choicePath, $fieldsetId, false, $params['hide'] );

			return $input.$script;
		}

		/**
		 * Méthode générique permettant de filtrer sur une plage de dates.
		 *
		 * params['addYear'] Ajoute X années au "maxYear" du "TO"
		 *
		 * @todo Options: dateFormat, maxYear, minYear, ...
		 *
		 * @param string $path
		 * @param array $params
		 * @return string
		 */
		public function dateRange( $path, array $params = array() ) {
			$default = array(
				'domain' => 'search_plugin',
				'options' => array(),
				'legend' => null,
				'hide' => false,
				'minYear_from' => date( 'Y' ) - 120,
				'minYear_to' => date( 'Y' ) - 120,
				'maxYear_from' => date( 'Y' ),
				'maxYear_to' => date( 'Y' ) + 5,
			);
			$params = $params + $default;

			$fieldsetId = $this->domId( $path ).'_from_to';

			$script = $this->PrototypeObserver->disableFieldsetOnCheckbox( $path, $fieldsetId, false, $params['hide'] );

			$legend = Hash::get( $params, 'legend' );
			if( $legend === null ) {
				if( $params['domain'] !== null ) {
					$legend = __d( $params['domain'], $path );
				}
				else {
					$legend = __m( $path );
				}
			}

			$input = $this->Form->input( $path, array( 'label' => 'Filtrer par '.lcfirst( $legend ), 'type' => 'checkbox' ) );

			$input .= $this->Html->tag(
				'fieldset',
				$this->Html->tag( 'legend', $legend )
				.$this->Form->input( $path.'_from',
					array(
						'label' => 'Du (inclus)',
						'type' => 'date',
						'dateFormat' => 'DMY',
						'maxYear' => $params['maxYear_from'],
						'minYear' => $params['minYear_from'],
						'default' => strtotime( '-1 week' )
					)
				)
				.$this->Form->input( $path.'_to',
					array(
						'label' => 'Au (inclus)',
						'type' => 'date',
						'dateFormat' => 'DMY',
						'maxYear' => $params['maxYear_to'],
						'minYear' => $params['minYear_to']
					)
				),
				array( 'id' => $fieldsetId )
			);

			return $script.$input;
		}
	}
?>