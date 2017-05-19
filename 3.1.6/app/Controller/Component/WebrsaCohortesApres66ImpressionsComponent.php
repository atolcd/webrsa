<?php
	/**
	 * Code source de la classe WebrsaCohortesApres66ImpressionsComponent.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller.Component
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'WebrsaAbstractCohortesImpressionsComponent', 'Controller/Component' );

	/**
	 * La classe WebrsaCohortesApres66ImpressionsComponent ...
	 *
	 * @package app.Controller.Component
	 */
	class WebrsaCohortesApres66ImpressionsComponent extends WebrsaAbstractCohortesImpressionsComponent
	{
		/**
		 * Components utilisés par ce component
		 *
		 * @var array
		 */
		public $components = array( 'Allocataires', 'Gedooo.Gedooo', 'WebrsaCohortesApres66' );
		
		/**
		 * Retourne les options de type "enum", c'est à dire liées aux schémas des
		 * tables de la base de données.
		 *
		 * La mise en cache se fera dans ma méthode _options().
		 *
		 * @return array
		 */
		protected function _optionsEnums( array $params ) {
			return $this->WebrsaCohortesApres66->{__FUNCTION__}($params);
		}

		/**
		 * Retourne les options stockées liées à des enregistrements en base de
		 * données, ne dépendant pas de l'utilisateur connecté.
		 *
		 * La mise en cache se fera dans ma méthode _options().
		 *
		 *
		 * @return array
		 */
		protected function _optionsRecords( array $params ) {
			return $this->WebrsaCohortesApres66->{__FUNCTION__}($params);
		}

		/**
		 * Retourne les noms des modèles dont des enregistrements seront mis en
		 * cache après l'appel à la méthode _optionsRecords() afin que la clé de
		 * cache générée par la méthode _options() se trouve associée dans
		 * ModelCache.
		 *
		 * @return array
		 */
		protected function _optionsRecordsModels( array $params ) {
			return $this->WebrsaCohortesApres66->{__FUNCTION__}($params);
		}

		/**
		 * Modifie la requête pour ramener la clé primaire de l'enregistrement,
		 * le document PDF et le chemin cmspath dans les résultats.
		 *
		 * @param array $query
		 * @param array $params
		 * @return array
		 */
		protected function _queryPdfs( array $query, array $params ) {
			$Controller = $this->_Collection->getController();
			
			// Champs nécessaires
			$query['fields'] = array(
				'Apre66.id',
			);
			
			return $query;
		}

		/**
		 * Retourne un array de PDF, sous la clé $params['documentPath'] (par défaut : Pdf.document) 
		 * à partir du query, ou le nombre de documents n'ayant pas pu être imprimés.
		 *
		 * @param array $query
		 * @param array $params
		 * @return integer|array
		 */
		protected function _pdfs( array $query, array $params ) {
			$Controller = $this->_Collection->getController();
			
			$query = $this->_queryPdfs( $query, $params );

			$Controller->{$params['modelName']}->forceVirtualFields = true;
			$datas = $Controller->{$params['modelName']}->find( 'all', $query );
			
			$results = array();
			foreach (Hash::extract($datas, '{n}.Apre66.id') as $apre66_id) {
				$results[] = array(
					'Pdf' => array(
						'document' => $Controller->{$params['modelName']}->WebrsaApre66->getNotificationAprePdf( $apre66_id, false )
					),
					'Apre66' => array(
						'id' => $apre66_id
					)
				);
			}
			
			return $results;
		}
		
		/**
		 * Post-traitement des résultats de la requête (par exemple pour la mise
		 * à jour d'une date d'impression).
		 * Cette fonction doit retourner vrai pour que l'envoi se fasse.
		 *
		 * @param array $results - Contenu du retour de la fonction _pdfs()
		 * @param array $params
		 * @return boolean
		 */
		protected function _postProcess( array $results, array $params ) {
			$this->_Collection->getController()->{$params['modelName']}->updateAllUnBound(
				array( 'Apre66.datenotifapre' => date( "'Y-m-d'" ) ),
				array(
					'"Apre66"."id"' => Hash::extract($results, '{n}.Apre66.id'),
					'"Apre66"."datenotifapre" IS NULL'
				)
			);
		 
			return parent::_postProcess($results, $params);
		}
	}
?>