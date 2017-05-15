<?php
	/**
	 * Code source de la classe WebrsaCohortesNonorientes66Component.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller.Component
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'WebrsaAbstractCohortesComponent', 'Controller/Component' );

	/**
	 * La classe WebrsaCohortesNonorientes66Component ...
	 *
	 * @package app.Controller.Component
	 */
	class WebrsaCohortesNonorientes66Component extends WebrsaAbstractCohortesComponent
	{
		/**
		 * Retourne les options de type "enum", c'est à dire liées aux schémas des
		 * tables de la base de données.
		 *
		 * @return array
		 */
		protected function _optionsEnums( array $params = array() ) {
			$Controller = $this->_Collection->getController();
			
			if( !isset( $Controller->Nonoriente66 ) ) {
				$Controller->loadModel( 'Nonoriente66' );
			}
			
			$options = parent::_optionsEnums( $params );
			$options = array_merge(
				$options,
				$Controller->Nonoriente66->enums()
			);
			
			return $options;
		}
		
		/**
		 * Retourne les options stockées liées à des enregistrements en base de
		 * données, ne dépendant pas de l'utilisateur connecté.
		 *
		 * @return array
		 */
		protected function _optionsRecords( array $params = array() ) {
			$Controller = $this->_Collection->getController();
			
			if( !isset( $Controller->Nonoriente66 ) ) {
				$Controller->loadModel( 'Nonoriente66' );
			}
			
			$options = parent::_optionsRecords($params);
			$options['Orientstruct']['structurereferente_id'] = $Controller->Nonoriente66->Orientstruct->Structurereferente->list1Options( array( 'orientation' => 'O' ) );
			$options['Orientstruct']['typeorient_id'] = $Controller->Nonoriente66->Orientstruct->Typeorient->listOptions(
				array('Typeorient.id' => Configure::read( 'Nonoriente66.notisemploi.typeorientId' ))
			);
			
			return $options;
		}

		/**
		 * Retourne les noms des modèles dont des enregistrements seront mis en
		 * cache après l'appel à la méthode _optionsRecords() afin que la clé de
		 * cache générée par la méthode _options() se trouve associée dans
		 * ModelCache.
		 *
		 * @see _optionsRecords(), _options()
		 *
		 * @return array
		 */
		protected function _optionsRecordsModels( array $params ) {
			$result = array_merge(
				parent::_optionsRecordsModels( $params ),
				array(
					'Structurereferente',
					'Referent',
				)
			);

			return $result;
		}
	}
?>