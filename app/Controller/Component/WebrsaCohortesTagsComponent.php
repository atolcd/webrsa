<?php
	/**
	 * Code source de la classe WebrsaCohortesTagsComponent.
	 *
	 * @package app.Controller.Component
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'WebrsaAbstractCohortesComponent', 'Controller/Component' );

	/**
	 * La classe WebrsaCohortesTagsComponent ...
	 *
	 * @package app.Controller.Component
	 */
	class WebrsaCohortesTagsComponent extends WebrsaAbstractCohortesComponent
	{
		/**
		 * Retourne les options de type "enum", c'est à dire liées aux schémas des
		 * tables de la base de données.
		 *
		 * La mise en cache se fera dans ma méthode _options().
		 *
		 * @return array
		 */
		protected function _optionsEnums( array $params ) {
			$Controller = $this->_Collection->getController();
			$Controller->loadModel('WebrsaOptionTag');

			$options = $Controller->WebrsaOptionTag->optionsEnums( parent::_optionsEnums( $params ) );

			if ( (integer)Configure::read('Cg.departement') === 66 ) {
				$options['Dossierpcg66']['orgpayeur'] = array('CAF'=>'CAF', 'MSA'=>'MSA');
				$options['Dossierpcg66']['haspiecejointe'] = array(0 => 'Non', 1 => 'Oui');

				$options = array_merge(
					$options,
					$Controller->Tag->EntiteTag->Foyer->Dossierpcg66->Personnepcg66->Traitementpcg66->enums()
				);
			}

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
			$Controller->loadModel('WebrsaOptionTag');

			$options = $Controller->WebrsaOptionTag->optionsRecords( parent::_optionsRecords( $params ) );

			// Dossier PCG
			if ( (integer)Configure::read('Cg.departement') === 66 ) {
				if( !isset( $Controller->Dossierpcg66 ) ) {
					$Controller->loadModel( 'Dossierpcg66' );
				}
				$options['Dossierpcg66']['typepdo_id'] = $Controller->Dossierpcg66->Typepdo->find( 'list' );
				$options['Dossierpcg66']['originepdo_id'] = $Controller->Dossierpcg66->Originepdo->find( 'list' );
				$options['Dossierpcg66']['serviceinstructeur_id'] = $Controller->Dossierpcg66->Serviceinstructeur->listOptions();
				$options['Traitementpcg66']['serviceinstructeur_id'] = $options['Dossierpcg66']['serviceinstructeur_id'];

				 $gestionnaires = $Controller->Dossierpcg66->User->find(
					'all',
					array(
						'fields' => array(
							'User.nom_complet',
							'( "User"."id" ) AS "User__gestionnaire"',
						),
						'conditions' => array(
							'User.isgestionnaire' => 'O'
						),
						'joins' => array(
							$Controller->Dossierpcg66->User->join( 'Poledossierpcg66', array( 'type' => 'INNER' ) ),
						),
						'order' => array( 'User.nom ASC', 'User.prenom ASC' ),
						'contain' => false
					)
				);
				$options['Dossierpcg66']['user_id'] = Hash::combine( $gestionnaires, '{n}.User.gestionnaire', '{n}.User.nom_complet' );

				$options['Dossierpcg66']['poledossierpcg66_id'] = $Controller->Dossierpcg66->User->Poledossierpcg66->WebrsaPoledossierpcg66->polesdossierspcgs66();

				$options['Situationpdo']['Situationpdo'] = $Controller->Dossierpcg66->Personnepcg66->Situationpdo->findForTraitement( 'list' );
				$options['Traitementpcg66']['situationpdo_id'] = $options['Situationpdo']['Situationpdo'];

				$options['Statutpdo']['Statutpdo'] = $Controller->Dossierpcg66->Personnepcg66->Statutpdo->findForTraitement( 'list' );

				$options['Traitementpcg66']['typecourrierpcg66_id'] = $Controller->Dossierpcg66->Personnepcg66->Traitementpcg66->Typecourrierpcg66->find(
					'list', array(
						'fields' => array(
							'Typecourrierpcg66.name'
						),
						'conditions' => array(
							'Typecourrierpcg66.isactif' => '1'
						)
					)
				);

				$options['Traitementpcg66']['descriptionpdo_id'] = $Controller->Dossierpcg66->Personnepcg66->Traitementpcg66->Descriptionpdo->find('list');
			}

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
			$Controller = $this->_Collection->getController();
			$Controller->loadModel('WebrsaOptionTag');

			$result = $Controller->WebrsaOptionTag->optionsRecordsModels( parent::_optionsRecordsModels( $params ) );

			return $result;
		}

		/**
		 * Permet de filtrer les options envoyées à la vue au moyen de la clé
		 * 'filters.accepted' dans le fichier de configuration.
		 *
		 * @param array $options
		 * @param array $params
		 * @return array
		 */
		protected function _optionsAccepted( array $options, array $params ) {
			$Controller = $this->_Collection->getController();
			$WebrsaRequestsmanager = $Controller->Components->load('WebrsaRequestsmanager');

			return $WebrsaRequestsmanager->optionsAccepted( parent::_optionsAccepted($options, $params), $params );
		}

		/**
		 * Retourne un array avec clés de paramètres suivantes complétées en
		 * fonction du contrôleur:
		 *	- modelName: le nom du modèle sur lequel se fera la pagination
		 *	- modelRechercheName: le nom du modèle de moteur de recherche
		 *	- searchKey: le préfixe des filtres renvoyés par le moteur de recherche
		 *	- searchKeyPrefix: le préfixe des champs configurés
		 *	- configurableQueryFieldsKey: les clés de configuration contenant les
		 *    champs à sélectionner dans la base de données.
		 *  - auto: la recherche doit-elle être lancée (avec les valeurs par défaut
		 *    des filtres de recherche) automatiquement au premier accès à la page,
		 *    lors de l'appel à une méthode search() ou cohorte(). Configurable
		 *    avec Configure::write( 'ConfigurableQuery.<Controller>.<action>.query.auto' )
		 *
		 * @param array $params
		 * @return array
		 */
		protected function _params( array $params = array() ) {
			$Controller = $this->_Collection->getController();
			$WebrsaRequestsmanager = $Controller->Components->load('WebrsaRequestsmanager');

			return $WebrsaRequestsmanager->params( parent::_params($params) );
		}

		/**
		 * Surcharge de _queryConditions permettant de modifier la configuration dans le cas d'une utilisation du Requestmanager
		 *
		 * @param array $query
		 * @param array $filters
		 * @param array $params
		 * @return type
		 */
		protected function _queryConditions( array $query, array $filters, array $params ) {
			$Controller = $this->_Collection->getController();
			$WebrsaRequestsmanager = $Controller->Components->load('WebrsaRequestsmanager');

			return $WebrsaRequestsmanager->queryConditions( parent::_queryConditions($query, $filters, $params), $filters, $params );
		}
	}
?>