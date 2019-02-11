<?php
	/**
	 * Code source de la classe Valprogfichecandidature66.
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Valprogfichecandidature66 ...
	 *
	 * @package app.Model
	 */
	class Valprogfichecandidature66 extends AppModel
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'Valprogfichecandidature66';

		/**
		 * Behaviors utilisés par le modèle.
		 *
		 * @var array
		 */
		public $actsAs = array(
			'Postgres.PostgresAutovalidate',
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesComparison',
			'Validation2.Validation2RulesFieldtypes',
		);

		/**
		 * Règles de validation.
		 *
		 * @var array
		 */
		public $validate = array(
			'name' => array(
				'checkUnique' => array(
					'rule' => array( 'checkUnique', array( 'progfichecandidature66_id', 'name' ) ),
					'message' => 'Valeur déjà utilisée'
				)
			),
			'progfichecandidature66_id' => array(
				'checkUnique' => array(
					'rule' => array( 'checkUnique', array( 'progfichecandidature66_id', 'name' ) ),
					'message' => 'Valeur déjà utilisée'
				)
			)
		);

		/**
		 * Associations "Belongs to".
		 *
		 * @var array
		 */
		public $belongsTo = array(
			'Progfichecandidature66' => array(
				'className' => 'Progfichecandidature66',
				'foreignKey' => 'progfichecandidature66_id',
				'conditions' => null,
				'type' => null,
				'fields' => null,
				'order' => null,
				'counterCache' => null
			),
		);

		public function dependantSelectOptions() {
			$results = $this->find('all',
				array(
					'fields' => array(
						'("Valprogfichecandidature66"."progfichecandidature66_id" || \'_\' || "Valprogfichecandidature66"."id" ) AS "Valprogfichecandidature66__dependentSelectValues"',
						'Valprogfichecandidature66.name'
					),
					'joins' => array(
						$this->join('Progfichecandidature66', array('type' => 'INNER'))
					),
					'order' => array(
						'Progfichecandidature66.name' => 'ASC',
						'Valprogfichecandidature66.name' => 'ASC',
					)
				)
			);

			$return = array();
			foreach ($results as $result) {
				$return[$result['Valprogfichecandidature66']['dependentSelectValues']] = $result['Valprogfichecandidature66']['name'];
			}

			return $return;
		}

		/**
		 * Il faut supprimer le cache en cas de modification
		 * @param boolean $created
		 */
		public function afterSave( $created, $options = array() ) {
			parent::afterSave( $created, $options );

			$possibleCacheKeys = array(
				"ActionscandidatsPersonnes_add_options",
				"ActionscandidatsPersonnes_edit_options",
			);

			foreach ($possibleCacheKeys as $cacheKey) {
				Cache::delete( $cacheKey );
			}
		}
	}
?>