<?php
	/**
	 * Code source de la classe Covstructurereferente.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::import( 'Model', 'Structurereferente' );

	/**
	 * La classe Covstructurereferente ...
	 *
	 * @package app.Model
	 */
	class Covstructurereferente extends Structurereferente
	{
		public $name = 'Covstructurereferente';
		public $useTable = 'structuresreferentes';

		/**
		 * Exécute les différentes méthods du modèle permettant la mise en cache.
		 * Utilisé au préchargement de l'application (/prechargements/index).
		 *
		 * @return boolean true en cas de succès, false en cas d'erreur,
		 * 	null pour les fonctions vides.
		 */
		public function prechargement() {
			return null;
		}
	}
?>