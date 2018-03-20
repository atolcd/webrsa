<?php
	/**
	 * Code source de la classe WebrsaQuestionnaired2pdv93.
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
	 * La classe WebrsaQuestionnaired2pdv93 ...
	 *
	 * @todo
	 *
	 * @package app.Model
	 */
	class WebrsaQuestionnaired2pdv93 extends WebrsaAbstractLogic implements WebrsaLogicAccessInterface
	{

		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaQuestionnaired2pdv93';

		/**
		 * Les modèles qui seront utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array( 'Questionnaired2pdv93' );

		/**
		 * Liste des alias vers Entreeromev3
		 *
		 * @var array
		 */
		public $romev3LinkedModels = array('Emploiromev3');

		/**
		 * Ajoute les virtuals fields pour permettre le controle de l'accès à une action
		 *
		 * @todo
		 *
		 * @param array $query
		 * @return type
		 */
		public function completeVirtualFieldsForAccess(array $query = array(), array $params = array()) {
			return $query;
		}

		/**
		 * Permet d'obtenir le nécéssaire pour calculer les droits d'accès métier à une action
		 *
		 * @todo
		 *
		 * @param array $conditions
		 * @return array
		 */
		public function getDataForAccess( array $conditions, array $params = array() ) {
			$query = array(
				'fields' => array(),
				'joins' => array(
					$this->Questionnaired2pdv93->join( 'Questionnaired1pdv93', array( 'type' => 'INNER' ) ),
					$this->Questionnaired2pdv93->Questionnaired1pdv93->join( 'Rendezvous', array( 'type' => 'INNER' ) )
				),
				'conditions' => $conditions,
				'contain' => false,
			);

			$results = $this->Questionnaired2pdv93->find( 'all', $this->completeVirtualFieldsForAccess( $query ) );
			return $results;
		}

		/**
		 * Permet d'obtenir les paramètres à envoyer à WebrsaAccess pour une personne en particulier
		 *
		 * @todo
		 *
		 * @see WebrsaAccess::getParamsList
		 * @param integer $personne_id
		 * @param array $params - Liste des paramètres actifs
		 */
		public function getParamsForAccess($personne_id, array $params = array()) {
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
			$status = $this->Questionnaired2pdv93->statusQuestionnaireD2( $personne_id );
			return $status['button'];
		}

		/**
		 * Retourne les options à utiliser dans le moteur de recherche, le
		 * formulaire d'ajout / de modification, etc.. suivant le CG connecté.
		 *
		 * @param array $params
		 * @return array
		 */
		public function options( array $params = array() ) {
			$params = $params + array( 'find' => true, 'allocataire' => false, 'alias' => 'Questionnaired2pdv93', 'enums' => true );

			$cacheKey = Inflector::underscore( $this->Questionnaired2pdv93->useDbConfig ).'_'.Inflector::underscore( $this->Questionnaired2pdv93->alias ).'_'.Inflector::underscore( __FUNCTION__ ).'_'.sha1( serialize( $params ) );
			$return = Cache::read( $cacheKey );

			if( $return === false ) {
				$return = array();

				if( $params['find'] ) {
					foreach( $this->romev3LinkedModels as $alias ) {
						$return = Hash::merge(
							$return,
							$this->Questionnaired2pdv93->{$alias}->options()
						);
					}
				}
			}

			return $return;
		}
	}
?>