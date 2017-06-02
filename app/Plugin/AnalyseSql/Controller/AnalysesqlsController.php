<?php
	/**
	 * Code source de la classe Analysesqls.
	 *
	 * @package AnalyseSql
	 * @subpackage app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses('AppController', 'Controller');
	App::uses('Analysesql', 'AnalyseSql.Utility');

	/**
	 * La classe Analysesqls permet de traiter une requête au format SQL afin de la décortiquer et de la rendre plus compréhensible pour un humain.
	 *
	 * @package AnalyseSql
	 * @subpackage app.Controller
	 */
	class AnalysesqlsController extends AppController
	{
		/**
		 * Nom du contrôleur.
		 *
		 * @var string
		 */
		public $name = 'Analysesqls';

		/**
		 * Modèles utilisés.
		 *
		 * @var array
		 */
		public $uses = array();
		
		/**
		 * Components utilisés.
		 *
		 * @var array
		 */
		public $components = array();

		/**
		 * Helpers utilisés.
		 *
		 * @var array
		 */
		public $helpers = array();

		/**
		 * Correspondances entre les méthodes publiques correspondant à des
		 * actions accessibles par URL et le type d'action CRUD.
		 *
		 * @var array
		 */
		public $crudMap = array('ajax_analyse' => 'read');
		
		/**
		 * Ne pas vérifier les droits sur ajax_analyse()
		 * 
		 * @var array 
		 */
		public $aucunDroit = array('ajax_analyse');
		
		/**
		 * On force le passage à l'AJAX car en cas de lag, renvoi une érreur 403.
		 *
		 * @param CakeRequest $request Request object for this controller. Can be null for testing,
		 *  but expect that features that use the request parameters will not work.
		 * @param CakeResponse $response Response object for this controller.
		 * @codeCoverageIgnore
		 */
		public function __construct( $request = null, $response = null ) {
			parent::__construct($request, $response);
			$this->request->params['isAjax'] = true;
		}
		
		/**
		 * Lance l'analyse sur la valeur $this->request->data['sql'] et renvoi un json pour une utilisation en AJAX
		 * 
		 * @codeCoverageIgnore
		 */
		public function ajax_analyse(){
			$json = Analysesql::analyse( $this->request->data['sql'] );
			
			$this->set( compact( 'json' ) );
			$this->layout = 'ajax';
			$this->render( '/Elements/json' );
		}
	}
?>
