<?php
	/**
	 * Code source de la classe WebrsaCohortesAlgoorientationComponent.
	 *
	 * PHP 7.2
	 *
	 * @package app.Controller.Component
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'WebrsaAbstractCohortesComponent', 'Controller/Component' );

	/**
	 * La classe WebrsaCohortesAlgoorientationComponent ...
	 *
	 * @package app.Controller.Component
	 */
	class WebrsaCohortesAlgoorientationComponent extends WebrsaAbstractCohortesComponent
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

			if( isset( $Controller->Personne ) === false ) {
				$Controller->loadModel( 'Personne' );
			}

			$result = Hash::merge(
				parent::_optionsEnums( $params ),
				$Controller->Personne->Orientstruct->enums(),
				$Controller->Personne->Foyer->Dossier->Suiviinstruction->enums(),
				array(
					'Orientstruct' => array(
						'statut_orient' => array( 'Orienté' => 'A valider', 'En attente' => 'En attente' )
					),
				)
			);


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
						'typeorient_id' =>  $Controller->Personne->Orientstruct->Typeorient->listOptions(),
						'structurereferente_id' => $Controller->Personne->Orientstruct->Structurereferente->list1Options('O'),
					),
                    'Personne' => array(
						'has_contratinsertion' => array(
							'1' => 'Oui',
							'0' => 'Non'
                        ),
                        'has_personne_referent' => array(
							'1' => 'Oui',
							'0' => 'Non'
						),
                        'is_inscritpe' => array(
							'1' => 'Oui',
							'0' => 'Non'
						)
					)
				)
			);
		}

    }