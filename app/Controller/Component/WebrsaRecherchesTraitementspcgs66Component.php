<?php
	/**
	 * Code source de la classe WebrsaRecherchesTraitementspcgs66Component.
	 *
	 * @package app.Controller.Component
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'WebrsaAbstractRecherchesComponent', 'Controller/Component' );

	/**
	 * La classe WebrsaRecherchesTraitementspcgs66Component ...
	 *
	 * @package app.Controller.Component
	 */
	class WebrsaRecherchesTraitementspcgs66Component extends WebrsaAbstractRecherchesComponent
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
		 * Retourne les options de type "enum", c'est à dire liées aux schémas des
		 * tables de la base de données.
		 *
		 * @return array
		 */
		protected function _optionsEnums( array $params = array() ) {
			$options = parent::_optionsEnums( $params );
			$options['Fichiermodule']['exists'] = array( 'Non', 'Oui' );
			$options['Dossier']['locked'] = array( 1 => '<img src="/img/icons/lock.png" alt="" title="Dossier verrouillé">' );

			return $options;
		}

		/**
		 * Retourne les options stockées liées à des enregistrements en base de
		 * données, ne dépendant pas de l'utilisateur connecté.
		 *
		 * @return array
		 */
		protected function _optionsRecords( array $params = array() ) {
			$Controller = $this->_Collection->getController();
			
			if( !isset( $Controller->Traitementpcg66 ) ) {
				$Controller->loadModel( 'Traitementpcg66' );
			}
			
			$options = parent::_optionsRecords($params);
			$options['Dossierpcg66']['poledossierpcg66_id'] = $Controller->Traitementpcg66->Personnepcg66->Dossierpcg66->User->Poledossierpcg66->find(
				'list', 
				array(
                    'conditions' => array('Poledossierpcg66.isactif' => '1'),
                    'order' => array('Poledossierpcg66.name ASC', 'Poledossierpcg66.id ASC')
				)
			);
			$options['Dossierpcg66']['user_id'] = $Controller->Traitementpcg66->Personnepcg66->Dossierpcg66->User->find(
				'list', 
				array(
                    'fields' => array('User.nom_complet'),
                    'conditions' => array('User.isgestionnaire' => 'O'),
                    'order' => array('User.nom ASC', 'User.prenom ASC')
				)
			);
			$options['Traitementpcg66']['situationpdo_id'] = $Controller->Traitementpcg66->Personnepcg66->Situationpdo->find(
				'list', 
				array(
					'order' => array('Situationpdo.libelle ASC'), 
					'conditions' => array('Situationpdo.isactif' => '1')
				)
			);
			$options['Traitementpcg66']['statutpdo_id'] = $Controller->Traitementpcg66->Personnepcg66->Statutpdo->find(
				'list', 
				array(
					'order' => array('Statutpdo.libelle ASC'), 
					'conditions' => array('Statutpdo.isactif' => '1')
				)
			);
			$options['Traitementpcg66']['descriptionpdo_id'] = $Controller->Traitementpcg66->Descriptionpdo->find('list');
			
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
					'Poledossierpcg66',
					'User',
					'Situationpdo',
					'Statutpdo',
					'Descriptionpdo',
				)
			);

			return $result;
		}
	}
?>
