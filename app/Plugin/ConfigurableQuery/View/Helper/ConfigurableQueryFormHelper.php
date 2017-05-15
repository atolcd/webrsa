<?php
	/**
	 * Code source de la classe DefaultFormHelper.
	 *
	 * PHP 5.4
	 *
	 * @package Default
	 * @subpackage View.Helper
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'DefaultFormHelper', 'Default.View/Helper' );

	/**
	 * La classe DefaultFormHelper étend la classe FormHelper de CakePHP
	 * dans le cadre de son utilisation dans le plugin Default.
	 *
	 * @package Default
	 * @subpackage View.Helper
	 */
	class ConfigurableQueryFormHelper extends DefaultFormHelper
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
			'Html',
		);

		/**
		 * Permet l'affichage d'érreur dans le cas où un Préfix est appliqué à un input
		 * Si self::$entityErrorPrefix = 'Cohorte' alors :
		 *	 Cohorte.0.Monmodel.field = Monmodel.0.field
		 * 
		 * @var string
		 */
		public $entityErrorPrefix = null;

		/**
		 * Réalise la traduction d'un label en utilisant la fontion __m() du
		 * plugin MultiDomainTranslator.
		 *
		 * @param string $fieldName
		 * @param string $text
		 * @param array $options
		 * @return string
		 */
		public function label( $fieldName = null, $text = null, $options = array( ) ) {
			return parent::label( $fieldName, $text === null ? $text : __m( $text ), $options );
		}

		/**
		 * Surchage de FormHelper::error() pour permettre l'affichage des erreurs sur les champs préfixés
		 * 
		 * @param string $field
		 * @param mixed $text
		 * @param array $options
		 * @return string
		 */
		public function error($field, $text = null, $options = array()) {
			if( !empty($this->entityErrorPrefix) ) {
				$field = preg_replace("/^{$this->entityErrorPrefix}\.([0-9]+)\.([^\.]+)\./", '\2.\1.', $field);
			}
			
			return parent::error($field, $text, $options);
		}
	}
?>