<?php
	/**
	 * Code source de la classe Foyerpiecejointe.
	 *
	 * PHP 7.2
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
    App::uses( 'AppModel', 'Model' );

    /**
	 * La classe Foyerpiecejointe ...
	 *
	 * @package app.Model
	 */
	class Foyerpiecejointe extends AppModel
	{
		public $name = 'Foyerpiecejointe';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
        public $recursive = 1;

        public $useTable = 'foyerspiecesjointes';

        /**
		 * Associations "Has One".
		 *
		 * @var array
		 */
		public $belongsTo = array(
			'Fichiermodule' => array(
				'className' => 'Fichiermodule',
				'foreignKey' => 'fichiermodule_id',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
			'User' => array(
				'className' => 'User',
				'foreignKey' => 'user_id',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
			'Foyer' => array(
				'className' => 'Foyer',
				'foreignKey' => 'foyer_id',
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
			'Categoriepiecejointe' => array(
				'className' => 'Categoriepiecejointe',
				'foreignKey' => 'categorie_id',
				'conditions' => array('Categoriepiecejointe.actif' => 1),
				'fields' => '',
				'order' => '',
				'limit' => '',
				'offset' => '',
				'exclusive' => '',
				'finderQuery' => '',
				'counterQuery' => ''
			),
        );


    }