<?php
	/**
	 * Code source de la classe Correspondancepersonne.
	 *
	 * @package app.Model
	 * @license Expression license is undefined on line 11, column 23 in Templates/CakePHP/CakePHP Model.php.
	 */

	/**
	 * La classe Correspondancepersonne ...
	 *
	 * @package app.Model
	 */
	class Correspondancepersonne extends AppModel
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'Correspondancepersonne';

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
		 * Associations "Belongs to".
		 *
		 * @var array
		 */
		public $belongsTo = array(
			'Personne1' => array(
				'className' => 'Personne',
				'foreignKey' => 'personne1_id',
			),
			'Personne2' => array(
				'className' => 'Personne',
				'foreignKey' => 'personne2_id',
			),
		);
		
		/**
		 * Recalcule les correspondances selon un personne_id donné
		 * @param integer $personne_id
		 * @param boolean $all
		 * @param integer $limit
		 * @return array
		 */
		public function updateByPersonneId( $personne_id, $all = true, $limit = false ) {
			$conditions = array(
				'OR' => array(
					'Personne1.id' => $personne_id,
					'Personne2.id' => $personne_id
				)
			);
			
			return $this->updateCorrespondance($conditions, $all, $limit);
		}

		/**
		 * Recalcule les correspondances entre les differents personne_id
		 * La suppression ne permet pas d'imposer une limite, donc ne réécrira pas dans la table si $limit != false
		 * Utile pour ce servir de cette fonction comme recherche
		 * 
		 * @param array $conditions
		 * @param boolean $all
		 * @param integer $limit 
		 * @return array
		 */
		public function updateCorrespondance( $conditions = array(), $all = true, $limit = false ) {
			$operateur = $all ? '<>' : '<';
			$limit = $limit ? ' LIMIT ' . $limit : '';
			$baseConditions = array(
				"Personne1.id $operateur Personne2.id",
				'nir_correct13( Personne1.nir )',
				'nir_correct13( Personne2.nir )',
				'SUBSTRING( TRIM( BOTH \' \' FROM Personne1.nir ) FROM 1 FOR 13 ) = SUBSTRING( TRIM( BOTH \' \' FROM Personne2.nir ) FROM 1 FOR 13 )',
			);
			
			$queryAnomalies = 'SELECT
				"Personne1".id AS "Personne1__id", "Personne2".id AS "Personne2__id"
				FROM personnes AS "Personne1",  personnes AS "Personne2"
				WHERE'
				. $this->getDataSource()->conditions( 
					array_merge( 
						$baseConditions,
						array( "Personne1.dtnai <> Personne2.dtnai"	), 
						$conditions
					), true, false
				) . $limit
			;
			$anomalies = $this->query($queryAnomalies);
		
			$queryCorrespondances = 'SELECT
				"Personne1".id AS "Personne1__id", "Personne2".id AS "Personne2__id"
				FROM personnes AS "Personne1",  personnes AS "Personne2"
				WHERE'
				. $this->getDataSource()->conditions( 
					array_merge( 
						$baseConditions,
						array( 'Personne1.dtnai = Personne2.dtnai'	), 
						$conditions
					), true, false
				) . $limit
			;
			$correspondances = $this->query($queryCorrespondances);

			$results = $this->_sort( array_merge($this->_buildSave($anomalies, true), $this->_buildSave($correspondances, false)) );
			
			$this->deleteAllUnBound( $this->_conditionsForDelete( $conditions ) );

			$this->saveMany( $results );
			
			return $results;
		}
		
		/**
		 * Prépare les données reçu pour l'enregistrement en saveMany
		 * @param array $datas
		 * @param boolean $anomalie
		 * @return array
		 */
		protected function _buildSave( $datas, $anomalie = false ) {
			$save = array();
			foreach( $datas as $data ) {
				if (isset($data['Personne1']['id']) && isset($data['Personne2']['id'])) {
					$save[] = array(
						'personne1_id' => $data['Personne1']['id'],
						'personne2_id' => $data['Personne2']['id'],
						'anomalie' => $anomalie
					);
				}
			}
			return $save;
		}
		
		/**
		 * Permet de trier par personne1_id les données destiné à la sauvegardes
		 * @param array $datas
		 * @return array
		 */
		protected function _sort( $datas ) {
			$key_personne1List = array();
			
			foreach( $datas as $key => $value ) {
				$key_personne1List[$value['personne1_id'] . '_' . $key] = $value;
			}
			
			array_multisort($key_personne1List);
			$result = array();
			
			foreach ( $key_personne1List as $key => $value ) {
				$result[] = $value;
			}
			
			return $result;
		}
		
		/**
		 * Interverti PersonneX.id en personneX_id pour la suppressions des lignes
		 * @param array $conditions
		 * @return array
		 */
		protected function _conditionsForDelete( $conditions ) {
			$return = array_words_replace($conditions, array(
				'Personne1.id' => 'Correspondancepersonne.personne1_id',
				'Personne2.id' => 'Correspondancepersonne.personne2_id',
			));
			
			if ( empty($return) ) {
				return array('1 = 1');
			}
			
			return $return;
		}
	}
?>