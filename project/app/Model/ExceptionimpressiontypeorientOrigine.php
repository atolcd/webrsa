<?php
	/**
	 * Code source de la classe ExceptionimpressiontypeorientOrigine.
	 *
	 * PHP 7.2
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe ExceptionimpressiontypeorientOrigine ...
	 *
	 * @package app.Model
	 */
	class ExceptionimpressiontypeorientOrigine extends AppModel
	{
		public $name = 'ExceptionimpressiontypeorientOrigine';

		/**
		 * Ce model utilise cette table de la base de données
		 *
		 * @var string
		 */
		public $useTable = 'exceptionsimpressionstypesorients_origines';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		public $actsAs = array(
			'Postgres.PostgresAutovalidate',
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes'
		);

		/**
		 * Associations "Belongs to".
		 *
		 * @var array
		 */
		public $belongsTo = array(
			'Exceptionimpressiontypeorient' => array(
				'className' => 'Exceptionimpressiontypeorient',
				'foreignKey' => 'excepimprtypeorient_id',
				'conditions' => null,
				'type' => null,
				'fields' => null,
				'order' => null,
				'counterCache' => null
			)
		);

		public function getOriginesParExceptions($id_exception){
			$origines = $this->findAllByExcepimprtypeorientId($id_exception, ['origine']);
			$liste_origines = '';
			if (!empty($origines)){
				foreach ($origines as $key => $origine){
					$liste_origines .= __d('orientstruct','ENUM::ORIGINE::'.($origines[$key]['ExceptionimpressiontypeorientOrigine']['origine']));
					if($key != sizeof($origines)-1) {
						$liste_origines .= " <br> ";
					}
				}
			}

			return $liste_origines;
		}
		public function getOriginesParExceptionsTableau($id_exception){
			$origines = $this->findAllByExcepimprtypeorientId($id_exception, ['origine']);
			$liste_origines = [];
			if (!empty($origines)){
				foreach ($origines as $key => $origine){
					$liste_origines[] = $origines[$key]['ExceptionimpressiontypeorientOrigine']['origine'];
				}
			}

			return $liste_origines;
		}
	}