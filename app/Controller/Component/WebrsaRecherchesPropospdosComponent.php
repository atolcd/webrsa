<?php
	/**
	 * Code source de la classe WebrsaRecherchesPropospdosComponent.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller.Component
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'WebrsaAbstractRecherchesComponent', 'Controller/Component' );

	/**
	 * La classe WebrsaRecherchesPropospdosComponent ...
	 *
	 * @package app.Controller.Component
	 */
	class WebrsaRecherchesPropospdosComponent extends WebrsaAbstractRecherchesComponent
	{
		/**
		 * Retourne les options de type "enum", c'est à dire liées aux schémas des
		 * tables de la base de données.
		 *
		 * @return array
		 */
		protected function _optionsEnums( array $params = array() ) {
			$Controller = $this->_Collection->getController();

			$options = Hash::merge(
				parent::_optionsEnums( $params ),
				$Controller->Propopdo->Decisionpropopdo->enums(),
				$Controller->Propopdo->Decisionpropopdo->Decisionpdo->enums()
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

			$options = parent::_optionsRecords( $params );

			$options = Hash::merge(
				$options,
				array(
					'Propopdo' => array(
						'typepdo_id' => $Controller->Propopdo->Typepdo->findForRecherche( 'list' ),
						'typenotifpdo_id' => $Controller->Propopdo->Typenotifpdo->find( 'list' ),
						'originepdo_id' => $Controller->Propopdo->Originepdo->findForRecherche( 'list' ),
						'serviceinstructeur_id' => $options['Serviceinstructeur']['id'],
						'user_id' => $Controller->Propopdo->User->find( 'list', array( 'fields' => array( 'User.nom_complet' ), 'conditions' => array( 'User.isgestionnaire' => 'O' ) ) )
					),
					'Decisionpropopdo' => array(
						'decisionpdo_id' => $Controller->Propopdo->Decisionpropopdo->Decisionpdo->findForRecherche( 'list' )
					)
				)
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
				 array( 'Typepdo', 'Typenotifpdo', 'Originepdo', 'User', 'Decisionpdo' )
			);

			return $result;
		}
	}
?>