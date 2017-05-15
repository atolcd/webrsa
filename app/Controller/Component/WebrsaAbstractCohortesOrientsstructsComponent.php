<?php
	/**
	 * Code source de la classe WebrsaAbstractCohortesOrientsstructsComponent.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller.Component
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'WebrsaAbstractCohortesComponent', 'Controller/Component' );

	/**
	 * La classe WebrsaAbstractCohortesOrientsstructsComponent ...
	 *
	 * @package app.Controller.Component
	 */
	abstract class WebrsaAbstractCohortesOrientsstructsComponent extends WebrsaAbstractCohortesComponent
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
			$departement = (int)Configure::read( 'Cg.departement' );

			if( isset( $Controller->Personne ) === false ) {
				$Controller->loadModel( 'Personne' );
			}

			// Pré-orientation
			$propo_algo = $Controller->Orientstruct->Typeorient->listOptionsPreorientationCohortes93();
			if( $departement === 93 ) {
				$propo_algo['NOTNULL'] = 'Renseigné';
				$propo_algo['NULL'] = 'Non renseigné';
			}

			$result = Hash::merge(
				parent::_optionsEnums( $params ),
				$Controller->Orientstruct->enums(),
				$Controller->Orientstruct->Personne->Foyer->Dossier->Suiviinstruction->enums(),
				array(
					'Orientstruct' => array(
						'propo_algo' => $propo_algo,
						'statut_orient' => array( 'Orienté' => 'A valider', 'En attente' => 'En attente' )
					),
					'Personne' => array(
						'has_dsp' => array(
							'1' => 'Oui',
							'0' => 'Non'
						)
					)
				)
			);

			unset( $result['Orientstruct']['statut_orient']['Non orienté'] ); // FIXME: dans la conf ?

			return $result;
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
			$Controller = $this->_Collection->getController();
			$departement = (int)Configure::read( 'Cg.departement' );

			if( isset( $Controller->Personne ) === false ) {
				$Controller->loadModel( 'Personne' );
			}

			if( !isset( $Controller->{$params['modelRechercheName']} ) ) {
				$Controller->loadModel( $params['modelRechercheName'] );
			}

			return Hash::merge(
				parent::_optionsRecords( $params ),
				array(
					'Orientstruct' => array(
						'typeorient_id' => $departement === 93
							? $Controller->Personne->Orientstruct->Typeorient->listOptionsCohortes93()
							: $Controller->Personne->Orientstruct->Typeorient->listOptions(),
						'structurereferente_id' => $Controller->Personne->Orientstruct->Structurereferente->list1Options(),
					),
					'structuresAutomatiques' => $Controller->{$params['modelRechercheName']}->structuresAutomatiques()
				)
			);
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
			return Hash::merge(
				parent::_optionsRecordsModels( $params ),
				array( 'Typeorient', 'Structurereferente' )
			);
		}
	}
?>