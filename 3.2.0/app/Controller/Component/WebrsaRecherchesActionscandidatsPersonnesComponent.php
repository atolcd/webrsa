<?php
	/**
	 * Code source de la classe WebrsaRecherchesActionscandidatsPersonnesComponent.
	 *
	 * @package app.Controller.Component
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'WebrsaAbstractRecherchesComponent', 'Controller/Component' );

	/**
	 * La classe WebrsaRecherchesActionscandidatsPersonnesComponent ...
	 *
	 * @package app.Controller.Component
	 */
	class WebrsaRecherchesActionscandidatsPersonnesComponent extends WebrsaAbstractRecherchesComponent
	{
		/**
		 * Components utilisés par ce component.
		 *
		 * @var array
		 */
		public $components = array(
			'Allocataires',
			'Gestionzonesgeos'
		);
		
		/**
		 * Retourne les options stockées liées à des enregistrements en base de
		 * données, ne dépendant pas de l'utilisateur connecté.
		 *
		 * @return array
		 */
		protected function _optionsRecords( array $params = array() ) {
			$Controller = $this->_Collection->getController();
			if( !isset( $Controller->{'ActioncandidatPersonne'} ) ) {
				$Controller->loadModel( 'ActioncandidatPersonne' );
			}
			
			$options = parent::_optionsRecords( $params );
			
			$options['ActioncandidatPersonne']['referent_id'] = $Controller->ActioncandidatPersonne->Referent->find( 'list', array( 'recursive' => -1, 'order' => array( 'nom', 'prenom' ) ) );
			$options['Contactpartenaire']['partenaire_id'] = $Controller->ActioncandidatPersonne->Actioncandidat->Partenaire->find( 'list', array( 'fields' => array( 'libstruc' ), 'order' => array( 'Partenaire.libstruc ASC' ) ) );
			$options['ActioncandidatPersonne']['actioncandidat_id'] = $Controller->ActioncandidatPersonne->Actioncandidat->listActionParPartenaire();
			$options['ActioncandidatPersonne']['motifsortie_id'] = $Controller->ActioncandidatPersonne->Motifsortie->find( 'list', array( 'order' => array( 'Motifsortie.name ASC') ) );

			return $options;
		}

		/**
		 * Retourne les noms des modèles dont des enregistrements seront mis en
		 * cache après l'appel à la méthode _optionsRecords() afin que la clé de
		 * cache générée par la méthode _options() se trouve associée dans
		 * ModelCache.
		 *
		 * @see _optionsRecords(), _options()
		 *
		 * @return array
		 */
		protected function _optionsRecordsModels( array $params ) {
			$result = array_merge(
				parent::_optionsRecordsModels( $params ),
				array(
					'Referent',
					'Partenaire',
					'Actioncandidat',
					'Motifsortie',
				)
			);

			return $result;
		}
	}
?>
