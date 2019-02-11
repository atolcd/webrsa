<?php
	/**
	 * Code source de la classe DefaultTableCellHelper.
	 *
	 * PHP 5.4
	 *
	 * @package Default
	 * @subpackage View.Helper
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'DefaultTableCellHelper', 'Default.View/Helper' );

	/**
	 * La classe DefaultTableCellHelper génère des cellules de corps de tableau
	 * qui sont des array avec, en clé 0 le contenu de la cellule, et en clé 1 les
	 * attributs de la cellule.
	 *
	 * Ces arrays sont utilisables par la méthode HtmlHelper::tableCells().
	 *
	 * @package Default
	 * @subpackage View.Helper
	 */
	class ConfigurableQueryTableCellHelper extends DefaultTableCellHelper
	{
		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'DefaultData' => array(
				'className' => 'ConfigurableQuery.ConfigurableQueryData'
			),
			'DefaultForm' => array(
				'className' => 'ConfigurableQuery.ConfigurableQueryForm'
			),
			'DefaultHtml' => array(
				'className' => 'ConfigurableQuery.ConfigurableQueryHtml'
			)
		);

		/**
		 * Surcharge de la méthode action() afin d'avoir la "multitraduction" des
		 * clés title, msgid, confirm.
		 *
		 * @param string $path
		 * @param array $htmlAttributes
		 * @return string
		 */
		public function action( $path, array $htmlAttributes = array() ) {
			if( isset( $htmlAttributes['label'] ) && !isset( $htmlAttributes['title'] ) ) {
				$htmlAttributes['title'] = $htmlAttributes['label'];
				unset( $htmlAttributes['label'] );
			}

			if( !isset( $htmlAttributes['msgid'] ) ) {
				$url = DefaultUrl::toArray( $path );
				$htmlAttributes['msgid'] = __m( DefaultUtility::msgid( $url ) );
			}

			if( isset( $htmlAttributes['confirm'] ) && $htmlAttributes['confirm'] === true ) {
				$htmlAttributes['confirm'] = __m( $path );
			}

			return parent::action( $path, $htmlAttributes );
		}
	}
?>