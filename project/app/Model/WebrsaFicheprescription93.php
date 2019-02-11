<?php
	/**
	 * Code source de la classe WebrsaFicheprescription93.
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
	 * La classe WebrsaFicheprescription93 ...
	 *
	 * @todo: Referent.horszone
	 *
	 * @package app.Model
	 */
	class WebrsaFicheprescription93 extends WebrsaAbstractLogic implements WebrsaLogicAccessInterface
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaFicheprescription93';

		/**
		 * Les modèles qui seront utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array( 'Ficheprescription93' );

		/**
		 * Ajoute les virtuals fields pour permettre le controle de l'accès à une action
		 *
		 * @param array $query
		 * @return type
		 */
		public function completeVirtualFieldsForAccess( array $query = array(), array $params = array() ) {
			$query['fields'][] = 'Ficheprescription93.statut';
			$query['fields'][] = 'Ficheprescription93.personne_id';
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
					'Ficheprescription93.id',
					'Ficheprescription93.personne_id',
					'Ficheprescription93.statut',
				),
				'conditions' => $conditions,
				'joins' => array(),
				'contain' => false
			);

			$results = $this->Ficheprescription93->find( 'all', $this->completeVirtualFieldsForAccess( $query ) );
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
		 * @param array $messages
		 * @return boolean
		 */
		public function ajoutPossible( $personne_id, $messages = null ) {
			$messages = (
				$messages === null || !is_array( $messages )
				? $this->Ficheprescription93->messages( $personne_id )
				: $messages
			);
			return $this->Ficheprescription93->addEnabled( $messages );
		}
	}
?>