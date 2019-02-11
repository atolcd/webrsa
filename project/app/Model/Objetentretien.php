<?php
	/**
	 * Code source de la classe Objetentretien.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Objetentretien ...
	 *
	 * @package app.Model
	 */
	class Objetentretien extends AppModel
	{

		public $name = 'Objetentretien';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		public $displayField = 'name';

		public $order = 'Objetentretien.id ASC';

		public $actsAs = array(
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Postgres.PostgresAutovalidate'
		);

		public $hasMany = array(
			'Entretien' => array(
				'className' => 'Entretien',
				'foreignKey' => 'objetentretien_id',
				'dependent' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			)
		);

		/**
		 * Retourne la liste des modèles odt paramétrés pour le impressions de
		 * cette classe.
		 *
		 * @return array
		 */
		public function modelesOdt() {
			$prefix = 'Objetentretien'.DS;

			$items = $this->find(
				'all',
				array(
					'fields' => array(
						'( \''.$prefix.'\' || "'.$this->alias.'"."modeledocument" || \'.odt\' ) AS "'.$this->alias.'__modele"',
					),
					'conditions' => array( ''.$this->alias.'.modeledocument IS NOT NULL' ),
					'recursive' => -1
				)
			);
			return Set::extract( $items, '/'.$this->alias.'/modele' );
		}

	}
?>