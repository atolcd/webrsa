<?php
	/**
	 * Code source de la classe WebrsaRelancenonrespectsanctionep93.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses('WebrsaAbstractLogic', 'Model');
	App::uses('WebrsaLogicAccessInterface', 'Model/Interface');
	App::uses('WebrsaModelUtility', 'Utility');

	/**
	 * La classe WebrsaRelancenonrespectsanctionep93 possède la logique métier
	 * concernant les relances pouvant aboutir en EP du CG 93.
	 *
	 * @package app.Model
	 */
	class WebrsaRelancenonrespectsanctionep93 extends WebrsaAbstractLogic implements WebrsaLogicAccessInterface
	{

		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaRelancenonrespectsanctionep93';

		/**
		 * Les modèles qui seront utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array( 'Relancenonrespectsanctionep93' );

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
		 * Permet d'obtenir le nécéssaire pour calculer les droits d'accès métier
		 * à une action.
		 *
		 * @fixme La jointure sur Pdf et le champ Pdf.id devraient se trouver dans completeVirtualFieldsForAccess
		 *
		 * @param array $conditions
		 * @return array
		 */
		public function getDataForAccess( array $conditions, array $params = array() ) {
			$query = array(
				'fields' => array(
					'Relancenonrespectsanctionep93.id',
					'Nonrespectsanctionep93.id',
					'Nonrespectsanctionep93.personne_id',
					'Pdf.id',
				),
				'conditions' => $conditions,
				'joins' => array(
					$this->Relancenonrespectsanctionep93->join( 'Nonrespectsanctionep93', array( 'type' => 'INNER' ) ),
					$this->Relancenonrespectsanctionep93->join( 'Pdf', array( 'type' => 'LEFT OUTER' ) )
				),
				'contain' => false,
				'order' => array(
					'Relancenonrespectsanctionep93.daterelance' => 'DESC',
					'Relancenonrespectsanctionep93.id' => 'DESC',
				)
			);

			$results = $this->Relancenonrespectsanctionep93->find('all', $this->completeVirtualFieldsForAccess($query));
			return $results;
		}

		/**
		 * Permet d'obtenir les paramètres à envoyer à WebrsaAccess pour une
		 * personne en particulier
		 *
		 * @todo
		 *
		 * @see WebrsaAccess::getParamsList
		 * @param integer $personne_id
		 * @param array $params - Liste des paramètres actifs
		 */
		public function getParamsForAccess( $personne_id, array $params = array() ) {
			$results = array();

			if (in_array('ajoutPossible', $params)) {
				$results['ajoutPossible'] = $this->ajoutPossible($personne_id);
			}

			return $results;
		}

		/**
		 * Permet de savoir si il est possible d'ajouter un enregistrement
		 *
		 * @param integer $personne_id
		 * @param array $errors
		 * @return boolean
		 */
		public function ajoutPossible( $personne_id, $errors = null ) {
			$results = $this->Relancenonrespectsanctionep93->getRelance(
                $personne_id,
                array(),
                false,
				null,
				null
            );

			$errors = (
				$errors === null || !is_array( $errors )
				? $this->Relancenonrespectsanctionep93->erreursPossibiliteAjout( $personne_id )
				: $errors
			);

			return !empty( $results ) && empty( $errors );
		}
	}
?>