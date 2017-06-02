<?php
	/**
	 * Code source de la classe WebrsaCohortesPropospdosValideesComponent.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller.Component
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'WebrsaAbstractRecherchesComponent', 'Controller/Component' );

	/**
	 * La classe WebrsaCohortesPropospdosValideesComponent ...
	 *
	 * @see WebrsaCohortesPropospdosNouvellesComponent
	 *
	 * @package app.Controller.Component
	 */
	class WebrsaCohortesPropospdosValideesComponent extends WebrsaAbstractRecherchesComponent
	{
		/**
		 * @inheritdoc
		 */
		protected function _optionsEnums( array $params ) {
			$Controller = $this->_Collection->getController();

			return Hash::merge(
				parent::_optionsEnums( $params ),
				$Controller->Propopdo->enums()
			);
		}

		/**
		 * @inheritdoc
		 */
		protected function _optionsRecords( array $params ) {
			$Controller = $this->_Collection->getController();

			$query = array(
				'fields' => array(
					'User.nom_complet'
				),
				'conditions' => array(
					'User.isgestionnaire' => 'O'
				),
				'order' => array(
					'User.nom_complet'
				)
			);

			return Hash::merge(
				parent::_optionsRecords( $params ),
				array(
					'Propopdo' => array(
						'typepdo_id' => $Controller->Propopdo->Typepdo->findForRecherche( 'list' ),
						'user_id' => $Controller->User->find( 'list', $query )
					),
					'Decisionpropopdo' => array(
						'decisionpdo_id' => $Controller->Propopdo->Decisionpropopdo->Decisionpdo->findForRecherche( 'list' )
					)
				)
			);
		}

		/**
		 * @inheritdoc
		 */
		protected function _optionsRecordsModels( array $params ) {
			return array_unique(
				array_merge(
					parent::_optionsRecordsModels( $params ),
					array( 'Decisionpropopdo', 'Typepdo', 'User' )
				)
			);
		}

		/**
		 * @inheritdoc
		 */
		protected function _getCohorteFields( array $params = array() ) {
			$Controller = $this->_Collection->getController();
			$fields = parent::_getCohorteFields($params);

			return $fields;
		}
	}
?>