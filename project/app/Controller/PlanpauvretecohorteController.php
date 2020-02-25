<?php
	/**
	 * Code source de la classe...
	 *
	 * PHP 7.2
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AppController', 'Controller' );

	/**
	 * La classe...
	 *
	 * @package app.Controller
	 */
	class PlanpauvretecohorteController extends AppController
	{
		/**
		 * Corrige le nombre total de la pagination classique dû au distinct dans la requête
		 *
		 * @param type $object
		 * @param type $scope
		 * @param type $whitelist
		 * @param type $progressivePaginate
		 * @return type
		 */
		public function paginate( $object = null, $scope = array( ), $whitelist = array( ), $progressivePaginate = null ) {
			$paginate = parent::paginate( $object, $scope, $whitelist, $progressivePaginate );

			$Search__Pagination__nombre_total = Hash::get($this->request->params, 'named.Search__Pagination__nombre_total');
			$count = Hash::get($this->request->params, 'paging.Personne.count');
			$limit = Hash::get($this->request->params, 'paging.Personne.limit');

			if ($Search__Pagination__nombre_total === '1' && $count > $limit) {
				$query = $this->paginate['Personne'];
				$query['fields'] = array ('COUNT(DISTINCT "Personne"."id") AS count');
				unset ($query['order']);

				$this->loadModel('Personne');
				$results = $this->Personne->find ('first', $query);
				$this->request->params['paging']['Personne']['count'] = $results[0]['count'];
			}

			return $paginate;
		}
	}