<?php
	/**
	 * Code source de la classe WebrsaRecherchesIndusComponent.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller.Component
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'WebrsaAbstractRecherchesComponent', 'Controller/Component' );

	/**
	 * La classe WebrsaRecherchesIndusComponent ...
	 *
	 * @package app.Controller.Component
	 */
	class WebrsaRecherchesIndusComponent extends WebrsaAbstractRecherchesComponent
	{
		/**
		 * Retourne les options de type "enum", c'est à dire liées aux schémas des
		 * tables de la base de données.
		 *
		 * @return array
		 */
		protected function _optionsEnums( array $params = array() ) {
			$Controller = $this->_Collection->getController();
			$this->Option = ClassRegistry::init( 'Option' );

			$options = Hash::merge(
				parent::_optionsEnums( $params ),
				array(
					'Dossier' => array(
						'typeparte' => ClassRegistry::init('Dossier')->enum('typeparte')
					),
					'Infofinanciere' => array(
						'compare' => array('<' => '<','>' => '>','<=' => '<=','>=' => '>='),
						'natpfcre' => ClassRegistry::init('Infofinanciere')->enum('natpfcre', array('type' => 'autreannulation'))
					),
				)
			);

			return $options;
		}
	}
?>