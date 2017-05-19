<?php
	/**
	 * Code source de la classe WebrsaTransfertpdv93.
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
	 * La classe WebrsaTransfertpdv93 ...
	 *
	 * @package app.Model
	 */
	class WebrsaTransfertpdv93 extends WebrsaAbstractLogic implements WebrsaLogicAccessInterface
	{

		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaTransfertpdv93';

		/**
		 * Les modèles qui seront utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array( 'Transfertpdv93' );

		/**
		 * Ajoute les virtuals fields pour permettre le controle de l'accès à une action
		 *
		 * @todo
		 *
		 * @param array $query
		 * @return type
		 */
		public function completeVirtualFieldsForAccess( array $query = array(), array $params = array() ) {
			return $query;
		}

		/**
		 * Permet d'obtenir le nécéssaire pour calculer les droits d'accès métier à une action
		 *
		 * @param array $conditions
		 * @return array
		 */
		public function getDataForAccess( array $conditions, array $params = array() ) {
			$query = array(
				'fields' => array(
					'Transfertpdv93.id',
					'Transfertpdv93.personne_id',
				),
				'conditions' => $conditions,
				'joins' => array(
					$this->Transfertpdv93->join( 'NvOrientstruct' ),
					$this->Transfertpdv93->NvOrientstruct->join( 'Personne' )
				),
				'contain' => false,
				'order' => array(
					'Transfertpdv93.created' => 'DESC',
					'Transfertpdv93.id' => 'DESC',
				)
			);

			$results = $this->Transfertpdv93->find( 'all', $this->completeVirtualFieldsForAccess( $query ) );
			return $results;
		}

		/**
		 * Permet d'obtenir les paramètres à envoyer à WebrsaAccess pour une personne en particulier
		 *
		 * @see WebrsaAccess::getParamsList
		 * @param integer $personne_id
		 * @param array $params - Liste des paramètres actifs
		 */
		public function getParamsForAccess( $personne_id, array $params = array() ) {
			$results = array();

			if( in_array( 'ajoutPossible', $params ) ) {
				$results['ajoutPossible'] = $this->ajoutPossible( $personne_id );
			}

			return $results;
		}

		/**
		 * Permet de savoir si il est possible d'ajouter un enregistrement
		 *
		 * @param integer $personne_id
		 * @return boolean
		 */
		public function ajoutPossible( $personne_id ) {
			return true;
		}
	}
?>