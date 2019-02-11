<?php
	/**
	 * Code source de la classe Modeletypecourrierpcg66.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Modeletypecourrierpcg66 ...
	 *
	 * @package app.Model
	 */
	class Modeletypecourrierpcg66 extends AppModel
	{
		public $name = 'Modeletypecourrierpcg66';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		public $order = 'Modeletypecourrierpcg66.name ASC';

		public $actsAs = array(
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Postgres.PostgresAutovalidate'
		);

		public $belongsTo = array(
			'Typecourrierpcg66' => array(
				'className' => 'Typecourrierpcg66',
				'foreignKey' => 'typecourrierpcg66_id',
				'conditions' => '',
				'fields' => '',
				'order' => ''
			)
		);

		public $hasMany = array(
			'Piecemodeletypecourrierpcg66' => array(
				'className' => 'Piecemodeletypecourrierpcg66',
				'foreignKey' => 'modeletypecourrierpcg66_id',
				'dependent' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
			'Modeletraitementpcg66' => array(
				'className' => 'Modeletraitementpcg66',
				'foreignKey' => 'modeletypecourrierpcg66_id',
				'dependent' => true,
				'conditions' => '',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
		);

		/**
		 * Retourne la liste des modèles odt paramétrés pour le impressions de
		 * cette classe.
		 *
		 * @return array
		 */
		public function modelesOdt() {
			$prefix = 'PCG66'.DS.'Traitementpcg66'.DS;

			$items = $this->find(
				'all',
				array(
					'fields' => array(
						'( \''.$prefix.'\' || "'.$this->alias.'"."modeleodt" || \'.odt\' ) AS "'.$this->alias.'__modele"',
					),
					'conditions' => array( ''.$this->alias.'.modeleodt IS NOT NULL' ),
					'recursive' => -1
				)
			);
			return Set::extract( $items, '/'.$this->alias.'/modele' );
		}
	}
?>
