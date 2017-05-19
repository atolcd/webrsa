<?php
	/**
	 * Code source de la classe WebrsaCohortesDossierspcgs66Component.
	 *
	 * @package app.Controller.Component
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'WebrsaAbstractCohortesComponent', 'Controller/Component' );

	/**
	 * La classe WebrsaCohortesDossierspcgs66Component ...
	 *
	 * @package app.Controller.Component
	 */
	class WebrsaCohortesDossierspcgs66Component extends WebrsaAbstractCohortesComponent
	{
		/**
		 * Permet de récupérer les options dans le controller
		 *
		 * @param array $params
		 * @return type
		 */
		public function getOptions( array $params = array() ) {
			return $this->_options($this->_params($params));
		}

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
			$Controller->loadModel('Tag');
			$Controller->loadModel('WebrsaOptionTag');

			$options = $Controller->WebrsaOptionTag->optionsEnums( parent::_optionsEnums( $params ) );

			$options['Dossierpcg66']['orgpayeur'] = array('CAF'=>'CAF', 'MSA'=>'MSA');
			$options['Dossierpcg66']['haspiecejointe'] = array(0 => 'Non', 1 => 'Oui');
			$options['Dossierpcg66']['create'] = array(0 => 'Non', 1 => 'Oui');

			$options = array_merge(
				$options,
				$Controller->Tag->EntiteTag->Foyer->Dossierpcg66->Personnepcg66->Traitementpcg66->enums()
			);

			$options['Cohorte'] = $options;

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

			if( !isset( $Controller->Dossierpcg66 ) ) {
				$Controller->loadModel( 'Dossierpcg66' );
			}
			$options['Dossierpcg66']['typepdo_id'] = $Controller->Dossierpcg66->Typepdo->find( 'list' );
			$options['Dossierpcg66']['originepdo_id'] = $Controller->Dossierpcg66->Originepdo->find( 'list' );
			$options['Dossierpcg66']['serviceinstructeur_id'] = $Controller->Dossierpcg66->Serviceinstructeur->listOptions();
			$options['Traitementpcg66']['serviceinstructeur_id'] = $options['Dossierpcg66']['serviceinstructeur_id'];

			// Concernant les pôles et les gestionnaires: il y a du traitement
			// et pas de filtre uniquement dans la gestion de liste des dossiers
			// PCGs en attente d'affectation.
			$traitement = '/dossierspcgs66/cohorte_enattenteaffectation'
				=== "/{$Controller->request->params['controller']}/{$Controller->request->params['action']}";
			$options['Dossierpcg66']['poledossierpcg66_id'] = $Controller->Dossierpcg66->User->Poledossierpcg66->WebrsaPoledossierpcg66->polesdossierspcgs66($traitement);
			$options['Dossierpcg66']['user_id'] = $Controller->Dossierpcg66->User->WebrsaUser->gestionnaires($traitement);
			$options['Dossierpcg66']['dependent_user_id'] = $Controller->Dossierpcg66->User->WebrsaUser->gestionnaires($traitement, true);


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

			$options['Decdospcg66Orgdospcg66']['orgtransmisdossierpcg66_id'] = $Controller->Dossierpcg66->Decisiondossierpcg66->Orgtransmisdossierpcg66->findForTraitement( 'list' );

			$options['Cohorte'] = $options;

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

			$result = array_merge(
				$Controller->WebrsaOptionTag->optionsRecordsModels( parent::_optionsRecordsModels( $params ) ),
				array(
					'Dossierpcg66',
					'Serviceinstructeur',
					'Originepdo',
					'Typepdo',
					'User',
					'Poledossierpcg66',
					'Situationpdo',
					'Statutpdo',
					'Typecourrierpcg66',
					'Decdospcg66Orgdospcg66'
				)
			);

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

		/**
		 * Permet de récupérer les cohorteFields du modele de recherche et de lui appliquer les valeurs par défaut
		 *
		 * @param array $params
		 * @return array
		 */
		protected function _getCohorteFields( array $params = array() ) {
			$Controller = $this->_Collection->getController();
			$fields = parent::_getCohorteFields($params);
			$options = $this->_options($params);

			switch ($Controller->action) {
				case 'cohorte_enattenteaffectation':
					$fields['Dossierpcg66.user_id']['options'] = Hash::get($options, 'Dossierpcg66.dependent_user_id');
					break;
				case 'cohorte_rsamajore':
				case 'cohorte_heberge':
					$user_id = $Controller->Session->read('Auth.User.id');
					$user_id = empty( $user_id ) && 'cli' === php_sapi_name()
						? null
						: $user_id;

					$query = array(
						'fields' => array('Poledossierpcg66.name'),
						'joins' => array($Controller->Dossierpcg66->User->join('Poledossierpcg66', array('type' => 'INNER'))),
						'conditions' => array('User.id' => $user_id),
						'contain' => false
					);
					$pole = Hash::get($Controller->Dossierpcg66->User->find('first', $query), 'Poledossierpcg66.name');
					$fields['Dossierpcg66.user_id']['value'] = $pole === 'PDU'
						? $user_id
						: null;
					$fields['Tag.limite']['minYear'] = date('Y');
					$fields['Tag.limite']['maxYear'] = date('Y') +4;
					break;
			}

			$keyConf = implode('.', array($params['searchKeyPrefix'], $Controller->name, $Controller->action, 'cohorte', 'options'));
			foreach ((array)Configure::read($keyConf) as $fieldName => $fieldOptions) {
				$fields[$fieldName]['options'] = $fieldOptions;
			}

			return $fields;
		}
	}
?>