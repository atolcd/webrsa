<?php
	/**
	 * Code source de l'interface ISearch et de la classe AbstractSearch.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model.Abstractclass
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * Interface pour les classes de moteurs de recherche.
	 *
	 * @package app.Model.Abstractclass
	 */
	interface ISearch
	{
		/**
		 * Retourne le querydata pour le moteur de recherche.
		 *
		 * @param array $types Le nom du modèle => le type de jointure
		 * @return array
		 */
		public function searchQuery( array $types = array() );

		/**
		 * Complète les conditions du querydata avec le contenu des filtres de
		 * recherche.
		 *
		 * @param array $query
		 * @param array $search
		 * @return array
		 */
		public function searchConditions( array $query, array $search );

		/**
		 * Moteur de recherche de base.
		 *
		 * @return array
		 */
		public function search( array $search = array() );

		/**
		 * Retourne les options nécessaires au formulaire de recherche, aux
		 * impressions, ...
		 *
		 * @param array $params
		 * @return array
		 */
		 public function options( array $params = array() );
	}

	/**
	 * La classe AbstractSearch contient des méthodes de base à utiliser dans les
	 * classes de moteurs de recherche.
	 *
	 * @package app.Model.Abstractclass
	 */
	abstract class AbstractSearch extends AppModel implements ISearch
	{
		/**
		 * Moteur de recherche de base.
		 *
		 * @param array $search Les valeurs du filtre de recherche
		 * @param array $types Les types de jointure alias => type
		 * @return array
		 */
		public function search( array $search = array(), array $types = array() ) {
			$query = $this->searchQuery( $types );

			$query = $this->searchConditions( $query, $search );

			return $query;
		}

		/**
		 * Exécute les différentes méthods du modèle permettant la mise en cache.
		 * Utilisé au préchargement de l'application (/prechargements/index).
		 *
		 * @return boolean true en cas de succès, false en cas d'erreur,
		 * 	null pour les méthodes qui ne font rien.
		 */
		public function prechargement() {
			$query = $this->searchQuery();
			return !empty( $query );
		}
	}
?>