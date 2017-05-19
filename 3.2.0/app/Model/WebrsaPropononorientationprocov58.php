<?php
	/**
	 * Code source de la classe WebrsaPropononorientationprocov58.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );
	App::uses( 'ConfigurableQueryFields', 'ConfigurableQuery.Utility' );

	/**
	 * La classe WebrsaPropononorientationprocov58 contient de la logique
	 * métier pour la thématique de COV "Proposition de maintien dans le social".
	 *
	 * @package app.Model
	 */
	class WebrsaPropononorientationprocov58 extends AppModel
	{

		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaPropononorientationprocov58';

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
			'Propononorientationprocov58'
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
				'Orientstruct' => 'VxOrientstruct',
				'Typeorient' => 'VxTypeorient',
				'Structurereferente' => 'VxStructurereferente',
				'Referent' => 'VxReferent',
			);

			$query['fields'] = array_merge(
				$query['fields'],
				array_words_replace(
					ConfigurableQueryFields::getModelsFields(
						array(
							$this->Propononorientationprocov58,
							$this->Propononorientationprocov58->Orientstruct,
							$this->Propononorientationprocov58->Typeorient,
							$this->Propononorientationprocov58->Structurereferente,
							$this->Propononorientationprocov58->Referent
						)
					),
					$replacements
				)
			);

			// FIXME: Typeorient lié à orientstruct, l'autre est une proposition ?
			$query['joins'][] = array_words_replace( $this->Propononorientationprocov58->join( 'Orientstruct', array( 'type' => 'INNER' ) ), $replacements );
			$query['joins'][] = array_words_replace( $this->Propononorientationprocov58->join( 'Typeorient', array( 'type' => 'INNER' ) ), $replacements );
			$query['joins'][] = array_words_replace( $this->Propononorientationprocov58->join( 'Structurereferente', array( 'type' => 'INNER' ) ), $replacements );
			$query['joins'][] = array_words_replace( $this->Propononorientationprocov58->join( 'Referent', array( 'type' => 'LEFT OUTER' ) ), $replacements );

			return $query;
		}
	}
?>