<?php
	/**
	 * Code source de la classe DefaultCsvHelper.
	 *
	 * PHP 5.3
	 *
	 * @package Default
	 * @subpackage View.Helper
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'DefaultCsvHelper', 'Default.View/Helper' );

	/**
	 * La classe DefaultCsvHelper ...
	 *
	 * @package Default
	 * @subpackage View.Helper
	 */
	class ConfigurableQueryCsvHelper extends DefaultCsvHelper
	{
		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array(
			'Csv',
			'DefaultData' => array(
				'className' => 'ConfigurableQuery.ConfigurableQueryData'
			)
		);

		/**
		 * Ajout d'un champ d'en-tête avec la traduction des noms des champs en
		 * fonction, soit de la clé "domain" de l'attribut d'un field, soit de la
		 * même clé des paramètres généraux.
		 *
		 * @param array $fields
		 * @param array $params Les paramètres généraux
		 */
		protected function _addHeaderRow( array $fields, array $params ) {
			$row = Hash::extract( $fields, '{s}.label' );

			if( !empty( $row ) ) {
				return $this->Csv->addRow( Hash::extract( $fields, '{s}.label' ) );
			}

			return parent::_addHeaderRow( $fields, $params );
		}
	}
?>