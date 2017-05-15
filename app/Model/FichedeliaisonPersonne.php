<?php
	/**
	 * Code source de la classe FichedeliaisonPersonne.
	 *
	 * @package app.Model
	 * @license Expression license is undefined on line 11, column 23 in Templates/CakePHP/CakePHP Model.php.
	 */

	/**
	 * La classe FichedeliaisonPersonne ...
	 *
	 * @package app.Model
	 */
	class FichedeliaisonPersonne extends AppModel
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'FichedeliaisonPersonne';

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
		public $actsAs = array(
			'Postgres.PostgresAutovalidate',
			'Validation2.Validation2Formattable',
		);

		/**
		 * Associations "Belongs to".
		 *
		 * @var array
		 */
		public $belongsTo = array(
			'Personne' => array(
				'className' => 'Personne',
				'foreignKey' => 'personne_id',
				'conditions' => null,
				'type' => null,
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
			'Fichedeliaison' => array(
				'className' => 'Fichedeliaison',
				'foreignKey' => 'fichedeliaison_id',
				'conditions' => null,
				'type' => null,
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
		);
		
		/**
		 * Permet d'obtenir une liste des personnes dans le foyer sous forme de phrase
		 * ex: array(12546 => 'Le demandeur (Monsieur JOHN DOE)')
		 * 
		 * @param integer $foyer_id
		 * @return array
		 */
		public function optionsConcerne($foyer_id) {
			$Foyer =& $this->Personne->Foyer;
			
			$query = array(
				'fields' => array(
					'Personne.id',
					'((CASE '
					. 'WHEN "Prestation"."rolepers" = \'DEM\' THEN \'Le demandeur\' '
					. 'WHEN "Prestation"."rolepers" = \'CJT\' THEN \'Le conjoin\' '
					. 'WHEN "Prestation"."rolepers" = \'ENF\' THEN \'Un enfant\' '
					. 'END) '
					. '|| \' (\' || '
					. '(CASE '
					. 'WHEN "Personne"."qual" = \'MR\' THEN \'Monsieur \' '
					. 'WHEN "Personne"."qual" = \'MME\' THEN \'Madame \' '
					. 'ELSE \'\' '
					. 'END) '
					. '|| "Personne"."prenom" || \' \' || "Personne"."nom" || \')\') AS "Personne__appelation"',
				),
				'recursive' => -1,
				'joins' => array(
					$Foyer->join('Personne'),
					$Foyer->Personne->join('Prestation', array('type' => 'INNER')),
				),
				'conditions' => array(
					'Foyer.id' => $foyer_id
				)
			);
			$results = $Foyer->find('all', $query);
			
			$options = array();
			foreach ($results as $result) {
				$options[Hash::get($result, 'Personne.id')] = Hash::get($result, 'Personne.appelation');
			}
			
			return $options;
		}
	}
?>