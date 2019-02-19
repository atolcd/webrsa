<?php
	/**
	 * Code source de la classe WebrsaQuestionnaireb7pdv93.
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
	 * La classe WebrsaQuestionnaireb7pdv93 ...
	 *
	 * @todo
	 *
	 * @package app.Model
	 */
	class WebrsaQuestionnairesb7pdvs93 extends WebrsaAbstractLogic implements WebrsaLogicAccessInterface
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaQuestionnaireb7pdv93';

		/**
		 * Les modèles qui seront utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array( 'Questionnaireb7pdv93', 'Typeemploi', 'Dureeemploi' );

		/**
		 * Liste des alias vers Entreeromev3
		 *
		 * @var array
		 */
		public $romev3LinkedModels = array('Expproromev3');

		/**
		 * Ajoute les virtuals fields pour permettre le controle de l'accès à une action
		 *
		 * @todo
		 *
		 * @param array $query
		 * @return type
		 */
		public function completeVirtualFieldsForAccess(array $query = array(), array $params = array()) {
			$fields = array();

			return Hash::merge($query, array('fields' => array_values($fields)));
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
				'joins' => array(),
				'conditions' => $conditions,
				'contain' => false,
			);

			$results = $this->Questionnaireb7pdv93->find( 'all', $this->completeVirtualFieldsForAccess( $query ) );
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
			$messages = (
				$messages === null || !is_array( $messages )
				? $this->Questionnaireb7pdv93->findById( $personne_id )
				: $messages
			);
			return $this->addEnabled( $messages );
		}

		/**
		 * Permet de savoir si un ajout est possible à partir des messages
		 * renvoyés par la méthode messages.
		 * Fonction rajoutée en PHP pour que le système fonctionne /!\ Pourquoi ??? à creuser ...
		 *
		 * @param array $messages
		 * @return boolean
		 */
		public function addEnabled( array $messages ) {

			$status = ! ( in_array( 'error', $messages ) || array_key_exists( 'Questionnaireb1pdv93.exists', $messages ) )  ;

			return $status;
		}

		/**
		 * Retourne les options à utiliser dans le moteur de recherche, le
		 * formulaire d'ajout / de modification, etc.. suivant le CG connecté.
		 *
		 * @param array $params
		 * @return array
		 */
		public function options( array $params = array() ) {
			$params = $params + array( 'find' => true, 'allocataire' => false, 'alias' => 'Questionnaireb7pdv93', 'enums' => true );

			$cacheKey = Inflector::underscore( $this->Questionnaireb7pdv93->useDbConfig ).'_'.Inflector::underscore( $this->Questionnaireb7pdv93->alias ).'_'.Inflector::underscore( __FUNCTION__ ).'_'.sha1( serialize( $params ) );
			$return = Cache::read( $cacheKey );

			if( $return === false ) {
				$return = array();

				$return['Typeemploi'] = $this->Typeemploi->find ('list');
				$return['Dureeemploi'] = $this->Dureeemploi->find ('list');

				if( $params['find'] ) {
					foreach( $this->romev3LinkedModels as $alias ) {
						$return = Hash::merge(
							$return,
							$this->Questionnaireb7pdv93->{$alias}->options()
						);
					}
				}
			}

			return $return;
		}
	}
?>