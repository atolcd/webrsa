<?php
	/**
	 * Code source de la classe WebrsaDecisionpropoorientsocialecov58.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );
	App::uses( 'ConfigurableQueryFields', 'ConfigurableQuery.Utility' );

	/**
	 * La classe WebrsaDecisionpropoorientsocialecov58 contient de la logique
	 * métier pour les décisions de la thématique de COV "Orientation sociale de
	 * fait".
	 *
	 * @package app.Model
	 */
	class WebrsaDecisionpropoorientsocialecov58 extends AppModel
	{

		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaDecisionpropoorientsocialecov58';

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
			'Decisionpropoorientsocialecov58'
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
				'Typeorient' => 'NvTypeorient',
				'Structurereferente' => 'NvStructurereferente',
				'Referent' => 'NvReferent',
			);

			$query['fields'] = array_merge(
				$query['fields'],
				array_words_replace(
					ConfigurableQueryFields::getModelsFields(
						array(
							$this->Decisionpropoorientsocialecov58,
							$this->Decisionpropoorientsocialecov58->Typeorient,
							$this->Decisionpropoorientsocialecov58->Structurereferente,
							$this->Decisionpropoorientsocialecov58->Referent
						)
					),
					$replacements
				)
			);

			$query['joins'][] = array_words_replace( $this->Decisionpropoorientsocialecov58->join( 'Typeorient', array( 'type' => 'INNER' ) ), $replacements );
			$query['joins'][] = array_words_replace( $this->Decisionpropoorientsocialecov58->join( 'Structurereferente', array( 'type' => 'INNER' ) ), $replacements );
			$query['joins'][] = array_words_replace( $this->Decisionpropoorientsocialecov58->join( 'Referent', array( 'type' => 'LEFT OUTER' ) ), $replacements );

			return $query;
		}
	}
?>