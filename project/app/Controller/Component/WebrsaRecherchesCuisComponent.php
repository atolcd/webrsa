<?php
	/**
	 * Code source de la classe WebrsaRecherchesCuisComponent.
	 *
	 * @package app.Controller.Component
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'WebrsaAbstractRecherchesComponent', 'Controller/Component' );

	/**
	 * La classe WebrsaRecherchesCuisComponent ...
	 *
	 * @package app.Controller.Component
	 */
	class WebrsaRecherchesCuisComponent extends WebrsaAbstractRecherchesComponent
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
			$Controller = $this->_Collection->getController();
			$cgDepartement = Configure::read( 'Cg.departement' );
			$modelCuiDpt = 'Cui' . $cgDepartement;
			$options = array();

			if( isset( $Controller->Cui->{$modelCuiDpt} ) ) {
				$options = $Controller->Cui->{$modelCuiDpt}->enums();

				// Liste de modeles potentiel pour un CG donné
				$modelPotentiel = array(
					'Accompagnementcui' . $cgDepartement,
					'Decisioncui' . $cgDepartement,
					'Personnecui' . $cgDepartement,
					'Partenairecui' . $cgDepartement,
					'Propositioncui' . $cgDepartement,
					'Rupturecui' . $cgDepartement,
					'Suspensioncui' . $cgDepartement,
				);

				foreach ( $modelPotentiel as $modelName ){
					if ( isset( $Controller->Cui->{$modelCuiDpt}->{$modelName} ) ){
						$options = Hash::merge( $options, $Controller->Cui->{$modelCuiDpt}->{$modelName}->enums() );
					}
				}
			}

			if( 66 == $cgDepartement ) {
				$options['Propositioncui66']['avis'] = $Controller->Cui->Cui66->Propositioncui66->enum( 'avis' );
			}

			$options['Adressecui']['canton'] = $this->Gestionzonesgeos->listeCantons();

			foreach( $Controller->Cui->beneficiairede as $key => $value ){
				$options['Cui']['beneficiairede'][] = $value;
			}

			$options = Hash::merge(
				$options,
				parent::_optionsEnums( $params ),
				$Controller->Cui->enums(),
				$Controller->Cui->Partenairecui->enums(),
				$Controller->Cui->Personnecui->enums()
			);

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
			$cgDepartement = Configure::read( 'Cg.departement' );
			$modelCuiDpt = 'Cui' . $cgDepartement;
			$typeContrat = 'Typecontratcui' . $cgDepartement;
			$libsec = 'Libsecactderact' . $cgDepartement . 'Secteur';

			$options = parent::_optionsRecords( $params );

			// On vérifi que les tables existent avant de charger les modeles
			$modelList = Hash::normalize(App::objects( 'model' ));
			foreach( array_keys($modelList) as $modelName ) {
				$modelList[$modelName] = true;
			}

			if( isset($modelList[$typeContrat]) && !isset( $Controller->{$typeContrat} ) ) {
				$Controller->loadModel( $typeContrat );
			}

			if( isset($modelList[$libsec]) && !isset( $Controller->{$libsec} ) ) {
				$Controller->loadModel( $libsec );
			}

			if ( isset($modelList[$modelCuiDpt]) && isset( $Controller->Cui->{$modelCuiDpt}) ) {
				$options[$modelCuiDpt]['datebutoir_select'] = array(
					1 => __d( $modelCuiDpt, 'ENUM::DATEBUTOIR_SELECT::1' ),
					2 => __d( $modelCuiDpt, 'ENUM::DATEBUTOIR_SELECT::2' ),
					3 => __d( $modelCuiDpt, 'ENUM::DATEBUTOIR_SELECT::3' ),
				);

				if ( isset($modelList[$typeContrat]) && isset($Controller->{$typeContrat}) ) {
					$options[$modelCuiDpt]['typecontrat'] = $Controller->{$typeContrat}->find( 'list',
						array( 'order' => 'name' )
					);
					$options[$modelCuiDpt]['typecontrat_actif'] = $Controller->{$typeContrat}->find( 'list',
						array( 'conditions' => array( 'actif' => true ), 'order' => 'name' )
					);
				}
			}

			if ( isset($Controller->{$libsec}) && is_object($Controller->{$libsec}) ) {
				$options['Partenairecui'] = array(
					'naf' => $Controller->{$libsec}->find(
						'list',
						array( 'contain' => false, 'order' => array( 'code' ) )
					),
				);
			}

			$communes = $Controller->Cui->Partenairecui->Adressecui->query('SELECT commune AS "Adressecui__commune" FROM adressescuis GROUP BY commune');
			foreach ( $communes as $value ) {
				$commune = Hash::get($value, 'Adressecui.commune');
				$options['Adressecui']['commune'][$commune] = $commune;
			}

			$options['Cui']['partenaire_id'] = $Controller->Cui->Partenaire->find( 'list', array( 'order' => array( 'Partenaire.libstruc' ) ) );

			$options = array_merge(
				$options,
				$Controller->Cui->Entreeromev3->options()
			);

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
			$cgDepartement = Configure::read( 'Cg.departement' );
			$modelCuiDpt = 'Cui' . $cgDepartement;
			$typeContrat = 'Typecontratcui' . $cgDepartement;
			$libsec = 'Libsecactderact' . $cgDepartement . 'Secteur';

			$result = array_merge(
				parent::_optionsRecordsModels( $params ),
				array(
					'Partenaire',
					'Adressecui',
					'Familleromev3',
					'Domaineromev3',
					'Metierromev3',
					'Appellationromev3',
					$modelCuiDpt,
					$typeContrat,
					$libsec
				)
			);

			return $result;
		}

		/**
		 *
		 * method afterSearch
		 *
		 * On redéfinit afterSearch pour faire de la correction spécifique
		 *
		 */
		public function afterSearch( array $params, array $results ) {
			/*
			 * On met la bonne traduction car le système d'export CSV n'ai pas prévu pour
			 * faire du remplacement de valeurs au sein de traductions.
			 */
			if ($params['configurableQueryFieldsKey'] == 'Cuis.exportcsv') {
				foreach ($results as $key => $result) {
					/*
					 * CD 66 : pour l'état du dossier CUI
					 */
					$find = 'ENUM::ETATDOSSIERCUI66::rupturecontrat__cui66';
					$replace = 'ENUM::ETATDOSSIERCUI66::rupturecontrat';
					if (preg_match ('#'.$find.'#', $result['Cui']['positioncui66'])) {
						$replace = __d ('cui66', $replace);
						$replace = preg_replace ('# %s#', '', $replace);
						$results[$key]['Cui']['positioncui66'] = preg_replace ('#'.$find.'#', $replace, $results[$key]['Cui']['positioncui66']);
					}
				}
			}

			return $results;
		}
	}
?>
