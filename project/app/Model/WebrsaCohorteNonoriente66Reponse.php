<?php
	/**
	 * Code source de la classe WebrsaCohorteNonoriente66Reponse.
	 *
	 * PHP 5.3
	 *
	 * @package app.Model
	 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
	 */
	App::uses( 'AbstractWebrsaCohorteNonoriente66', 'Model/Abstractclass' );
	App::uses( 'ConfigurableQueryFields', 'ConfigurableQuery.Utility' );

	/**
	 * La classe WebrsaCohorteNonoriente66Reponse ...
	 *
	 * @package app.Model
	 */
	class WebrsaCohorteNonoriente66Reponse extends AbstractWebrsaCohorteNonoriente66
	{
		/**
		 * Nom du modèle.
		 *
		 * @var string
		 */
		public $name = 'WebrsaCohorteNonoriente66Reponse';
		
		/**
		 * Liste des champs de formulaire à inserer dans le tableau de résultats
		 * 
		 * @var array
		 */
		public $cohorteFields = array(
			'Personne.id' => array( 'type' => 'hidden' ),
			'Historiqueetatpe.id' => array( 'type' => 'hidden' ),
			'Nonoriente66.id' => array( 'type' => 'hidden' ),
			'Nonoriente66.personne_id' => array( 'type' => 'hidden' ),
			'Nonoriente66.origine' => array( 'type' => 'hidden' ),
			'Nonoriente66.historiqueetatpe_id' => array( 'type' => 'hidden' ),
			'Nonoriente66.user_id' => array( 'type' => 'hidden' ),
			'Orientstruct.origine' => array( 'type' => 'hidden' ),
			'Orientstruct.personne_id' => array( 'type' => 'hidden' ),
			'Orientstruct.statut_orient' => array( 'type' => 'hidden' ),
			'Nonoriente66.selection' => array( 'type' => 'checkbox' ),
			'Nonoriente66.reponseallocataire' => array( 'type' => 'radio', 'legend' => false ),
			'Orientstruct.typeorient_id' => array( 'empty' => true ),
			'Orientstruct.structurereferente_id' => array( 'empty' => true ),
			'Orientstruct.date_valid' => array( 'type' => 'date' )
		);
		
		/**
		 * Retourne le querydata de base, en fonction du département, à utiliser
		 * dans le moteur de recherche.
		 *
		 * @param array $types Les types de jointure alias => type
		 * @return array
		 */
		public function searchQuery( array $types = array() ) {
			$types += array(
				// INNER JOIN
				'Foyer' => 'INNER',
				'Prestation' => 'INNER',
				'Adressefoyer' => 'INNER',
				'Calculdroitrsa' => 'INNER',
				'Dossier' => 'INNER',
				'Situationdossierrsa' => 'INNER',
				'Adresse' => 'INNER',
				'Nonorient66' => 'INNER',
				
				// LEFT OUTER JOIN
				'Orientstruct' => 'LEFT OUTER',
				'Structurereferente' => 'LEFT OUTER',
				'Typeorient' => 'LEFT OUTER',
				'Informationpe' => 'LEFT OUTER',
				'Canton' => 'LEFT OUTER',
				'PersonneReferent' => 'LEFT OUTER',
				'Referentparcours' => 'LEFT OUTER',
				'Structurereferenteparcours' => 'LEFT OUTER',
				'Historiqueetatpe' => 'LEFT OUTER',
				'Detaildroitrsa' => 'LEFT OUTER',
			);
			
			$cacheKey = Inflector::underscore( $this->useDbConfig ).'_'.Inflector::underscore( $this->alias ).'_'.Inflector::underscore( __FUNCTION__ ).'_'.sha1( serialize( $types ) );
			$query = Cache::read( $cacheKey );

			if( $query === false ) {
				$query = parent::searchQuery($types);
				
				// Force la présence des champs nbenfants et canton, utiles pour le prepareFormDataCohorte()
				$query['fields'][] = $query['fields']['Foyer.nbenfants'];
				if( Configure::read( 'CG.cantons' ) ) {
					$query['fields'][] = 'Canton.canton';
				}
				
				$query['conditions']['Nonoriente66.origine'] = 'notisemploi';
				$query['conditions'][] = array(
					'OR' => array(
						'Orientstruct.id IS NULL',
						'Orientstruct.statut_orient !=' => 'Orienté'
					)
				);
				
				Cache::write($cacheKey, $query);
			}
			
			return $query;
		}
		
		/**
		 * Logique de sauvegarde de la cohorte
		 * 
		 * @param type $data
		 * @param type $params
		 * @return boolean
		 */
		public function saveCohorte( array $data, array $params = array(), $user_id = null ) {
			foreach ( $data as $key => $value ) {
				// Si non selectionné, on retire tout
				if ( $value['Nonoriente66']['selection'] === '0' ) {
					unset($data[$key]);
					continue;
				}
				
				$data[$key]['Orientstruct']['structurereferente_id'] = suffix(Hash::get($value, 'Orientstruct.structurereferente_id'));
				$data[$key]['Orientstruct']['personne_id'] = Hash::get($value, 'Personne.id');
				$data[$key]['Orientstruct']['origine'] = 'cohorte';
				$data[$key]['Orientstruct']['statut_orient'] = 'Orienté';
			}
			
			$this->Nonoriente66->begin();
			
			$success = !empty($data) && $this->Nonoriente66->saveAll($data, array('atomic' => false));
			$success = !empty($data)
				&& $this->Nonoriente66->Personne->Orientstruct->saveAll($data, array('atomic' => false))
				&& $success;
			
			if ($success) {
				$this->Nonoriente66->commit();
			} else {
				$this->Nonoriente66->rollback();
			}
			
			return $success;
		}
		
		/**
		 * Préremplissage du formulaire en cohorte
		 *
		 * @param array $results
		 * @param array $params
		 * @param array $options
		 * @return array
		 */
		public function prepareFormDataCohorte( array $results, array $params = array(), array &$options = array() ) {
			$prepro = Configure::read( 'Nonoriente66.TypeorientIdPrepro' );
			$social = Configure::read( 'Nonoriente66.TypeorientIdSocial' );
			
			$structurereferente = $this->Nonoriente66->structuresAutomatiques();
			
			foreach ($results as $key => $value) {
				$typeorient =& $results[$key]['Orientstruct']['typeorient_id'];
				$typeorient = Hash::get($value, 'Foyer.nbenfants') === 0 ? $prepro : $social;
				if ( Configure::read( 'CG.cantons' ) && isset($structurereferente[Hash::get($value, 'Canton.canton')][$typeorient]) ) {
					$results[$key]['Orientstruct']['structurereferente_id'] = $structurereferente[Hash::get($value, 'Canton.canton')][$typeorient];
				}
			}
			
			return $results;
		}
	}
?>