<?php
	/**
	 * Fichier source de la classe WebrsaHelper.
	 *
	 * PHP 5.3
	 *
	 * @package app.View.Helper
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppHelper', 'View/Helper' );

	/**
	 * La classe WebrsaHelper ...
	 *
	 * @package app.View.Helper
	 */
	class WebrsaHelper extends AppHelper
	{
		public $helpers = array( 'Html' );

		/**
		 *
		 */
		public function blocAdresse( $data, $options ) {
			$default = array(
				'separator' => '<br />',
				'options' => array(),
				'alias' => 'Adresse',
				'ville' => false
			);
			$options = array_merge( $default, $options );

			$return = Set::classicExtract( $data, "{$options['alias']}.numvoie" )
				.' '.Set::classicExtract( $data, "{$options['alias']}.libtypevoie" )
				.' '.Set::classicExtract( $data, "{$options['alias']}.nomvoie" )
				.$options['separator'].Set::classicExtract( $data, "{$options['alias']}.compladr" )
				.' '.Set::classicExtract( $data, "{$options['alias']}.complideadr" );

			if( $options['ville'] ) {
				$return .= $options['separator'].Set::classicExtract( $data, "{$options['alias']}.codepos" )
						.' '.Set::classicExtract( $data, "{$options['alias']}.nomcom" );
			}

			return $return;
		}
	}
?>