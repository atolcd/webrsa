<?php
	/**
	 * Code source de la classe WebrsaRecherchesRendezvousComponent.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller.Component
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'WebrsaAbstractRecherchesComponent', 'Controller/Component' );

	/**
	 * La classe WebrsaRecherchesRendezvousComponent ...
	 *
	 * @package app.Controller.Component
	 */
	class WebrsaRecherchesRendezvousComponent extends WebrsaAbstractRecherchesComponent
	{
		/**
		 * Components utilisés par ce component.
		 *
		 * @var array
		 */
		public $components = array( 'Allocataires', 'InsertionsBeneficiaires', 'WebrsaUsers', 'Gestionzonesgeos' );

		/**
		 * Surcharge de la méthode params pour limiter les utilisateurs externes
		 * au code INSEE ou à la valeur de structurereferente_id du Rendezvous.
		 *
		 * @param array $params
		 * @return array
		 */
		protected function _params( array $params = array() ) {
			$defaults = array(
				'structurereferente_id' => 'Rendezvous.structurereferente_id'
			);

			return parent::_params( $params + $defaults );
		}

		/**
		 * Retourne les options de type "enum", c'est à dire liées aux schémas des
		 * tables de la base de données.
		 *
		 * @return array
		 */
		protected function _optionsEnums( array $params = array() ) {
			$Controller = $this->_Collection->getController();
			$Controller->loadModel( 'Option' );

			$options = Hash::merge(
				parent::_optionsEnums( $params ),
				array(
					'Personne' => array(
						'trancheage' => Configure::read( 'Search.Options.enums.personne.Personne.trancheage'),
					),
					// TODO
					'Prestation' => array(
						'exists' => array(
							'0' => 'Sans prestation',
							'1' => 'Demandeur ou Conjoint du RSA'
						)
					)
				)
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

			$options = Hash::merge(
				parent::_optionsRecords( $params ),
				array(
					'Rendezvous' => array(
						'statutrdv_id' => $Controller->Rendezvous->Statutrdv->find( 'list' ),
						'typerdv_id' => $Controller->Rendezvous->Typerdv->find( 'list', array( 'fields' => array( 'id', 'libelle' ) ) ),
						'permanence_id' => $Controller->Rendezvous->Permanence->find( 'list' )
					)
				)
			);

			if( Configure::read( 'Rendezvous.useThematique' ) ) {
				$options['Rendezvous']['thematiquerdv_id'] = $Controller->Rendezvous->Thematiquerdv->find( 'list', array( 'fields' => array( 'Thematiquerdv.id', 'Thematiquerdv.name', 'Thematiquerdv.typerdv_id' ) ) );
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
			$result = array_merge(
				parent::_optionsRecordsModels( $params ),
				array( 'Statutrdv', 'Typerdv', 'Permanence' )
			);

			if( Configure::read( 'Rendezvous.useThematique' ) ) {
				$result[] = 'Thematiquerdv';
			}

			return $result;
		}

		/**
		 * Retourne les options stockées en session, liées à l'utilisateur connecté.
		 *
		 * @return array
		 */
		protected function _optionsSession( array $params ) {
			$departement = Configure::read( 'Cg.departement' );

			$options = parent::_optionsSession( $params );

			if( $departement == 93 ) {
				$options = Hash::merge(
					$options,
					$this->Allocataires->optionsSessionCommunautesr( 'Rendezvous' )
				);
			}

			return $options;
		}


		/**
		 * Surcharge afin de permettre, pour le CD 93, que les RDV ne soient plus
		 * filtrés automatiquement suivant le code INSEE de l'adresse actuelle du
		 * bénéficiaire par-rapport aux zones géographiques auxquelles l'utilisateur
		 * a accès, mais par-rapport aux structures référentes auxquelles
		 * l'utilisateur a accès (celles qui se trouvent dans la liste déroulante
		 * "Structure proposant le RDV").
		 *
		 * @param array $query
		 * @param array $filters
		 * @param array $params
		 * @return array
		 */
		protected function _queryConditions( array $query, array $filters, array $params ) {
			$Controller = $this->_Collection->getController();
			$departement = Configure::read( 'Cg.departement' );

			if( 93 == $departement ) {
				$type = $Controller->Session->read( 'Auth.User.type' );

				if( 0 === strpos( $type, 'externe_' ) ) {
					$params['completequery_zonesgeos_disabled'] = true;

					$query['fields'] = array_merge(
						$query['fields'],
						array(
							'Adressefoyer.dtemm',
							'Adresse.numcom',
							'Adressefoyer02.dtemm',
							'Adresse02.numcom',
							'Adressefoyer03.dtemm',
							'Adresse03.numcom'
						)
					);

					$structuresreferentesUser = $this->WebrsaUsers->structuresreferentes();
					$structuresreferentesTerritoire = $this->InsertionsBeneficiaires->structuresreferentes(
						array(
							'type' => 'ids',
							'prefix' => false
						)
					);

					$codesinsee = array_keys( $this->Gestionzonesgeos->listeCodesInsee() );

					$query['conditions'][] = array(
						'OR' => array(
							array(
								'Rendezvous.structurereferente_id' => $structuresreferentesUser
							),
							array(
								'Rendezvous.structurereferente_id' => $structuresreferentesTerritoire,
								'OR' => array(
									array(
										'Rendezvous.daterdv >= Adressefoyer.dtemm',
										'Adresse.numcom' => $codesinsee
									),
									array(
										'Adresse02.id IS NOT NULL',
										'Rendezvous.daterdv < Adressefoyer.dtemm',
										'Rendezvous.daterdv >= Adressefoyer02.dtemm',
										'Adresse02.numcom' => $codesinsee
									),
									array(
										'Adresse03.id IS NOT NULL',
										'Rendezvous.daterdv < Adressefoyer02.dtemm',
										'Rendezvous.daterdv >= Adressefoyer03.dtemm',
										'Adresse03.numcom' => $codesinsee
									)
								)
							)
						)
					);
				}
			}

			$query = parent::_queryConditions( $query, $filters, $params );

			return $query;
		}
	}
?>