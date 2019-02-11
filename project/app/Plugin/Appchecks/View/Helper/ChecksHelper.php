<?php
	/**
	 * Code source de la classe ChecksHelper.
	 *
	 * PHP 5.3
	 *
	 * @package Appchecks
	 * @subpackage View.Helper
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppHelper', 'View/Helper' );

	/**
	 * La classe ChecksHelper fournit des méthodes pour réprésentater les résultats
	 * de vérifications de l'environnement applicatif.
	 *
	 * @package Appchecks
	 * @subpackage View.Helper
	 */
	class ChecksHelper extends AppHelper
	{
		public $helpers = array( 'Html' );

		/**
		 *
		 * @param array $elements
		 * @param string $class
		 * @return string
		 */
		public function checklist( array $elements, $class = null ) {
			$return = '';

			if( !empty( $elements ) ) {
				$return .= '<ul class="check '.$class.'">';
				foreach( $elements as $element => $found ) {
					$return .= $this->Html->tag( 'li', $element, array( 'class' => ( $found ? 'success' : 'error' ) ) );
				}
				$return .= '</ul>';
			}

			return $return;
		}

		/**
		 *
		 * @param array $elements
		 * @param string $class
		 * @return string
		 */
		public function table( array $elements, $class = 'values' ) {
			$rows = array();

			if( !empty( $elements ) ) {
				foreach( $elements as $name => $result ) {
					if( !is_array( $result ) ) {
						$th = $this->Html->tag( 'th', $name );
						$rows[] = $this->Html->tag( 'tr', $th, array( 'class' => ( $result ? 'success' : 'error' ) ) );
					}
					else {
						$th = $this->Html->tag( 'th', $name );
						$tdValue = $this->Html->tag( 'td', ( !isset( $result['value'] ) ? '' : $result['value'] ), array( 'class' => 'value' ) );
						$tdMessage = $this->Html->tag( 'td', ( !isset( $result['message'] ) ? '' : $result['message'] ), array( 'class' => 'message' ) );
						$rows[] = $this->Html->tag( 'tr', "{$th}{$tdValue}{$tdMessage}", array( 'class' => ( isset( $result['success'] ) && $result['success'] ? 'success' : 'error' ) ) );
					}
				}
			}

			$return = $this->Html->tag( 'tbody', implode( $rows ) );
			return $this->Html->tag( 'table', $return, array( 'class' => "checks {$class}" ) );
		}
	}
?>
