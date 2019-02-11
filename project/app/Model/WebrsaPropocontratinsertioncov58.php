<?php
	/**
	 * Code source de la classe WebrsaPropocontratinsertioncov58.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses('ConfigurableQueryFields', 'ConfigurableQuery.Utility');
	App::uses('WebrsaAbstractLogic', 'Model');
	App::uses('WebrsaLogicAccessInterface', 'Model/Interface');
	App::uses('WebrsaModelUtility', 'Utility');

	/**
	 * La classe WebrsaPropocontratinsertioncov58 contient de la logique
	 * métier pour la thématique de COV "Proposition de CER".
	 *
	 * @package app.Model
	 */
	class WebrsaPropocontratinsertioncov58 extends WebrsaAbstractLogic implements WebrsaLogicAccessInterface
	{

		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaPropocontratinsertioncov58';

		/**
		 * Ce modèle n'utilise pas directement de table.
		 *
		 * @var integer
		 */
		public $useTable = false;

		/**
		 * Behaviors utilisés par le modèle.
		 *
		 * @var array
		 */
		public $actsAs = array();

		/**
		 * Modèles utilisés par le modèle.
		 *
		 * @var array
		 */
		public $uses = array(
			'Propocontratinsertioncov58'
		);

		/**
		 * Complète un querydata avec les données de la thématique.
		 *
		 * @param array $query
		 * @return array
		 */
		public function completeQuery( array $query ) {
			$query += array(
				'fields' => array(),
				'conditions' => array(),
				'joins' => array()
			);

			$replacements = array(
				'Structurereferente' => 'VxStructurereferente',
				'Referent' => 'VxReferent',
			);

			$query['fields'] = array_merge(
				$query['fields'],
				array_words_replace(
					ConfigurableQueryFields::getModelsFields(
						array(
							$this->Propocontratinsertioncov58,
							$this->Propocontratinsertioncov58->Structurereferente,
							$this->Propocontratinsertioncov58->Referent
						)
					),
					$replacements
				)
			);

			$query['joins'][] = array_words_replace( $this->Propocontratinsertioncov58->join( 'Structurereferente', array( 'type' => 'INNER' ) ), $replacements );
			$query['joins'][] = array_words_replace( $this->Propocontratinsertioncov58->join( 'Referent', array( 'type' => 'LEFT OUTER' ) ), $replacements );

			return $query;
		}

		/**
		 * Ajoute les virtuals fields pour permettre le controle de l'accès à une action
		 *
		 * @param array $query
		 * @return type
		 */
		public function completeVirtualFieldsForAccess(array $query = array(), array $params = array()) {
			$fields = array(
				'Passagecov58.etatdossiercov'
			);

			$query['joins'] = isset($query['joins']) ? $query['joins'] : array();
			$joinsAvailables = Hash::extract($query, 'joins.{n}.alias');

			if (!in_array('Dossiercov58', $joinsAvailables)) {
				$query['joins'][] = $this->Propocontratinsertioncov58->join('Dossiercov58');
			}
			if (!in_array('Passagecov58', $joinsAvailables)) {
				$query['joins'][] = $this->Propocontratinsertioncov58->Dossiercov58->join('Passagecov58',
					array(
						'conditions' => array(
							'Passagecov58.id IN ('
							. 'SELECT a.id FROM passagescovs58 AS a '
							. 'INNER JOIN dossierscovs58 AS b ON a.dossiercov58_id = b.id '
							. 'WHERE b.id = "Dossiercov58"."id" '
							. 'ORDER BY a.created DESC'
							. 'LIMIT 1'
							. ')'
						)
					)
				);
			}

			return Hash::merge($query, array('fields' => array_values($fields)));
		}

		/**
		 * Permet d'obtenir le nécéssaire pour calculer les droits d'accès métier à une action
		 *
		 * @param array $conditions
		 * @return array
		 */
		public function getDataForAccess(array $conditions, array $params = array()) {
			$query = array(
				'fields' => array(
					'Propocontratinsertioncov58.id',
					'Personne.id',
				),
				'conditions' => $conditions,
				'joins' => array(
					$this->Propocontratinsertioncov58->join('Dossiercov58'),
					$this->Propocontratinsertioncov58->Dossiercov58->join('Personne'),
				),
				'contain' => false,
				'order' => array(
					'Propocontratinsertioncov58.datedemande' => 'DESC',
					'Dossiercov58.created' => 'DESC',
					'Propocontratinsertioncov58.id' => 'DESC',
				)
			);

			$results = $this->Contratinsertion->find('all', $this->completeVirtualFieldsForAccess($query));
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
			return array();
		}
	}
?>