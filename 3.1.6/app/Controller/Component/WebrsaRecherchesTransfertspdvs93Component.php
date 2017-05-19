<?php
	/**
	 * Code source de la classe WebrsaRecherchesTransfertspdvs93Component.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller.Component
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'WebrsaAbstractRecherchesComponent', 'Controller/Component' );

	/**
	 * La classe WebrsaRecherchesTransfertspdvs93Component ...
	 *
	 * @package app.Controller.Component
	 */
	class WebrsaRecherchesTransfertspdvs93Component extends WebrsaAbstractRecherchesComponent
	{
		/**
		 * Surcharge pour permettre de limiter les résultats de la recherche à
		 * ceux dont l'adresse de rang 02 uniquement est sur une des zones
		 * géographiques couverte par la structure référente de l'utilisateur
		 * connecté lorsque celui-ci est un externe (CG 93).
		 *
		 * @param array $query
		 * @param array $filters
		 * @param array $params
		 * @return type
		 */
		protected function _queryConditions( array $query, array $filters, array $params ) {
			$query = parent::_queryConditions( $query, $filters, $params );

			if( Configure::read( 'Cg.departement' ) == 93 ) {
				$Controller = $this->_Collection->getController();
				$type = $Controller->Session->read( 'Auth.User.type' );

				if( stristr( $type, 'externe_' ) !== false ) {
					$query['conditions'][] = array( 'Adressefoyer.rgadr' => '02' );
				}
			}

			return $query;
		}

		/**
		 * Retourne les options stockées en session, liées à l'utilisateur connecté.
		 *
		 * @return array
		 */
		protected function _optionsSession( array $params ) {
			$Controller = $this->_Collection->getController();
			$options = Hash::merge(
				parent::_optionsSession( $params ),
				array(
					'Orientstruct' => array(
						'typeorient_id' => $Controller->InsertionsBeneficiaires->typesorients( array( 'conditions' => array() ) ),
						'structurereferente_id' => $Controller->InsertionsBeneficiaires->structuresreferentes( array( 'type' => 'list', 'prefix' => true ) )
					)
				),
				$this->Allocataires->optionsSessionCommunautesr( 'NvOrientstruct' )
			);

			return $options;
		}
	}
?>