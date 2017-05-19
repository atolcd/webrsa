<?php
	/**
	 * Code source de la classe WebrsaPropoorientationcov58.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'ConfigurableQueryFields', 'ConfigurableQuery.Utility' );

	/**
	 * La classe WebrsaPropoorientationcov58 contient de la logique métier pour
	 * la thématique de COV "Proposition d'orientation".
	 *
	 * @package app.Model
	 */
	class WebrsaPropoorientationcov58 extends AppModel
	{

		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaPropoorientationcov58';

		/**
		 * Ce modèle n'utilise pas directement de table.
		 *
		 * @var integer
		 */
		public $useTable = false;

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = -1;

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
			'Propoorientationcov58'
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

			$replacements = array();

			$query['fields'] = array_merge(
				$query['fields'],
				array_words_replace(
					ConfigurableQueryFields::getModelsFields(
						array(
							$this->Propoorientationcov58,
							$this->Propoorientationcov58->Typeorient,
							$this->Propoorientationcov58->Structurereferente,
							$this->Propoorientationcov58->Referent
						)
					),
					$replacements
				)
			);

			$query['joins'][] = $this->Propoorientationcov58->join( 'Typeorient', array( 'type' => 'INNER' ) );
			$query['joins'][] = $this->Propoorientationcov58->join( 'Structurereferente', array( 'type' => 'INNER' ) );
			$query['joins'][] = $this->Propoorientationcov58->join( 'Referent', array( 'type' => 'LEFT OUTER' ) );

			return $query;
		}
	}
?>