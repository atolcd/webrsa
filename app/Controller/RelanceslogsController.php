<?php
	/**
	 * Code source de la classe RelanceslogsController.
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaParametragesController', 'Controller' );

	/**
	 * La classe RelanceslogsController ...
	 *
	 * @package app.Controller
	 */
	class RelanceslogsController extends AbstractWebrsaParametragesController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Relanceslogs';

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array(
			'Relancelog',
		);

		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array(
			'InsertionsBeneficiaires',
			'Search.SearchPrg' => array(
				'actions' => array(
					'index',
				)
			),
			'WebrsaParametrages'
		);

		/**
		 * Liste des tags.
		 *
		 */
		public function index() {
			$search = (array)Hash::get( $this->request->data, 'Search' );
			if( !empty( $search ) ) {
				$dateRDV = $search["Relanceslogs"]["daterdv"]["year"].'-'.$search["Relanceslogs"]["daterdv"]["month"].'-'.$search["Relanceslogs"]["daterdv"]["day"];
				$contitions	= array('Relancelog.daterdv = \''.$dateRDV.'\'');
				$query = array(
					'order' => 'Relancelog.id',
					'conditions' => $contitions
				);
				$query['limit'] = 100;
				
				$this->paginate = $query;
				$results = $this->paginate( 'Relancelog', array(), array(), !Hash::get($search, 'Pagination.nombre_total') );
				$this->set( compact( 'results' ) );
			}
		}
	}
?>