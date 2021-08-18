<?php
	/**
	 * Code source de la classe WebrsaRecherchesOrientsstructsComponent.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller.Component
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'WebrsaAbstractRecherchesComponent', 'Controller/Component' );

	/**
	 * La classe WebrsaRecherchesOrientsstructsComponent ...
	 *
	 * @package app.Controller.Component
	 */
	class WebrsaRecherchesOrientsstructsComponent extends WebrsaAbstractRecherchesComponent
	{
		/**
		 * Surcharge de la méthode params pour limiter les utilisateurs externes
		 * au code INSEE ou à la valeur de structurereferente_id de l'orientation.
		 *
		 * @param array $params
		 * @return array
		 */
		protected function _params( array $params = array() ) {
			$defaults = array(
				'structurereferente_id' => 'Orientstruct.structurereferente_id'
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
			$departement = Configure::read( 'Cg.departement' );

			$exists = array( '1' => 'Oui', '0' => 'Non' );

			$options = Hash::merge(
				parent::_optionsEnums( $params ),
				array(
					'Orientstruct' => array(
						'statut_orient' => $Controller->Orientstruct->enum( 'statut_orient' )
					),
					'Personne' => array(
						'has_contratinsertion' => $exists,
						'has_personne_referent' => $exists,
						'is_inscritpe' => $exists,
					)
				)
			);

			if( $departement == 58 ) {
				$options['Activite']['act'] = $Controller->Orientstruct->Personne->Activite->enum( 'act' );
			}

			return $options;
		}

		/**
		 * Retourne les options stockées en session, liées à l'utilisateur connecté.
		 *
		 * @return array
		 */
		protected function _optionsSession( array $params ) {
			$Controller = $this->_Collection->getController();
			$departement = Configure::read( 'Cg.departement' );

			$options = Hash::merge(
				parent::_optionsSession( $params ),
				array(
					'Orientstruct' => array(
						'typeorient_id' => $Controller->InsertionsBeneficiaires->typesorients(),
						'structurereferente_id' => $Controller->InsertionsBeneficiaires->structuresreferentes( array( 'type' => 'list', 'conditions' => array( 'Structurereferente.orientation' => 'O' ) + $Controller->InsertionsBeneficiaires->conditions['structuresreferentes'] ) ),
					)
				)
			);

			if( $departement == 93 ) {
				$options['Orientstruct']['propo_algo'] = $Controller->Orientstruct->Typeorient->listTypeParent();
				$options = Hash::merge(
					$options,
					$this->Allocataires->optionsSessionCommunautesr( 'Orientstruct' )
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

			$options = Hash::merge(
				parent::_optionsRecords( $params ),
				array(
					'Orientstruct' => array(
						'structureorientante_id' => $Controller->Orientstruct->Structurereferente->listOptions( array( 'orientation' => 'O' ) ),
						'referentorientant_id' => $Controller->Orientstruct->Structurereferente->Referent->WebrsaReferent->listOptions(),
					)
				)
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
			return array_unique(
				array_merge(
					parent::_optionsRecordsModels( $params ),
					array( 'Typeorient', 'Structurereferente', 'Referent' )
				)
			);
		}
	}
?>