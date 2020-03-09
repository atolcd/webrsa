<?php
	/**
	 * Code source de la classe Configurationcatégorie.
	 *
	 * PHP 7.2
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppModel', 'Model' );

	/**
	 * La classe Configuration ...
	 *
	 * @package app.Model
	 */
	class ConfigurationCategorie extends AppModel
	{
		public $name = 'ConfigurationCategorie';

		/**
		 * Récursivité par défaut du modèle.
		 *
		 * @var integer
		 */
		public $recursive = 1;

		/**
		 * Ce model utilise cette table de la base de données
		 *
		 * @var string
		 */
		public $useTable = 'configurationscategories';

		/**
		 * Behaviors utilisés par le modèle.
		 *
		 * @var array
		 */
		public $actsAs = array(
			'Validation2.Validation2Formattable',
			'Validation2.Validation2RulesFieldtypes',
			'Postgres.PostgresAutovalidate'
		);

		/*
		 *  Insère en BDD les noms de fichier $nomCategorie dans categorie
		 *  @param string $nomCategorie
		 *
		 *  @return int
		*/
 		public function setCategorie($nomCategorie) {
			// Récupération de l'id de la catégorie, ou création si besoin
			$id = $this->field('id', array(
				'ConfigurationCategorie.lib_categorie LIKE' => $nomCategorie)
			);
			if($id === false)
			{
				$query['lib_categorie'] = $nomCategorie;
				$this->clear();
				$this->set($query);
				$this->save();
				$id = $this->id;
			}
			return $id;
		}

		/*
		 *  Récupère en BDD l'ID de $nomCategorie dans categorie
		 *  @param string $nomCategorie
		 *
		 *  @return int
		*/
		public function getCategorie($nomCategorie) {
			$id = $this->field('id', array(
				'ConfigurationCategorie.lib_categorie LIKE' => $nomCategorie)
			);

			return $id;
		}
	}