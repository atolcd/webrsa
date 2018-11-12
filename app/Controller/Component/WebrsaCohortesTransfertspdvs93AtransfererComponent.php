<?php
	/**
	 * Code source de la classe WebrsaCohortesTransfertspdvs93AtransfererComponent.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller.Component
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'WebrsaAbstractCohortesComponent', 'Controller/Component' );

	/**
	 * La classe WebrsaCohortesTransfertspdvs93AtransfererComponent ...
	 *
	 * @package app.Controller.Component
	 */
	class WebrsaCohortesTransfertspdvs93AtransfererComponent extends WebrsaAbstractCohortesComponent
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

			return Hash::merge(
				parent::_optionsEnums( $params ),
				array(
					'Transfertpdv93' => array(
						'action' => array( '1' => 'Valider', '0' => 'En attente' )
					)
				),
				$Controller->Dossier->Foyer->Personne->Contratinsertion->enums(),
				$Controller->Dossier->Foyer->Personne->Contratinsertion->Cer93->enums()
			);
		}

		/**
		 * Retourne les options stockées liées à des enregistrements en base de
		 * données, ne dépendant pas de l'utilisateur connecté.
		 *
		 * La mise en cache se fera dans ma méthode _options().
		 *
		 * @see _optionsRecordsModels(), _options()
		 *
		 * @return array
		 */
		protected function _optionsRecords( array $params ) {
			$Controller = $this->_Collection->getController();

			if( !isset( $Controller->Cohortetransfertpdv93 ) ) {
				$Controller->loadModel( 'Cohortetransfertpdv93' );
			}

			if( !isset( $Controller->{$params['modelRechercheName']} ) ) {
				$Controller->loadModel( $params['modelRechercheName'] );
			}

			$structuresreferentesParCodeInsee = $Controller->Dossier->Foyer->Personne->Orientstruct->Structurereferente->WebrsaStructurereferente->listeParCodeInsee();

			return Hash::merge(
				parent::_optionsRecords( $params ),
				array(
					'Orientstruct' => array(
						'typeorient_id' => $Controller->Dossier->Foyer->Personne->Orientstruct->Typeorient->listOptions()
					),
					'Transfertpdv93' => array(
						'structurereferente_dst_id' => array()
					),
					'Structurereferente' => array(
						'listeParCodeInsee' => $structuresreferentesParCodeInsee
					)
				)
			);
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
			return array_merge(
				parent::_optionsRecordsModels( $params ),
				array( 'Typeorient', 'Structurereferente', 'StructurereferenteZonegeographique', 'Zonegeographique' )
			);
		}

		/**
		 * Retourne les options stockées en session, liées à l'utilisateur connecté.
		 *
		 * @return array
		 */
		protected function _optionsSession( array $params ) {
			$Controller = $this->_Collection->getController();

			return Hash::merge(
				parent::_optionsSession( $params ),
				array(
					'Structurereferente' => array(
						'listeParTypeorientId' => $Controller->InsertionsBeneficiaires->structuresreferentes(
							array(
								'prefix' => false,
								'type' => 'typeorient_id',
								'conditions' => array (
									'Structurereferente.actif' => 'O',
									'Structurereferente.actif_cohorte' => 'O',
								)
							)
						)
					)
				)
			);
		}
	}
?>