<?php
	/**
	 * Code source de la classe WebrsaRecherchesApresComponent.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller.Component
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'WebrsaAbstractRecherchesComponent', 'Controller/Component' );

	/**
	 * La classe WebrsaRecherchesApresComponent ...
	 *
	 * @package app.Controller.Component
	 */
	class WebrsaRecherchesApresComponent extends WebrsaAbstractRecherchesComponent
	{
		public $Apre = null;

		public function __construct(\ComponentCollection $collection, $settings = array()) {
			parent::__construct($collection, $settings);

			$this->Apre = ClassRegistry::init( 'Apre'.Configure::read( 'Apre.suffixe' ) );
		}

		protected function _optionsEnums( array $params ) {
			$departement = Configure::read( 'Cg.departement' );

			$options = parent::_optionsEnums( $params );
			if( $departement == 66 ) {
				$options = Hash::merge(
					$options,
					$this->Apre->Aideapre66->enums()
				);
			}

			return $options;
		}

		protected function _optionsRecords( array $params ) {
			$Controller = $this->_Collection->getController();
			$options = parent::_optionsRecords( $params );
			$departement = Configure::read( 'Cg.departement' );

			$options['Apre']['structurereferente_id'] = $this->Apre->Structurereferente->listOptions( array( 'orientation' => 'O' ) );
			$options['Apre']['referent_id'] = $this->Apre->Structurereferente->Referent->WebrsaReferent->listOptions();

			if( $departement == 66 ) {
				$options = Hash::merge(
					$options,
					array(
						'Aideapre66' => array(
							'themeapre66_id' => $this->Apre->Aideapre66->Themeapre66->find('list'),
							'typeaideapre66_id' => $this->Apre->Aideapre66->Typeaideapre66->listOptions()
						)
					)
				);
			}
			else if( $departement == 93 ) {
				if( !isset( $Controller->Tiersprestataireapre ) ) {
					$Controller->loadModel( 'Tiersprestataireapre' );
				}

				$options['Tiersprestataireapre']['id'] = $Controller->Tiersprestataireapre->find( 'list' );
			}

			return $options;
		}

		protected function _optionsRecordsModels( array $params ) {
			$departement = Configure::read( 'Cg.departement' );

			$result = parent::_optionsRecordsModels( $params );

			$result = array_merge(
				$result,
				array( 'Typeorient', 'Structurereferente', 'Referent' )
			);

			if( $departement == 66 ) {
			$result = array_merge(
				$result,
				array( 'Themeapre66', 'Typeaideapre66' )
			);
			}
			else if( $departement == 93 ) {
				$result[] = 'Tiersprestataireapre';
			}

			return $result;
		}
	}
?>