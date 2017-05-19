<?php
	/**
	 * Code source de la classe WebrsaSignalementep.
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
	 * La classe WebrsaSignalementep ...
	 *
	 * @package app.Model
	 */
	class WebrsaSignalementep extends WebrsaAbstractLogic implements WebrsaLogicAccessInterface
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaSignalementep';

		/**
		 * Les modèles qui seront utilisés par ce modèle.
		 *
		 * @var array
		 */
		public $uses = array( 'Signalementep93' );

		/**
		 * Ajoute les virtuals fields pour permettre le controle de l'accès à une action
		 *
		 * @param array $query
		 * @return type
		 */
		public function completeVirtualFieldsForAccess( array $query = array(), array $params = array() ) {
			$sqDossierepEncours = $this->Signalementep93->Dossierep->vfDossierepEnCours(
				'Contratinsertion.personne_id',
				array( 'signalementseps93', 'contratscomplexeseps93' )
			);

			$sqDossierepPossible = $this->Signalementep93->Dossierep->vfDossierepPossible(
				'Contratinsertion.personne_id'
			);

			$query['fields'] = array_merge(
				$query['fields'],
				array(
					'Contratinsertion.id',
					'Contratinsertion.decision_ci',
					'Contratinsertion.dd_ci',
					'Contratinsertion.df_ci',
					'Cer93.positioncer',
					"( {$sqDossierepPossible} ) AS \"Dossierep__possible\"",
					"( {$sqDossierepEncours} ) AS \"Dossierep__encours_cer\"",
					'Dossierep.actif',
					'Passagecommissionep.etatdossierep'
				)
			);

			if( false === WebrsaModelUtility::findJoinKey( 'Cer93', $query ) ) {
				$query['joins'][] = $this->Signalementep93->Contratinsertion->join( 'Cer93', array( 'type' => 'INNER' ) );
			}

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
					$this->Signalementep93->join( 'Contratinsertion', array( 'type' => 'INNER' ) ),
					$this->Signalementep93->Contratinsertion->join( 'Cer93', array( 'type' => 'INNER' ) ),
					$this->Signalementep93->join( 'Dossierep', array( 'type' => 'INNER' ) ),
					$this->Signalementep93->Dossierep->join(
						'Passagecommissionep',
						array(
							'type' => 'LEFT OUTER',
							'conditions' => array(
								'OR' => array(
									'Passagecommissionep.id IS NULL',
									'Passagecommissionep.id IN ( '.$this->Signalementep93->Dossierep->Passagecommissionep->sqDernier().' )'
								)
							)
						)
					)
				),
				'conditions' => $conditions,
				'contain' => false,
			);

			$results = $this->Signalementep93->find( 'all', $this->completeVirtualFieldsForAccess( $query ) );
//debug( compact( 'query', 'results' ) );
			return $results;
		}

		/**
		 * Permet d'obtenir les paramètres à envoyer à WebrsaAccess pour une personne en particulier
		 *
		 * @todo
		 *
		 * @see WebrsaAccess::getParamsList
		 * @param integer $parent_id
		 * @param array $params - Liste des paramètres actifs
		 */
		public function getParamsForAccess( $parent_id, array $params = array() ) {
			$results = array();

			if( in_array( 'ajoutPossible', $params ) ) {
				$results['ajoutPossible'] = $this->ajoutPossible( $parent_id );
			}

			return $results;
		}

		/**
		 * Permet de savoir si il est possible d'ajouter un enregistrement
		 *
		 * @param integer $parent_id L'id de l'enregistrement de contratsinsertions
		 * @return boolean
		 */
		public function ajoutPossible( $parent_id ) {
			App::uses( 'WebrsaAccessSignalementseps', 'Utility' );
			$sqDossierepEncours = $this->Signalementep93->Dossierep->vfDossierepEnCours(
				'Contratinsertion.personne_id',
				array( 'signalementseps93', 'contratscomplexeseps93' )
			);

			$sqDossierepPossible = $this->Signalementep93->Dossierep->vfDossierepPossible(
				'Contratinsertion.personne_id'
			);

			$query = array(
				'fields' => array(
					'Contratinsertion.id',
					'Contratinsertion.decision_ci',
					'Contratinsertion.dd_ci',
					'Contratinsertion.df_ci',
					'Cer93.positioncer',
					"( {$sqDossierepPossible} ) AS \"Dossierep__possible\"",
					"( {$sqDossierepEncours} ) AS \"Dossierep__encours_cer\""
				),
				'joins' => array(
					$this->Signalementep93->Contratinsertion->join( 'Cer93', array( 'type' => 'INNER' ) )
				),
				'contain' => false,
				'conditions' => array(
					'Contratinsertion.id' => $parent_id,
				)
			);
			$record = $this->Signalementep93->Contratinsertion->find( 'first', $query );

			return WebrsaAccessSignalementseps::check( 'Signalementseps', 'add', $record );
		}
	}
?>