<?php
	/**
	 * Code source de la classe WebrsaRecherchesApresEligibiliteComponent.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller.Component
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'WebrsaRecherchesApresComponent', 'Controller/Component' );

	/**
	 * La classe WebrsaRecherchesApresEligibiliteComponent ...
	 *
	 * @package app.Controller.Component
	 */
	class WebrsaRecherchesApresEligibiliteComponent extends WebrsaRecherchesApresComponent
	{
		/**
		 * Stocke les anciennes valeurs des attributs deepAfterFind des différents
		 * modèles.
		 *
		 * @var array
		 */
		protected $_deepAfterFind = array();

		/**
		 * Stocke les anciennes valeurs et modifie les valeurs des attributs
		 * deepAfterFind des modèles Apre et Relanceapre à false avant la requête.
		 *
		 * @param array $params Les paramètres de la recherche
		 * @param array $query Le querydata à compléter
		 * @return array
		 */
		public function beforeSearch( array $params, array $query ) {
			$Controller = $this->_Collection->getController();

			$this->_deepAfterFind['Apre'] = $Controller->{$params['modelName']}->deepAfterFind;
			$this->_deepAfterFind['Relanceapre'] = $Controller->{$params['modelName']}->Relanceapre->deepAfterFind;

			$Controller->{$params['modelName']}->deepAfterFind = false;
			$Controller->{$params['modelName']}->Relanceapre->deepAfterFind = false;

			return $query;
		}

		/**
		 * Modifie les valeurs des attributs deepAfterFind des modèles Apre et
		 * Relanceapre à leur ancienne valeur après la requête.
		 *
		 * @param array $params Les paramètres de la recherche
		 * @param array $results Les enregistrements à compléter
		 * @return array
		 */
		public function afterSearch( array $params, array $results ) {
			$Controller = $this->_Collection->getController();

			$Controller->{$params['modelName']}->deepAfterFind = $this->_deepAfterFind[$params['modelName']];
			$Controller->{$params['modelName']}->Relanceapre->deepAfterFind = $this->_deepAfterFind['Relanceapre'];

			return $results;
		}
	}
?>