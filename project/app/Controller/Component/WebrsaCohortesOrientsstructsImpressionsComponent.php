<?php
	/**
	 * Code source de la classe WebrsaCohortesOrientsstructsImpressionsComponent.
	 *
	 * PHP 5.3
	 *
	 * @package app.Controller.Component
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'WebrsaAbstractCohortesImpressionsComponent', 'Controller/Component' );

	/**
	 * La classe WebrsaCohortesOrientsstructsImpressionsComponent ...
	 *
	 * @package app.Controller.Component
	 */
	class WebrsaCohortesOrientsstructsImpressionsComponent extends WebrsaAbstractCohortesImpressionsComponent
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
			$departement = Configure::read( 'Cg.departement' );

			if( isset( $Controller->Personne ) === false ) {
				$Controller->loadModel( 'Personne' );
			}

			// Pré-orientation
			$propo_algo = $Controller->Orientstruct->Typeorient->listOptionsPreorientationCohortes93();
			if( $departement == 93 ) {
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
						'impression' => array(
							'1' => 'Imprimé',
							'0' => 'Non imprimé'
						)
					)
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
						'typeorient_id' => $Controller->InsertionsBeneficiaires->typesorients(),
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

		/**
		 * Modifie la requête pour ramener la clé primaire de l'enregistrement,
		 * le document PDF et le chemin cmspath dans les résultats.
		 *
		 * @param array $query
		 * @param array $params
		 * @return array
		 */
		protected function _queryPdfs( array $query, array $params ) { // TODO: pas dans la classe mère
			// Changement de limit
			unset( $query['limit'] );

			if( $limit = Configure::read( 'nb_limit_print' ) ) {
				$query['limit'] = $limit;
			}

			// Champs nécessaires
			$query['fields'] = array(
				'Orientstruct.id',
				'Pdf.document',
				'Pdf.cmspath',
			);

			// Jointure supplémentaire nécessaire
			$query['joins'][] = array(
				'table'      => 'pdfs',
				'alias'      => 'Pdf',
				'type'       => 'INNER',
				'foreignKey' => false,
				'conditions' => array(
					'Pdf.fk_value = Orientstruct.id',
					'Pdf.modele' => 'Orientstruct',
				)
			);

			return $query;
		}

		/**
		 * Retourne un array de PDF, sous la clé <FIXME> à partir du query, ou
		 * le nombre de documents n'ayant pas pu être imprimés.
		 *
		 * @param array $query
		 * @param array $params
		 * @return integer|array
		 */
		protected function _pdfs( array $query, array $params ) {
			$Controller = $this->_Collection->getController();

			$query = $this->_queryPdfs( $query, $params );

			$Controller->{$params['modelName']}->forceVirtualFields = true;
			$results = $Controller->{$params['modelName']}->find( 'all', $query );

			// Si le contenu du PDF n'est pas dans la table pdfs, aller le chercher sur le serveur CMS
			// TODO: Une méthode qui n'agirait que sur les résultats
			$errors = 0;
			foreach( $results as $i => $result ) {
				if( empty( $result['Pdf']['document'] ) && !empty( $result['Pdf']['cmspath'] ) ) {
					$pdf = Cmis::read( $result['Pdf']['cmspath'], true );
					if( !empty( $pdf['content'] ) ) {
						$results[$i]['Pdf']['document'] = $pdf['content'];
					}
				}

				// Gestion des erreurs: si on n'a toujours pas le document
				if( empty( $results[$i]['Pdf']['document'] ) ) {
					$errors++;
				}
			}

			return $errors === 0 ? $results : $errors;
		}

		/**
		 * Post-traitement des résultats de la requête (par exemple pour la mise
		 * à jour d'une date d'impression).
		 * Cette fonction doit retourner vrai pour que l'envoi se fasse.
		 *
		 * @param array $results
		 * @param array $params
		 * @return boolean
		 */
		protected function _postProcess( array $results, array $params ) { // TODO: return true
			$Controller = $this->_Collection->getController();

			$Controller->{$params['modelName']}->begin();

			$conditions = array(
				'Orientstruct.id' => Hash::extract( $results, '{n}.Orientstruct.id' ),
				'Orientstruct.date_impression IS NULL'
			);

			$query = array(
				'fields' => array( 'Orientstruct.id' ),
				'conditions' => $conditions,
				'contain' => false,
				'recursive' => -1
			);
			$found = $Controller->Personne->Orientstruct->find( 'first', $query );

			$success = empty( $found ) || $Controller->Personne->Orientstruct->updateAllUnBound(
				array( 'Orientstruct.date_impression' => date( "'Y-m-d'" ) ),
				$conditions
			);

			if( $success ) {
				$Controller->{$params['modelName']}->commit();
			}
			else {
				$Controller->{$params['modelName']}->rollback();
			}

			return $success;
		}
	}
?>