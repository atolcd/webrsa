<?php
	/**
	 * Code source de la classe AbstractWebrsaCohorte.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'WebrsaRechercheInterface', 'Model/Interface' );

	/**
	 * La classe AbstractWebrsaCohorte ...
	 *
	 * @package app.Model
	 */
	abstract class AbstractWebrsaCohorte extends AppModel implements WebrsaRechercheInterface
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'AbstractWebrsaCohorte';

		/**
		 * On n'utilise pas de table.
		 *
		 * @var mixed
		 */
		public $useTable = false;

		/**
		 * Behaviors utilisés par le modèle.
		 *
		 * @var array
		 */
		public $actsAs = array( 'Conditionnable' );

		/**
		 * Liste des clés de configuration utilisées par le moteur de recherche,
		 * pour vérification du paramétrage.
		 *
		 * @see checkParametrage()
		 *
		 * @var array
		 */
		public $keysRecherche = array();

		/**
		 * Liste des champs de formulaire à inserer dans le tableau de résultats
		 *
		 * @var array
		 */
		public $cohorteFields = array();

		/**
		 * Valeurs par défaut pour le préremplissage des champs du formulaire de cohorte
		 * ex: array(
		 *		'Mymodel' => array( 'Myfield' => 'MyValue' ) )
		 * )
		 *
		 * @var array
		 */
		public $defaultValues = array();

		/**
		 * Préremplissage du formulaire en cohorte
		 *
		 * @param array $results
		 * @param array $params
		 * @param array $options
		 * @return array
		 */
		public function prepareFormDataCohorte( array $results, array $params = array(), array &$options = array() ) {
			$data = array();
			for ($i=0; $i<count($results); $i++) {
				$data[$i] = $results[$i];
				$data[$i] += $this->defaultValues;
			}

			return $data;
		}

		/**
		 * Logique de sauvegarde de la cohorte
		 *
		 * @param type $data
		 * @param type $params
		 * @return boolean
		 */
		public function saveCohorte( array $data, array $params = array(), $user_id = null ) {
			return true;
		}

		/**
		 * Retourne le querydata de base, en fonction du département, à utiliser
		 * dans le moteur de recherche.
		 *
		 * @param array $types Les types de jointure alias => type
		 * @return array
		 */
		public function searchQuery(array $types = array()) {
			// Il est très important d'avoir un Dossier.id pour les cohortes (pour les jetons)
			$query['fields'][] = 'Dossier.id';
			$this->cohorteFields['Dossier.id'] = array( 'type' => 'hidden', 'hidden' => true );

			return $query;
		}

		/**
		 * Retourne un querydata, en fonction du département, prenant en compte
		 * les différents filtres du moteur de recherche.
		 *
		 * @param array $params
		 * @return array
		 */
		public function search( array $search ) {
			$query = $this->searchQuery();
			$query = $this->searchConditions( $query, $search );

			return $query;
		}

		/**
		 * Préchargement de la méthode searchQuery().
		 *
		 * @return boolean true en cas de succès, false en cas d'erreur
		 */
		public function prechargement() {
			$success = true;

			$query = $this->searchQuery();
			$success = !empty( $query ) && $success;

			return $success;
		}

		/**
		 * Vérification que les champs spécifiés dans le paramétrage par les clés
		 * XXXX.fields, XXXX.innerTable et XXXX.exportcsv dans le webrsa.inc existent
		 * bien dans la requête de recherche renvoyée par la méthode searchQuery().
		 *
		 * La clé keys permet de spécifier les clés de configuration contenant
		 * les champs à utiliser dans les moteurs de recherche; par défaut, les
		 * valeurs contenues dans l'attribut $keysRecherche de la classe.
		 *
		 * Si la clé query est vide, alors la méthode search() de la classe sera
		 * appelée avec un array vide en paramètre.
		 *
		 * Export possible des champs disponibles dans des fichiers .csv du
		 * répertoire app/tmp/logs si exportcsv vaut true (par défaut).
		 *
		 * @see $keysRecherche
		 *
		 * @param array $params Paramètres supplémentaires: clés keys, query et
		 *		exportcsv
		 * @return array
		 */
		public function checkParametrage( array $params = array() ) {
			$params += array(
				'keys' => $this->keysRecherche,
				'query' => $this->search( array() ),
				'exportcsv' => false
			);

			// Vérification de l'existence des champs paramétrés dans le querydata
			$return = ConfigurableQueryFields::getErrors( $params['keys'], $params['query'] );

			// Export des champs disponibles
			if( $params['exportcsv'] === true ) {
				$fileName = TMP.DS.'logs'.DS.$this->name.'__searchQuery__cg'.Configure::read( 'Cg.departement' ).'.csv';
				ConfigurableQueryFields::exportQueryFields( $params['query'], Inflector::tableize( $this->name ), $fileName );
			}

			return $return;
		}
	}
?>