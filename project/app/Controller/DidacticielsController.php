<?php
	/**
	 * Code source de la classe DidacticielsController.
	 *
	 * PHP 7.2
	 *
	 * @package app.Controller
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */

	App::uses('AppController', 'Controller');

	/**
	 * Static content controller
	 *
	 * Override this controller by placing a copy in controllers directory of an application
	 *
	 * @package       app.Controller
	 * @link http://book.cakephp.org/2.0/en/controllers/pages-controller.html
	 */
	class DidacticielsController extends AppController {

		/**
		 * Controller name
		 *
		 * @var string
		 */
		public $name = 'Didacticiels';

		/**
		 * This controller does not use a model
		 *
		 * @var array
		 */
		public $uses = array();

		/**
		 * Displays a view
		 *
		 * @param mixed What page to display
		 * @return void
		 */
		public function display() {
			$path = func_get_args();

			$count = count($path);
			if (!$count) {
				$this->redirect('/didacticiels/accueil');
			}
			$page = $subpage = $title_for_layout = null;

			if (!empty($path[0])) {
				$page = $path[0];
			}
			if (!empty($path[1])) {
				$subpage = $path[1];
			}
			if (!empty($path[$count - 1])) {
				$title_for_layout = Inflector::humanize($path[$count - 1]);
			}
			$this->set(compact('page', 'subpage', 'title_for_layout'));

			//ajout Harry
			$this->layout = "didac";
			$this->render(implode('/', $path));
		}
	}
