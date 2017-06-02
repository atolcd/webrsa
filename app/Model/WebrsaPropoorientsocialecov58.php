<?php
	/**
	 * Code source de la classe WebrsaPropoorientsocialecov58.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );
	App::uses( 'ConfigurableQueryFields', 'ConfigurableQuery.Utility' );

	/**
	 * La classe WebrsaPropoorientsocialecov58 contient de la logique métier pour
	 * la thématique de COV "Orientation sociale de fait".
	 *
	 * @package app.Model
	 */
	class WebrsaPropoorientsocialecov58 extends AppModel
	{

		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaPropoorientsocialecov58';

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
			'Propoorientsocialecov58'
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
				'Structurereferente' => 'Structurereferenterdv',
				'Referent' => 'Referentrdv',
			);

			$query['fields'] = array_merge(
				$query['fields'],
				array_words_replace(
					ConfigurableQueryFields::getModelsFields(
						array(
							$this->Propoorientsocialecov58->Rendezvous,
							$this->Propoorientsocialecov58->Rendezvous->Structurereferente,
							$this->Propoorientsocialecov58->Rendezvous->Referent,
							$this->Propoorientsocialecov58->Rendezvous->Typerdv,
							$this->Propoorientsocialecov58->Rendezvous->Statutrdv
						)
					),
					$replacements
				)
			);

			$query['joins'][] = $this->Propoorientsocialecov58->join( 'Rendezvous', array( 'type' => 'INNER' ) );
			$query['joins'][] = array_words_replace( $this->Propoorientsocialecov58->Rendezvous->join( 'Structurereferente', array( 'type' => 'INNER' ) ), $replacements );
			$query['joins'][] = array_words_replace( $this->Propoorientsocialecov58->Rendezvous->join( 'Referent', array( 'type' => 'LEFT OUTER' ) ), $replacements );
			$query['joins'][] = $this->Propoorientsocialecov58->Rendezvous->join( 'Typerdv', array( 'type' => 'INNER' ) );
			$query['joins'][] = $this->Propoorientsocialecov58->Rendezvous->join( 'Statutrdv', array( 'type' => 'INNER' ) );

			return $query;
		}
	}
?>