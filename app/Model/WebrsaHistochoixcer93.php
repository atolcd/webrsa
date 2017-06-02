<?php
	/**
	 * Code source de la classe WebrsaHistochoixcer93.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'WebrsaAbstractLogic', 'Model' );
	App::uses( 'WebrsaLogicAccessInterface', 'Model/Interface' );
	App::uses( 'WebrsaModelUtility', 'Utility' );

	/**
	 * La classe WebrsaHistochoixcer93 ...
	 *
	 * @package app.Model
	 */
	class WebrsaHistochoixcer93 extends WebrsaAbstractLogic implements WebrsaLogicAccessInterface
	{

		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaHistochoixcer93';

		/**
		 * Les modèles qui seront utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array( 'Histochoixcer93' );

		/**
		 * Ajoute les virtuals fields pour permettre le controle de l'accès à une action
		 *
		 * @param array $query
		 * @return type
		 */
		public function completeVirtualFieldsForAccess( array $query = array(), array $params = array() ) {
			$query['fields'] = array_merge(
				$query['fields'],
				array(
					'Contratinsertion.decision_ci',
					'Contratinsertion.dd_ci',
					'Contratinsertion.df_ci',
					'Cer93.positioncer',
				)
			);
			return $query;
		}

		/**
		 * Permet d'obtenir le nécéssaire pour calculer les droits d'accès métier à une action
		 *
		 * @param array $conditions
		 * @return array
		 */
		public function getDataForAccess(array $conditions, array $params = array()) {
			$query = array(
				'fields' => array(),
				'joins' => array(
					$this->Histochoixcer93->Cer93->Contratinsertion->join( 'Cer93', array( 'type' => 'INNER' ) )
				),
				'conditions' => $conditions,
				'contain' => false,
			);

			$results = $this->Histochoixcer93->Cer93->Contratinsertion->find( 'all', $this->completeVirtualFieldsForAccess( $query ) );
			return $results;
		}

		/**
		 * Permet d'obtenir les paramètres à envoyer à WebrsaAccess pour une personne en particulier
		 *
		 * @see WebrsaAccess::getParamsList
		 * @param integer $personne_id
		 * @param array $params - Liste des paramètres actifs
		 */
		public function getParamsForAccess($personne_id, array $params = array()) {
			$results = array();
			return $results;
		}
	}
?>