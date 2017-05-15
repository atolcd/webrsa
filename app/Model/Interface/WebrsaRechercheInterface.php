<?php
	/**
	 * Code source de l'interface WebrsaRechercheInterface.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model.Interface
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	/**
	 * L'interface WebrsaRechercheInterface concerne les moteurs de recherche
	 * "standardisés" de web-rsa 3.0.0.
	 *
	 * @package app.Model.Interface
	 */
	// TODO: $baseModelName
	interface WebrsaRechercheInterface
	{
		/**
		 * Retourne le querydata du moteur de recherche avec les conditions de base.
		 * Comme il ne dépend que de variables stables (département, ...), il peut
		 * être mis en cache.
		 *
		 * @param array $types Les types de jointure à utiliser sous la forme
		 *	alias => type
		 * @return array
		 */
		public function searchQuery( array $types = array()/*, $baseModelName = 'Personne'*/ );

		/**
		 * Applique les filtres du moteur de recherche au querydata de base
		 *
		 * @param array $query Le querydata de base
		 * @param array $search Les filtres du moteur de recherche
		 * @return array
		 */
		public function searchConditions( array $query, array $search );

		/**
		 * Retourne le querydata à utiliser dans le moteur de recherche à partir
		 * des valeurs des filtres du moteur de recherche et du département.
		 *
		 * @return array
		 */
		public function search( array $search/*, $baseModelName = 'Personne'*/ );

		/**
		 * Exécute les méthodes pouvant mettre des données en cache (searchQuery,
		 * ...) afin de pouvoir le précharger avant utilisation.
		 *
		 * @return boolean true en cas de succès, false en cas d'erreur,
		 * 	null pour les fonctions vides.
		 */
		public function prechargement();

		/**
		 * Vérification que les champs spécifiés dans le paramétrage par les clés
		 * XXXX.fields, XXXX.innerTable et XXXX.exportcsv dans le webrsa.inc existent
		 * bien dans la requête de recherche renvoyée par la méthode searchQuery().
		 *
		 * @see $keysRecherche
		 *
		 * @param array $params Paramètres supplémentaires (clé 'query' possible)
		 * @return array
		 * @todo Utiliser AbstractWebrsaRecherche
		 */
		public function checkParametrage( array $params = array() );
	}
?>